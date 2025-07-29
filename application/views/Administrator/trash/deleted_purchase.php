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

    #searchForm select {
        padding: 0;
        border-radius: 4px;
    }

    #searchForm .form-group {
        margin-right: 5px;
    }

    #searchForm * {
        font-size: 13px;
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
<div id="purchaseRecord">
    <div class="row" style="margin:0;">
        <fieldset class="scheduler-border scheduler-search">
            <legend class="scheduler-border">Search Deleted Purchase</legend>
            <div class="control-group">
                <div class="col-md-12">
                    <form class="form-inline" id="searchForm" @submit.prevent="getSearchResult">
                        <div class="form-group">
                            <label>Search Type</label>
                            <select class="form-select" style="margin: 0;width:150px;height:26px;" v-model="searchType" @change="onChangeSearchType">
                                <option value="">All</option>
                                <option value="supplier">By Supplier</option>
                                <option value="user">By User</option>
                            </select>
                        </div>

                        <div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'supplier' && suppliers.length > 0 ? '' : 'none'}">
                            <label>Supplier</label>
                            <v-select v-bind:options="suppliers" v-model="selectedSupplier" label="display_name"></v-select>
                        </div>

                        <div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'user' && users.length > 0 ? '' : 'none'}">
                            <label>User</label>
                            <v-select v-bind:options="users" v-model="selectedUser" label="FullName"></v-select>
                        </div>

                        <div class="form-group" v-bind:style="{display: searchTypesForRecord.includes(searchType) ? '' : 'none'}">
                            <label>Record Type</label>
                            <select class="form-control" v-model="recordType" @change="purchases = []">
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

                        <div class="form-group" style="margin-top: -1px;">
                            <input type="submit" value="Search">
                        </div>
                    </form>
                </div>
            </div>
        </fieldset>
    </div>

    <div class="row" style="display:none;" v-bind:style="{display: purchases.length > 0 ? '' : 'none'}">
        <div class="col-md-12 text-right">
            <a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
        </div>
        <div class="col-md-12">
            <div class="table-responsive" id="reportContent">
                <table class="table record-table table-hover table-bordered" v-if="(searchTypesForRecord.includes(searchType)) && recordType == 'with_details'" style="display:none" v-bind:style="{display: (searchTypesForRecord.includes(searchType)) && recordType == 'with_details' ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Invoice No.</th>
                            <th>Date</th>
                            <th>Supplier Name</th>
                            <th>AddedBy</th>
                            <th>DeletedBy</th>
                            <th>Deleted Time</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="purchase in purchases">
                            <tr>
                                <td>{{ purchase.PurchaseMaster_InvoiceNo }}</td>
                                <td>{{ purchase.PurchaseMaster_OrderDate }}</td>
                                <td>{{ purchase.Supplier_Name }}</td>
                                <td>{{ purchase.added_by }}</td>
                                <td>{{ purchase.deleted_by }}</td>
                                <td>{{ purchase.DeletedTime | dateFormat('DD-MM-YYYY, h:mm:ss a') }}</td>
                                <td style="text-align: left;">{{ purchase.purchaseDetails[0].Product_Name }}</td>
                                <td style="text-align:center;">{{ purchase.purchaseDetails[0].PurchaseDetails_TotalQuantity }}</td>
                                <td style="text-align:right;">{{ purchase.purchaseDetails[0].PurchaseDetails_Rate }}</td>
                                <td style="text-align:right;">{{ purchase.purchaseDetails[0].PurchaseDetails_TotalAmount }}</td>
                                <td style="text-align:center;">
                                    <a href="" title="Deleted Purchase Invoice" v-bind:href="`/deleted_purchase_invoice/${purchase.PurchaseMaster_SlNo}`" target="_blank"><i class="fa fa-file-text"></i></a>
                                </td>
                            </tr>
                            <tr v-for="(product, sl) in purchase.purchaseDetails.slice(1)">
                                <td colspan="3" v-bind:rowspan="purchase.purchaseDetails.length - 1" v-if="sl == 0"></td>
                                <td style="text-align: left;">{{ product.Product_Name }}</td>
                                <td style="text-align:center;">{{ product.PurchaseDetails_TotalQuantity }}</td>
                                <td style="text-align:right;">{{ product.PurchaseDetails_Rate }}</td>
                                <td style="text-align:right;">{{ product.PurchaseDetails_TotalAmount }}</td>
                                <td></td>
                            </tr>
                            <tr style="font-weight:bold;">
                                <td colspan="4" style="font-weight:normal;"><strong>Note: </strong>{{ purchase.PurchaseMaster_Description }}</td>
                                <td style="text-align:center;">Total Quantity<br>{{ purchase.purchaseDetails.reduce((prev, curr) => {return prev + parseFloat(curr.PurchaseDetails_TotalQuantity)}, 0) }}</td>
                                <td></td>
                                <td style="text-align:right;">
                                    Total: {{ purchase.PurchaseMaster_TotalAmount }}<br>
                                    Paid: {{ purchase.PurchaseMaster_PaidAmount }}<br>
                                    Due: {{ purchase.PurchaseMaster_DueAmount }}
                                </td>
                                <td></td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <table class="table record-table table-hover table-bordered" v-if="(searchTypesForRecord.includes(searchType)) && recordType == 'without_details'" style="display:none" v-bind:style="{display: (searchTypesForRecord.includes(searchType)) && recordType == 'without_details' ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Invoice No.</th>
                            <th>Date</th>
                            <th>Supplier Name</th>
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
                            <th>Deleted Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="purchase in purchases">
                            <td>{{ purchase.PurchaseMaster_InvoiceNo }}</td>
                            <td>{{ purchase.PurchaseMaster_OrderDate }}</td>
                            <td>{{ purchase.Supplier_Name }}</td>
                            <td style="text-align:right;">{{ purchase.PurchaseMaster_SubTotalAmount }}</td>
                            <td style="text-align:right;">{{ purchase.PurchaseMaster_Tax }}</td>
                            <td style="text-align:right;">{{ purchase.PurchaseMaster_DiscountAmount }}</td>
                            <td style="text-align:right;">{{ purchase.PurchaseMaster_Freight }}</td>
                            <td style="text-align:right;">{{ purchase.PurchaseMaster_TotalAmount }}</td>
                            <td style="text-align:right;">{{ purchase.PurchaseMaster_PaidAmount }}</td>
                            <td style="text-align:right;">{{ purchase.PurchaseMaster_DueAmount }}</td>
                            <td style="text-align:left;">{{ purchase.PurchaseMaster_Description }}</td>
                            <td>{{ purchase.added_by }}</td>
                            <td>{{ purchase.deleted_by }}</td>
                            <td>{{ purchase.DeletedTime | dateFormat('DD-MM-YYYY, h:mm:ss a') }}</td>
                            <td style="text-align:center;">
                                <a href="" title="Deleted Purchase Invoice" v-bind:href="`/deleted_purchase_invoice/${purchase.PurchaseMaster_SlNo}`" target="_blank"><i class="fa fa-file-text"></i></a>
                            </td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td colspan="3" style="text-align:right;">Total</td>
                            <td style="text-align:right;">{{ purchases.reduce((prev, curr)=>{return prev + parseFloat(curr.PurchaseMaster_SubTotalAmount)}, 0) }}</td>
                            <td style="text-align:right;">{{ purchases.reduce((prev, curr)=>{return prev + parseFloat(curr.PurchaseMaster_Tax)}, 0) }}</td>
                            <td style="text-align:right;">{{ purchases.reduce((prev, curr)=>{return prev + parseFloat(curr.PurchaseMaster_DiscountAmount)}, 0) }}</td>
                            <td style="text-align:right;">{{ purchases.reduce((prev, curr)=>{return prev + parseFloat(curr.PurchaseMaster_Freight)}, 0) }}</td>
                            <td style="text-align:right;">{{ purchases.reduce((prev, curr)=>{return prev + parseFloat(curr.PurchaseMaster_TotalAmount)}, 0) }}</td>
                            <td style="text-align:right;">{{ purchases.reduce((prev, curr)=>{return prev + parseFloat(curr.PurchaseMaster_PaidAmount)}, 0) }}</td>
                            <td style="text-align:right;">{{ purchases.reduce((prev, curr)=>{return prev + parseFloat(curr.PurchaseMaster_DueAmount)}, 0) }}</td>
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

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#purchaseRecord',
        data() {
            return {
                searchType: '',
                recordType: 'without_details',
                dateFrom: moment().format('YYYY-MM-DD'),
                dateTo: moment().format('YYYY-MM-DD'),
                suppliers: [],
                selectedSupplier: null,
                products: [],
                selectedProduct: null,
                users: [],
                selectedUser: null,
                categories: [],
                selectedCategory: null,
                purchases: [],
                searchTypesForRecord: ['', 'user', 'supplier'],
                searchTypesForDetails: ['quantity', 'category']
            }
        },
        filters: {
            dateFormat(dt, format) {
                return moment(dt).format(format);
            },
        },
        methods: {
            onChangeSearchType() {
                this.purchases = [];
                if (this.searchType == 'user') {
                    this.getUsers();
                } else if (this.searchType == 'supplier') {
                    this.getSuppliers();
                }
            },
            getSuppliers() {
                axios.get('/get_suppliers').then(res => {
                    this.suppliers = res.data;
                })
            },
            getUsers() {
                axios.get('/get_users').then(res => {
                    this.users = res.data;
                })
            },
            getSearchResult() {
                if (this.searchType != 'user') {
                    this.selectedUser = null;
                }

                if (this.searchType != 'supplier') {
                    this.selectedSupplier = null;
                }

                this.getPurchaseRecord();
            },
            getPurchaseRecord() {
                let filter = {
                    userFullName: this.selectedUser == null || this.selectedUser.FullName == '' ? '' : this.selectedUser.FullName,
                    supplierId: this.selectedSupplier == null ? '' : this.selectedSupplier.Supplier_SlNo,
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo,
                    status: 'd'
                }

                let url = '/get_purchases';
                if (this.recordType == 'with_details') {
                    url = '/get_purchase_record';
                }

                axios.post(url, filter)
                    .then(res => {
                        if (this.recordType == 'with_details') {
                            this.purchases = res.data;
                        } else {
                            this.purchases = res.data.purchases;
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
                    userText = `<strong>Purchase by: </strong> ${this.selectedUser.FullName}`;
                }

                let supplierText = '';
                if (this.selectedSupplier != null && this.selectedSupplier.Supplier_SlNo != '' && this.searchType == 'quantity') {
                    supplierText = `<strong>Supplier: </strong> ${this.selectedSupplier.Supplier_Name}<br>`;
                }


                let reportContent = `
					<div class="container">
						<div class="row">
							<div class="col-xs-12 text-center">
								<h3>Deleted Purchase Record</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6">
								${userText} ${supplierText} ${productText} ${categoryText}
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
					<link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
					<style>
						.record-table{
							width: 100%;
							border-collapse: collapse;
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