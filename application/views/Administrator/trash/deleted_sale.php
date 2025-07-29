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


    .record-table {
        width: 100%;
        border-collapse: collapse;
    }

    .record-table thead {
        background-color: #0097df;
        color: white;
    }

    .record-table th,
    .record-table td {
        padding: 3px;
        border: 1px solid #454545;
    }

    .record-table th {
        text-align: center;
    }
</style>
<div id="salesRecord">
    <div class="row" style="margin: 0;">
        <fieldset class="scheduler-border scheduler-search">
            <legend class="scheduler-border">Search Deleted Sales</legend>
            <div class="control-group">
                <div class="col-md-12">
                    <form class="form-inline" id="searchForm" @submit.prevent="getSearchResult">
                        <div class="form-group">
                            <label>Search Type</label>
                            <select class="form-control" v-model="searchType" @change="onChangeSearchType">
                                <option value="">All</option>
                                <option value="customer">By Customer</option>
                                <option value="employee">By Employee</option>
                                <option value="user">By User</option>
                            </select>
                        </div>

                        <div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'customer' && customers.length > 0 ? '' : 'none'}">
                            <label>Customer</label>
                            <v-select v-bind:options="customers" v-model="selectedCustomer" label="display_name"></v-select>
                        </div>

                        <div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'employee' && employees.length > 0 ? '' : 'none'}">
                            <label>Employee</label>
                            <v-select v-bind:options="employees" v-model="selectedEmployee" label="Employee_Name"></v-select>
                        </div>

                        <div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'user' && users.length > 0 ? '' : 'none'}">
                            <label>User</label>
                            <v-select v-bind:options="users" v-model="selectedUser" label="FullName"></v-select>
                        </div>

                        <div class="form-group" v-bind:style="{display: searchTypesForRecord.includes(searchType) ? '' : 'none'}">
                            <label>Record Type</label>
                            <select class="form-control" v-model="recordType" @change="sales = []" style="margin: 0;">
                                <option value="without_details">Without Details</option>
                                <option value="with_details">With Details</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">From</label>
                            <input type="date" class="form-control" v-model="dateFrom">
                        </div>

                        <div class="form-group">
                            <label for="">To</label>
                            <input type="date" class="form-control" v-model="dateTo">
                        </div>

                        <div class="form-group">
                            <input type="submit" value="Search">
                        </div>
                    </form>
                </div>
            </div>
        </fieldset>
    </div>

    <div class="row" style="display:none;" v-bind:style="{display: sales.length > 0 ? '' : 'none'}">
        <div class="col-md-12 text-right">
            <a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
        </div>
        <div class="col-md-12">
            <div class="table-responsive" id="reportContent">
                <table class="table table-hover record-table table-bordered" v-if="(searchTypesForRecord.includes(searchType)) && recordType == 'with_details'" style="display:none" v-bind:style="{display: (searchTypesForRecord.includes(searchType)) && recordType == 'with_details' ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Invoice No.</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Employee Name</th>
                            <th>AddedBy</th>
                            <th>DeletedBy</th>
                            <th>Deleted Time</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="sale in sales">
                            <tr>
                                <td>{{ sale.SaleMaster_InvoiceNo }}</td>
                                <td>{{ sale.SaleMaster_SaleDate }}</td>
                                <td>{{ sale.Customer_Name }}</td>
                                <td>{{ sale.Employee_Name }}</td>
                                <td>{{ sale.added_by }}</td>
                                <td>{{ sale.deleted_by }}</td>
                                <td>{{ sale.DeletedTime | dateFormat('DD-MM-YYYY, h:mm:ss a') }}</td>
                                <td>{{ sale.saleDetails[0].Product_Name }}</td>
                                <td style="text-align:right;">{{ sale.saleDetails[0].SaleDetails_Rate }}</td>
                                <td style="text-align:center;">{{ sale.saleDetails[0].SaleDetails_TotalQuantity }}</td>
                                <td style="text-align:right;">{{ sale.saleDetails[0].SaleDetails_TotalAmount }}</td>
                                <td style="text-align:center;">
                                    <a href="" title="Sale Invoice" v-bind:href="`/deleted_sale_invoice/${sale.SaleMaster_SlNo}`" target="_blank"><i class="fa fa-file"></i></a>
                                </td>
                            </tr>
                            <tr v-for="(product, sl) in sale.saleDetails.slice(1)">
                                <td colspan="7" v-bind:rowspan="sale.saleDetails.length - 1" v-if="sl == 0"></td>
                                <td>{{ product.Product_Name }}</td>
                                <td style="text-align:right;">{{ product.SaleDetails_Rate }}</td>
                                <td style="text-align:center;">{{ product.SaleDetails_TotalQuantity }}</td>
                                <td style="text-align:right;">{{ product.SaleDetails_TotalAmount }}</td>
                                <td></td>
                            </tr>
                            <tr style="font-weight:bold;">
                                <td colspan="9" style="font-weight:normal;"><strong>Note: </strong>{{ sale.SaleMaster_Description }}</td>
                                <td style="text-align:center;">Total Quantity<br>{{ sale.saleDetails.reduce((prev, curr) => {return prev + parseFloat(curr.SaleDetails_TotalQuantity)}, 0) }}</td>
                                <td style="text-align:right;">
                                    Total: {{ sale.SaleMaster_TotalSaleAmount }}<br>
                                    Paid: {{ sale.SaleMaster_PaidAmount }}<br>
                                    Due: {{ sale.SaleMaster_DueAmount }}
                                </td>
                                <td></td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <table class="table table-hover record-table table-bordered" v-if="(searchTypesForRecord.includes(searchType)) && recordType == 'without_details'" style="display:none" v-bind:style="{display: (searchTypesForRecord.includes(searchType)) && recordType == 'without_details' ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Invoice No.</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Employee Name</th>
                            <th>Sub Total</th>
                            <th>VAT</th>
                            <th>Discount</th>
                            <th>Transport Cost</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Note</th>
                            <th>AddedBy</th>
                            <th>DeletedBy</th>
                            <th>DeletedTime</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="sale in sales">
                            <td>{{ sale.SaleMaster_InvoiceNo }}</td>
                            <td>{{ sale.SaleMaster_SaleDate }}</td>
                            <td>{{ sale.Customer_Name }}</td>
                            <td>{{ sale.Employee_Name }}</td>
                            <td style="text-align:right;">{{ sale.SaleMaster_SubTotalAmount }}</td>
                            <td style="text-align:right;">{{ sale.SaleMaster_TaxAmount }}</td>
                            <td style="text-align:right;">{{ sale.SaleMaster_TotalDiscountAmount }}</td>
                            <td style="text-align:right;">{{ sale.SaleMaster_Freight }}</td>
                            <td style="text-align:right;">{{ sale.SaleMaster_TotalSaleAmount }}</td>
                            <td style="text-align:right;">{{ sale.SaleMaster_PaidAmount }}</td>
                            <td style="text-align:right;">{{ sale.SaleMaster_DueAmount }}</td>
                            <td style="text-align:left;">{{ sale.SaleMaster_Description }}</td>
                            <td>{{ sale.added_by }}</td>
                            <td>{{ sale.deleted_by }}</td>
                            <td>{{ sale.DeletedTime | dateFormat('DD-MM-YYYY, h:mm:ss a') }}</td>
                            <td style="text-align:center;">
                                <a href="" title="Sale Invoice" v-bind:href="`/deleted_sale_invoice/${sale.SaleMaster_SlNo}`" target="_blank"><i class="fa fa-file"></i></a>
                            </td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td colspan="5" style="text-align:right;">Total</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_SubTotalAmount)}, 0) }}</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_TaxAmount)}, 0) }}</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_TotalDiscountAmount)}, 0) }}</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_Freight)}, 0) }}</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_TotalSaleAmount)}, 0) }}</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_PaidAmount)}, 0) }}</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_DueAmount)}, 0) }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lodash.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#salesRecord',
        data() {
            return {
                searchType: '',
                recordType: 'without_details',
                dateFrom: moment().format('YYYY-MM-DD'),
                dateTo: moment().format('YYYY-MM-DD'),
                customers: [],
                selectedCustomer: null,
                employees: [],
                selectedEmployee: null,
                products: [],
                selectedProduct: null,
                users: [],
                selectedUser: null,
                categories: [],
                selectedCategory: null,
                sales: [],
                searchTypesForRecord: ['', 'user', 'customer', 'employee'],
            }
        },
        filters: {
            dateFormat(dt, format) {
                return moment(dt).format(format);
            },
        },
        methods: {
            onChangeSearchType() {
                this.sales = [];
                if (this.searchType == 'user') {
                    this.getUsers();
                } else if (this.searchType == 'customer') {
                    this.getCustomers();
                } else if (this.searchType == 'employee') {
                    this.getEmployees();
                }
            },
            getCustomers() {
                axios.get('/get_customers').then(res => {
                    this.customers = res.data;
                })
            },
            getEmployees() {
                axios.get('/get_employees').then(res => {
                    this.employees = res.data;
                })
            },
            getUsers() {
                axios.get('/get_users').then(res => {
                    this.users = res.data;
                })
            },
            getSearchResult() {
                if (this.searchType != 'customer') {
                    this.selectedCustomer = null;
                }

                if (this.searchType != 'employee') {
                    this.selectedEmployee = null;
                }

                this.getSalesRecord();
            },
            getSalesRecord() {
                let filter = {
                    userFullName: this.selectedUser == null || this.selectedUser.FullName == '' ? '' : this.selectedUser.FullName,
                    customerId: this.selectedCustomer == null || this.selectedCustomer.Customer_SlNo == '' ? '' : this.selectedCustomer.Customer_SlNo,
                    employeeId: this.selectedEmployee == null || this.selectedEmployee.Employee_SlNo == '' ? '' : this.selectedEmployee.Employee_SlNo,
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo,
                    status: 'd'
                }

                let url = '/get_sales';
                if (this.recordType == 'with_details') {
                    url = '/get_sales_record';
                }

                axios.post(url, filter)
                    .then(res => {
                        if (this.recordType == 'with_details') {
                            this.sales = res.data;
                        } else {
                            this.sales = res.data.sales;
                        }
                    })
            },
            async print() {
                let dateText = '';
                if (this.dateFrom != '' && this.dateTo != '') {
                    dateText = `Statement from <strong>${this.dateFrom}</strong> to <strong>${this.dateTo}</strong>`;
                }

                let userText = '';
                if (this.selectedUser != null && this.selectedUser.FullName != '' && this.searchType == 'user') {
                    userText = `<strong>Sold by: </strong> ${this.selectedUser.FullName}`;
                }

                let customerText = '';
                if (this.selectedCustomer != null && this.selectedCustomer.Customer_SlNo != '' && this.searchType == 'customer') {
                    customerText = `<strong>Customer: </strong> ${this.selectedCustomer.Customer_Name}<br>`;
                }

                let employeeText = '';
                if (this.selectedEmployee != null && this.selectedEmployee.Employee_SlNo != '' && this.searchType == 'employee') {
                    employeeText = `<strong>Employee: </strong> ${this.selectedEmployee.Employee_Name}<br>`;
                }


                let reportContent = `
					<div class="container">
						<div class="row">
							<div class="col-xs-12 text-center">
								<h3>Deleted Sales Record</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6">
								${userText} ${customerText} ${employeeText} ${productText} ${categoryText}
							</div>
							<div class="col-xs-6 text-right">
								${dateText}
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
							</div>
						</div>
					</div>
				`;

                var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}`);
                reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

                reportWindow.document.head.innerHTML += `
					<style>
						.record-table{
							width: 100%;
							border-collapse: collapse;
						}
						.record-table thead{
							background-color: #0097df;
							color:white;
						}
						.record-table th, .record-table td{
							padding: 3px;
							border: 1px solid #454545;
						}
						.record-table th{
							text-align: center;
						}
					</style>
				`;
                reportWindow.document.body.innerHTML += reportContent;

                if (this.searchType == '' || this.searchType == 'user') {
                    let rows = reportWindow.document.querySelectorAll('.record-table tr');
                    rows.forEach(row => {
                        row.lastChild.remove();
                    })
                }

                reportWindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                reportWindow.print();
                reportWindow.close();
            }
        }
    })
</script>