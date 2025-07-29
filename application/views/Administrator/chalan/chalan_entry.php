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
</style>

<div id="chalan" class="row">
	<div class="col-xs-12 col-md-12 col-lg-12">
		<fieldset class="scheduler-border entryFrom">
			<div class="control-group">
				<div class="row">
					<div class="form-group">
						<label class="col-md-1 control-label no-padding-right"> Invoice no </label>
						<div class="col-md-2">
							<input type="text" class="form-control" v-model="chalan.invoiceNo" readonly />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-1 control-label no-padding-right"> Chalan. By </label>
						<div class="col-md-2">
							<input type="text" class="form-control" v-model="chalan.chalanBy" readonly />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-1 control-label no-padding-right"> Ch. From </label>
						<div class="col-md-2">
							<v-select id="branchDropdown" v-bind:options="branches" label="Branch_name" v-model="selectedBranch" disabled></v-select>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-3">
							<input class="form-control" type="date" v-model="chalan.chalanDate" />
						</div>
					</div>
				</div>
			</div>
		</fieldset>
	</div>


	<div class="col-xs-9 col-md-9 col-lg-9">
		<fieldset class="scheduler-border" style="margin-bottom: 5px;padding-bottom: 5px">
			<legend class="scheduler-border">Customer & Product Information</legend>
			<div class="control-group">
				<div class="row">
					<div class="col-xs-12 col-md-5">
						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right"> Customer </label>
							<div class="col-xs-8" style="display: flex;align-items:center;margin-bottom:5px;">
								<div style="width: 86%;">
									<v-select v-bind:options="customers" style="margin: 0;" label="display_name" v-model="selectedCustomer" v-on:input="customerOnChange" @search="onSearchCustomer"></v-select>
								</div>
								<div style="width: 13%;margin-left:2px;">
									<a href="<?= base_url('customer') ?>" class="add-button" target="_blank" title="Add New Customer"><i class="fa fa-plus" aria-hidden="true"></i></a>
								</div>
							</div>
						</div>

						<div class="form-group" style="display:none;" v-bind:style="{display: selectedCustomer.Customer_Type == 'G' ? '' : 'none'}">
							<label class="col-xs-4 control-label no-padding-right"> Name </label>
							<div class="col-xs-8">
								<input type="text" id="customerName" placeholder="Customer Name" class="form-control" v-model="selectedCustomer.Customer_Name" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? false : true" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-xs-4 control-label no-padding-right"> Mobile No </label>
							<div class="col-xs-8">
								<input type="text" id="mobileNo" placeholder="Mobile No" class="form-control" v-model="selectedCustomer.Customer_Mobile" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? false : true" />
							</div>
						</div>
					</div>

					<div class="col-xs-12 col-md-3">
						<div class="form-group">
							<label class="col-xs-3 control-label no-padding-right" style="padding-left: 0;"> Address </label>
							<div class="col-xs-9">
								<textarea id="address" rows="3" placeholder="Address" class="form-control" v-model="selectedCustomer.Customer_Address" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? false : true"></textarea>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<form v-on:submit.prevent="addToCart">
							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> <span v-html="selectedProduct.is_service == 'true' ? 'Service' : 'Product'"></span> </label>
								<div class="col-xs-9" style="display: flex;align-items:center;margin-bottom:5px;">
									<div style="width: 86%;">
										<v-select v-bind:options="products" style="margin: 0;" v-model="selectedProduct" label="display_text" @input="productOnChange" @search="onSearchProduct"></v-select>
									</div>
									<div style="width: 13%;margin-left:2px;">
										<a href="<?= base_url('product') ?>" class="add-button" target="_blank" title="Add New Product"><i class="fa fa-plus" aria-hidden="true"></i></a>
									</div>
								</div>
							</div>

							<div class="form-group" style="display: none;">
								<label class="col-xs-3 control-label no-padding-right"> Sale Rate </label>
								<div class="col-xs-9">
									<input type="number" min="0" step="any" placeholder="Rate" class="form-control" v-model="selectedProduct.Product_SellingPrice" v-on:input="productTotal" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> Quantity </label>
								<div class="col-xs-9">
									<input type="number" min="0" step="any" id="quantity" placeholder="Qty" class="form-control" ref="quantity" v-model="selectedProduct.quantity" v-on:input="productTotal" autocomplete="off" required />
								</div>
							</div>

							<div class="form-group" style="display:none;">
								<label class="col-xs-3 control-label no-padding-right"> Discount</label>
								<div class="col-xs-9">
									<span>(%)</span>
									<input type="text" placeholder="Discount" class="form-control" style="display: inline-block; width: 90%" />
								</div>
							</div>

							<div class="form-group" style="display:none;">
								<label class="col-xs-3 control-label no-padding-right"> Amount </label>
								<div class="col-xs-9">
									<input type="text" placeholder="Amount" class="form-control" v-model="selectedProduct.total" readonly />
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> Note </label>
								<div class="col-xs-9">
									<input type="text" placeholder="Product Note" class="form-control" v-model="productNote" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> </label>
								<div class="col-xs-9 text-right">
									<button type="submit" class="btnCart">Add to Cart</button>
								</div>
							</div>
						</form>

					</div>
					<div class="col-md-2" style="display:none;">
						<input type="password" ref="productPurchaseRate" v-model="selectedProduct.Product_Purchase_Rate" v-on:mousedown="toggleProductPurchaseRate" v-on:mouseup="toggleProductPurchaseRate" v-on:mouseout="$refs.productPurchaseRate.type = 'password'" readonly title="Purchase rate (click & hold)" style="font-size:12px;width:100%;text-align: center;">
					</div>
				</div>
			</div>
		</fieldset>


		<div class="col-xs-12 col-md-12 col-lg-12" style="padding-left: 0px;padding-right: 0px;">
			<div class="table-responsive">
				<table class="table table-bordered table-hover" style="color:#000;">
					<thead>
						<tr class="">
							<th style="width:10%;color:#000;">Sl</th>
							<th style="width:30%;color:#000;">Product Name</th>
							<th style="width:15%;color:#000;">Category</th>
							<th style="width:15%;color:#000;">Note</th>
							<th style="width:7%;color:#000;">Qty</th>
							<!-- <th style="width:8%;color:#000;">Rate</th>
							<th style="width:15%;color:#000;">Total</th> -->
							<th style="width:15%;color:#000;">Action</th>
						</tr>
					</thead>
					<tbody style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
						<tr v-for="(product, sl) in cart">
							<td>{{ sl + 1 }}</td>
							<td style="padding-left: 4px;text-align:left;">{{ product.name }}</td>
							<td>{{ product.categoryName }}</td>
							<td>{{ product.note }}</td>
							<td>{{ product.quantity }}</td>
							<!-- <td>{{ product.salesRate }}</td>
							<td>{{ product.total }}</td> -->
							<td><a href="" v-on:click.prevent="removeFromCart(sl)"><i class="fa fa-trash"></i></a></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>


	<div class="col-xs-3 col-md-3 col-md-3 col-lg-3">
		<fieldset class="scheduler-border" style="margin-bottom: 5px;padding-bottom: 5px">
			<legend class="scheduler-border">Cart Information</legend>
			<div class="control-group">
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table style="color:#000;margin-bottom: 0px;border-collapse: collapse;">
								<tr style="display: none;">
									<td>
										<div class="form-group">
											<label class="col-md-12 control-label no-padding-right">Sub Total</label>
											<div class="col-md-12">
												<input type="number" min="0" step="any" class="form-control" v-model="chalan.subTotal" readonly />
											</div>
										</div>
									</td>
								</tr>

								<tr style="display: none;">
									<td>
										<div class="form-group">
											<label class="col-md-12 control-label no-padding-right"> Vat </label>
											<div class="col-md-4">
												<input type="number" min="0" step="any" class="form-control" v-model="vatPercent" v-on:input="calculateTotal" />
											</div>
											<label class="col-md-1 control-label no-padding-right">%</label>
											<div class="col-md-7">
												<input type="number" min="0" step="any" readonly class="form-control" v-model="chalan.vat" />
											</div>
										</div>
									</td>
								</tr>

								<tr style="display:none;">
									<td>
										<div class="form-group">
											<label class="col-md-12 control-label no-padding-right">Freight</label>
											<div class="col-md-12">
												<input type="number" min="0" step="any" class="form-control" />
											</div>
										</div>
									</td>
								</tr>

								<tr style="display: none;">
									<td>
										<div class="form-group">
											<label class="col-md-12 control-label no-padding-right">Discount Persent</label>

											<div class="col-md-4">
												<input type="number" min="0" step="any" class="form-control" v-model="discountPercent" v-on:input="calculateTotal" />
											</div>

											<label class="col-md-1 control-label no-padding-right">%</label>

											<div class="col-md-7">
												<input type="number" min="0" step="any" id="discount" class="form-control" v-model="chalan.discount" v-on:input="calculateTotal" />
											</div>

										</div>
									</td>
								</tr>

								<tr >
									<td>
										<div class="form-group">
											<label class="col-md-6 control-label no-padding-right">Total Qty</label>
											<div class="col-md-2"></div>
											<div class="col-md-2">
												<strong style="color: rgb(209, 91, 71);font-size: 17px;">
													{{ chalan.total_qty }}
												</strong>
											</div>
											<div class="col-md-8"></div>
										</div>
									</td>
								</tr>

								<tr>
									<td>
										<div class="form-group">
											<div class="col-xs-6 col-md-6" style="display: block;width: 50%;">
												<input type="button" class="btn btn-sm" value="Save" v-on:click="savechalan" style="width:100%;background: green !important;border: 0;border-radius: 5px;" />
											</div>
											<div class="col-xs-6 col-md-6" style="display: block;width: 50%;">
												<a class="btn btn-sm" href="/chalan_entry" 
													style="background: #2d1c5a !important;border: 0;width: 100%;display: flex; justify-content: center;border-radius: 5px;">
													New chalan
												</a>
											</div>
										</div>
									</td>
								</tr>

							</table>
						</div>
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
		el: '#chalan',
		data() {
			return {
				chalan: {
					chalanId: parseInt('<?php echo $chalanId; ?>'),
					invoiceNo: '<?php echo $invoice; ?>',
					customerName: '',
					customerMobile: '',
					customerAddress: '',
					chalanBy: '<?php echo $this->session->userdata("FullName"); ?>',
					chalanFrom: '',
					chalanDate: '',
					subTotal: 0.00,
					discount: 0.00,
					vat: 0.00,
					total: 0.00,
					total_qty: 0.00
				},
				productNote: '',
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
				vatPercent: 0,
				discountPercent: 0,
				cart: [],
				branches: [],
				selectedBranch: {
					branch_id: "<?php echo $this->session->userdata('BRANCHid'); ?>",
					Branch_name: "<?php echo $this->session->userdata('Branch_name'); ?>"
				},
				products: [],
				selectedProduct: {
					Product_SlNo: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: 0,
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0.00,
					total: 0.00
				}
			}
		},
		created() {
			this.chalan.chalanDate = moment().format('YYYY-MM-DD');
			this.getBranches();
			this.getCustomers();
			this.getProducts();

			if (this.chalan.chalanId != 0) {
				this.getchalans();
			}
		},
		methods: {
			getBranches() {
				axios.get('/get_branches').then(res => {
					this.branches = res.data;
				})
			},
			async getCustomers() {
				await axios.post('/get_customers', {
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
					this.products = res.data;
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
			productOnChange() {
				this.productNote = '';
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
				if (this.selectedProduct.Product_SlNo != "") {
					this.$refs.quantity.focus();
				}
			},
			toggleProductPurchaseRate() {
				this.$refs.productPurchaseRate.type = this.$refs.productPurchaseRate.type == 'text' ? 'password' : 'text';
			},
			async customerOnChange() {
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
				if (this.selectedCustomer.Customer_SlNo == "") {
					return;
				}
			},
			addToCart() {
				let product = {
					productId: this.selectedProduct.Product_SlNo,
					categoryName: this.selectedProduct.ProductCategory_Name,
					name: this.selectedProduct.Product_Name,
					note: this.productNote,
					//salesRate: this.selectedProduct.Product_SellingPrice,
					quantity: this.selectedProduct.quantity,
					//total: this.selectedProduct.total
				}
				if (product.productId == '') {
					Swal.fire({
						icon: "error",
						text: "Select Product",
					});
					return;
				}
				if (product.quantity == 0 || product.quantity == '') {
					Swal.fire({
						icon: "error",
						text: "Enter quantity",
					});
					return;
				}

				let cartInd = this.cart.findIndex(p => p.productId == product.productId);
				if (cartInd > -1) {
					this.cart.splice(cartInd, 1);
				}

				this.cart.unshift(product);
				this.clearProduct();
				this.calculateTotal();
			},
			removeFromCart(ind) {
				this.cart.splice(ind, 1);
				this.calculateTotal();
			},
			clearProduct() {
				this.productNote = '';
				this.selectedProduct = {
					Product_SlNo: '',
					display_text: 'Select Product',
					Product_Name: '',
					quantity: 0,
					Product_SellingPrice: 0.00,
					total: 0.00
				}
			},
			calculateTotal() {

				// this.chalan.subTotal = this.cart.reduce((prev, curr) => {
				// 	return prev + parseFloat(curr.total)
				// }, 0).toFixed(2);

				this.chalan.total_qty = this.cart.reduce((prev, curr) => {
					return prev + parseFloat(curr.quantity)
				}, 0);

				// this.chalan.vat = ((parseFloat(this.chalan.subTotal) * parseFloat(this.vatPercent)) / 100).toFixed(2);
				// if (event.target.id == 'discount') {
				// 	this.discountPercent = (parseFloat(this.chalan.discount) / parseFloat(this.chalan.subTotal) * 100).toFixed(2);
				// } else {
				// 	this.chalan.discount = ((parseFloat(this.chalan.subTotal) * parseFloat(this.discountPercent)) / 100).toFixed(2);
				// }
				// this.chalan.total = ((parseFloat(this.chalan.subTotal) + parseFloat(this.chalan.vat)) - parseFloat(this.chalan.discount)).toFixed(2);
			},
			savechalan() {
				if (this.cart.length == 0) {
					Swal.fire({
						icon: "error",
						text: "Cart is empty",
					});
					return;
				}

				let url = "/add_chalan";
				if (this.chalan.chalanId != 0) {
					url = "/update_chalan";
				}

				this.chalan.chalanFrom = this.selectedBranch.branch_id;

				let data = {
					chalan: this.chalan,
					cart: this.cart,
					customer: this.selectedCustomer
				}
				axios.post(url, data).then(async res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						let conf = confirm('Do you want to view invoice?');
						if (conf) {
							window.open('/chalan_invoice/' + r.chalanId, '_blank');
							await new Promise(r => setTimeout(r, 1000));
							window.location = '/chalan_entry';
						} else {
							window.location = '/chalan_entry';
						}
					}
				})
			},
			getchalans() {
				axios.post('/get_chalans', {
					chalanId: this.chalan.chalanId
				}).then(res => {
					let r = res.data;
					let chalan = r.chalans[0];
					this.chalan.chalanBy = chalan.AddBy;
					this.chalan.invoiceNo = chalan.SaleMaster_InvoiceNo;
					this.chalan.salesFrom = chalan.branch_id;
					this.chalan.salesDate = chalan.SaleMaster_SaleDate;
					this.chalan.subTotal = chalan.SaleMaster_SubTotalAmount;
					this.chalan.discount = chalan.SaleMaster_TotalDiscountAmount;
					this.chalan.vat = chalan.SaleMaster_TaxAmount;
					this.chalan.total = chalan.SaleMaster_TotalSaleAmount;

					this.vatPercent = parseFloat(this.chalan.vat) * 100 / parseFloat(this.chalan.subTotal);
					this.discountPercent = parseFloat(this.chalan.discount) * 100 / parseFloat(this.chalan.subTotal);
					this.selectedCustomer = {
						Customer_SlNo: chalan.SalseCustomer_IDNo ?? "",
						Customer_Name: chalan.SaleMaster_customer_name,
						Customer_Mobile: chalan.SaleMaster_customer_mobile,
						Customer_Address: chalan.SaleMaster_customer_address,
						Customer_Type: chalan.customerType,
						display_name: chalan.owner_name == null ? "Cash Customer" : `${chalan.Customer_Name} - ${chalan.Customer_Code} - ${chalan.SaleMaster_customer_mobile}`
					}

					r.chalanDetails.forEach(product => {
						let cartProduct = {
							productId: product.Product_IDNo,
							categoryName: product.ProductCategory_Name,
							name: product.Product_Name,
							note: product.product_note,
							salesRate: product.SaleDetails_Rate,
							quantity: product.SaleDetails_TotalQuantity,
							total: product.SaleDetails_TotalAmount
						}
						this.cart.push(cartProduct);
					})
					this.getProducts();
				})
			}
		}
	})
</script>