<div id="departments">
	<form @submit.prevent="saveDepartment">
		<div class="row" style="margin: 0;">
			<fieldset class="scheduler-border">
				<legend class="scheduler-border">Department Entry Form</legend>
				<div class="control-group">
					<div class="col-xs-12 col-md-6 col-md-offset-3">
						<div class="form-group clearfix">
							<label class="control-label col-xs-4 col-md-4">Department Name:</label>
							<div class="col-xs-8 col-md-8">
								<input type="text" class="form-control" v-model="department.Department_Name" required>
							</div>
						</div>
						<div class="form-group clearfix">
							<div class="col-xs-12 col-md-12 text-right">
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
				<datatable :columns="columns" :data="departments" :filter-by="filter" style="margin-bottom: 5px;">
					<template scope="{ row }">
						<tr>
							<td>{{ row.sl }}</td>
							<td>{{ row.Department_Name }}</td>
							<td>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<i class="btnEdit fa fa-pencil" @click="editDepartment(row)"></i>
									<i class="btnDelete fa fa-trash" @click="deleteDepartment(row.Department_SlNo)"></i>
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
		el: '#departments',
		data() {
			return {
				department: {
					Department_SlNo: 0,
					Department_Name: '',
				},
				departments: [],

				columns: [{
						label: 'Sl',
						field: 'sl',
						align: 'center'
					},
					{
						label: 'Department Name',
						field: 'Department_Name',
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
			this.getDepartments();
		},
		methods: {
			getDepartments() {
				axios.get('/get_departments').then(res => {
					this.departments = res.data.map((item, index) => {
						item.sl = index + 1;
						return item;
					});
				})
			},
			saveDepartment() {
				let url = '/add_department';
				if (this.department.Department_SlNo != 0) {
					url = '/update_department';
				}

				axios.post(url, this.department).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.status) {
						this.resetForm();
						this.getDepartments();
					}
				})
			},
			editDepartment(department) {
				let keys = Object.keys(this.department);
				keys.forEach(key => {
					this.department[key] = department[key];
				})
			},
			deleteDepartment(departmentId) {
				if (confirm('Are you sure?')) {
					axios.post('/delete_department', {
						departmentId: departmentId
					}).then(res => {
						let r = res.data;
						alert(r.message);
						if (r.status) {
							this.getDepartments();
						}
					})
				}
			},
			resetForm() {
				this.department = {
					Department_SlNo: 0,
					Department_Name: '',
				}
			}
		}
	})
</script>