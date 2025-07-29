<style>
	.v-select {
		margin-bottom: 5px;
		background: #fff;
		border-radius: 3px;
	}

	.v-select.open .dropdown-toggle {
		border-bottom: 1px solid #ccc;
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

	#customerPayment label {
		font-size: 13px;
	}

	#customerPayment select {
		border-radius: 3px;
		padding: 0;
	}

	.add-button {
		padding: 2.8px;
		width: 100%;
		background-color: #d15b47;
		display: block;
		text-align: center;
		color: white;
		cursor: pointer;
		border-radius: 3px;
	}

	.add-button:hover {
		color: white;
	}
</style>

<div id="customerPayment">
	<div class="row">
		<div class="col-md-12" style="margin:0;">
			<fieldset class="scheduler-border">
				<legend class="scheduler-border">Customer Payment Form</legend>
				<div class="control-group">
					<form @submit.prevent="saveCustomerPayment">
						<div class="row">
							<div class="col-md-5 col-md-offset-1">
								<div class="form-group">
									<label class="col-md-4 control-label">Transaction Type</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<select class="form-control" v-model="payment.CPayment_TransactionType" required>
											<option value=""></option>
											<option value="CR">Receive</option>
											<option value="CP">Payment</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Payment Type</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<select class="form-control" v-model="payment.CPayment_Paymentby" required>
											<option value="cash">Cash</option>
											<option value="bank">Bank</option>
										</select>
									</div>
								</div>
								<div class="form-group" style="display:none;" v-bind:style="{display: payment.CPayment_Paymentby == 'bank' ? '' : 'none'}">
									<label class="col-md-4 control-label">Bank Account</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<v-select v-bind:options="filteredAccounts" v-model="selectedAccount" label="display_text" placeholder="Select account"></v-select>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label no-padding-right"> Customer </label>
									<label class="col-md-1">:</label>
									<div class="col-md-7" style="display: flex;align-items:center;margin-bottom:5px;">
										<div style="width: 86%;">
											<v-select v-bind:options="customers" style="margin: 0;" label="display_name" v-model="selectedCustomer" v-on:input="getCustomerDue" @search="onSearchCustomer"></v-select>
										</div>
										<div style="width: 13%;margin-left:2px;">
											<a href="<?= base_url('customer') ?>" class="add-button" target="_blank" title="Add New Customer"><i class="fa fa-plus" aria-hidden="true"></i></a>
										</div>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Due</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<input type="text" class="form-control" v-model="payment.CPayment_previous_due" disabled>
									</div>
								</div>
							</div>

							<div class="col-md-5">
								<div class="form-group">
									<label class="col-md-4 control-label">Payment Date</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<input type="date" style="margin-bottom: 4px;" class="form-control" v-model="payment.CPayment_date" required @change="getCustomerPayments" v-bind:disabled="userType == 'u' ? true : false">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Description</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<input type="text" class="form-control" v-model="payment.CPayment_notes">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Amount</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<input type="number" class="form-control" v-model="amount" @input="calculateDiscount()" required>
									</div>
								</div>
								<div class="form-group" v-if="payment.CPayment_TransactionType=='CR'">
									<label class="col-md-4 control-label">Discount</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<input type="number" class="form-control" v-model="payment.CPayment_discount" @input="calculateDiscount()" required>
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-md-4 control-label">Net Amount</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<input type="number" class="form-control" v-model="payment.CPayment_amount" readonly required>
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-7 col-md-offset-5 text-right">
										<input type="button" @click="resetForm" class="btnReset" value="Reset">
										<input type="submit" :disabled="paymentProgress" class="btnSave" value="Save">
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12 form-inline">
			<div class="form-group">
				<label for="filter" class="sr-only">Filter</label>
				<input type="text" class="form-control" v-model="filter" placeholder="Filter">
			</div>
		</div>
		<div class="col-md-12">
			<div class="table-responsive">
				<datatable :columns="columns" :data="payments" :filter-by="filter" style="margin-bottom: 5px;">
					<template scope="{ row }">
						<tr>
							<td>{{ row.CPayment_invoice }}</td>
							<td>{{ row.CPayment_date }}</td>
							<td>{{ row.Customer_Name }}</td>
							<td>{{ row.transaction_type }}</td>
							<td>{{ row.payment_by }}</td>
							<td>{{ row.CPayment_discount }}</td>
							<td>{{ row.CPayment_amount }}</td>
							<td>{{ row.CPayment_notes }}</td>
							<td>{{ row.added_by }}</td>
							<td>
								<i class="fa fa-file" style="margin-right: 5px;font-size: 14px;cursor: pointer;" @click="window.location = `/paymentAndReport/${row.CPayment_id}`"></i>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<i class="btnEdit fa fa-pencil" @click="editPay(row)"></i>
									<i class="btnDelete fa fa-trash" @click="deletePayment(row.CPayment_id)"></i>
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
		el: '#customerPayment',
		data() {
			return {
				payment: {
					CPayment_id: "<?= $paymentId ?>",
					CPayment_customerID: null,
					CPayment_TransactionType: 'CR',
					CPayment_Paymentby: 'cash',
					account_id: null,
					CPayment_date: moment().format('YYYY-MM-DD'),
					CPayment_amount: '',
					CPayment_discount: '',
					CPayment_notes: '',
					CPayment_previous_due: 0
				},
				payments: [],
				customers: [],
				selectedCustomer: {
					display_name: 'Select Customer',
					Customer_Name: ''
				},
				amount         : '',
				accounts       : [],
				selectedAccount: null,
				paymentProgress: false,
				userType       : '<?php echo $this->session->userdata("accountType"); ?>',

				columns: [{
						label: 'Transaction Id',
						field: 'CPayment_invoice',
						align: 'center'
					},
					{
						label: 'Date',
						field: 'CPayment_date',
						align: 'center'
					},
					{
						label: 'Customer',
						field: 'Customer_Name',
						align: 'center'
					},
					{
						label: 'Transaction Type',
						field: 'transaction_type',
						align: 'center'
					},
					{
						label: 'Payment by',
						field: 'payment_by',
						align: 'center'
					},
					{
						label: 'Discount',
						field: 'CPayment_discount',
						align: 'center'
					},
					{
						label: 'Amount',
						field: 'CPayment_amount',
						align: 'center'
					},
					{
						label: 'Description',
						field: 'CPayment_notes',
						align: 'center'
					},
					{
						label: 'Saved By',
						field: 'AddBy',
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
		computed: {
			filteredAccounts() {
				let accounts = this.accounts.filter(account => account.status == '1');
				return accounts.map(account => {
					account.display_text = `${account.account_name} - ${account.account_number} (${account.bank_name})`;
					return account;
				})
			},
		},
		created() {
			this.getCustomers();
			this.getAccounts();
			this.getCustomerPayments();
			// if (this.payment.CPayment_id != 0) {
			// 	this.editPayment();
			// }
		},
		methods: {
			getCustomerPayments() {
				let data = {
					dateFrom: this.payment.CPayment_date,
					dateTo: this.payment.CPayment_date
				}
				axios.post('/get_customer_payments', data).then(res => {
					this.payments = res.data;
				})
			},

			calculateDiscount() {
				let productTotal = (parseFloat(this.amount) - parseFloat(this.payment.CPayment_discount)).toFixed(2);
				if(this.payment.CPayment_discount != '' || this.payment.CPayment_discount != 0){
					this.payment.CPayment_amount = +productTotal;
				}else{
					this.payment.CPayment_amount = +this.amount;
				}
				// product wise discount
			},
			getCustomers() {
				axios.post('/get_customers', {
					forSearch: 'yes'
				}).then(res => {
					this.customers = res.data;
				})
			},
			getCustomerDue() {
				if (this.selectedCustomer == null || this.selectedCustomer.Customer_SlNo == undefined) {
					return;
				}

				axios.post('/get_customer_due', {
					customerId: this.selectedCustomer.Customer_SlNo
				}).then(res => {
					this.payment.CPayment_previous_due = res.data[0].dueAmount;
				})
			},
			async onSearchCustomer(val, loading) {
				if (val.length > 2) {
					loading(true);
					await axios.post("/get_customers", {
							name: val,
						})
						.then(res => {
							let r = res.data;
							this.customers = r.filter(item => item.status == 'a')
							loading(false)
						})
				} else {
					loading(false)
					await this.getCustomers();
				}
			},
			getAccounts() {
				axios.get('/get_bank_accounts')
					.then(res => {
						this.accounts = res.data;
					})
			},
			saveCustomerPayment() {
				if (this.payment.CPayment_Paymentby == 'bank') {
					if (this.selectedAccount == null) {
						alert('Select an account');
						return;
					} else {
						this.payment.account_id = this.selectedAccount.account_id;
					}
				} else {
					this.payment.account_id = null;
				}
				if (this.selectedCustomer == null || this.selectedCustomer.Customer_SlNo == undefined) {
					alert('Select Customer');
					return;
				}

				this.payment.CPayment_customerID = this.selectedCustomer.Customer_SlNo;

				let url = '/add_customer_payment';
				if (this.payment.CPayment_id != 0) {
					url = '/update_customer_payment';
				}
				this.paymentProgress = true;
				axios.post(url, this.payment).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						this.resetForm();
						this.paymentProgress = false;
						this.getCustomerPayments();
						let invoiceConfirm = confirm('Do you want to view invoice?');
						if (invoiceConfirm == true) {
							window.open('/paymentAndReport/' + r.paymentId, '_blank');
						}
					}
				})
			},

			editPay(payment) {
 				// window.open('/customerPaymentPage/' + row.CPayment_id, '_blank');
				let keys = Object.keys(this.payment);
				keys.forEach(key => {
					this.payment[key] = payment[key];
				})

				this.selectedCustomer = {
					Customer_SlNo: payment.CPayment_customerID,
					Customer_Name: payment.Customer_Name,
					display_name: `${payment.CPayment_customerID} - ${payment.Customer_Name}`
				}

				this.amount = (+payment.CPayment_amount + +payment.CPayment_discount).toFixed(2);

				if (payment.CPayment_Paymentby == 'bank') {
					this.selectedAccount = {
						account_id: payment.account_id,
						account_name: payment.account_name,
						account_number: payment.account_number,
						bank_name: payment.bank_name,
						display_text: `${payment.account_name} - ${payment.account_number} (${payment.bank_name})`
					}
				}
			},

			editPayment(paymentId) {

				axios.post('/get_customer_payments', {
					paymentId: paymentId
				}).then(res => {
					let payment = res.data[0];

					let keys = Object.keys(this.payment);
					keys.forEach(key => {
						this.payment[key] = payment[key];
						this.selectedCustomer = {
							Customer_SlNo: payment.CPayment_customerID,
							Customer_Name: payment.Customer_Name,
							display_name: `${payment.CPayment_customerID} - ${payment.Customer_Name}`
						}
		
						this.amount = (+payment.CPayment_amount + +payment.CPayment_discount).toFixed(2);
		
						if (payment.CPayment_Paymentby == 'bank') {
							this.selectedAccount = {
								account_id: payment.account_id,
								account_name: payment.account_name,
								account_number: payment.account_number,
								bank_name: payment.bank_name,
								display_text: `${payment.account_name} - ${payment.account_number} (${payment.bank_name})`
							}
						}
					})

				})

			},
			deletePayment(paymentId) {
				let deleteConfirm = confirm('Are you sure?');
				if (deleteConfirm == false) {
					return;
				}
				axios.post('/delete_customer_payment', {
					paymentId: paymentId
				}).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						this.getCustomerPayments();
					}
				})
			},
			resetForm() {
				this.payment.CPayment_id = 0;
				this.payment.CPayment_customerID = '';
				this.payment.CPayment_amount = '';
				this.payment.CPayment_discount = '';
				this.payment.CPayment_notes = '';
				this.amount = '';
				this.selectedCustomer = {
					display_name: 'Select Customer',
					Customer_Name: ''
				}

				this.payment.CPayment_previous_due = 0;
			}
		}
	})
</script>