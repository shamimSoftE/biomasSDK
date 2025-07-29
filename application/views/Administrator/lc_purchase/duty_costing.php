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

                        <div class="form-group" style="display:none;" v-bind:style="{display: costing.Item_Type == 'Product' ? '' : 'none'}">
                            <label class="col-sm-4 control-label no-padding-right"> Purchase Inv</label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <v-select v-bind:options="invoices" label="invoice_text" v-model="selectedInvoice" v-on:input="viewPurchaseProduct" placeholder="Select Invoice" disabled></v-select>
                            </div>
                        </div>

                        <div class="form-group" style="display:none;" v-bind:style="{display: costing.Item_Type == 'Product' ? '' : 'none'}">
                            <label class="col-sm-4 control-label no-padding-right"> Product <i class="fa fa-info-circle" aria-hidden="true" title="Duty Costing => PC / Kg x Per USD = Total Dollar x Currency Rate = Total BDT x Percentage" style="margin-top: 5px; font-size: 20px;"></i></label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <v-select id="product" v-bind:options="products" label="display_text" v-model="selectedProduct" placeholder="Select Product" v-on:input="productOnChange"></v-select>
                            </div>
                        </div>


                        <!-- <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Pcs / KG </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" step="0.01" placeholder="Pcs / KG" class="form-control" id="pc_kg" ref="pc_kg" v-model="costing.Pcs_Kg" required v-on:input="calculateTotal" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Per USD ($)</label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" step="0.01" placeholder="Per USD ($)" class="form-control" v-model="costing.Per_USD" required v-on:input="calculateTotal" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Total Dollar </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" placeholder="Total Dollar" class="form-control" v-model="costing.Total_Dollar" required disabled />
                            </div>
                        </div> -->
                    </div>
                    <div class="col-md-6">
                        <!-- <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Currency Rate </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" step="0.01" placeholder="Currency Rate" class="form-control" v-model="costing.Currency_Rate" required v-on:input="calculateTotal" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Total BDT </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" placeholder="Total BDT" class="form-control" v-model="costing.Total_BDT" required disabled />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Percentage (%) </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" step="0.01" placeholder="Percentage (%)" class="form-control" v-model="costing.Percentage" required v-on:input="calculateTotal" />
                            </div>
                        </div> -->

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Total Amount </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" step="0.001" placeholder="Total Amount" id="totalAmount" class="form-control" v-model="costing.Total_Amount" required @input="calculateTotal" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Quantity </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                            
                                <input type="number" placeholder="0" class="form-control" v-model="costing.quantity" v-on:input="productOnChange" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Per Pcs Costing </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-7">
                                <input type="number" placeholder="0" class="form-control" v-model="costing.perPcsCosting" v-on:input="calculateTotal" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"></label>
                            <label class="col-sm-1 control-label no-padding-right"></label>
                            <div class="col-sm-7">
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

    <div class="row">
        <div class="col-sm-12 form-inline">
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
                            <td>{{ row.invoice_text }}</td>
                            <td>{{ row.display_name }}</td>
                            <td>{{ row.Pcs_Kg }}</td>
                            <td>{{ row.Per_USD }}</td>
                            <td>{{ row.Currency_Rate }}</td>
                            <td>{{ row.Percentage }}</td>
                            <td>{{ row.quantity }}</td>
                            <td>{{ row.perPcsCosting }}</td>
                            <td>{{ row.Total_Amount }}</td>
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
                    Pcs_Kg: '',
                    Per_USD: '',
                    Total_Dollar: 0,
                    Currency_Rate: '',
                    Total_BDT: 0,
                    Percentage: '',
                    Total_Amount: 0,
                    quantity: 0,
                    perPcsCosting: 0
                },
                lc_numbers: [],
                selectedLCNo: null,
                products: [],
                selectedProduct: null,
                perPcsCosting: null,
               
                selectedInvoice: null,
                invoices: [],
                duties: [],
                selectQuantity: null,
                columns: [{
                        label: 'Entry Date',
                        field: 'Costing_Date',
                        align: 'center'
                    },
                    {
                        label: 'Invoice No',
                        field: 'invoice_text',
                        align: 'center'
                    },
                    {
                        label: 'Product',
                        field: 'display_name',
                        align: 'center'
                    },
                   
                    {
                        label: 'Pcs / KG',
                        field: 'Pcs_Kg',
                        align: 'center'
                    },
                    {
                        label: 'Per USD ($)',
                        field: 'Per_USD',
                        align: 'center'
                    },
                    {
                        label: 'Currency Rate',
                        field: 'Currency_Rate',
                        align: 'center'
                    },
                    {
                        label: 'Percentage (%)',
                        field: 'Percentage',
                        align: 'center'
                    },
                    {
                        label: 'Quantity',
                        field: 'quantity',
                        align: 'center'
                    },
                    {
                        label: 'Per Pcs Costing',
                        field: 'perPcsCosting',
                        align: 'center'
                    },
                    {
                        label: 'Total Amount',
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
                filter: '',
                isEditAble: false
            }
        },

        created() {
            this.getLCNumbers();
            this.getDutyCostings();
            this.getPurchases();
        },
        methods: {
            getLCNumbers() {
                // axios.post('/get_LC', { status: 'p' }).then(res => {
                 axios.post('/get_lc_purchase_record').then(res => {
                    this.lc_numbers = res.data;
                })
            },
            getPurchases() {
                axios.get("/get_purchases").then(res => {
                    this.invoices = res.data.purchases.filter((r) => r.status == 'p');
                })
            },
           
            async lcOnChange() {
                if(this.selectedLCNo != null && this.selectedLCNo.purchase_id != null) {
                    let purchase = this.invoices.find(item => item.PurchaseMaster_SlNo ==  this.selectedLCNo.purchase_id);
                    this.selectedInvoice = purchase;
                    this.costing.Item_Type = 'Product';
                }
            },
            
            viewPurchaseProduct() {
                this.selectedProduct == null;
                if (this.selectedInvoice == null) {
                    return
                }
                axios.post('/get_purchase_products', {
                    PurchaseId: this.selectedInvoice.PurchaseMaster_SlNo
                }).then(res => {
                    this.products = res.data.products;
                })
                if(this.isEditAble == false) {
                    let productSearchBox = document.querySelector('#product input[role="combobox"]');
                    productSearchBox.focus();
                }
            },
          
            onItemTypeChange() {
                this.selectedProduct = null;
            },

            // perCostingOnChange() {
            //     let totalPerPcsValue = (parseFloat(this.costing.Total_Amount) / parseFloat(this.selectQuantity)).toFixed(2);
            //     this.perPcsCosting = parseFloat(totalPerPcsValue).toFixed(2);
            // },
           
            productOnChange() {
                if(this.isEditAble == false) {
                    this.costing.quantity = this.selectedProduct ? this.selectedProduct.PurchaseDetails_TotalQuantity : '';
                    // this.$refs.pc_kg.focus();

                    let totalPerPcsValue = (parseFloat(this.costing.Total_Amount) / parseFloat(this.costing.quantity)).toFixed(2);
                    this.costing.perPcsCosting = parseFloat(totalPerPcsValue).toFixed(2);
                } 
            },
            
            calculateTotal() {
                let pc_kg = isNaN(this.costing.Pcs_Kg) ? 0 : this.costing.Pcs_Kg;
                let per_usd = isNaN(this.costing.Per_USD) ? 0 : this.costing.Per_USD;
                let currency_rate = isNaN(this.costing.Currency_Rate) ? 0 : this.costing.Currency_Rate;
                let percentage = isNaN(this.costing.Percentage) ? 0 : this.costing.Percentage;

                let total_dollar = (parseFloat(pc_kg) * parseFloat(per_usd)).toFixed(2);
                let total_bdt = (parseFloat(total_dollar) * parseFloat(currency_rate)).toFixed(2);

                let total_amount = this.costing.Total_Amount;
                if (event.target.id != 'totalAmount') {
                    total_amount = ((parseFloat(total_bdt) * parseFloat(percentage)) / 100).toFixed(2);
                }
                
                this.costing.Total_Dollar = isNaN(total_dollar) ? 0 : total_dollar;
                this.costing.Total_BDT = isNaN(total_bdt) ? 0 : total_bdt;
                this.costing.Total_Amount = isNaN(total_amount) ? 0 : total_amount;

                let totalPerPcsValue = (parseFloat(this.costing.Total_Amount) / parseFloat(this.costing.quantity)).toFixed(2);
                this.costing.perPcsCosting = parseFloat(totalPerPcsValue).toFixed(2);
            },

            addDutyCosting() {
                if (this.selectedLCNo == null) {
                    alert('Select a LC No');
                    return;
                }

                this.costing.Lcc_SlNo = this.selectedLCNo.lc_purchase_master_id;
                this.costing.PurchaseMaster_SlNo = this.selectedInvoice.PurchaseMaster_SlNo;      
               
                if (this.selectedProduct != null) {
                    this.costing.Product_SlNo = this.selectedProduct.Product_SlNo;
                } else {
                    this.costing.Product_SlNo = null;
                }               
                
                let url = '/add_duty_costing';
                if (this.costing.Costing_SlNo != 0) {
                    url = '/update_duty_costing'
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
                this.isEditAble = true;

                let keys = Object.keys(this.costing);
                keys.forEach(key => this.costing[key] = costing[key]);
                
                this.selectedLCNo = {
                    Lcc_SlNo: costing.Lcc_SlNo,
                    Lcc_No: costing.Lcc_No,
                    lc_purchase_master_id: costing.Lcc_SlNo,
                    purchase_id: costing.PurchaseMaster_SlNo,
                    PurchaseMaster_InvoiceNo: costing.Lcc_No,
                    lc_number: costing.Lcc_No
                };

                this.selectedInvoice = {
                    PurchaseMaster_SlNo: costing.PurchaseMaster_SlNo,
                    invoice_text: `${costing.PurchaseMaster_InvoiceNo} - ${costing.Supplier_Name}`
                };
                
                this.selectedProduct = {
                    Product_SlNo: costing.Product_SlNo,
                    display_text: `${costing.Product_Code} - ${costing.Product_Name}`
                };
            },

            deleteDutyCosting(costingId) {
                let deleteConfirm = confirm('Are you sure?');
                if (deleteConfirm == false) {
                    return;
                }
                axios.post('/delete_duty_costing', {
                    costingId: costingId
                }).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getDutyCostings();
                    }
                })
            },

            async getDutyCostings() {
                await axios.get('/get_duty_costings').then(res => {
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
                    Pcs_Kg: '',
                    Per_USD: '',
                    Total_Dollar: 0,
                    Currency_Rate: '',
                    Total_BDT: 0,
                    Percentage: '',
                    Total_Amount: 0,
                    perPcsCosting: 0
                };

                // this.selectedLCNo = null;
                this.selectedProduct = null;
            }
        }
    })
</script>