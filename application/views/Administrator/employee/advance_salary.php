<style>
    .v-select {
		margin-bottom: 5px;
		background: #fff;
	}

	.v-select .dropdown-toggle {
		padding: 0px;
		height: 25px;
		border: none;
	}

	.v-select input[type=search],
	.v-select input[type=search]:focus {
		margin: 0px;
	}

	.v-select .vs__selected-options {
		overflow: hidden;
		flex-wrap: nowrap;
	}

	.v-select .selected-tag {
		margin: 2px 0px;
		white-space: nowrap;
		position: absolute;
		left: 0px;
	}

	.v-select .vs__actions {
		margin-top: -5px;
	}

	.v-select .dropdown-menu {
		width: auto;
		overflow-y: auto;
	}
</style>

<div id="salaryAdvance">
	<form @submit.prevent="saveSalaryAdvance">
		<div class="row" style="margin: 0;">
			<fieldset class="scheduler-border">
				<legend class="scheduler-border">Salary Advance Form</legend>
				<div class="control-group">
					<div class="col-xs-12 col-md-2"></div>
					<div class="col-xs-12 col-md-4">
                        <div class="form-group">
                            <label class="col-xs-3 col-md-3 control-label no-padding-right"> Employee </label>
                            <div class="col-xs-8 col-md-8">
                                <v-select v-bind:options="employees" v-model="selectedEmployee" label="display_name" placeholder="Select Employee"></v-select>
                            </div>
                            <div class="col-xs-1 col-md-1" style="padding: 0;">
								<a href="<?= base_url('employee') ?>" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px;margin-left: -8px;" target="_blank" title="Add New Month"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
							</div>
                        </div>

                        <div class="form-group">
							<label class="col-xs-3 col-md-3 control-label no-padding-right"> Month </label>
							<div class="col-xs-8 col-md-8">
								<v-select v-bind:options="months" label="month_name" v-model="selectedMonth"></v-select>
							</div>
							<div class="col-xs-1 col-md-1" style="padding: 0;">
								<a href="<?= base_url('month') ?>" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px;margin-left: -8px;" target="_blank" title="Add New Month"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
							</div>
						</div>
                        <div class="form-group">
                            <label class="col-xs-3 col-md-3 control-label no-padding-right"> Amount </label>
                            <div class="col-xs-9 col-md-9">
                                <input type="number" placeholder="Salary Advance" class="form-control" v-model="salary.advance" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-12 col-md-4">
                        <div class="form-group">
							<label class="col-xs-2 col-md-2 control-label no-padding-right"> Note </label>
							<div class="col-xs-10 col-md-10">
                                <textarea class="form-control" v-model="salary.note"></textarea>
							</div>
						</div>
                        <div class="form-group">
							<div class="col-xs-12 col-md-12 text-right">
								<input type="submit" class="btnSave" value="Save">
							</div>
						</div>
                    </div>
                    <div class="col-xs-12 col-md-2"></div>
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
				<datatable :columns="columns" :data="salaryAdvances" :filter-by="filter" style="margin-bottom: 5px;">
					<template scope="{ row }">
						<tr>
							<td>{{ row.sl }}</td>
							<td>{{ row.Employee_Name }} - {{ row.Employee_ID }}</td>
							<td>{{ row.month_name }}</td>
							<td>{{ row.advance }}</td>
							<td>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<i class="btnEdit fa fa-pencil" @click="editAdvanceSalary(row)"></i>
									<i class="btnDelete fa fa-trash" @click="deleteAdvanceSalary(row.id)"></i>
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
		el: '#salaryAdvance',
		data() {
			return {
				salary: {
                    advanceSalaryId: 0,
					advance: 0,
					note: null,
					employee_id: '',
					month_id: '',
				},
				salaryAdvances: [],

				columns: [{
						label: 'Sl',
						field: 'sl',
						align: 'center'
					},
					{
						label: 'Employee Name',
						field: 'employee_name',
						align: 'center'
					},
					{
						label: 'Month',
						field: 'month_name',
						align: 'center'
					},
					{
						label: 'Advance',
						field: 'advance',
						align: 'center'
					},
					{
						label: 'Action',
						align: 'center',
						filterable: false
					}
				],
                employees: [],
				selectedEmployee: null,
                months: [],
				selectedMonth : null,
				page: 1,
				per_page: 100,
				filter: ''
			}
		},
		created() {
			this.getAdvanceSalary();
            this.getEmployees();
            this.getMonths();
		},
		methods: {
			getAdvanceSalary() {
				axios.get('/get_salary_advance').then(res => {
					this.salaryAdvances = res.data.map((item, index) => {
						item.sl = index + 1;
						return item;
					});
				})
			},
            getEmployees() {
				axios.get('/get_employees').then(res => {
					this.employees = res.data.map(item => {
						item.display_name = `${item.Employee_Name} - ${item.Employee_ID}`;
						return item;
					});
				})
			},

            getMonths() {
				axios.get('/get_months').then(res => {
					this.months = res.data;
				})
			},

			saveSalaryAdvance() {
                if (this.selectedEmployee == null ) {
					alert('Please select employee');
					return;
                }else{
                    this.salary.employee_id = this.selectedEmployee.Employee_SlNo;
				}

                if (this.selectedMonth == null) {
					alert('Please select month');
					return;
				}else{
                    this.salary.month_id = this.selectedMonth.month_id;
				}

				let url = '/add_advance_salary';
				if (this.salary.advanceSalaryId != 0) {
					url = '/update_advance_salary';
				}				

				axios.post(url, this.salary).then(res => {
					let r = res.data;					
					alert(r.message);
					if (r.success) {
						this.resetForm();
						this.getAdvanceSalary();
					}
				})
			},
			editAdvanceSalary(info) {
				let keys = Object.keys(this.salary);
				keys.forEach(key => {
					this.salary[key] = info[key];
				});
				this.selectedEmployee = {
					Employee_SlNo: info.employee_id,
					Employee_Name: info.Employee_Name,
					display_name: info.Employee_Name+'-'+info.Employee_ID
				};
				this.selectedMonth = {
					month_id: info.month_id,
					month_name: info.month_name,
				};
				this.salary.advanceSalaryId = info.id;
			},

			deleteAdvanceSalary(advanceId) {
				if (confirm('Are you sure?')) {
					axios.post('/delete_salary_advance', {
						advanceId: advanceId
					}).then(res => {
						let r = res.data;
						alert(r.message);
						if (r.success) {
							this.getAdvanceSalary();
						}
					})
				}
			},
			resetForm() {
				this.salary = {
					advance: 0,
					note: null,
					employee_id: '',
					month_id: '',
				};
				this.selectedEmployee = null;
				this.selectedMonth = null;
			}
		}
	})
</script>