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

    #customerPaymentReport label {
        font-size: 13px;
    }

    #customerPaymentReport select {
        border-radius: 3px;
        padding: 0px;
    }

    #customerPaymentReport .form-group {
        margin-right: 5px;
    }

    #customerPaymentReport .search-button {
        margin-top: -6px;
    }

    #transactionsTable th {
        text-align: center;
    }
</style>

<div id="customerPaymentReport">
    <div class="row" style="margin:0;">
        <fieldset class="scheduler-border scheduler-search">
            <legend class="scheduler-border">Customer Payment Report</legend>
            <div class="control-group">
                <div class="col-md-12">
                    <form class="form-inline" @submit.prevent="getPaymentReport">
                        <div class="form-group">
                            <label>Customer</label>
                            <v-select v-bind:options="customers" style="margin: 0;" label="display_name" v-model="selectedCustomer" ></v-select>
                        </div>

                        <!-- <div class="form-group">
                            <label>Transaction Type</label>
                            <select class="form-control" v-model="filter.transactionType" @change="resetData">
                                <option value="">All</option>
                               	<option value="cash">Cash</option>
								<option value="bank">Bank</option>
                            </select>
                        </div> -->

                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" class="form-control" v-model="filter.dateFrom" @change="resetData">
                        </div>

                        <div class="form-group">
                            <label>to</label>
                            <input type="date" class="form-control" v-model="filter.dateTo" @change="resetData">
                        </div>

                        <div class="form-group">
                            <input type="submit" value="search" class="search-button">
                        </div>
                    </form>
                </div>
            </div>
        </fieldset>
    </div>

    <div class="row" style="display:none;" v-bind:style="{display: transactions.length > 0 ? '' : 'none'}">
        <div class="col-md-12" style="margin-bottom: 10px;">
            <a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
        </div>
        <div class="col-md-12">
            <div class="table-responsive" id="reportContent">
                <table class="table table-bordered table-condensed" id="transactionsTable">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Transaction Id</th>
                            <th>Date</th>
                            <th>Customer </th>
                            <th>Transaction Type</th>
                            <th>Payment By</th>
                            <th>Discount </th>
                            <th>In Amount </th>
                            <th>Out Amount </th>
                            <th>Saved By</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(transaction, sl) in transactions">
                            <td style="text-align:right">{{ sl + 1}}</td>
                            <td style="text-align:left;">
                                
                                {{ transaction.CPayment_invoice }}
                                
                            </td>
                            <td>{{ transaction.CPayment_date }}</td>
                            <td style="text-align: start;">{{ transaction.Customer_Name }}</td>
                            <td>{{ transaction.transaction_type }}</td>
                            <td style="text-align: start;">{{ transaction.payment_by }}</td>
                            <td style="text-align: start;">{{ transaction.CPayment_discount }}</td>
                            <td style="text-align: start;">{{ transaction.CPayment_amount }}</td>
                            <td style="text-align:right">{{ transaction.out_amount }}</td>
                            <td style="text-align:right">{{ transaction.added_by }}</td>
                            <td>
                                <i class="fa fa-file" style="margin-right: 5px;font-size: 14px;cursor: pointer;" @click="window.location = `/paymentAndReport/${transaction.CPayment_id}`"></i>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<i class="btnDelete fa fa-trash" @click="deletePayment(transaction.CPayment_id)"></i>
								<?php } ?>
                            </td>
                        </tr>
                    </tbody>

                    <!-- <tfoot>
                        <tr style="font-weight:bold;">
                            <td colspan="7" style="text-align:right;">Total &nbsp;</td>
                            <td style="text-align:right;">{{ totalDeposit = this.transactions.reduce((prev, curr) => { return prev + parseFloat(curr.deposit)}, 0) }}</td>
                            <td style="text-align:right;">{{ totalWithdraw = this.transactions.reduce((prev, curr) => { return prev + parseFloat(curr.withdraw)}, 0) }}</td>
                        </tr>
                    </tfoot> -->
                </table>
            </div>
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
                transactions: [],
                filter: {
                    customerId: null,
                    transactionType: '',
                    dateFrom: moment().format('YYYY-MM-DD'),
                    dateTo: moment().format('YYYY-MM-DD')
                },
                customers: [],
				selectedCustomer: null,
            }
        },
        
        created() {
            this.getCustomers();
        },
        methods: {
           	getCustomers() {
				axios.post('/get_customers', {
					forSearch: 'yes'
				}).then(res => {
					this.customers = res.data;
				})
			},

            getPaymentReport() {
                if (this.selectedCustomer != null) {
                    this.filter.customerId = this.selectedCustomer.Customer_SlNo;
                } else {                    
                    this.filter.customerId = null;
                }

                axios.post('/get_customer_payments', this.filter)
                    .then(res => {
                        this.transactions = res.data;
                    })
                    .catch(error => {
                        if (error.response) {
                            alert(`${error.response.status}, ${error.response.statusText}`);
                        }
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
						this.getPaymentReport();
					}
				})
			},

            resetData() {
                this.transactions = [];
            },

            async print() {
                let accountText = '';
                if (this.selectedAccount != null) {
                    accountText = `<strong>Account: </strong> ${this.selectedAccount.account_number} (${this.selectedAccount.bank_name})<br>`;
                }

                typeText = '';
                if (this.filter.transactionType != '') {
                    typeText = `<strong>Transaction Type: </strong> ${this.filter.transactionType}`;
                }

                dateText = '';
                if (this.filter.dateFrom != '' && this.filter.dateTo != '') {
                    dateText = `Statement from <strong>${this.filter.dateFrom}</strong> to <strong>${this.filter.dateTo}</strong>`;
                }
                let reportContent = `
					<div class="container">
						<h4 style="text-align:center">Bank Transaction Report</h4 style="text-align:center">
                        <div class="row">
                            <div class="col-xs-6">${accountText} ${typeText}</div>
                            <div class="col-xs-6 text-right">${dateText}</div>
                        </div>
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
							</div>
						</div>
					</div>
				`;

                var printWindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}`);
                printWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

                printWindow.document.head.innerHTML += `
                    <style>
                        #transactionsTable th{
                            text-align: center;
                        }
                    </style>
                `;
                printWindow.document.body.innerHTML += reportContent;

                printWindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                printWindow.print();
                printWindow.close();
            }
        }
    })
</script>