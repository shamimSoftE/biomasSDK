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

	#supplierPayment label {
		font-size: 13px;
	}

	#supplierPayment select {
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
		background-color: #d15b47;
		color: white;
	}
</style>
<div id="supplierPayment">
	<div class="row">
		<div class="col-md-12" style="margin:0;">
			<fieldset class="scheduler-border">
				<legend class="scheduler-border">Supplier Payment Form</legend>
				<div class="control-group">
					<form @submit.prevent="saveSupplierPayment">
						<div class="row">
							<div class="col-md-5 col-md-offset-1">
								<div class="form-group">
									<label class="col-md-4 control-label">Transaction Type</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<select class="form-control" v-model="payment.SPayment_TransactionType" required>
											<option value=""></option>
											<option value="CP">Payment</option>
											<option value="CR">Receive</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Payment Type</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<select class="form-control" v-model="payment.SPayment_Paymentby" required>
											<option value="cash">Cash</option>
											<option value="bank">Bank</option>
										</select>
									</div>
								</div>
								<div class="form-group" style="display:none;" v-bind:style="{display: payment.SPayment_Paymentby == 'bank' ? '' : 'none'}">
									<label class="col-md-4 control-label">Bank Account</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<v-select v-bind:options="filteredAccounts" v-model="selectedAccount" label="display_text" placeholder="Select account"></v-select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-4 control-label no-padding-right"> Supplier </label>
									<label class="col-md-1">:</label>
									<div class="col-xs-7" style="display: flex;align-items:center;margin-bottom:5px;">
										<div style="width: 86%;">
											<v-select v-bind:options="suppliers" style="margin: 0;" v-model="selectedSupplier" v-on:input="getSupplierDue" @search="onSearchSupplier" label="display_name"></v-select>
										</div>
										<div style="width: 13%;margin-left:2px;">
											<a href="<?= base_url('supplier') ?>" class="add-button" target="_blank" title="Add New Supplier"><i class="fa fa-plus" aria-hidden="true"></i></a>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Due</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<input type="text" class="form-control" v-model="supplierDue" disabled>
									</div>
								</div>
							</div>

							<div class="col-md-5">
								<div class="form-group">
									<label class="col-md-4 control-label">Payment Date</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<input type="date" style="margin-bottom: 4px;" class="form-control" v-model="payment.SPayment_date" required @change="getSupplierPayments" v-bind:disabled="userType == 'u' ? true : false">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Description</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<input type="text" class="form-control" v-model="payment.SPayment_notes">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Amount</label>
									<label class="col-md-1">:</label>
									<div class="col-md-7">
										<input type="number" class="form-control" v-model="payment.SPayment_amount" required>
									</div>
								</div>
								<div class="form-group">
									<div class="col-md-7 col-md-offset-5 text-right">
										<input type="button" @click="resetForm" class="btnReset" value="Reset">
										<input type="submit" class="btnSave" value="Save">
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
							<td>{{ row.SPayment_invoice }}</td>
							<td>{{ row.SPayment_date }}</td>
							<td>{{ row.Supplier_Name }}</td>
							<td>{{ row.transaction_type }}</td>
							<td>{{ row.payment_by }}</td>
							<td>{{ row.SPayment_amount }}</td>
							<td>{{ row.SPayment_notes }}</td>
							<td>{{ row.added_by }}</td>
							<td>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<i class="btnEdit fa fa-pencil" @click="editPayment(row)"></i>
									<i class="btnDelete fa fa-trash" @click="deletePayment(row.SPayment_id)"></i>
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
		el: '#supplierPayment',
		data() {
			return {
				payment: {
					SPayment_id: 0,
					SPayment_customerID: null,
					SPayment_TransactionType: 'CP',
					SPayment_Paymentby: 'cash',
					account_id: null,
					SPayment_date: moment().format('YYYY-MM-DD'),
					SPayment_amount: '',
					SPayment_notes: ''
				},
				payments: [],
				suppliers: [],
				selectedSupplier: {
					display_name: 'Select Supplier',
					Supplier_Name: ''
				},
				supplierDue: 0,
				accounts: [],
				selectedAccount: null,
				userType: '<?php echo $this->session->userdata("accountType"); ?>',

				columns: [{
						label: 'Transaction Id',
						field: 'SPayment_invoice',
						align: 'center'
					},
					{
						label: 'Date',
						field: 'SPayment_date',
						align: 'center'
					},
					{
						label: 'Supplier',
						field: 'Supplier_Name',
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
						label: 'Amount',
						field: 'SPayment_amount',
						align: 'center'
					},
					{
						label: 'Description',
						field: 'SPayment_notes',
						align: 'center'
					},
					{
						label: 'Saved By',
						field: 'Addby',
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
			this.getSuppliers();
			this.getAccounts();
			this.getSupplierPayments();
		},
		methods: {
			getSupplierPayments() {
				let data = {
					dateFrom: this.payment.SPayment_date,
					dateTo: this.payment.SPayment_date
				}
				axios.post('/get_supplier_payments', data).then(res => {
					this.payments = res.data;
				})
			},
			getSuppliers() {
				axios.post('/get_suppliers', {
					forSearch: 'yes'
				}).then(res => {
					this.suppliers = res.data;
				})
			},
			async onSearchSupplier(val, loading) {
				if (val.length > 2) {
					loading(true);
					await axios.post("/get_suppliers", {
							name: val,
						})
						.then(res => {
							let r = res.data;
							this.suppliers = r.filter(item => item.status == 'a')
							loading(false)
						})
				} else {
					loading(false)
					await this.getSuppliers();
				}
			},
			getSupplierDue() {
				if (this.selectedSupplier == null || this.selectedSupplier.Supplier_SlNo == undefined) {
					return;
				}

				axios.post('/get_supplier_due', {
					supplierId: this.selectedSupplier.Supplier_SlNo
				}).then(res => {
					this.supplierDue = res.data[0].due;
				})
			},
			getAccounts() {
				axios.get('/get_bank_accounts')
					.then(res => {
						this.accounts = res.data;
					})
			},
			saveSupplierPayment() {
				if (this.payment.SPayment_Paymentby == 'bank') {
					if (this.selectedAccount == null) {
						alert('Select an account');
						return;
					} else {
						this.payment.account_id = this.selectedAccount.account_id;
					}
				} else {
					this.payment.account_id = null;
				}
				if (this.selectedSupplier == null || this.selectedSupplier.Supplier_SlNo == undefined) {
					alert('Select Supplier');
					return;
				}

				this.payment.SPayment_customerID = this.selectedSupplier.Supplier_SlNo;

				let url = '/add_supplier_payment';
				if (this.payment.SPayment_id != 0) {
					url = '/update_supplier_payment';
				}
				axios.post(url, this.payment).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						this.resetForm();
						this.getSupplierPayments();
					}
				})
			},
			editPayment(payment) {
				let keys = Object.keys(this.payment);
				keys.forEach(key => {
					this.payment[key] = payment[key];
				})

				this.selectedSupplier = {
					Supplier_SlNo: payment.SPayment_customerID,
					Supplier_Name: payment.Supplier_Name,
					display_name: `${payment.SPayment_customerID} - ${payment.Supplier_Name}`
				}

				if (payment.SPayment_Paymentby == 'bank') {
					this.selectedAccount = {
						account_id: payment.account_id,
						account_name: payment.account_name,
						account_number: payment.account_number,
						bank_name: payment.bank_name,
						display_text: `${payment.account_name} - ${payment.account_number} (${payment.bank_name})`
					}
				}
			},
			deletePayment(paymentId) {
				let deleteConfirm = confirm('Are you sure?');
				if (deleteConfirm == false) {
					return;
				}
				axios.post('/delete_supplier_payment', {
					paymentId: paymentId
				}).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						this.getSupplierPayments();
					}
				})
			},
			resetForm() {
				this.payment.SPayment_id = 0;
				this.payment.SPayment_customerID = '';
				this.payment.SPayment_amount = '';
				this.payment.SPayment_notes = '';

				this.selectedSupplier = {
					display_name: 'Select Supplier',
					Supplier_Name: ''
				}

				this.supplierDue = 0;
			}
		}
	})
</script>