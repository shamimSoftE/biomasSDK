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

	#branchDropdown .vs__actions button {
		display: none;
	}

	#branchDropdown .vs__actions .open-indicator {
		height: 15px;
		margin-top: 7px;
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
</style>

<div id="costingDutyForm">

    <fieldset class="scheduler-border entryFrom">
        <div class="control-group">
            <div class="row" style="margin-top: 15px;">
                <form class="form-horizontal" @submit.prevent="addCosting">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> LC No </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <v-select v-bind:options="lc_numbers" v-model="selectedLCNo" label="PurchaseMaster_InvoiceNo" placeholder="Select LC Number" v-on:input="lcOnChange"></v-select>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Product </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <v-select id="product" v-bind:options="products" label="Product_Name" v-model="selectedProduct" placeholder="Select Product" v-on:input="productOnChange"></v-select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> LC Expense </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" step="any" placeholder="LC Cost" class="form-control" v-model="costing.total_expense" required readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" title="Total Value"> Total BDT</label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" step="any" placeholder="Total BDT" class="form-control" v-model="costing.total_value" required  readonly />
                            </div>
                        </div>

                        
                    </div>
                    <div class="col-md-6">
                        
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Expense Coast </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" placeholder="Expense Coast" class="form-control" v-model="costing.expense_coast" required readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Product Value </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" placeholder="Per CBM Cost" class="form-control" v-model="costing.product_value" required disabled />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Quantity </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" step="any" placeholder="Quantity" class="form-control" v-model="costing.Quantity" required  readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Product Cost </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" placeholder="Per Pcs Cost" class="form-control" v-model="costing.product_coast" required disabled />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"></label>
                            <label class="col-sm-1 control-label no-padding-right"></label>
                            <div class="col-sm-5">
                                <button type="submit" class="btn btn-sm btn-success" style=" background: rgb(45, 28, 90) !important; border: 0px; border-radius: 5px;">
                                    Submit
                                    <i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </fieldset> 

    <div class="row" style="margin-top: 10px;">
        <div class="col-md-12 form-inline">
			<div class="form-group">
				<label for="filter" class="sr-only">Filter</label>
				<input type="text" class="form-control" v-model="filter" placeholder="Filter">
			</div>
		</div>
        
        <div class="col-md-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="duties" :filter-by="filter">
                    <template scope="{ row }">
                        <tr>
                            <td>{{ row.Costing_Date }}</td>
                            <td>{{ row.Lcc_No }}</td>
                            <td>{{ row.display_name }}</td>
                            <td>{{ row.total_expense }}</td>
                            <td>{{ row.total_value }}</td>
                            <td>{{ row.Quantity }}</td>
                            <td>{{ row.product_coast }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <!-- <button type="button" class="button edit" @click="editDutyCosting(row)">
                                        <i class="fa fa-pencil"></i>
                                    </button> -->
                                    <button type="button" class="button" @click="deleteDutyCosting(row.Costing_SlNo)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                <?php } ?>
                            </td>
                        </tr>
                    </template>
                </datatable>
                <datatable-pager v-model="page" type="abbreviated" :per-page="per_page"></datatable-pager>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#costingDutyForm',
        data() {
            return {
                costing: {
                    Costing_SlNo: 0,
                    Costing_Date: moment().format('YYYY-MM-DD'),
                    Lcc_SlNo: '',
                    Item_Type: 'Product',
                    Product_SlNo: '',
                    PurchaseMaster_SlNo: '',
                    product_coast: 0,
                    product_value: 0,
                    total_expense: 0,
                    total_value: 0,
                    Quantity: 0,
                    expense_coast: 0
                },
                lc_numbers: [],
                selectedLCNo: null,
                products: [],
                selectedProduct: null,
                materials: [],
                selectedMaterial: null,
                duties: [],
                selectedInvoice: null,
                invoices: [],
                selectedMaterialInvoice: null,
                materialInvoices: [],
                columns: [{
                        label: 'Entry Date',
                        field: 'Costing_Date',
                        align: 'center'
                    },
                    {
                        label: 'LC No',
                        field: 'Lcc_No',
                        align: 'center'
                    },
                    {
                        label: 'Product',
                        field: 'display_name',
                        align: 'center'
                    },
                    {
                        label: 'Expense',
                        field: 'total_expense',
                        align: 'center'
                    },
                    {
                        label: 'Total Value',
                        field: 'total_value',
                        align: 'center'
                    },
                    
                    // {
                    //     label: 'Product Price',
                    //     field: 'product_value',
                    //     align: 'center'
                    // },
                    {
                        label: 'Quantity',
                        field: 'Quantity',
                        align: 'center'
                    },
                    {
                        label: 'Per Pcs Cost',
                        field: 'product_coast',
                        align: 'center'
                    },
                    {
                        label: 'Action',
                        align: 'center',
                        filterable: false
                    }
                ],
                page: 1,
                per_page: 10,
                filter: ''
            }
        },

        created() {
            this.getLCNumbers();
            this.getDutyCostings();
            // this.getPurchases();
        },
        methods: {
            getLCNumbers() {
                axios.post('/get_pending_lc_purchase_record').then(res => {
                    this.lc_numbers = res.data;
                })
            },
          
            async lcOnChange() {
                if(this.selectedLCNo != null && this.selectedLCNo.purchase_id != null) {
                    let purchase = this.invoices.find(item => item.PurchaseMaster_SlNo ==  this.selectedLCNo.purchase_id);
                    this.selectedInvoice = purchase;
                    this.costing.Item_Type = 'Product';
                }

                this.products = this.selectedLCNo.purchaseDetails;
                // console.log(this.selectedLCNo);
                // return;

                this.costing.total_expense = this.selectedLCNo.expDetails.reduce((prev, curr)=> {return prev + parseFloat(curr.amount)}, 0).toFixed(2);
                this.costing.total_value = this.selectedLCNo.purchaseDetails.reduce((prev, item)=> {return prev + parseFloat(item.PurchaseDetails_TotalAmount)}, 0).toFixed(2);
                // this.costing.expense_coast = parseFloat(this.costing.total_value / this.costing.total_expense).toFixed(2);
                this.costing.expense_coast = (parseFloat(this.costing.total_expense) / parseFloat(this.costing.total_value)).toFixed(6);
            },

            async getPurchaseProduct() {
                this.selectedProduct == null;
                if (this.selectedInvoice == null) {
                    return
                }
                axios.post('/get_purchase_products', {
                    PurchaseId: this.selectedInvoice.PurchaseMaster_SlNo
                }).then(res => {
                    this.products = res.data.products;
                })
                let productSearchBox = document.querySelector('#product input[role="combobox"]');
                productSearchBox.focus();
            },
            
            productOnChange() {
                this.costing.Quantity = this.selectedProduct.PurchaseDetails_TotalQuantity;
                this.costing.product_value = this.selectedProduct.PurchaseDetails_Rate;
                this.costing.product_coast = parseFloat((this.costing.product_value * this.costing.expense_coast) / this.costing.Quantity).toFixed(6);
                
            },

            addCosting() {
                if (this.selectedLCNo == null) {
                    alert('Select a LC No');
                    return;
                }

                if (this.costing.Item_Type == 'Product' && this.selectedProduct == null) {
                    alert('Select a Product');
                    return;
                }
                
                this.costing.Lcc_SlNo = this.selectedLCNo.lc_purchase_master_id;
                
                this.costing.PurchaseMaster_SlNo = this.selectedLCNo == null || this.selectedLCNo == '' ? null : this.selectedLCNo.purchase_id;
                
                if (this.selectedProduct != null) {
                    this.costing.Product_SlNo = this.selectedProduct.Product_IDNo;
                } else {
                    this.costing.Product_SlNo = null;
                }
                
                let url = '/add_cbm_costing';
                if (this.costing.Costing_SlNo != 0) {
                    url = '/update_cbm_costing'
                }
                axios.post(url, this.costing).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.resetForm();
                        this.getDutyCostings();
                    }
                    this.getLCNumbers();
                })
            },

            // editDutyCosting(costing) {
            //     let keys = Object.keys(this.costing);
            //     keys.forEach(key => this.costing[key] = costing[key]);

            //     this.selectedLCNo = {
            //         Lcc_SlNo: costing.Lcc_SlNo,
            //         lc_purchase_master_id: costing.Lcc_SlNo,
            //         purchase_id: costing.PurchaseMaster_SlNo,
            //         PurchaseMaster_InvoiceNo: costing.Lcc_No,
            //         Lcc_No: costing.Lcc_No,
            //         lc_number: costing.Lcc_No
            //     }
            //     this.selectedProduct = {
            //         Product_Name: costing.Product_Name,
            //         Product_SlNo: costing.Product_SlNo,
            //     };
            // },

            deleteDutyCosting(costingId) {
                let deleteConfirm = confirm('Are you sure?');
                if (deleteConfirm == false) {
                    return;
                }
                axios.post('/delete_cbm_costing', {
                    costingId: costingId
                }).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getDutyCostings();
                    }
                })
            },

            getDutyCostings() {
                axios.get('/get_cbm_costings').then(res => {
                    this.duties = res.data;
                })
            },

            resetForm() {
                this.costing = {
                    Costing_SlNo: 0,
                    Costing_Date: moment().format('YYYY-MM-DD'),
                    Lcc_SlNo: '',
                    Item_Type: 'Product',
                    Product_SlNo: '',
                    PurchaseMaster_SlNo: '',
                    product_coast: 0,
                    product_value: 0,
                    total_expense: 0,
                    total_expense: 0,
                    total_value: 0,
                    Quantity: 0,
                    expense_coast: 0
                };

                this.selectedLCNo = null;
                this.selectedProduct = null;
                this.selectedMaterial = null;
                this.selectedInvoice = null;
            }
        }
    })
</script>