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

<div class="row" id="purchase">
	<div class="col-xs-12 col-md-12">
		<fieldset class="scheduler-border entryFrom">
			<div class="control-group">
				<div class="row">
					<div class="form-group">
						<label class="col-xs-4 col-md-1 control-label no-padding-right"> Invoice no </label>
						<div class="col-xs-8 col-md-2">
							<input type="text" id="invoice" style="margin: 0;" class="form-control" name="invoice" v-model="purchase.invoice" readonly />
						</div>
					</div>

					<div class="form-group">
						<label class="col-xs-4 col-md-2 control-label no-padding-right"> LC For </label>
						<div class="col-xs-8 col-md-3">
							<v-select id="branchDropdown" style="margin: 0;" v-bind:options="branches" v-model="selectedBranch" label="Branch_name" disabled></v-select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-xs-4 col-md-1 control-label no-padding-right"> Date </label>
						<div class="col-xs-8 col-md-3">
							<input class="form-control" style="margin: 0;" id="purchaseDate" name="purchaseDate" type="date" v-model="purchase.purchaseDate" v-bind:disabled="userType == 'u' ? true : false" />
						</div>
					</div>
					
				</div>
			</div>
		</fieldset>
	</div>
		<div class="col-xs-12 col-md-12" style="margin-top: 5px;">
		<fieldset class="scheduler-border entryFrom" style="margin-top: 5px;">
			<div class="control-group">
				<div class="row">
					<div class="form-group">
						<label class="col-xs-4 col-md-1 control-label no-padding-right"> LC No </label>
						<div class="col-xs-8 col-md-2">
							<input type="text" id="lc_no" style="margin: 0;" class="form-control" name="lc_no" v-model="purchase.lc_no" require placeholder="LC No"/>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-4 col-md-2 control-label no-padding-right"> PI No </label>
						<div class="col-xs-8 col-md-3">
							<input type="text" id="pi_no" style="margin: 0;" class="form-control" name="pi_no" v-model="purchase.pi_no" require placeholder="PI No" />
							
						</div>
					</div>

					<div class="form-group">
						<label class="col-xs-4 col-md-1 control-label no-padding-right"> LC By </label>
						<div class="col-xs-8 col-md-3">
							<v-select v-bind:options="employees" v-model="selectedEmployee" label="display_name" placeholder="Select Employee"></v-select>
						</div>
					</div>
				</div>
			</div>
		</fieldset>
	</div>

	<div class="col-xs-12 col-md-9">
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<fieldset class="scheduler-border">
					<legend class="scheduler-border">Supplier Information</legend>
					<div class="control-group">

						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right"> Purchase Inv. </label>
							<div class="col-xs-8">
								<v-select v-bind:options="purchases" v-model="selectedPurchase" v-on:input="onChangePurchase" label="invoice_text" placeholder="Select Purchase Invoice"></v-select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right"> Bank </label>
							<div class="col-xs-8" style="display: flex;align-items:center;margin-bottom:5px;">
								<div style="width: 86%;">
									<v-select v-bind:options="banks" style="margin: 0;" v-model="selectedBank" placeholder="Select Bank"  label="display_name"></v-select>
								</div>
								<div style="width: 13%;margin-left:2px;">
									<a href="<?= base_url('bank_accounts') ?>" class="add-button" target="_blank" title="Add New Supplier"><i class="fa fa-plus" aria-hidden="true"></i></a>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right"> Supplier </label>
							<div class="col-xs-8" style="display: flex;align-items:center;margin-bottom:5px;">
								<div style="width: 86%;">
									<v-select v-bind:options="suppliers" style="margin: 0;" v-model="selectedSupplier" v-on:input="onChangeSupplier" @search="onSearchSupplier" label="display_name"></v-select>
								</div>
								<div style="width: 13%;margin-left:2px;">
									<a href="<?= base_url('supplier') ?>" class="add-button" target="_blank" title="Add New Supplier"><i class="fa fa-plus" aria-hidden="true"></i></a>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right"> Name </label>
							<div class="col-xs-8">
								<input type="text" placeholder="Supplier Name" class="form-control" v-model="selectedSupplier.Supplier_Name" v-bind:disabled="selectedSupplier.Supplier_Type == 'G' || selectedSupplier.Supplier_Type == 'N' ? false : true" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right"> Mobile No </label>
							<div class="col-xs-8">
								<input type="text" placeholder="Mobile No" class="form-control" v-model="selectedSupplier.Supplier_Mobile" v-bind:disabled="selectedSupplier.Supplier_Type == 'G' || selectedSupplier.Supplier_Type == 'N' ? false : true" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right"> Address </label>
							<div class="col-xs-8">
								<textarea class="form-control" v-model="selectedSupplier.Supplier_Address" v-bind:disabled="selectedSupplier.Supplier_Type == 'G' || selectedSupplier.Supplier_Type == 'N' ? false : true"></textarea>
							</div>
						</div>
					</div>
				</fieldset>
			</div>

			<div class="col-xs-12 col-md-6 no-padding-left">
				<fieldset class="scheduler-border">
					<legend class="scheduler-border">Product Information</legend>
					<div class="control-group">
						<form v-on:submit.prevent="addToCart">		

							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> Product </label>
								<div class="col-xs-9" style="display: flex;align-items:center;margin-bottom:5px;">
									<div style="width: 86%;">
										<v-select v-bind:options="products" id="product" style="margin: 0;" v-model="selectedProduct" label="display_text" v-on:input="onChangeProduct"></v-select>
									</div>
									<div style="width: 13%;margin-left:2px;">
										<a href="<?= base_url('product') ?>" class="add-button" target="_blank" title="Add New Product"><i class="fa fa-plus" aria-hidden="true"></i></a>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> Currency </label>
								<div class="col-xs-4">
									<select name=""  class="form-control" v-model="currency_name" require>
										<option value="INR">INR</option>
										<option value="USD">USD</option>
										<option value="YUAN">YUAN</option>
									</select>
								</div>
								<label class="col-xs-1 control-label no-padding-right"> Qty </label>
								<div class="col-xs-4">
									<input type="number" step="0.01" id="quantity" name="quantity" class="form-control" placeholder="Quantity" ref="quantity" v-model="selectedProduct.quantity" v-on:input="productTotal();productValueTotal()" readonly autocomplete="off" />
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-xs-3">Unit:</label>
								<div class="col-xs-9">
									<select class="form-control" v-if="units.length == 0"></select>
									<v-select v-bind:options="units" v-model="selectedUnit" label="Unit_Name" v-if="units.length > 0"></v-select>
								</div>
							</div>

							<div class="form-group" >
								<label class="col-xs-3 control-label no-padding-right" style="padding-right: 0;"> Per Value</label>
								<div class="col-xs-3">
									<input type="text" id="productTotal" placeholder="0" class="form-control" v-model="selectedProduct.perForeignAmount" v-on:input="productValueTotal" />
								</div>
								<label class="col-xs-2 control-label no-padding-right"> Total Val</label>
								<div class="col-xs-4">
									<input type="text" id="productTotal" placeholder="Amount" class="form-control" v-model="selectedProduct.totalForeignAmount" />
								</div>
							</div>

							<div class="form-group" >
								<label class="col-xs-3 control-label no-padding-right" title="Currency Rate"> Cur. Rate </label>
								<div class="col-xs-9">
									<input type="number" id="currencyRate" placeholder="Rate" step="any" class="form-control" v-model="selectedProduct.Product_currency_Rate" v-on:input="productTotal" />
								</div>
							</div>

							<div class="form-group" title="Purchase Rate">
								<label class="col-xs-3 control-label no-padding-right" title="Purchase Rate"> Pur.Rate </label>
								<div class="col-xs-9">
									<input type="number" id="salesRate" placeholder="Rate" step="any" class="form-control" v-model="selectedProduct.Product_Purchase_Rate" v-on:input="productTotal" readonly />
								</div>
							</div>

							<div class="form-group" >
								<label class="col-xs-3 control-label no-padding-right"> Total Amount </label>
								<div class="col-xs-9">
									<input type="text" id="productTotal" placeholder="Amount" class="form-control" v-model="selectedProduct.total" readonly />
								</div>
							</div>


							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right"> </label>
								<div class="col-xs-8">
									<button type="submit" class="btnCart pull-right">Add Cart</button>
								</div>
							</div>
						</form>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="col-xs-12 col-md-12" style="padding-left: 0px;padding-right: 0px;">
			<div class="table-responsive">
				<table class="table table-bordered table-hover" style="color:#000;margin-bottom: 5px;">
					<thead>
						<tr>
							<th style="width:4%;color:#000;">SL</th>
							<th style="width:30%;color:#000;">Product Name</th>
							<th style="width:13%;color:#000;">Category</th>
							<th style="width:8%;color:#000;">Currency</th>
							<th style="width:8%;color:#000;">Currency Rate</th>
							<th style="width:8%;color:#000;" title="Total currency rate">
								Total Value
							</th>
							<th style="width:5%;color:#000;">Qty</th>
							<th style="width:5%;color:#000;">Unit</th>
							<th style="width:8%;color:#000;">Rate</th>
							<th style="width:13%;color:#000;">Total</th>
							<th style="width:5%;color:#000;">Action</th>
						</tr>
					</thead>
					<tbody style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
						<tr v-for="(product, sl) in cart">
							<td>{{ sl + 1}}</td>
							<td style="text-align: left;padding-left:3px;">{{ product.name }}</td>
							<td>{{ product.categoryName }}</td>
							<td>{{ product.currencyName }}</td>
							<td>
								<input type="number" min="0" step="any" 
								v-model="product.ProductCurrencyRate" 
								style="margin:0;padding: 0 5px; width: 70px; text-align: center;"
								@input="currencyRateChange(product)" />
							</td>
							<td>
								{{ product.totalForeignAmount }}
								<!-- <input type="number" min="0" step="any" v-model="product.currency_rate" style="margin:0;padding: 0 5px; width: 70px; text-align: center;" @input="quantityRateChange" /> -->
							</td>
							<td>
								{{ product.quantity }}
								<!-- <input type="number" min="0" step="any" v-model="product.quantity" style="margin:0;padding: 0 5px; width: 70px; text-align: center;" @input="quantityRateChange" /> -->
							</td>
							<td>{{ product.Unitname }} </td>
							<td>
								{{ product.purchaseRate }}
							</td>
							<td>{{ product.total }}</td>
							<td><a href="" v-on:click.prevent="removeFromCart(sl)"><i class="fa fa-trash"></i></a></td>
						</tr>

						<tr>
							<td colspan="8"></td>
						</tr>

						<tr style="font-weight: bold;">
							<td colspan="8">Note</td>
							<td colspan="3">Total</td>
						</tr>

						<tr>
							<td colspan="8"><textarea class="form-control" style="font-size:13px;margin-top:3px;" placeholder="Note" v-model="purchase.note"></textarea></td>
							<td colspan="3" style="padding-top: 15px;font-size:18px;">{{ purchase.total }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- <div class="col-xs-12 col-md-3">
		<fieldset class="scheduler-border">
			<legend class="scheduler-border">Expense Details</legend>
			<div class="control-group">
				<form v-on:submit.prevent="addToCartExp">
					<div class="form-group">
						<div class="col-xs-12">
							<v-select v-bind:options="expneses" style="margin: 0;" v-model="selectedExpense" placeholder="Select Expense"  label="name"></v-select>
						</div>
					
						<div class="col-xs-6" style="padding-right: 0; margin-top: 5px;">
							<input type="number"   min="0" step="any" id="total" class="form-control" v-model="exp_amount" />
						</div>
						<div class="col-xs-6" style="margin-top: 5px;">
							<button type="submit" style="padding: 2px 5px;" class="btnCart pull-right">Add</button>
						</div>
					</div>
				</form>
			</div>
			<div class="control-group">
				<div class="row">
					<div class="col-xs-12">
							
							<div class="table-responsive">
								<table class="table table-bordered table-hover" style="color:#000;margin-bottom: 5px;">
									<thead>
										<tr>
											<th style="width:4%;color:#000;">SL</th>
											<th style="width:30%;color:#000;">Exp Name</th>
											<th style="width:13%;color:#000;">Total</th>
											<th style="width:5%;color:#000;">Action</th>
										</tr>
									</thead>
									<tbody style="display:none;" v-bind:style="{display: expcart.length > 0 ? '' : 'none'}">
										<tr v-for="(expense, sl) in expcart">
											<td>{{ sl + 1}}</td>
											<td style="text-align: left;padding-left:3px;">{{ expense.name }}</td>
											<td>{{ expense.total }}</td>
											<td><a href="" v-on:click.prevent="removeFromExpCart(sl)"><i class="fa fa-trash"></i></a></td>
										</tr>
									</tbody>
								</table>
							</div>
					</div>
				</div>
			</div>
		</fieldset>
	</div> -->

	<div class="col-xs-12 col-md-3">
		<fieldset class="scheduler-border">
			<legend class="scheduler-border">Amount Details</legend>
			<div class="control-group">
				<div class="row">
					<div class="col-xs-12">
						<form @submit.prevent="saveLcData">
							<div class="table-responsive">
								<table style="color:#000;margin-bottom: 0px;width:100%">
									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Sub Total</label>
												<div class="col-xs-12">
													<input type="number" min="0" step="any" id="total" class="form-control" v-model="purchase.subTotal" readonly />
												</div>
											</div>
										</td>
									</tr>

									<!-- <tr>
                                        <td>
                                            <div class="form-group">
                                                <label class="col-xs-12 control-label no-padding-right">CBM</label>
                                                <div class="col-xs-12">
                                                    <input type="number" id="subTotal" class="form-control" v-model="purchase.cbm" />
                                                </div>
                                            </div>
                                        </td>
                                    </tr> -->

									<tr>
                                        <td>
                                            <div class="form-group">
                                                <label class="col-xs-6 control-label no-padding-right"> Currency </label>
                                                <label class="col-xs-6 control-label no-padding-right"> Currency Rate </label>
												<div class="col-xs-6">
													<select class="form-control" v-model="purchase.currency_name">
														<option value="INR">INR</option>
														<option value="USD">USD</option>
														<option value="YUAN">YUAN</option>
													</select>
												</div>
												<div class="col-xs-6">
                                                    <input type="number" class="form-control" v-model="purchase.currency_rate" v-on:input="freightCalculate" />
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
									<tr>
                                        <td>
                                            <div class="form-group">
                                                <label class="col-xs-6 control-label no-padding-right">Qty</label>
                                                <label class="col-xs-6 control-label no-padding-right">Freight</label>
                                                <div class="col-xs-6">
                                                    <input type="number" class="form-control" v-model="purchase.freight_qty" v-on:input="freightCalculate" />
                                                </div>
                                                <div class="col-xs-6">
                                                    <input type="number" class="form-control" v-model="purchase.freight" readonly />
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

									<!-- <tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Total Exp</label>
												<div class="col-xs-12">
													<input type="number" min="0" step="any" id="total" class="form-control" v-model="purchase.freight" readonly />
												</div>
											</div>
										</td>
									</tr> -->

									<tr>
                                        <td>
                                            <div class="form-group">
                                                <label class="col-xs-12 control-label no-padding-right"> Paid Per. (%) </label>
                                                <div class="col-xs-12">
                                                    <input type="number" id="" class="form-control" v-model="purchase.paidPercentage" v-on:input="calculateTotal" autocomplete="off" />
                                                </div>
                                            </div>
                                        </td>
                                    </tr>


									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Total</label>
												<div class="col-xs-12">
													<input type="number" min="0" step="any" id="total" class="form-control" v-model="purchase.total" readonly />
												</div>
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="form-group text-right">
												<div class="col-xs-6" style="display: block;width: 50%;">
													<input type="submit" class="btn" value="Save" v-bind:disabled="purchaseOnProgress == true ? true : false" style="width:100%;background: green !important;border: 0;border-radius: 5px;outline:none;">
												</div>
												<div class="col-xs-6" style="display: block;width: 50%;">
													<input type="button" class="btn" onclick="window.location = '<?php echo base_url(); ?>lc_purchases'" value="New LC" style="width:100%;background: #2d1c5a !important;border: 0;border-radius: 5px;">
												</div>
											</div>
										</td>
									</tr>
								</table>
							</div>
						</form>
					</div>
				</div>
			</div>
		</fieldset>
	</div>

</div>



<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#purchase',
		data() {
			return {
				purchase: {
					purchaseId  : parseInt('<?php echo $purchaseId; ?>'),
					invoice     : '<?php echo $invoice; ?>',
					purchaseFor : '',
					empId       : '',
					cbm         : '',
					purchaseDate: moment().format('YYYY-MM-DD'),
					supplierId  : '',
					paidPercentage: 0.00,
					subTotal    : 0.00,
					currency_rate: 0.00,
					freight_qty : 0.00,
					freight     : 0.00,
					total       : 0.00,
					paid        : 0.00,
					due         : 0.00,
					note        : '',
					pi_no       : '',
					lc_no       : '',
				},
				vatPercent: 0.00,
				branches: [],
				selectedBranch: {
					branch_id: "<?php echo $this->session->userdata('BRANCHid'); ?>",
					Branch_name: "<?php echo $this->session->userdata('Branch_name'); ?>"
				},
				currency_name:'',
				currency_rate: 0,
				currency_value: 0,
				exp_amount: 0,
				expneses: [],
				selectedExpense: null,
				employees: [],
				selectedEmployee: null,
				suppliers: [],
				selectedSupplier: {
					Supplier_SlNo: "",
					Supplier_Code: '',
					Supplier_Name: 'Select Supplier',
					display_name: 'Select Supplier',
					Supplier_Mobile: '',
					Supplier_Address: '',
					Supplier_Type: ''
				},
				banks: [],
				selectedBank : null,
				products: [],
				selectedProduct: {
					Product_SlNo: '',
					Product_Code: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: '',
					PurchaseDetails_TotalQuantity: 0,
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0.00,
					Product_currency_Rate: 0,
                    perForeignAmount: 0,
                    totalForeignAmount: 0,
					total: ''
				},
				cart: [],
				purchases: [],
                selectedPurchase: null,
				expcart: [],
				purchaseOnProgress: false,
				keyPressed: '',
				units:[],
				selectedUnit: null,
				click: false,
				userType: '<?php echo $this->session->userdata("accountType") ?>'
			}
		},
		async created() {
			await this.getAccounts();
			await this.getSuppliers();
			this.getEmployees();
			this.getBranches();
			// this.getProducts();
			this.getExpneses();
			this.getPurchases();
			this.getUnits();

			if (this.purchase.purchaseId != 0) {
				await this.getPurchase();
			}
		},
		methods: {
			 getUnits() {
				axios.get('/get_units').then(res => {
					this.units = res.data;
				})
			},
			getBranches() {
				axios.get('/get_branches').then(res => {
					this.branches = res.data;
				})
			},
			async getAccounts() {
                axios.get('/get_loan_accounts')
				.then(res => {
					let accounts = res.data;
					this.banks = accounts.map(account => {
                    	account.display_name = `${account.account_name} - ${account.account_number} (${account.bank_name})`;
                    return account;
                })
				})
            },

			getPurchases() {
                // const excludedIds = this.lc_numbers.map(item => item.purchase_id);
                axios.get('/get_purchases').then(res => {
                    this.purchases = res.data.purchases.filter(p => p.status == 'p');
                    // const filteredPurchases = purchases.filter(
                    //     purchase => !excludedIds.includes(purchase.PurchaseMaster_SlNo)
                    // );
                    // this.purchases = filteredPurchases;
                })
            },

			productValueTotal() {
                let totalAmountValue = (parseFloat(this.selectedProduct.quantity) * parseFloat(this.selectedProduct.perForeignAmount)).toFixed(2);
                this.selectedProduct.totalForeignAmount = parseFloat(totalAmountValue).toFixed(2);
            },

			async onChangePurchase() {
                if(this.selectedPurchase != null) {

                    this.cart = [];					

                    if (this.selectedPurchase != null) {
                        await axios.post('/get_purchases', {
                            purchaseId: this.selectedPurchase.PurchaseMaster_SlNo
                        }).then(res => {
                            let r = res.data;
                            this.products = r.purchaseDetails;
                            this.products.map((p) => {
                                p.currencyName = 'USD';
                                p.display_text = p.Product_Name+ ' - '+ p.Product_Code;
                                return p;
                            });
                        })
                        this.calculateTotal();
                    }
                }
            },

			getExpneses() {
				axios.get('/get_lc_expanses').then(res => {
					this.expneses = res.data.map((item, index) => {
						item.sl = index + 1;
						return item;
					});
				})
			},

			async getSuppliers() {
				await axios.post('/get_suppliers', {
					forSearch: 'yes'
				}).then(res => {
					this.suppliers = res.data;
				})
			},
			 getEmployees() {
				axios.get('/get_employees').then(res => {
					this.employees = res.data;
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

			onChangeSupplier() {
				if (this.selectedSupplier == null) {
					this.selectedSupplier = {
						Supplier_SlNo: "",
						Supplier_Code: '',
						Supplier_Name: 'Select Supplier',
						display_name: 'Select Supplier',
						Supplier_Mobile: '',
						Supplier_Address: '',
						Supplier_Type: ''
					}
					return;
				}
				this.calculateTotal();
			},

			getProducts() {
				axios.post('/get_products', {
					isService: 'false',
					forSearch: 'yes'
				}).then(res => {
					this.products = res.data;
				})
			},
			
			// async onSearchProduct(val, loading) {
			// 	if (val.length > 2) {
			// 		loading(true);
			// 		await axios.post("/get_products", {
			// 				name: val,
			// 				isService: 'false'
			// 			})
			// 			.then(res => {
			// 				let r = res.data;
			// 				this.products = r.filter(item => item.status == 'a' && item.is_service == 'false');
			// 				loading(false)
			// 			})
			// 	} else {
			// 		loading(false)
			// 		await this.getProducts();
			// 	}
			// },

			freightCalculate() {
				this.purchase.freight = (parseFloat(this.purchase.currency_rate) * parseFloat(this.purchase.freight_qty)).toFixed(2);
				this.calculateTotal();
			},

			onChangeProduct() {
				if (this.selectedProduct == null) {
					this.selectedProduct = {
						Product_SlNo: '',
						Product_Code: '',
						display_text: 'Select Product',
						Product_Name: '',
						Unit_Name: '',
						quantity: '',
						Product_currency_Rate: 0,
						perForeignAmount: 0,
						totalForeignAmount: 0,
						Product_Purchase_Rate: '',
						Product_SellingPrice: 0.00,
						total: ''
					}
					return
				}
				if (this.selectedProduct.Product_SlNo == '') {
					return
				}
				this.selectedProduct.quantity = this.selectedProduct.PurchaseDetails_TotalQuantity
				this.$refs.quantity.focus();
			},

			productTotal() {
				let currencyProductPrice = (parseFloat(this.selectedProduct.totalForeignAmount) / parseFloat(this.selectedProduct.quantity)).toFixed(2);
                this.selectedProduct.Product_Purchase_Rate = (parseFloat(currencyProductPrice) * parseFloat(this.selectedProduct.Product_currency_Rate)).toFixed(2);
                this.selectedProduct.total = (parseFloat(this.selectedProduct.quantity) * parseFloat(this.selectedProduct.Product_Purchase_Rate)).toFixed(2);
			},

			currencyRateChange(product) {
                product.currencyProductPrice = (parseFloat(product.totalForeignAmount) / parseFloat(product.quantity)).toFixed(2);
                product.purchaseRate = (parseFloat(product.ProductCurrencyRate) * parseFloat(product.currencyProductPrice)).toFixed(2);
                product.total =(parseFloat(product.purchaseRate) * parseFloat(product.quantity)).toFixed(2);
            },
		
			addToCart() {
				if (this.selectedProduct == null) {
					Swal.fire({
						icon: "error",
						text: "Select Product",
					});
					return;
				}

				if (this.currency_name == '' || this.currency_name == '') {
					Swal.fire({
						icon: "error",
						text: "Select Currency",
					});
					return;
				}

				let product = {
					productId     : this.selectedProduct.Product_IDNo,
					productCode   : this.selectedProduct.Product_Code,
					name          : this.selectedProduct.Product_Name,
					categoryId    : this.selectedProduct.ProductCategory_ID,
					categoryName  : this.selectedProduct.ProductCategory_Name,
					totalForeignAmount: this.selectedProduct.totalForeignAmount,
                    ProductCurrencyRate: this.selectedProduct.Product_currency_Rate,
                    // currencyName: this.selectedProduct.currencyName,
                    currencyName  : this.currency_name,
					purchaseRate  : this.selectedProduct.Product_Purchase_Rate,
					salesRate     : this.selectedProduct.Product_SellingPrice,
					quantity      : this.selectedProduct.quantity,
					Unit_ID		  : this.selectedUnit.Unit_SlNo,
					Unitname	  : this.selectedUnit.Unit_Name,
					total         : this.selectedProduct.total
				}

				if (product.productId == '') {
					document.querySelector("#product [type='search']").focus();
					return;
				}
				if (product.quantity == 0 || product.quantity == '') {
					Swal.fire({
						icon: "error",
						text: "Enter quantity",
					});
					return;
				}

				if (product.purchaseRate == 0 || product.purchaseRate == '') {
					Swal.fire({
						icon: "error",
						text: "Enter purchase rate",
					});
					return;
				}
				if (product.totalForeignAmount == 0 || product.totalForeignAmount == '') {
					Swal.fire({
						icon: "error",
						text: "Enter total value",
					});
					return;
				}

				if (product.ProductCurrencyRate == 0 || product.ProductCurrencyRate == '') {
					Swal.fire({
						icon: "error",
						text: "Enter currency rate",
					});
					return;
				}

				if (product.Unit_ID == 0 || product.Unit_ID == '') {
					Swal.fire({
						icon: "error",
						text: "Select unit",
					});
					return;
				}

				let cartInd = this.cart.findIndex(p => p.productId == product.productId);
				if (cartInd > -1) {
					this.cart.splice(cartInd, 1);
				}

				this.cart.unshift(product);
				this.clearSelectedProduct();
				this.calculateTotal();
			},

			addToCartExp() {
				if (this.selectedExpense == null) {
					Swal.fire({
						icon: "error",
						text: "Select Expense",
					});
					return;
				}
				let cartInd = this.expcart.findIndex(p => p.expId == this.selectedExpense.id);
				if (cartInd > -1) {
					this.expcart.splice(cartInd, 1);
				}

				let expense = {
					expId: this.selectedExpense.id,
					name: this.selectedExpense.name,
					total: this.exp_amount
				}

				if (expense.expId == '') {
					document.querySelector("#product [type='search']").focus();
					return;
				}
				
				if (product.exp_amount == 0 || product.exp_amount == '') {
					Swal.fire({
						icon: "error",
						text: "Enter Amount",
					});
					return;
				}
				this.expcart.push(expense);
				this.calculateTotal();
			},

			quantityRateChange() {
				this.cart = this.cart.map(item => {
					item.purchaseRate = parseFloat(parseFloat(item.currency_rate) * parseFloat(item.currency_value)).toFixed(2);
					item.total = parseFloat(parseFloat(item.purchaseRate) * parseFloat(item.quantity)).toFixed(2);
					return item;
				})
				this.calculateTotal();
			},

			async removeFromCart(ind) {
				if (this.cart[ind].id) {
					let stock = await axios.post('/get_product_stock', {
						productId: this.cart[ind].productId
					}).then(res => res.data);
					if (this.cart[ind].quantity > stock) {
						Swal.fire({
							icon: "error",
							text: "Stock unavailable",
						});
						return;
					}
				}
				this.cart.splice(ind, 1);
				this.calculateTotal();
			},

			async removeFromExpCart(ind) {
				this.expcart.splice(ind, 1);
				this.calculateTotal();
			},

			clearSelectedProduct() {
				this.selectedProduct = {
					Product_SlNo: '',
					Product_Code: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: '',
					Product_currency_Rate: 0,
                    perForeignAmount: 0,
                    totalForeignAmount: 0,
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0.00,
					total: ''
				}
				this.currency_name = '';
				this.currency_value = 0;
				this.currency_rate = 0;
				this.selectedUnit = null;
			},

			calculateTotal() {
				this.purchase.subTotal = this.cart.reduce((prev, curr) => {
					return prev + parseFloat(curr.total);
				}, 0).toFixed(2);

				// this.purchase.freight = this.expcart.reduce((prev, curr) => {
				// 	return prev + parseFloat(curr.total);
				// }, 0).toFixed(2);


				// this.purchase.total = parseFloat(+productTotal + +materialTotal).toFixed(2)
				this.purchase.total = (parseFloat(this.purchase.subTotal) + parseFloat(this.purchase.freight)).toFixed(2);
                this.purchase.paid = (+this.purchase.total / 100) * +this.purchase.paidPercentage;
                this.purchase.due = this.purchase.total - this.purchase.paid;
			},

			saveLcData() {
				if (this.keyPressed == 'Enter' && !this.click) {
					this.click = true;
					return;
				}

				if (this.selectedBank == null) {
					Swal.fire({
						icon: "error",
						text: "Select Bank",
					});
					return;
				}

				if (this.selectedSupplier.Supplier_SlNo == null || this.selectedSupplier == null) {
					Swal.fire({
						icon: "error",
						text: "Select supplier",
					});
					return;
				}

				if (this.selectedPurchase == null) {
					Swal.fire({
						icon: "error",
						text: "Select purchase Invoice",
					});
					return;
				}

				if (this.purchase.purchaseDate == '') {
					Swal.fire({
						icon: "error",
						text: "Enter purchase date",
					});
					return;
				}

				if (this.cart.length == 0) {
					Swal.fire({
						icon: "error",
						text: "Cart is empty",
					});
					return;
				}

				this.purchase.purchaseFor = this.selectedBranch.branch_id;
				this.purchase.supplierId  = this.selectedSupplier.Supplier_SlNo;
				this.purchase.account_id = this.selectedBank.account_id;
				this.purchase.empId = this.selectedEmployee != null ? this.selectedEmployee.Employee_SlNo : null;
				this.purchase.PurchaseMaster_SlNo = this.selectedPurchase != null ? this.selectedPurchase.PurchaseMaster_SlNo : null;

				let data = {
					purchase: this.purchase,
					cartProducts: this.cart,
					// cartExp: this.expcart,
				}
			
				let url = '/add_lc_purchase';
				if (this.purchase.purchaseId != 0) {
					url = '/update_lc_purchase';
				}

				this.purchaseOnProgress = true;
				axios.post(url, data).then(async res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						let conf = confirm('Do you want to view invoice?');
						if (conf) {
							window.open(`/lc_purchase_invoice_print/${r.purchaseId}`, '_blank');
							await new Promise(r => setTimeout(r, 1000));
							window.location = '/lc_purchases';
						} else {
							window.location = '/lc_purchases';
						}
					} else {
						if (r.branch_status == false) {
							location.reload();
						}
						this.purchaseOnProgress = false;
					}
				})
			},
			async getPurchase() {
				await axios.post('/get_lc_purchases', {
					purchaseId: this.purchase.purchaseId
				}).then(res => {
					let r = res.data;
					let purchase = r.purchases[0];

					this.selectedSupplier.Supplier_SlNo    = purchase.Supplier_SlNo;
					this.selectedSupplier.Supplier_Code    = purchase.Supplier_Code;
					this.selectedSupplier.Supplier_Name    = purchase.Supplier_Name;
					this.selectedSupplier.Supplier_Mobile  = purchase.Supplier_Mobile;
					this.selectedSupplier.Supplier_Address = purchase.Supplier_Address;
					this.selectedSupplier.Supplier_Type    = purchase.supplierType;
					this.purchase.supplierType             = purchase.supplierType;

					this.purchase.invoice      = purchase.PurchaseMaster_InvoiceNo;
					this.purchase.purchaseFor  = purchase.PurchaseMaster_PurchaseFor;
					this.purchase.purchaseDate = purchase.PurchaseMaster_OrderDate;
					this.purchase.supplierId   = purchase.Supplier_SlNo;
					this.purchase.subTotal     = purchase.PurchaseMaster_SubTotalAmount;
					this.purchase.freight      = purchase.PurchaseMaster_Freight;
					this.purchase.currency_name= purchase.currency_name;
					this.purchase.currency_rate= purchase.currency_rate;
					this.purchase.freight_qty  = purchase.freight_qty;
					this.purchase.total        = purchase.PurchaseMaster_TotalAmount;
					this.purchase.cbm          = purchase.cbm;
					this.purchase.paidPercentage = purchase.paidPercentage;
					this.purchase.note         = purchase.PurchaseMaster_Description;

					this.purchase.lc_no      = purchase.lc_no;
					this.purchase.pi_no      = purchase.pi_no;
					this.purchase.account_id = purchase.account_id;
					this.purchase.empId      = purchase.Employee_SlNo;


					this.selectedBank = {
						account_id : purchase.account_id,
						display_name : purchase.bank_text,

					};
					
					this.selectedEmployee = {
						account_id  : purchase.empId,
						display_name: purchase.display_emp
					};

					this.selectedPurchase = {
						PurchaseMaster_SlNo: purchase.purchase_id,
						invoice_text: purchase.p_purchaseInvoice + '-' + purchase.p_supplierName
					};


					this.vatPercent = (this.purchase.vat * 100) / this.purchase.subTotal;

					setTimeout(() => {
						r.purchaseDetails.forEach(product => {
							let cartProduct = {
								productId     : product.Product_IDNo,
								name          : product.Product_Name,
								categoryId    : product.ProductCategory_ID,
								categoryName  : product.ProductCategory_Name,
								totalForeignAmount: product.currency_value,
								ProductCurrencyRate: product.currency_rate,
								currencyName  : product.currency_name,
								purchaseRate  : product.PurchaseDetails_Rate,
								salesRate     : product.Product_SellingPrice,
								quantity      : product.PurchaseDetails_TotalQuantity,
								Unit_ID		  : product.unit_id,
								Unitname	  : product.Unit_Name,
								total         : product.PurchaseDetails_TotalAmount
							}	
							this.cart.push(cartProduct);
						});
					}, 500);
					
					r.expDetails.forEach(expnense => {
						let expnenses = {
							id   : expnense.eid,
							expId: expnense.exp_id,
							name : expnense.name,
							total: expnense.amount
						}
						this.expcart.push(expnenses);
					});

					
					this.selectedSupplier = {
						Supplier_SlNo: purchase.Supplier_SlNo,
						Supplier_Code: purchase.Supplier_Code,
						Supplier_Name: purchase.Supplier_Name,
						display_name: purchase.Supplier_Name + '-' + purchase.Supplier_Code + '-' + purchase.Supplier_Mobile,
						Supplier_Mobile: purchase.Supplier_Mobile,
						Supplier_Address: purchase.Supplier_Address,
						Supplier_Type: purchase.supplierType
					};

				})
			}
		},

		mounted() {
			var projectThis = this;
			window.addEventListener('keydown', function(event) {
				projectThis.keyPressed = event.key;
			});
		},
	})
</script>