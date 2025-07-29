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
						<label class="col-xs-4 col-md-2 control-label no-padding-right"> Purchase For </label>
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

	<div class="col-xs-12 col-md-9">
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<fieldset class="scheduler-border">
					<legend class="scheduler-border">Supplier Information</legend>
					<div class="control-group">
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

			<div class="col-xs-12 col-md-5 no-padding-left">
				<fieldset class="scheduler-border">
					<legend class="scheduler-border">Product Information</legend>
					<div class="control-group">
						<form v-on:submit.prevent="addToCart">
							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right"> Product </label>
								<div class="col-xs-8" style="display: flex;align-items:center;margin-bottom:5px;">
									<div style="width: 86%;">
										<v-select v-bind:options="products" id="product" style="margin: 0;" v-model="selectedProduct" label="display_text" v-on:input="onChangeProduct" @search="onSearchProduct"></v-select>
									</div>
									<div style="width: 13%;margin-left:2px;">
										<a href="<?= base_url('product') ?>" class="add-button" target="_blank" title="Add New Product"><i class="fa fa-plus" aria-hidden="true"></i></a>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right"> Pur. Rate </label>
								<div class="col-xs-4">
									<input type="number" step="any" id="purchaseRate" name="purchaseRate" class="form-control" placeholder="Pur. Rate" v-model="selectedProduct.Product_Purchase_Rate" v-on:input="productTotal" autocomplete="off" />
								</div>

								<label class="col-xs-1 control-label no-padding-right"> Qty </label>
								<div class="col-xs-3">
									<input type="number" step="0.01" id="quantity" name="quantity" class="form-control" placeholder="Quantity" ref="quantity" v-model="selectedProduct.quantity" v-on:input="productTotal" autocomplete="off" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right"> Discount</label>
								<div class="col-xs-3">
									<input type="number" min="0" step="any" id="productDiscountPercent" placeholder="Discount" v-on:input="productTotal" class="form-control" v-model="productDiscountPercent" style="display: inline-block;" />
								</div>
								<div class="col-xs-1">
									<span>(%)</span>
								</div>
								<div class="col-xs-4">
									<input type="number" min="0" step="any" id="productDiscount" placeholder="Discount" class="form-control" v-on:input="productTotal" v-model="productDiscountAmount" style="display: inline-block;" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right"> Total Amount </label>
								<div class="col-xs-8">
									<input type="text" id="productTotal" name="productTotal" class="form-control" readonly v-model="selectedProduct.total" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right"> Selling Price </label>
								<div class="col-xs-8">
									<input type="text" id="sellingPrice" name="sellingPrice" class="form-control" v-model="selectedProduct.Product_SellingPrice" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right"> Note </label>
								<div class="col-xs-8">
									<input type="text" class="form-control" v-model="productNote" />
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

			<div class="col-xs-12 col-md-1 no-padding" style="height: 164px;background: #93d2f5;border: 1px solid gray;margin-top: 10px;border-radius: 5px;">
				<div style="display:none;" v-bind:style="{display:selectedProduct.is_service == 'true' ? 'none' : ''}">
					<div style="height: 169px;display:flex;flex-direction:column;justify-content:center;">
						<div class="text-center" 
							style="display:none;font-size: 10px;line-height: 1;margin-bottom: 3px;"
							v-bind:style="{color: productStock > 0 ? 'green' : 'red', display: selectedProduct.Product_SlNo == '' ? 'none' : ''}">
							{{ productStockText }}
						</div class="text-center">
						<input type="text" id="productStock" v-model="productStock" readonly style="border:none;font-size:13px;width:100%;text-align:center;color:green"><br>
						<input type="text" id="stockUnit" v-model="selectedProduct.Unit_Name" readonly style="border:none;font-size:12px;width:100%;text-align: center;margin-bottom:2px;"><br>
						<input type="password" ref="productPurchaseRate" v-model="selectedProduct.Product_Purchase_Rate" v-on:mousedown="toggleProductPurchaseRate" v-on:mouseup="toggleProductPurchaseRate" readonly title="Purchase rate (click & hold)" style="font-size:12px;width:100%;text-align: center;">
					</div>
				</div>
			</div>

		</div>
		<div class="col-xs-12 col-md-12" style="padding-left: 0px;padding-right: 0px;">
			<div class="table-responsive">
				<table class="table table-bordered table-hover" style="color:#000;margin-bottom: 5px;">
					<thead>
						<tr>
							<th style="width:4%;color:#000;">SL</th>
							<th style="width:30%;color:#000;">Product Name</th>
							<th style="width:15%;color:#000;">Note</th>
							<th style="width:13%;color:#000;">Category</th>	
							<th style="width:5%;color:#000;">Quantity</th>
							<th style="width:5%;color:#000;">Unit</th>
							<th style="width:8%;color:#000;">Rate</th>
							<th style="width:8%;color:#000;">Dis(%)</th>
							<th style="width:8%;color:#000;">Dis(tk)</th>
							<th style="width:13%;color:#000;">Total</th>
							<th style="width:5%;color:#000;">Action</th>
						</tr>
					</thead>
					<tbody style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
						<tr v-for="(product, sl) in cart">
							<td>{{ sl + 1}}</td>
							<td style="text-align: left;padding-left:3px;">{{ product.name }}</td>
							<td style="text-align: left;padding-left:3px;">{{ product.note }}</td>
							<td>{{ product.categoryName }}</td>
							<td>
								<input type="number" min="0" step="any" v-model="product.quantity" style="margin:0;padding: 0 5px; width: 70px; text-align: center;" @input="quantityRateChange" />
							</td>
							<td>{{ product.unit }}</td>
							<td>
								<input type="number" min="0" step="any" v-model="product.purchaseRate" style="margin:0;padding: 0 5px; width: 120px; text-align: center;" @input="quantityRateChange" />
							</td>
							<td>{{ product.discountPercent }}</td>
							<td>{{ product.discountAmount }}</td>
							<td>{{ product.total }}</td>
							<td><a href="" v-on:click.prevent="removeFromCart(sl)"><i class="fa fa-trash"></i></a></td>
						</tr>

						<tr>
							<td colspan="11"></td>
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

	<div class="col-xs-12 col-md-3">
		<fieldset class="scheduler-border">
			<legend class="scheduler-border">Amount Details</legend>
			<div class="control-group">
				<div class="row">
					<div class="col-xs-12">
						<form @submit.prevent="savePurchase">
							<div class="table-responsive">
								<table style="color:#000;margin-bottom: 0px;">
									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Sub Total</label>
												<div class="col-xs-12">
													<input type="number" min="0" step="any" id="subTotal" name="subTotal" class="form-control" v-model="purchase.subTotal" readonly />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Discount</label>
												<div class="col-xs-12">
													<input type="number" min="0" step="any" id="discount" name="discount" class="form-control" v-model="purchase.discount" v-on:input="calculateTotal" />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin: 0;"> Vat </label>
												<div class="col-xs-4 no-padding-right">
													<input type="number" min="0" step="any" class="form-control" id="vatPercent" name="vatPercent" v-model="vatPercent" v-on:input="calculateTotal" />
												</div>
												<label class="col-xs-1"> % </label>
												<div class="col-xs-6 no-padding-right">
													<input type="number" min="0" step="any" class="form-control" id="vat" name="vat" v-model="purchase.vat" readonly />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Transport / Labour Cost</label>
												<div class="col-xs-12">
													<input type="number" min="0" step="any" id="freight" name="freight" class="form-control" v-model="purchase.freight" v-on:input="calculateTotal" />
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
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right" style="margin:0;">Paid</label>
												<div class="col-xs-12">
													<input type="number" min="0" step="any" id="paid" class="form-control" v-model="purchase.paid" v-on:input="calculateTotal" v-bind:disabled="selectedSupplier.Supplier_Type == 'G' ? true : false" />
												</div>
											</div>
										</td>
									</tr>

									<tr v-if="purchase.purchaseId == 0">
										<td>
											<div class="form-group">
												<div class="col-xs-2">
													<input type="checkbox" id="lc_purchase" v-model="purchase.is_lc_purchase">
												</div>
												<label class="control-label no-padding-right col-xs-7" for="lc_purchase">Is LC Purchase</label>
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-6 control-label no-padding-right" style="margin:0;">Due</label>
												<label class="col-xs-6 control-label no-padding-right" style="margin:0;">Prev. Due</label>
											</div>
										</td>
									</tr>

									
									<tr>
										<td>
											<div class="form-group">
												<div class="col-xs-6">
													<input type="number" min="0" step="any" id="due" name="due" class="form-control" v-model="purchase.due" readonly />
												</div>
												<div class="col-xs-6">
													<input type="number" min="0" step="any" id="previousDue" name="previousDue" class="form-control" v-model="purchase.previousDue" readonly style="color:red;" />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group text-right">
												<div class="col-xs-6" style="display: block;width: 50%;">
													<input type="submit" class="btn" value="Purchase" v-bind:disabled="purchaseOnProgress == true ? true : false" style="width:100%;background: green !important;border: 0;border-radius: 5px;outline:none;">
												</div>
												<div class="col-xs-6" style="display: block;width: 50%;">
													<input type="button" class="btn" onclick="window.location = '<?php echo base_url(); ?>purchase'" value="New Purch.." style="width:100%;background: #2d1c5a !important;border: 0;border-radius: 5px;">
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
					purchaseId: parseInt('<?php echo $purchaseId; ?>'),
					invoice: '<?php echo $invoice; ?>',
					purchaseFor: '',
					purchaseDate: moment().format('YYYY-MM-DD'),
					supplierId: '',
					subTotal: 0.00,
					vat: 0.00,
					discount: 0.00,
					freight: 0.00,
					total: 0.00,
					paid: 0.00,
					due: 0.00,
					previousDue: 0.00,
					note: '',
					is_lc_purchase:false,
				},
				vatPercent: 0.00,
				branches: [],
				selectedBranch: {
					branch_id: "<?php echo $this->session->userdata('BRANCHid'); ?>",
					Branch_name: "<?php echo $this->session->userdata('Branch_name'); ?>"
				},
				suppliers: [],
				selectedSupplier: {
					Supplier_SlNo: "",
					Supplier_Code: '',
					Supplier_Name: 'Cash Supplier',
					display_name: 'Cash Supplier',
					Supplier_Mobile: '',
					Supplier_Address: '',
					Supplier_Type: 'G'
				},
				oldPreviousDue: 0,
				products: [],
				selectedProduct: {
					Product_SlNo: '',
					Product_Code: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: '',
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0.00,
					total: ''
				},
				productDiscountPercent: 0,
				productDiscountAmount: 0,
				productNote: '',
				cart: [],
				productStock: '',
				productStockText: '',
				purchaseOnProgress: false,
				keyPressed: '',
				click: false,
				userType: '<?php echo $this->session->userdata("accountType") ?>'
			}
		},
		async created() {
			await this.getSuppliers();
			this.getBranches();
			this.getProducts();

			if (this.purchase.purchaseId != 0) {
				await this.getPurchase();
			}
		},
		methods: {
			getBranches() {
				axios.get('/get_branches').then(res => {
					this.branches = res.data;
				})
			},
			toggleProductPurchaseRate() {
				this.$refs.productPurchaseRate.type = this.$refs.productPurchaseRate.type == 'text' ? 'password' : 'text';
			},
			async getSuppliers() {
				await axios.post('/get_suppliers', {
					forSearch: 'yes'
				}).then(res => {
					this.suppliers = res.data;
					this.suppliers.unshift({
						Supplier_SlNo: "",
						Supplier_Code: '',
						Supplier_Name: 'Cash Supplier',
						display_name: 'Cash Supplier',
						Supplier_Mobile: '',
						Supplier_Address: '',
						Supplier_Type: 'G'
					}, {
						Supplier_SlNo: "",
						Supplier_Code: '',
						Supplier_Name: '',
						display_name: 'New Supplier',
						Supplier_Mobile: '',
						Supplier_Address: '',
						Supplier_Type: 'N'
					})
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
						Supplier_Name: '',
						display_name: 'Cash Supplier',
						Supplier_Mobile: '',
						Supplier_Address: '',
						Supplier_Type: 'G'
					}
					this.purchase.previousDue = 0;
					return
				}
				if (this.selectedSupplier.Supplier_SlNo == "") {
					this.purchase.previousDue = 0;
					return;
				}

				axios.post('/get_supplier_due', {
					supplierId: this.selectedSupplier.Supplier_SlNo
				}).then(res => {
					if (res.data.length > 0) {
						this.purchase.previousDue = res.data[0].due;
					} else {
						this.purchase.previousDue = 0;
					}
				})

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
			async onSearchProduct(val, loading) {
				if (val.length > 2) {
					loading(true);
					await axios.post("/get_products", {
							name: val,
							isService: 'false'
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
			async onChangeProduct() {
				if (this.selectedProduct == null) {
					this.selectedProduct = {
						Product_SlNo: '',
						Product_Code: '',
						display_text: 'Select Product',
						Product_Name: '',
						Unit_Name: '',
						quantity: '',
						Product_Purchase_Rate: '',
						Product_SellingPrice: 0.00,
						total: ''
					}
					return
				}				

				if (this.selectedProduct.Product_SlNo == '') {
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
				}
				this.$refs.quantity.focus();
			},

			productTotal() {
				let productTotal = parseFloat(this.selectedProduct.quantity * this.selectedProduct.Product_Purchase_Rate).toFixed(2);

				// product wise discount
				if (event.target.id == 'productDiscountPercent') {
					this.productDiscountAmount = ((parseFloat(productTotal) * parseFloat(this.productDiscountPercent)) / 100).toFixed(2); //get amount to percentage
				} else {
					this.productDiscountPercent = (parseFloat(this.productDiscountAmount) / parseFloat(productTotal) * 100).toFixed(2); //get discount to amount
				}
				this.selectedProduct.total = productTotal - this.productDiscountAmount;
			},
			addToCart() {
				if (this.selectedProduct == null) {
					return;
				}
				let cartInd = this.cart.findIndex(p => p.productId == this.selectedProduct.Product_SlNo);
				if (cartInd > -1) {
					this.cart.splice(cartInd, 1);
				}
				console.log(this.selectedProduct);
				

				let product = {
					productId: this.selectedProduct.Product_SlNo,
					name: this.selectedProduct.Product_Name,
					unit: this.selectedProduct.Unit_Name,
					categoryId: this.selectedProduct.ProductCategory_ID,
					categoryName: this.selectedProduct.ProductCategory_Name,
					purchaseRate: this.selectedProduct.Product_Purchase_Rate,
					salesRate: this.selectedProduct.Product_SellingPrice,
					quantity: this.selectedProduct.quantity,
					discountAmount: this.productDiscountAmount,
					discountPercent: this.productDiscountPercent,
					note: this.productNote,
					total: this.selectedProduct.total
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

				this.cart.push(product);
				this.clearSelectedProduct();
				this.calculateTotal();
			},
			quantityRateChange() {
				let total = 0;
				this.cart = this.cart.map(item => {
					total = parseFloat(parseFloat(item.purchaseRate) * parseFloat(item.quantity)).toFixed(2);
					// product wise discount
					item.discountAmount = ((parseFloat(total) * parseFloat(item.discountPercent)) / 100).toFixed(2); 
					item.total = total - item.discountAmount;			
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
			clearSelectedProduct() {
				this.selectedProduct = {
					Product_SlNo: '',
					Product_Code: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: '',
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0.00,
					total: ''
				};
				this.productDiscountAmount = 0;
				this.productDiscountPercent = 0;
				this.productNote = '';
			},
			calculateTotal() {
				this.purchase.subTotal = this.cart.reduce((prev, curr) => {
					return prev + parseFloat(curr.total);
				}, 0).toFixed(2);
				this.purchase.total = (parseFloat(this.purchase.subTotal) - parseFloat(this.purchase.discount)).toFixed(2);
				this.purchase.vat = ((this.purchase.total * parseFloat(this.vatPercent)) / 100).toFixed(2);
				this.purchase.total = (parseFloat(this.purchase.total) + parseFloat(this.purchase.vat) + parseFloat(this.purchase.freight)).toFixed(2);

				if (event.target.id == 'paid') {
					this.purchase.due = (parseFloat(this.purchase.total) - parseFloat(this.purchase.paid)).toFixed(2);
				} else {
					this.purchase.paid = this.purchase.total;
					this.purchase.due = 0;
				}
			},
			savePurchase() {
				if (this.keyPressed == 'Enter' && !this.click) {
					this.click = true;
					return;
				}

				if (this.selectedSupplier.Supplier_SlNo == null || this.selectedSupplier == null) {
					Swal.fire({
						icon: "error",
						text: "Select supplier",
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

				let data = {
					purchase: this.purchase,
					cartProducts: this.cart,
					supplier: this.selectedSupplier,
				}

				let url = '/add_purchase';
				if (this.purchase.purchaseId != 0) {
					url = '/update_purchase';
				}

				this.purchaseOnProgress = true;
				axios.post(url, data).then(async res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						let conf = confirm('Do you want to view invoice?');
						if (conf) {
							window.open(`/purchase_invoice_print/${r.purchaseId}`, '_blank');
							await new Promise(r => setTimeout(r, 1000));
							window.location = '/purchase';
						} else {
							window.location = '/purchase';
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
				await axios.post('/get_purchases', {
					purchaseId: this.purchase.purchaseId
				}).then(res => {
					let r = res.data;
					let purchase = r.purchases[0];

					this.selectedSupplier.Supplier_SlNo = purchase.Supplier_SlNo;
					this.selectedSupplier.Supplier_Code = purchase.Supplier_Code;
					this.selectedSupplier.Supplier_Name = purchase.Supplier_Name;
					this.selectedSupplier.Supplier_Mobile = purchase.Supplier_Mobile;
					this.selectedSupplier.Supplier_Address = purchase.Supplier_Address;
					this.selectedSupplier.Supplier_Type = purchase.supplierType;
					this.selectedSupplier.display_name = purchase.supplierType == 'G' ? 'Cash Supplier' : `${purchase.Supplier_Code} - ${purchase.Supplier_Name}`;
					this.purchase.supplierType = purchase.supplierType;

					this.purchase.invoice = purchase.PurchaseMaster_InvoiceNo;
					this.purchase.purchaseFor = purchase.PurchaseMaster_PurchaseFor;
					this.purchase.purchaseDate = purchase.PurchaseMaster_OrderDate;
					this.purchase.supplierId = purchase.Supplier_SlNo;
					this.purchase.subTotal = purchase.PurchaseMaster_SubTotalAmount;
					this.purchase.vat = purchase.PurchaseMaster_Tax;
					this.purchase.discount = purchase.PurchaseMaster_DiscountAmount;
					this.purchase.freight = purchase.PurchaseMaster_Freight;
					this.purchase.total = purchase.PurchaseMaster_TotalAmount;
					this.purchase.paid = purchase.PurchaseMaster_PaidAmount;
					this.purchase.due = purchase.PurchaseMaster_DueAmount;
					this.purchase.previousDue = purchase.previous_due;
					this.purchase.note = purchase.PurchaseMaster_Description;

					this.oldPreviousDue = purchase.previous_due;

					this.vatPercent = (this.purchase.vat * 100) / this.purchase.subTotal;

					r.purchaseDetails.forEach(product => {
						let cartProduct = {
							id: product.PurchaseDetails_SlNo,
							productId: product.Product_IDNo,
							name: product.Product_Name,
							unit: product.Unit_Name,
							categoryId: product.ProductCategory_ID,
							categoryName: product.ProductCategory_Name,
							purchaseRate: product.PurchaseDetails_Rate,
							salesRate: product.Product_SellingPrice,
							quantity: product.PurchaseDetails_TotalQuantity,
							discountAmount: product.discountAmount,
							discountPercent: product.discountPercent,
							note: product.note,
							total: product.PurchaseDetails_TotalAmount
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