<style>
	.v-select {
		float: right;
		min-width: 200px;
		background: #fff;
		margin-left: 5px;
		border-radius: 4px !important;
		margin-top: -2px;
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

	input{
		margin: 0 !important;
	}
</style>
<div class="row" id="customerPaymentReport" style="margin: 0;">
	<fieldset class="scheduler-border scheduler-search">
		<legend class="scheduler-border">Customer Payment Reports</legend>
		<div class="control-group">
			<div class="col-md-12">
				<form class="form-inline" id="searchForm" @submit.prevent="getReport">
					<div class="form-group">
						<label class="control-label"> Customer </label>
						<v-select v-bind:options="customers" v-model="selectedCustomer" label="display_name"></v-select>
					</div>

					<div class="form-group">
						<label class="control-label"> From </label>
						<input type="date" class="form-control" v-model="dateFrom">
						<label class="control-label text-center" style="width:30px"> to </label>
						<input type="date" class="form-control" v-model="dateTo">
					</div>

					<div class="form-group">
						<input type="submit" value="Show">
					</div>
				</form>
			</div>
		</div>
	</fieldset>

	<div class="col-xs-12 no-padding" style="display:none;" v-bind:style="{display: showTable ? '' : 'none'}">
		<div class="text-right">
			<a href="" v-on:click.prevent="print">
				<i class="fa fa-print"></i> Print
			</a>
		</div>
		<div class="table-responsive" id="reportTable">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="text-align:center">Date</th>
						<th style="text-align:center">Description</th>
						<th style="text-align:center">Bill</th>
						<th style="text-align:center">Paid</th>
						<th style="text-align:center">Discount</th>
						<th style="text-align:center">Inv.Due</th>
						<th style="text-align:center">Retruned</th>
						<th style="text-align:center">Paid to customer</th>
						<th style="text-align:center">Balance</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td></td>
						<td style="text-align:left;">Previous Balance</td>
						<td colspan="6"></td>
						<td style="text-align:right;">{{ parseFloat(previousBalance).toFixed(2) }}</td>
					</tr>
					<tr v-for="payment in payments">
						<td>{{ payment.date }}</td>
						<td style="text-align:left;">
							<a :href="`${payment.invoice_url}`" target="_blank" rel="noopener noreferrer">
								{{ payment.description }}
							</a>
						</td>
						<td style="text-align:right;">{{ parseFloat(payment.bill).toFixed(2) }}</td>
						<td style="text-align:right;">{{ parseFloat(payment.paid).toFixed(2) }}</td>
						<td style="text-align:right;">{{ parseFloat(payment.discount).toFixed(2) }}</td>
						<td style="text-align:right;">{{ parseFloat(payment.due).toFixed(2) }}</td>
						<td style="text-align:right;">{{ parseFloat(payment.returned).toFixed(2) }}</td>
						<td style="text-align:right;">{{ parseFloat(payment.paid_out).toFixed(2) }}</td>
						<td style="text-align:right;">{{ parseFloat(payment.balance).toFixed(2) }}</td>
					</tr>
				</tbody>
				<tbody v-if="payments.length == 0">
					<tr>
						<td colspan="9">No records found</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#customerPaymentReport',
		data() {
			return {
				customers: [],
				selectedCustomer: null,
				dateFrom: null,
				dateTo: null,
				payments: [],
				previousBalance: 0.00,
				showTable: false
			}
		},
		created() {
			let today = moment().format('YYYY-MM-DD');
			this.dateTo = today;
			this.dateFrom = moment().format('YYYY-MM-DD');
			this.getCustomers();
		},
		methods: {
			getCustomers() {
				axios.get('/get_customers').then(res => {
					this.customers = res.data;
				})
			},
			getReport() {
				if (this.selectedCustomer == null) {
					alert('Select customer');
					return;
				}
				let data = {
					dateFrom: this.dateFrom,
					dateTo: this.dateTo,
					customerId: this.selectedCustomer.Customer_SlNo
				}

				axios.post('/get_customer_ledger', data).then(res => {
					this.payments = res.data.payments;
					this.previousBalance = res.data.previousBalance;
					this.showTable = true;
				})
			},
			async print() {
				let reportContent = `
					<div class="container">
						<h4 style="text-align:center">Customer payment report</h4 style="text-align:center">
						<div class="row">
							<div class="col-xs-6" style="font-size:12px;">
								<strong>Customer Code: </strong> ${this.selectedCustomer.Customer_Code}<br>
								<strong>Name: </strong> ${this.selectedCustomer.Customer_Name}<br>
								<strong>Address: </strong> ${this.selectedCustomer.Customer_Address}<br>
								<strong>Mobile: </strong> ${this.selectedCustomer.Customer_Mobile}<br>
							</div>
							<div class="col-xs-6 text-right">
								<strong>Statement from</strong> ${this.dateFrom} <strong>to</strong> ${this.dateTo}
							</div>
						</div>
					</div>
					<div class="container">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportTable').innerHTML}
							</div>
						</div>
					</div>
				`;

				var mywindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}`);
				mywindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

				mywindow.document.body.innerHTML += reportContent;

				mywindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				mywindow.print();
				mywindow.close();
			}
		}
	})
</script>