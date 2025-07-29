<div id="categories">
	<form @submit.prevent="saveCategory">
		<div class="row" style="margin: 0;">
			<fieldset class="scheduler-border">
				<legend class="scheduler-border">Category Entry Form</legend>
				<div class="control-group">
					<div class="col-xs-12 col-md-6 col-md-offset-3">
						<div class="form-group clearfix">
							<label class="control-label col-xs-4 col-md-4">Category Name:</label>
							<div class="col-xs-8 col-md-8">
								<input type="text" class="form-control" v-model="category.ProductCategory_Name" required>
							</div>
						</div>
						<div class="form-group clearfix">
							<label class="control-label col-xs-4 col-md-4">Description:</label>
							<div class="col-xs-8 col-md-8">								
								<textarea class="form-control" v-model="category.ProductCategory_Description"></textarea>
							</div>
						</div>
						<div class="form-group clearfix">
							<div class="col-xs-12 col-md-12 text-right">
								<input type="button" class="btnReset" value="Reset" @click="resetForm">
								<input type="submit" class="btnSave" value="Save">
							</div>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</form>

	<div class="row">
		<div class="col-sm-12 form-inline">
			<div class="form-group">
				<label for="filter" class="sr-only">Filter</label>
				<input type="text" class="form-control" v-model="filter" placeholder="Filter">
			</div>
		</div>
		<div class="col-md-12">
			<div class="table-responsive">
				<datatable :columns="columns" :data="categories" :filter-by="filter" style="margin-bottom: 5px;">
					<template scope="{ row }">
						<tr>
							<td>{{ row.sl }}</td>
							<td>{{ row.ProductCategory_Name }}</td>
							<td>{{ row.ProductCategory_Description }}</td>
							<td>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<i class="btnEdit fa fa-pencil" @click="editCategory(row)"></i>
									<i class="btnDelete fa fa-trash" @click="deleteCategory(row.ProductCategory_SlNo)"></i>
								<?php } ?>
							</td>
						</tr>
					</template>
				</datatable>
				<datatable-pager v-model="page" type="abbreviated" :per-page="per_page" style="margin-bottom: 50px;"></datatable-pager>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#categories',
		data() {
			return {
				category: {
					ProductCategory_SlNo: 0,
					ProductCategory_Name: '',
					ProductCategory_Description: '',
				},
				categories: [],

				columns: [{
						label: 'Sl',
						field: 'sl',
						align: 'center'
					},
					{
						label: 'Category Name',
						field: 'ProductCategory_Name',
						align: 'center'
					},
					{
						label: 'Description',
						field: 'ProductCategory_Description',
						align: 'center'
					},
					{
						label: 'Action',
						align: 'center',
						filterable: false
					}
				],
				page: 1,
				per_page: 100,
				filter: ''
			}
		},
		created() {
			this.getCategories();
		},
		methods: {
			getCategories() {
				axios.get('/get_categories').then(res => {
					this.categories = res.data.map((item, index) => {
						item.sl = index + 1;
						return item;
					});
				})
			},
			saveCategory() {
				if (this.category.ProductCategory_Name == '') {
					Swal.fire({
						icon: "error",
						text: "Category name is empty!",
					});
					return;
				}
				let url = '/add_category';
				if (this.category.ProductCategory_SlNo != 0) {
					url = '/update_category';
				}

				axios.post(url, this.category).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.status) {
						this.resetForm();
						this.getCategories();
					}
				})
			},
			editCategory(category) {
				let keys = Object.keys(this.category);
				keys.forEach(key => {
					this.category[key] = category[key];
				})
			},
			deleteCategory(categoryId) {
				if (confirm('Are you sure?')) {
					axios.post('/delete_category', {
						categoryId: categoryId
					}).then(res => {
						let r = res.data;
						alert(r.message);
						if (r.status) {
							this.getCategories();
						}
					})
				}
			},
			resetForm() {
				this.category = {
					ProductCategory_SlNo: 0,
					ProductCategory_Name: '',
					ProductCategory_Description: '',
				}
			}
		}
	})
</script>