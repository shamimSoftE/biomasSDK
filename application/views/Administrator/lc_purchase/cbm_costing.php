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
                <form class="form-horizontal" @submit.prevent="addDutyCosting">
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
                            <label class="col-sm-4 control-label no-padding-right"> Per CBM </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" step="any" placeholder="Per CBM" class="form-control" id="per_cbm" ref="per_cbm" v-model="costing.Per_CBM" required v-on:input="calculateTotal" readonly/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Per Ctn</label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" step="any" placeholder="Per Ctn" class="form-control" v-model="costing.Per_Ctn" required v-on:input="calculateTotal" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Total Ctn </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" placeholder="Total Ctn" class="form-control" v-model="costing.Total_Ctn" required readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> LC Expense </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" step="any" placeholder="LC Cost" class="form-control" v-model="costing.total_expense" required v-on:input="calculateTotal" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> CBM </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" step="any" placeholder="CBM" class="form-control" v-model="costing.CBM_Cost" required v-on:input="calculateTotal" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Per CBM Cost </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" placeholder="Per CBM Cost" class="form-control" v-model="costing.Per_CBM_Cost" required disabled />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Quantity </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" step="any" placeholder="Quantity" class="form-control" v-model="costing.Quantity" required v-on:input="calculateTotal" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Per Pcs Cost </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" placeholder="Per Pcs Cost" class="form-control" v-model="costing.Per_Pcs_Cost" required disabled />
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
                            <!-- <td>{{ row.material_text }}</td> -->
                            <td>{{ row.Per_CBM }}</td>
                            <td>{{ row.Per_Ctn }}</td>
                            <td>{{ row.Total_Ctn }}</td>
                            <td>{{ row.total_expense }}</td>
                            <td>{{ row.CBM_Cost }}</td>
                            <td>{{ row.Per_CBM_Cost }}</td>
                            <td>{{ row.Quantity }}</td>
                            <td>{{ row.Per_Pcs_Cost }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <button type="button" class="button edit" @click="editDutyCosting(row)">
                                        <i class="fa fa-pencil"></i>
                                    </button>
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
                    Per_CBM: '',
                    Per_Ctn: '',
                    Total_Ctn: 0,
                    total_expense: '',
                    CBM_Cost: '',
                    Per_CBM_Cost: '',
                    Quantity: '',
                    Per_Pcs_Cost: ''
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
                        label: 'Per CBM',
                        field: 'Per_CBM',
                        align: 'center'
                    },
                    {
                        label: 'Per Ctn',
                        field: 'Per_Ctn',
                        align: 'center'
                    },
                    {
                        label: 'Total Ctn',
                        field: 'Total_Ctn',
                        align: 'center'
                    },
                    {
                        label: 'LC Cost',
                        field: 'total_expense',
                        align: 'center'
                    },
                    {
                        label: 'CBM Cost',
                        field: 'CBM_Cost',
                        align: 'center'
                    },
                    {
                        label: 'Per CBM Cost',
                        field: 'Per_CBM_Cost',
                        align: 'center'
                    },
                    {
                        label: 'Quantity',
                        field: 'Per_Pcs_Cost',
                        align: 'center'
                    },
                    {
                        label: 'Per Pcs Cost',
                        field: 'Total_Amount',
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
                axios.post('/get_lc_purchase_record').then(res => {
                    this.lc_numbers = res.data;
                })
            },
            // getPurchases() {
            //     axios.get("/get_purchases").then(res => {
            //         this.invoices = res.data.purchases.filter((r) => r.status == 'p');
            //     })
            // },
          
            async lcOnChange() {
                this.costing.CBM_Cost = this.selectedLCNo?.cbm
                
                if(this.selectedLCNo != null && this.selectedLCNo.purchase_id != null) {
                    let purchase = this.invoices.find(item => item.PurchaseMaster_SlNo ==  this.selectedLCNo.purchase_id);
                    this.selectedInvoice = purchase;
                    this.costing.Item_Type = 'Product';
                }

                this.products = this.selectedLCNo.purchaseDetails;

                this.costing.total_expense = this.selectedLCNo.expDetails.reduce((prev, curr)=> {return prev + parseFloat(curr.amount)}, 0).toFixed(2);
                // this.costing.total_value = this.selectedLCNo.purchaseDetails.reduce((prev, item)=> {return prev + parseFloat(item.PurchaseDetails_TotalAmount)}, 0).toFixed(2);
                this.costing.Per_CBM_Cost = parseFloat(this.costing.total_expense / this.costing.CBM_Cost).toFixed(2);
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
            
            async productOnChange() {
                // this.costing.Per_CBM = this.selectedProduct.perCBM;
                // this.costing.Per_Ctn = this.selectedProduct.perCTN;
                // this.costing.Total_Ctn = this.selectedProduct.totalCTN;
                console.log(this.selectedProduct);
                
                this.costing.Quantity = this.selectedProduct.PurchaseDetails_TotalQuantity;
                
                this.calculateTotal();
                this.$refs.per_cbm.focus();
            },

            calculateTotal() {
                let per_cbm = isNaN(this.costing.Per_CBM) ? 0 : this.costing.Per_CBM;
                let per_ctn = isNaN(this.costing.Per_Ctn) ? 0 : this.costing.Per_Ctn;
                let total_expense = isNaN(this.costing.total_expense) ? 0 : this.costing.total_expense;
                let cbm_cost = isNaN(this.costing.CBM_Cost) ? 0 : this.costing.CBM_Cost;
                let quantity = isNaN(this.costing.Quantity) ? 0 : this.costing.Quantity;

                let total_ctn = (parseFloat(per_cbm) / parseFloat(per_ctn)).toFixed(2);
                let per_cbm_cost = (parseFloat(total_expense) / (parseFloat(cbm_cost))).toFixed(2);
                // let per_pcs_cost = ((parseFloat(per_cbm_cost) / parseFloat(total_ctn)) / parseFloat(quantity)).toFixed(2);
                let per_pcs_cost = ((parseFloat(per_cbm_cost) * parseFloat(per_cbm)) / parseFloat(quantity)).toFixed(2);

                this.costing.Total_Ctn = isNaN(total_ctn) ? 0 : total_ctn;
                this.costing.Per_CBM_Cost = isNaN(per_cbm_cost) ? 0 : per_cbm_cost;
                this.costing.Per_Pcs_Cost = isNaN(per_pcs_cost) ? 0 : per_pcs_cost;
            },

            addDutyCosting() {
                if (this.selectedLCNo == null) {
                    alert('Select a LC No');
                    return;
                }

                if (this.costing.Item_Type == 'Product' && this.selectedProduct == null) {
                    alert('Select a Product');
                    return;
                }

                if (this.costing.Per_CBM == '' ||
                    this.costing.Per_Ctn == '' ||
                    this.costing.Total_Ctn == '' ||
                    this.costing.total_expense == '' ||
                    this.costing.CBM_Cost == '' ||
                    this.costing.Per_CBM_Cost == '' ||
                    this.costing.Quantity == '' ||
                    this.costing.Per_Pcs_Cost == '') {
                    alert('Input field is required.');
                    return;
                }

                this.costing.Lcc_SlNo = this.selectedLCNo.lc_purchase_master_id;
                
                this.costing.PurchaseMaster_SlNo = this.selectedInvoice == null || this.selectedInvoice == '' ? null : this.selectedInvoice.PurchaseMaster_SlNo;
                

                if (this.selectedProduct != null) {
                    this.costing.Product_SlNo = this.selectedProduct.Product_SlNo;
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
                })
            },

            editDutyCosting(costing) {
                let keys = Object.keys(this.costing);
                keys.forEach(key => this.costing[key] = costing[key]);

                this.selectedLCNo = {
                    Lcc_SlNo: costing.Lcc_SlNo,
                    lc_purchase_master_id: costing.Lcc_SlNo,
                    purchase_id: costing.PurchaseMaster_SlNo,
                    PurchaseMaster_InvoiceNo: costing.Lcc_No,
                    Lcc_No: costing.Lcc_No,
                    lc_number: costing.Lcc_No
                }

                if (costing.Item_Type == 'Product') {
                    this.selectedInvoice = {
                        PurchaseMaster_SlNo: costing.PurchaseMaster_SlNo,
                        invoice_text: `${costing.PurchaseMaster_InvoiceNo} - ${costing.Supplier_Name}`
                    }
                }              

                if (costing.Item_Type == 'Product') {
                    this.selectedProduct = {
                        Product_SlNo: costing.Product_SlNo,
                        display_text: `${costing.Product_Code} - ${costing.Product_Name}`
                    }
                }
                
                setTimeout(() => {
                    this.costing.CBM_Cost = costing.CBM_Cost
                    this.costing.Per_CBM = costing.Per_CBM
                    this.costing.Per_CBM_Cost = costing.Per_CBM_Cost
                    this.costing.Per_Ctn = costing.Per_Ctn
                    this.costing.Per_Pcs_Cost = costing.Per_Pcs_Cost
                    this.costing.Quantity = costing.Quantity
                    this.costing.Total_Ctn = costing.Total_Ctn
                }, 1000);
            },

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
                    Material_IDNo: '',
                    PurchaseMaster_SlNo: '',
                    Per_CBM: '',
                    Per_Ctn: '',
                    Total_Ctn: 0,
                    total_expense: '',
                    CBM_Cost: '',
                    Per_CBM_Cost: '',
                    Quantity: '',
                    Per_Pcs_Cost: ''
                };

                this.selectedLCNo = null;
                this.selectedProduct = null;
                this.selectedMaterial = null;
                this.selectedInvoice = null;
            }
        }
    })
</script>