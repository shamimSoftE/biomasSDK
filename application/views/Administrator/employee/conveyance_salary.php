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

<div id="salaryConveyance">
	<form @submit.prevent="saveSalaryConveyance">
		<div class="row" style="margin: 0;">
			<fieldset class="scheduler-border">
				<legend class="scheduler-border">Salary Conveyance Form</legend>
				<div class="control-group">
					<div class="col-xs-12 col-md-1"></div>
					<div class="col-xs-12 col-md-5">
                        
						<div class="form-group">
                            <label class="col-xs-3 col-md-3 control-label no-padding-right"> Employee </label>
                            <div class="col-xs-8 col-md-8">
                                <v-select v-bind:options="employees" v-model="selectedEmployee" label="display_name" placeholder="Select Employee"></v-select>
                            </div>
                            <div class="col-xs-1 col-md-1" style="padding: 0;">
								<a href="<?= base_url('employee') ?>" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px;margin-left: -8px;" target="_blank" title="Add New Month"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
							</div>
                        </div>

                        <!-- <div class="form-group">
							<label class="col-xs-3 col-md-3 control-label no-padding-right"> Month </label>
							<div class="col-xs-8 col-md-8">
								<v-select v-bind:options="months" label="month_name" v-model="selectedMonth"></v-select>
							</div>
							<div class="col-xs-1 col-md-1" style="padding: 0;">
								<a href="<?= base_url('month') ?>" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px;margin-left: -8px;" target="_blank" title="Add New Month"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
							</div>
						</div> -->

						<div class="form-group">
							<label class="col-xs-3 col-md-3 control-label no-padding-right"> Transaction Type </label>
							<div class="col-xs-3 col-md-3">
								<select class="form-control" v-model="salary.transaction_type">
									<option value="payment">Payment</option>
									<option value="receive">Receive</option>
								</select>
							</div>
						
							<label class="col-xs-3 col-md-3 control-label no-padding-right" style="padding-left: 0;">Payment Type</label>
							<div class="col-xs-3 col-md-3">
								<select class="form-control" v-model="salary.payment_type">
									<option value="Cash">Cash</option>
									<option value="Bank">Bank</option>
								</select>
							</div>
						</div>
						<div class="form-group" style="display:none;" v-bind:style="{display: salary.payment_type == 'Bank' ? '' : 'none'}">
							<label class="col-xs-3 col-md-3 control-label">Bank Account</label>
							<div class="col-xs-9 col-md-9">
								<v-select v-bind:options="accounts" v-model="selectedAccount" label="display_text" placeholder="Select account"></v-select>
							</div>
						</div>

						<div class="form-group">
                            <label class="col-xs-3 col-md-3 control-label no-padding-right"> Amount </label>
                            <div class="col-xs-9 col-md-9">
                                <input type="number" placeholder="Salary Conveyance" class="form-control" v-model="salary.conveyance" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-12 col-md-5">
						
                        
                        <div class="form-group">
							<label class="col-xs-2 col-md-2 control-label no-padding-right"> Note </label>
							<div class="col-xs-10 col-md-10">
                                <textarea class="form-control" rows="3" cols="30" v-model="salary.note"></textarea>
							</div>
						</div>
                        <div class="form-group">
							<div class="col-xs-12 col-md-12 text-right">
								<input type="submit" class="btnSave" value="Save">
							</div>
						</div>
                    </div>
                    <div class="col-xs-12 col-md-1"></div>
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
				<datatable :columns="columns" :data="salaryConveyance" :filter-by="filter" style="margin-bottom: 5px;">
					<template scope="{ row }">
						<tr>
							<td>{{ row.sl }}</td>
							<td style="text-transform: capitalize;">{{ row.transaction_type }} </td>
							<td>{{ row.payment_type }} </td>
							<td>{{ row.bank_name }} - {{ row.account_number }}</td>
							<td>{{ row.Employee_Name }} - {{ row.Employee_ID }}</td>
							<!-- <td>{{ row.month_name }}</td> -->
							<td>{{ row.conveyance }}</td>
							<td>{{ row.note }}</td>
							<td>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<i class="btnEdit fa fa-pencil" @click="editCondenseSalary(row)"></i>
									<i class="btnDelete fa fa-trash" @click="deleteCondenseSalary(row.id)"></i>
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
		el: '#salaryConveyance',
		data() {
			return {
				salary: {
                    conveyanceSalaryId: 0,
					conveyance: 0,
					note: null,
					employee_id: '',
					month_id: '',
					transaction_type: 'payment',
					payment_type: 'Cash',
				},

				accounts       : [],
				selectedAccount: null,
				
				salaryConveyance: [],

				columns: [{
						label: 'Sl',
						field: 'sl',
						align: 'center'
					},
					{
						label: 'Transaction Type',
						field: 'transaction_type',
						align: 'center'
					},
					{
						label: 'Payment Type',
						field: 'payment_type',
						align: 'center'
					},
					{
						label: 'Bank Account',
						field: 'bank_name',
						align: 'center'
					},
					{
						label: 'Employee Name',
						field: 'employee_name',
						align: 'center'
					},
					// {
					// 	label: 'Month',
					// 	field: 'month_name',
					// 	align: 'center'
					// },
					{
						label: 'Conveyance',
						field: 'conveyance',
						align: 'center'
					},
					{
						label: 'Note',
						field: 'note',
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
			this.getCondenseSalary();
            this.getEmployees();
			this.getAccounts();
            this.getMonths();
		},
		methods: {
			getCondenseSalary() {
				axios.get('/get_salary_conveyance').then(res => {
					this.salaryConveyance = res.data.map((item, index) => {
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
			getAccounts() {
				axios.get('/get_bank_accounts')
				.then(res => {
					let accounts = res.data.filter(account => account.status == '1');
					this.accounts = accounts.map(account => {
						account.display_text = `${account.bank_name} - ${account.account_number} (${account.branch_name})`;
						return account;
					})
				})
			},

			saveSalaryConveyance() {
                if (this.selectedEmployee == null ) {
					alert('Please select employee');
					return;
                }else{
                    this.salary.employee_id = this.selectedEmployee.Employee_SlNo;
				}

                // if (this.selectedMonth == null) {
				// 	alert('Please select month');
				// 	return;
				// }else{
                //     this.salary.month_id = this.selectedMonth.month_id;
				// }

				if (this.salary.payment_type == 'Bank') {
					if (this.selectedAccount == null) {
						alert('Please select a bank');
						return;
					}
					this.salary.account_id = this.selectedAccount.account_id
				}

				let url = '/add_conveyance_salary';
				if (this.salary.conveyanceSalaryId != 0) {
					url = '/update_conveyance_salary';
				}				

				axios.post(url, this.salary).then(res => {
					let r = res.data;					
					alert(r.message);
					if (r.success) {
						this.resetForm();
						this.getCondenseSalary();
					}
				})
			},

			editCondenseSalary(info) {
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
				this.selectedAccount = {
					account_id: info.account_id,
					display_text: info.bank_name+'- '+info.account_number +' ('+info.branch_name+')'
				};
				this.salary.conveyanceSalaryId = info.id;
			},
			deleteCondenseSalary(conveyanceId) {
				if (confirm('Are you sure?')) {
					axios.post('/delete_salary_conveyance', {
						conveyanceId: conveyanceId
					}).then(res => {
						let r = res.data;
						alert(r.message);
						if (r.success) {
							this.getCondenseSalary();
						}
					})
				}
			},
			resetForm() {
				this.salary = {
					conveyanceSalaryId: 0,
					conveyance: 0,
					note: null,
					employee_id: '',
					month_id: '',
					transaction_type: 'payment',
					payment_type: 'Cash',
				};
				this.selectedEmployee = null;
				this.selectedMonth = null;
			}
		}
	})
</script>