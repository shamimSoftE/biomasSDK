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

	.add-button:hover {
		color: white;
	}

	input[disabled=disabled] {
		color: #ffff !important;
	}
</style>

<div id="sales" class="row">
	<div class="col-xs-12 col-md-12 col-lg-12">
		<fieldset class="scheduler-border entryFrom">
			<div class="control-group">
				<div class="row">
					<div class="form-group">
						<label class="col-xs-4 col-md-1 control-label no-padding-right"> Invoice no </label>
						<div class="col-xs-8 col-md-2">
							<input type="text" id="invoiceNo" class="form-control" v-model="sales.invoiceNo" readonly />
						</div>
					</div>

					<div class="form-group">
						<label class="col-xs-4 col-md-1 control-label no-padding-right"> Sales By </label>
						<div class="col-xs-8 col-md-2">
							<v-select v-bind:options="employees" v-model="selectedEmployee" label="display_name" placeholder="Select Employee"></v-select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-xs-4 col-md-1 control-label no-padding-right"> Sales From </label>
						<div class="col-xs-8 col-md-2">
							<v-select id="branchDropdown" v-bind:options="branches" label="Branch_name" v-model="selectedBranch" disabled></v-select>
						</div>
					</div>

					<div class="form-group">
						<div class="col-xs-12 col-md-3">
							<input class="form-control" id="salesDate" type="date" v-model="sales.salesDate" v-bind:disabled="userType == 'u' ? true : false" />
						</div>
					</div>
				</div>
			</div>
		</fieldset>
	</div>


	<div class="col-xs-12 col-md-9">
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<fieldset class="scheduler-border" style="margin-bottom: 5px;padding: 0 4px 3px 0;">
					<legend class="scheduler-border">Customer Information</legend>
					<div class="control-group">
						<div class="form-group clearfix" style="margin-bottom: 3px;">
							<label class="col-xs-4 control-label no-padding-right" style="margin: 0;"> Sales Type </label>
							<div class="col-xs-8">
								<input type="radio" name="salesType" value="retail" v-model="sales.salesType" v-on:change="onSalesTypeChange"> Retail &nbsp;
								<input type="radio" name="salesType" value="wholesale" v-model="sales.salesType" v-on:change="onSalesTypeChange"> Wholesale
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right"> Customer </label>
							<div class="col-xs-8" style="display: flex;align-items:center;margin-bottom:5px;">
								<div style="width: 86%;">
									<v-select v-bind:options="customers" style="margin: 0;" label="display_name" v-model="selectedCustomer" @input="customerOnChange()" @search="onSearchCustomer"></v-select>
								</div>
								<div style="width: 13%;margin-left:2px;">
									<a href="<?= base_url('customer') ?>" class="add-button" target="_blank" title="Add New Customer"><i class="fa fa-plus" aria-hidden="true"></i></a>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right"> Name </label>
							<div class="col-xs-8">
								<input type="text" id="customerName" placeholder="Customer Name" class="form-control" v-model="selectedCustomer.Customer_Name" v-bind:disabled="selectedCustomer.Customer_Type == 'G' || selectedCustomer.Customer_Type == 'N' ? false : true" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right"> Mobile No </label>
							<div class="col-xs-8">
								<input type="text" id="mobileNo" placeholder="Mobile No" class="form-control" v-model="selectedCustomer.Customer_Mobile" v-bind:disabled="selectedCustomer.Customer_Type == 'G' || selectedCustomer.Customer_Type == 'N' ? false : true" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right"> Address </label>
							<div class="col-xs-8">
								<input type="text" id="address" placeholder="Address" class="form-control" v-model="selectedCustomer.Customer_Address" v-bind:disabled="selectedCustomer.Customer_Type == 'G' || selectedCustomer.Customer_Type == 'N' ? false : true" />
							</div>
						</div>
					</div>
				</fieldset>
			</div>

			<div class="col-xs-12 col-md-6 no-padding-left">
				<fieldset class="scheduler-border" style="margin-bottom: 5px;padding: 0 4px 3px 0;" :style="{background: selectedProduct.is_service == 'true' ? '#ffecc0 !important' : ''}">
					<legend class="scheduler-border"><span v-html="selectedProduct.is_service == 'true' ? 'Service' : 'Product'"></span> Information</legend>
					<div class="control-group">
						<form v-on:submit.prevent="addToCart">
							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> <span v-html="selectedProduct.is_service == 'true' ? 'Service' : 'Product'"></span> </label>
								<div class="col-xs-9" style="display: flex;align-items:center;margin-bottom:5px;">
									<div style="width: 86%;">
										<v-select v-bind:options="products" id="product" style="margin: 0;" v-model="selectedProduct" label="display_text" @input="productOnChange" @search="onSearchProduct"></v-select>
									</div>
									<div style="width: 13%;margin-left:2px;">
										<a href="<?= base_url('product') ?>" class="add-button" target="_blank" title="Add New Product"><i class="fa fa-plus" aria-hidden="true"></i></a>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> Sale Rate </label>
								<div class="col-xs-9">
									<input type="number" id="salesRate" placeholder="Rate" step="0.01" class="form-control" v-model="selectedProduct.Product_SellingPrice" v-on:input="productTotal" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> Quantity </label>
								<div class="col-xs-9">
									<input type="number" step="0.01" id="quantity" placeholder="Qty" class="form-control" ref="quantity" v-model="selectedProduct.quantity" v-on:input="productTotal" autocomplete="off" required />
								</div>
							</div>

							<div class="form-group" style="display:none;">
								<label class="col-xs-3 control-label no-padding-right"> Discount</label>
								<div class="col-xs-9">
									<span>(%)</span>
									<input type="text" id="productDiscount" placeholder="Discount" class="form-control" style="display: inline-block; width: 90%" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> Amount </label>
								<div class="col-xs-9">
									<input type="text" id="productTotal" placeholder="Amount" class="form-control" v-model="selectedProduct.total" readonly />
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> </label>
								<div class="col-xs-9">
									<button type="submit" class="btnCart pull-right">Add to Cart</button>
								</div>
							</div>
						</form>
					</div>
				</fieldset>
			</div>
		

			<div class="col-xs-12 col-md-12 no-padding-right">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr class="">
								<th style="width:6%;color:#000;">Sl</th>
								<th style="width:10%;color:#000;">Code</th>
								<th style="width:30%;color:#000;">Product/Service Name</th>
								<th style="width:15%;color:#000;">Category</th>
								<th style="width:7%;color:#000;">Qty</th>
								<th style="width:8%;color:#000;">Rate</th>
								<th style="width:15%;color:#000;">Total</th>
								<th style="width:10%;color:#000;">Action</th>
							</tr>
						</thead>
						<tbody style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
							<tr v-for="(product, sl) in cart" :style="{background: product.is_service == 'true' ? '#ffecc0' : ''}">
								<td>{{ sl + 1 }}</td>
								<td>{{ product.productCode }}</td>
								<td style="text-align: left;padding-left:3px;">{{ product.name }}</td>
								<td>{{ product.categoryName }}</td>
								<td>
									<input type="number" min="0" step="any" v-model="product.quantity" style="margin:0;padding: 0 5px; width: 70px; text-align: center;" @input="quantityRateChange" />
								</td>
								<td>
									<input type="number" min="0" step="any" v-model="product.salesRate" style="margin:0;padding: 0 5px; width: 120px; text-align: center;" @input="quantityRateChange" />
								</td>
								<td>{{ product.total }}</td>
								<td><a href="" v-on:click.prevent="removeFromCart(sl)"><i class="fa fa-trash"></i></a></td>
							</tr>

							<tr>
								<td colspan="8"></td>
							</tr>

							<tr style="font-weight: bold;">
								<td colspan="5">Note</td>
								<td colspan="3">Total</td>
							</tr>

							<tr>
								<td colspan="5"><textarea class="form-control" style="font-size:13px;margin-top:3px;" placeholder="Note" v-model="sales.note"></textarea></td>
								<td colspan="3" style="padding-top: 15px;font-size:18px;">{{ sales.total }}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


	<div class="col-xs-12 col-md-3">
		<fieldset class="scheduler-border" style="margin-bottom: 5px;padding-bottom: 5px">
			<legend class="scheduler-border">Amount Details</legend>
			<div class="control-group">
				<div class="row">
					<div class="col-xs-12">
						<form @submit.prevent="saveSales">
							<div class="table-responsive">
								<table style="color:#000;margin-bottom: 0px;border-collapse: collapse;">
									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Sub Total</label>
												<div class="col-xs-12">
													<input type="number" min="0" step="any" id="subTotal" class="form-control" v-model="sales.subTotal" readonly />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Discount</label>

												<div class="col-xs-4">
													<input type="number" min="0" step="any" id="discountPercent" class="form-control" v-model="discountPercent" v-on:input="calculateTotal" />
												</div>

												<label class="col-xs-1 control-label no-padding-right">%</label>

												<div class="col-xs-7">
													<input type="number" min="0" step="any" id="discount" class="form-control" v-model="sales.discount" v-on:input="calculateTotal" />
												</div>

											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Vat</label>

												<div class="col-xs-4">
													<input type="number" min="0" step="any" id="vatPercent" class="form-control" v-model="vatPercent" v-on:input="calculateTotal" />
												</div>

												<label class="col-xs-1 control-label no-padding-right">%</label>

												<div class="col-xs-7">
													<input type="number" min="0" step="any" id="vat" class="form-control" :readonly="sales.vatPercent > 0 ? false : true" v-model="sales.vat" v-on:input="calculateTotal" />
												</div>

											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Transport Cost</label>
												<div class="col-xs-12">
													<input type="number" min="0" step="any" id="transportCost" class="form-control" v-model="sales.transportCost" v-on:input="calculateTotal" />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Total</label>
												<div class="col-xs-12">
													<input type="number" id="total" class="form-control" v-model="sales.total" readonly />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Payment Type</label>
												<div class="col-xs-12">
													<select name="" id="" class="form-control" v-model="sales.payment_type" @change="calculateTotal">
														<option value="Cash">Cash</option>
														<option value="Bank">Bank</option>
													</select>
												</div>
											</div>
										</td>
									</tr>

									<tr style="display:none" v-bind:style="{display: sales.payment_type == 'Bank' ? '' : 'none'}">
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Payment Type</label>
												<div class="col-xs-12">
												<v-select v-bind:options="filteredAccounts" v-model="selectedAccount" label="display_text" placeholder="Select Account"></v-select>
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-6 control-label no-padding-right" style="margin:0;">Cash</label>
												<label class="col-xs-6 control-label no-padding-right" style="margin:0;">Bank</label>
												<div class="col-xs-6">
													<input type="number" min="0" step="any" id="cash_paid" class="form-control" v-model="sales.cash_paid" v-on:input="calculateTotal" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? true : false" />
												</div>
												<div class="col-xs-6">
													<input type="number" min="0" step="any" id="bank_paid" class="form-control" v-model="sales.bank_paid" v-on:input="calculateTotal" v-bind:disabled="sales.payment_type == 'Cash' ? true : false" />
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Paid</label>
												<div class="col-xs-12">
													<input type="number" min="0" step="any" id="paid" class="form-control" v-model="sales.paid" v-on:input="calculateTotal" disabled />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label" style="margin:0;">Due</label>
												<div class="col-xs-6">
													<input type="number" min="0" step="any" id="due" class="form-control" v-model="sales.due" readonly />
												</div>
												<div class="col-xs-6">
													<input type="number" min="0" step="any" id="previousDue" class="form-control" v-model="sales.previousDue" readonly style="color:red;" />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<div class="col-xs-6 col-md-6" style="display: block;width: 50%;">
													<input type="submit" class="btn btn-sm" value="Sale" style="width:100%;background: green !important;border: 0;border-radius: 5px;outline:none;" v-bind:disabled="saleOnProgress ? true : false" />
												</div>
												<div class="col-xs-6 col-md-6" style="display: block;width: 50%;">
													<a class="btn btn-sm" v-bind:href="`/sales`" style="background: #2d1c5a !important;border: 0;width: 100%;display: flex; justify-content: center;border-radius: 5px;">New Sale</a>
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
		el: '#sales',
		data() {
			return {
				sales: {
					salesId      : parseInt('<?php echo $salesId; ?>'),
					invoiceNo    : '<?php echo $invoice; ?>',
					salesBy      : '<?php echo $this->session->userdata("FullName"); ?>',
					salesType    : 'retail',
					salesFrom    : '',
					salesDate    : '',
					customerId   : '',
					accountId    : '',
					employeeId   : null,
					subTotal     : 0.00,
					discount     : 0.00,
					vatPercent   : 0.00,
					vat          : 0.00,
					payment_type : 'Cash',
					sale_type    : 'Order',
					transportCost: 0.00,
					total        : 0.00,
					cash_paid    : 0.00,
					bank_paid    : 0.00,
					paid         : 0.00,
					previousDue  : 0.00,
					status       : 'p',
					due          : 0.00,
					note         : '',
				},
				vatPercent: 0,
				discountPercent: 0,
				cart: [],
				employees: [],
				selectedEmployee: null,
				branches: [],
				selectedBranch: {
					branch_id: "<?php echo $this->session->userdata('BRANCHid'); ?>",
					Branch_name: "<?php echo $this->session->userdata('Branch_name'); ?>"
				},

				accounts: [],
				selectedAccount: null,

				customers: [],
				selectedCustomer: {
					Customer_SlNo: '',
					Customer_Code: '',
					Customer_Name: 'Cash Customer',
					display_name: 'Cash Customer',
					Customer_Mobile: '',
					Customer_Address: '',
					Customer_Type: 'G'
				},
				oldCustomerId: null,
				oldPreviousDue: 0,
				products: [],
				selectedProduct: {
					Product_SlNo: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: 0,
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0.00,
					vat: 0.00,
					total: 0.00
				},
				productPurchaseRate: '',
				productStockText: '',
				productStock: '',
				saleOnProgress: false,
				sales_due_on_update: 0,
				keyPressed: '',
				click: false,
				userType: '<?php echo $this->session->userdata("accountType"); ?>'
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
		async created() {
			this.sales.salesDate = moment().format('YYYY-MM-DD');
			await this.getEmployees();
			await this.getBranches();
			await this.getCustomers();
			await this.getAccounts();
			this.getProducts();

			if (this.sales.salesId != 0) {
				await this.getSales();
			}
		},
		
		methods: {
			getAccounts() {
                axios.get('/get_bank_accounts')
                    .then(res => {
                        this.accounts = res.data;
                    })
            },
			getEmployees() {
				axios.get('/get_employees').then(res => {
					this.employees = res.data.map(item => {
						item.display_name = `${item.Employee_Name} - ${item.Employee_ID}`;
						return item;
					});
				})
			},
			getBranches() {
				axios.get('/get_branches').then(res => {
					this.branches = res.data;
				})
			},
			async getCustomers() {
				await axios.post('/get_customers', {
					customerType: this.sales.salesType,
					forSearch: 'yes'
				}).then(res => {
					this.customers = res.data;
					this.customers.unshift({
						Customer_SlNo: '',
						Customer_Code: '',
						Customer_Name: 'Cash Customer',
						display_name: 'Cash Customer',
						Customer_Mobile: '',
						Customer_Address: '',
						Customer_Type: 'G'
					}, {
						Customer_SlNo: '',
						Customer_Code: '',
						Customer_Name: '',
						display_name: 'New Customer',
						Customer_Mobile: '',
						Customer_Address: '',
						Customer_Type: 'N'
					})
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
			getProducts() {
				axios.post('/get_products', {
					forSearch: 'yes'
				}).then(res => {
					if (this.sales.salesType == 'wholesale') {
						this.products = res.data.filter((product) => product.Product_WholesaleRate > 0);
						this.products.map((product) => {
							return product.Product_SellingPrice = product.Product_WholesaleRate;
						})
					} else {
						this.products = res.data;
					}
				})
			},
			async onSearchProduct(val, loading) {
				if (val.length > 2) {
					loading(true);
					await axios.post("/get_products", {
							name: val,
						})
						.then(res => {
							let r = res.data;
							this.products = r.filter(item => item.status == 'a');
							loading(false)
						})
				} else {
					loading(false)
					await this.getProducts();
				}
			},
			productTotal() {
				this.selectedProduct.total = (parseFloat(this.selectedProduct.quantity) * parseFloat(this.selectedProduct.Product_SellingPrice)).toFixed(2);
			},
			onSalesTypeChange() {
				this.selectedCustomer = {
					Customer_SlNo: '',
					Customer_Code: '',
					Customer_Name: 'Cash Customer',
					display_name: 'Cash Customer',
					Customer_Mobile: '',
					Customer_Address: '',
					Customer_Type: 'G'
				}
				this.getCustomers();

				this.clearProduct();
				this.getProducts();
			},
			async customerOnChange() {
				this.sales.previousDue = 0;
			

				if (this.selectedCustomer == null) {
					this.selectedCustomer = {
						Customer_SlNo: '',
						Customer_Code: '',
						Customer_Name: 'Cash Customer',
						display_name: 'Cash Customer',
						Customer_Mobile: '',
						Customer_Address: '',
						Customer_Type: 'G'
					}
					return
				}
				if (this.selectedCustomer.Customer_SlNo == '' &&  this.selectedCustomer.Customer_Type != "G") {
					return;
				}

				if (this.sales.salesId != 0 && this.oldCustomerId != parseInt(this.selectedCustomer.Customer_SlNo)) {
					let changeConfirm = confirm('Changing customer will set previous due to current due amount. Do you really want to change customer?');
					if (changeConfirm == false) {
						return;
					}
				} else if (this.sales.salesId != 0 && this.oldCustomerId == parseInt(this.selectedCustomer.Customer_SlNo)) {
					this.sales.previousDue = this.oldPreviousDue;
					return;
				}

				await this.getCustomerDue();

				this.sales.payment_type = "Cash";
				this.selectedAccount = null;

				this.calculateTotal();
			},
			async getCustomerDue() {
				await axios.post('/get_customer_due', {
					customerId: this.selectedCustomer.Customer_SlNo
				}).then(res => {
					if (res.data.length > 0) {
						this.sales.previousDue = res.data[0].dueAmount;
					} else {
						this.sales.previousDue = 0;
					}
				})
			},
			async productOnChange() {
				if (this.selectedProduct == null) {
					this.selectedProduct = {
						Product_SlNo: '',
						display_text: 'Select Product',
						Product_Name: '',
						Unit_Name: '',
						quantity: 0,
						Product_Purchase_Rate: '',
						Product_SellingPrice: 0.00,
						vat: 0.00,
						total: 0.00
					}
					return
				}
				if ((this.selectedProduct.Product_SlNo != '' || this.selectedProduct.Product_SlNo != 0)) {
					if (this.selectedProduct.is_service == 'false') {
						this.productStock = await axios.post('/get_product_stock', {
							productId: this.selectedProduct.Product_SlNo
						}).then(res => {
							return res.data;
						})

						this.productStockText = this.productStock > 0 ? "Available Stock" : "Stock Unavailable";
					}
					this.$refs.quantity.focus();
				}

			},
			toggleProductPurchaseRate() {
				this.$refs.productPurchaseRate.type = this.$refs.productPurchaseRate.type == 'text' ? 'password' : 'text';
			},
			addToCart() {
				let product = {
					productId: this.selectedProduct.Product_SlNo,
					productCode: this.selectedProduct.Product_Code,
					categoryName: this.selectedProduct.ProductCategory_Name,
					name: this.selectedProduct.Product_Name,
					salesRate: this.selectedProduct.Product_SellingPrice,
					vat: this.selectedProduct.vat,
					is_service: this.selectedProduct.is_service,
					quantity: this.selectedProduct.quantity,
					total: this.selectedProduct.total,
					purchaseRate: this.selectedProduct.Product_Purchase_Rate
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

				if (product.salesRate == 0 || product.salesRate == '') {
					Swal.fire({
						icon: "error",
						text: "Enter sales rate",
					});
					return;
				}
				

				let cartInd = this.cart.findIndex(p => p.productId == product.productId);
				if (cartInd > -1) {
					this.cart.splice(cartInd, 1);
				}

				this.cart.push(product);
				this.clearProduct();
				this.calculateTotal();
			},
			quantityRateChange() {
				this.cart = this.cart.map(item => {
					item.total = parseFloat(parseFloat(item.salesRate) * parseFloat(item.quantity)).toFixed(2);
					return item;
				})
				this.calculateTotal();
			},
			removeFromCart(ind) {
				this.cart.splice(ind, 1);
				this.calculateTotal();
			},
			clearProduct() {
				this.selectedProduct = {
					Product_SlNo: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: 0,
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0.00,
					vat: 0.00,
					total: 0.00
				}
				this.productStock = '';
				this.productStockText = '';
			},
			calculateTotal() {
				this.sales.subTotal = this.cart.reduce((prev, curr) => {
					return prev + parseFloat(curr.total)
				}, 0).toFixed(2);
				if (event.target.id != 'transportCost') {
					this.sales.vat = this.cart.reduce((prev, curr) => {
						return +prev + +(curr.total * (curr.vat / 100))
					}, 0);
				}
				if (event.target.id == 'discountPercent') {
					this.sales.discount = ((parseFloat(this.sales.subTotal) * parseFloat(this.discountPercent)) / 100).toFixed(2);
				} else {
					this.discountPercent = (parseFloat(this.sales.discount) / parseFloat(this.sales.subTotal) * 100).toFixed(2);
				}
				this.sales.total = (parseFloat(this.sales.subTotal) - parseFloat(this.sales.discount)).toFixed(2);
				if (event.target.id == 'vatPercent') {
					this.sales.vat = ((parseFloat(this.sales.total) * parseFloat(this.vatPercent)) / 100).toFixed(2);
				} else if (event.target.id == 'vat') {
					this.vatPercent = (parseFloat(this.sales.vat) / parseFloat(this.sales.total) * 100).toFixed(2);
				} else {
					this.sales.vat = ((parseFloat(this.sales.total) * parseFloat(this.vatPercent)) / 100).toFixed(2);
				}
				this.sales.total = parseFloat(parseFloat(this.sales.vat) + parseFloat(this.sales.total) + parseFloat(this.sales.transportCost)).toFixed(2);
				
				let due = 0.00;
				let bank = 0.00;
				if(this.selectedCustomer.Customer_Type === 'G'){
					
					if(event.target.id == 'cash_paid'){
						this.sales.bank_paid =(parseFloat(this.sales.paid) - parseFloat(this.sales.cash_paid)).toFixed(2);
						this.sales.paid = (parseFloat(this.sales.cash_paid)+parseFloat(this.sales.bank_paid)).toFixed(2);
						return;
					}else if(event.target.id == 'bank_paid'){
						this.sales.cash_paid =(parseFloat(this.sales.total) - parseFloat(this.sales.bank_paid)).toFixed(2);
						this.sales.paid = (parseFloat(this.sales.cash_paid)+parseFloat(this.sales.bank_paid)).toFixed(2);
						return;
					}
						this.sales.bank_paid = +bank;
						this.sales.cash_paid = this.sales.total;
						this.sales.paid = this.sales.total;

						console.log(this.sales.paid);
						console.log(this.sales.bank_paid);
						console.log(this.sales.cash_paid );
						this.sales.due = +due;
					return;
				}

				

				if (event.target.id != 'cash_paid' && event.target.id != 'bank_paid') {
					this.sales.cash_paid = this.sales.total;
					this.sales.paid = (parseFloat(this.sales.total) + parseFloat(this.sales.bank_paid)).toFixed(2);
					this.sales.due = +due;
				} else {
						this.sales.paid = (parseFloat(this.sales.cash_paid)+parseFloat(this.sales.bank_paid)).toFixed(2);
						due = (parseFloat(this.sales.total) - (parseFloat(this.sales.cash_paid)+parseFloat(this.sales.bank_paid))).toFixed(2);
						this.sales.due = +due;
					
				}
				
			},
			async saveSales() {
				if (this.keyPressed == 'Enter' && !this.click) {
					this.click = true;
					return;
				}
				if (this.selectedCustomer == null) {
					Swal.fire({
						icon: "error",
						text: "Select Customer",
					});
					return;
				}
				if(this.sales.payment_type == 'Bank'){
					if(this.selectedAccount == null){
						Swal.fire({
						icon: "error",
						text: "Select Bank",
						});
						return;
					}

					this.sales.accountId = this.selectedAccount.account_id;
				}else{
					this.sales.accountId = '';

				}



				if (this.cart.length == 0) {
					Swal.fire({
						icon: "error",
						text: "Cart is empty",
					});
					return;
				}

				let url = "/add_orders";
				if (this.sales.salesId != 0) {
					url = "/update_orders";
				}
				if (parseFloat(this.selectedCustomer.Customer_Credit_Limit) < (parseFloat(this.sales.due) + parseFloat(this.sales.previousDue))) {
					Swal.fire({
						icon: "error",
						text: `Customer credit limit (${this.selectedCustomer.Customer_Credit_Limit}) exceeded`,
					});
					return;
				}

				if (this.selectedEmployee != null && this.selectedEmployee.Employee_SlNo != null) {
					this.sales.employeeId = this.selectedEmployee.Employee_SlNo;
				} else {
					this.sales.employeeId = null;
				}
				this.sales.salesFrom = this.selectedBranch.branch_id;

				let data = {
					sales: this.sales,
					cart: this.cart,
					customer: this.selectedCustomer
				}
				this.saleOnProgress = true;
				axios.post(url, data).then(async res => {
					let r = res.data;
					if (r.success) {
						let conf = confirm(`${r.message}, Do you want to view invoice?`);
						if (conf) {
							window.open('/order_invoice_print/' + r.salesId, '_blank');
							await new Promise(r => setTimeout(r, 1000));
							window.location = '/orders';
						} else {
							window.location = '/orders';
						}
					} else {
						alert(r.message);
						this.saleOnProgress = false;
						if (r.branch_status == false) {
							location.reload();
						}
					}
				})
			},
			async getSales() {
				await axios.post('/get_sales', {
					salesId: this.sales.salesId,sale_type:'order',status:'p'
				}).then(res => {
					let r                        = res.data;
					let sales                    = r.sales[0];
					    this.sales.salesBy       = sales.AddBy;
					    this.sales.salesFrom     = sales.branch_id;
					    this.sales.salesDate     = sales.SaleMaster_SaleDate;
					    this.sales.salesType     = sales.SaleMaster_SaleType;
					    this.sales.customerId    = sales.SalseCustomer_IDNo;
					    this.sales.employeeId    = sales.Employee_SlNo;
					    this.sales.subTotal      = sales.SaleMaster_SubTotalAmount;
					    this.sales.discount      = sales.SaleMaster_TotalDiscountAmount;
					    this.sales.vat           = sales.SaleMaster_TaxAmount;
					    this.sales.transportCost = sales.SaleMaster_Freight;
					    this.sales.total         = sales.SaleMaster_TotalSaleAmount;
					    this.sales.paid          = sales.SaleMaster_PaidAmount;
					    this.sales.previousDue   = sales.SaleMaster_Previous_Due;
					    this.sales.due           = sales.SaleMaster_DueAmount;
					    this.sales.note          = sales.SaleMaster_Description;
						this.sales.accountId = sales.account_id;
					
					this.sales.cash_paid    = sales.cash_paid;
					this.sales.bank_paid    = sales.bank_paid;
					this.sales.payment_type = sales.payment_type;
					this.sales.status       = sales.status;
					this.sales.sale_type    = sales.sale_type;

					this.oldCustomerId = sales.SalseCustomer_IDNo;
					this.oldPreviousDue = sales.SaleMaster_Previous_Due;
					this.sales_due_on_update = sales.SaleMaster_DueAmount;

					this.vatPercent = parseFloat(this.sales.vat) * 100 / parseFloat(this.sales.subTotal);
					this.discountPercent = parseFloat(this.sales.discount) * 100 / parseFloat(this.sales.subTotal);

					if(sales.payment_type == "Bank"){
						this.selectedAccount = {
							account_id: sales.account_id,
							display_text: `${sales.account_name} - ${sales.account_number} (${sales.bank_name})`
						}
					}else{
						this.selectedAccount = null;
					}

					this.selectedCustomer = {
						Customer_SlNo: sales.SalseCustomer_IDNo == null ? '' : sales.SalseCustomer_IDNo,
						Customer_Code: sales.Customer_Code,
						Customer_Name: sales.Customer_Name,
						display_name: sales.customerType == 'G' ? 'Cash Customer' : `${sales.Customer_Code} - ${sales.Customer_Name}`,
						Customer_Mobile: sales.Customer_Mobile,
						Customer_Address: sales.Customer_Address,
						Customer_Type: sales.customerType
					}

					r.saleDetails.forEach(product => {
						let cartProduct = {
							productCode: product.Product_Code,
							productId: product.Product_IDNo,
							categoryName: product.ProductCategory_Name,
							name: product.Product_Name,
							salesRate: product.SaleDetails_Rate,
							vat: product.SaleDetails_Tax,
							is_service: product.is_service,
							quantity: product.SaleDetails_TotalQuantity,
							total: product.SaleDetails_TotalAmount,
							purchaseRate: product.Purchase_Rate,
						}

						this.cart.push(cartProduct);
					})
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