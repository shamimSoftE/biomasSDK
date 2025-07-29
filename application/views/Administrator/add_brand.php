<div id="brands">
	<form @submit.prevent="saveBrand">
		<div class="row" style="margin: 0;">
			<fieldset class="scheduler-border">
				<legend class="scheduler-border">Brand Entry Form</legend>
				<div class="control-group">
					<div class="col-xs-12 col-md-6 col-md-offset-3">
						<div class="form-group clearfix">
							<label class="control-label col-xs-4 col-md-4">Brand Name:</label>
							<div class="col-xs-8 col-md-8">
								<input type="text" class="form-control" v-model="brand.brand_name" required>
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
				<datatable :columns="columns" :data="brands" :filter-by="filter" style="margin-bottom: 5px;">
					<template scope="{ row }">
						<tr>
							<td>{{ row.sl }}</td>
							<td>{{ row.brand_name }}</td>
							<td>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<i class="btnEdit fa fa-pencil" @click="editBrand(row)"></i>
									<i class="btnDelete fa fa-trash" @click="deleteBrand(row.brand_SiNo)"></i>
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
		el: '#brands',
		data() {
			return {
				brand: {
					brand_SiNo: 0,
					brand_name: '',
				},
				brands: [],

				columns: [{
						label: 'Sl',
						field: 'sl',
						align: 'center'
					},
					{
						label: 'Brand Name',
						field: 'brand_name',
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
			this.getBrands();
		},
		methods: {
			getBrands() {
				axios.get('/get_brands').then(res => {
					this.brands = res.data.map((item, index) => {
						item.sl = index + 1;
						return item;
					});
				})
			},
			saveBrand() {
				if (this.brand.brand_name == '') {
					Swal.fire({
						icon: "error",
						text: "Brand name is empty!",
					});
					return;
				}
				let url = '/add_brand';
				if (this.brand.brand_SiNo != 0) {
					url = '/update_brand';
				}

				axios.post(url, this.brand).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.status) {
						this.resetForm();
						this.getBrands();
					}
				})
			},
			editBrand(brand) {
				let keys = Object.keys(this.brand);
				keys.forEach(key => {
					this.brand[key] = brand[key];
				})
			},
			deleteBrand(brandId) {
				if (confirm('Are you sure?')) {
					axios.post('/delete_brand', {
						brandId: brandId
					}).then(res => {
						let r = res.data;
						alert(r.message);
						if (r.status) {
							this.getBrands();
						}
					})
				}
			},
			resetForm() {
				this.brand = {
					brand_SiNo: 0,
					brand_name: '',
				}
			}
		}
	})
</script>