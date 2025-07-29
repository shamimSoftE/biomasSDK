<style>
	.v-select {
		background: #fff;
		border-radius: 4px !important;
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

	.v-select .dropdown-menu {
		width: auto;
		overflow-y: auto;
	}
</style>

<div class="row" id="purchaseReturn">
	<div class="col-xs-12 col-md-12 col-lg-12">
		<fieldset class="scheduler-border scheduler-search">
			<legend class="scheduler-border">Purchase Return</legend>
			<div class="control-group">
				<div class="form-group">
					<label class="col-xs-1 control-label no-padding-right" for="purchaseInvoiceno"> Invoice no </label>
					<div class="col-xs-3">
						<v-select v-bind:options="invoices" label="invoice_text" v-model="selectedInvoice" v-bind:disabled="purchaseReturn.returnId == 0 ? false : true" @search="onSearchInvoice"></v-select>
					</div>
					<div class="col-xs-2">
						<button type="button" style="padding: 1px 15px;" @click="getPurchaseDetailsForReturn" v-bind:disabled="purchaseReturn.returnId == 0 ? false : true">View</button>
					</div>
				</div>
			</div>
		</fieldset>
	</div>
	<div class="col-xs-12 col-md-12 col-lg-12" v-if="cart.length > 0" style="display:none" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
		<br>
		<div class="table-responsive">
			<br>
			<div class="col-md-6">
				Return date: <input type="date" v-model="purchaseReturn.returnDate" v-bind:disabled="userType == 'u' ? true : false">
				<br><br>
				Check Stock <input type="checkbox" v-model="checkStock">
			</div>
			<div class="col-md-6 text-right">
				<h4 style="margin:0px;padding:0px;">Supplier Information</h4>
				Name: {{ selectedInvoice.Supplier_Name }}<br>
				Address: {{ selectedInvoice.Supplier_Address }}<br>
				Mobile: {{ selectedInvoice.Supplier_Mobile }}
			</div>
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:5%;">Sl</th>
						<th style="width: 30%;">Product</th>
						<th>Quantity</th>
						<th>Amount</th>
						<th>Already returned quantity</th>
						<th>Already returned amount</th>
						<th>Return Quantity</th>
						<th>Return Rate</th>
						<th>Return Amount</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(product, sl) in cart">
						<td>{{ sl + 1 }}</td>
						<td style="text-align:left;padding-left:4px;">{{ product.Product_Name }}</td>
						<td>{{ product.PurchaseDetails_TotalQuantity }}</td>
						<td>{{ product.PurchaseDetails_TotalAmount }}</td>
						<td>{{ product.returned_quantity }}</td>
						<td>{{ product.returned_amount }}</td>
						<td><input type="text" style="padding: 2px 5px;font-size: 12px;margin:2px 0;" v-model="product.return_quantity" v-on:input="productReturnTotal(sl)"></td>
						<td><input type="text" style="padding: 2px 5px;font-size: 12px;margin:2px 0;" v-model="product.return_rate" v-on:input="productReturnTotal(sl)"></td>
						<td>{{ product.return_amount }}</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="5" style="text-align:right;padding-top:15px;">Note</td>
						<td colspan="2">
							<textarea style="width: 100%" v-model="purchaseReturn.note"></textarea>
						</td>
						<td>
							<button :disabled="save_disabled ? true : false" class="btn btn-success pull-left" v-on:click="savePurchaseReturn">Save</button>
						</td>
						<td>Total: {{ purchaseReturn.total }}</td>
					</tr>
				</tfoot>
			</table>
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
		el: '#purchaseReturn',
		data() {
			return {
				invoices: [],
				selectedInvoice: null,
				cart: [],
				purchaseReturn: {
					returnId: parseInt('<?php echo $returnId; ?>'),
					returnDate: moment().format('YYYY-MM-DD'),
					total: 0.00,
					note: ''
				},
				userType: '<?php echo $this->session->userdata("accountType"); ?>',
				returnDetails: [],
				checkStock: true,
				save_disabled: false,
			}
		},
		created() {
			this.getPurchases();
			if (this.purchaseReturn.returnId != 0) {
				this.getReturn();
			}
		},
		methods: {
			getPurchases() {
				axios.post('/get_purchases', {
					forSearch: 'yes'
				}).then(res => {
					this.invoices = res.data.purchases;
				})
			},
			async onSearchInvoice(val, loading) {
				if (val.length > 2) {
					loading(true);
					await axios.post("/get_purchases", {
							name: val,
						})
						.then(res => {
							let r = res.data;
							this.invoices = r.purchases
							loading(false)
						})
				} else {
					loading(false)
					await this.getPurchases();
				}
			},
			async getPurchaseDetailsForReturn() {
				if (this.selectedInvoice == null) {
					Swal.fire({
						icon: "error",
						text: "Select invoice",
					});
					return;
				}
				await axios.post('/get_purchasedetails_for_return', {
					purchaseId: this.selectedInvoice.PurchaseMaster_SlNo
				}).then(res => {
					this.cart = res.data;
				})
			},
			async productReturnTotal(ind) {
				this.save_disabled = true;
				if (this.checkStock) {
					let stock = await axios.post('/get_product_stock', {
							productId: this.cart[ind].Product_IDNo
						})
						.then(res => {
							return res.data;
						})

					let returnDetail = this.returnDetails.find(rd => rd.PurchaseReturnDetailsProduct_SlNo == this.cart[ind].Product_IDNo);
					stock = +stock + +(returnDetail?.PurchaseReturnDetails_ReturnQuantity ?? 0);
					if (stock < this.cart[ind].return_quantity) {
						Swal.fire({
							icon: "error",
							text: "Unavailable stock",
						});
						this.cart[ind].return_quantity = '';
						return;
					}
				}
				if (this.cart[ind].return_quantity > (this.cart[ind].PurchaseDetails_TotalQuantity - this.cart[ind].returned_quantity)) {
					Swal.fire({
						icon: "error",
						text: "Return quantity is not valid",
					});
					this.cart[ind].return_quantity = '';
					return;
				}
				if (parseFloat(this.cart[ind].return_rate) > parseFloat(this.cart[ind].PurchaseDetails_Rate)) {
					Swal.fire({
						icon: "error",
						text: "Rate is not valid",
					});
					this.cart[ind].return_rate = '';
					return;
				}

				this.cart[ind].return_amount = parseFloat(this.cart[ind].return_quantity) * parseFloat(this.cart[ind].return_rate);
				this.calculateTotal();
				this.save_disabled = false;
			},
			calculateTotal() {
				this.purchaseReturn.total = this.cart.reduce((prev, cur) => {
					return prev + (cur.return_amount ? parseFloat(cur.return_amount) : 0.00)
				}, 0);
			},
			savePurchaseReturn() {
				let filteredCart = this.cart.filter(product => product.return_quantity > 0 && product.return_rate > 0);

				if (filteredCart.length == 0) {
					Swal.fire({
						icon: "error",
						text: "No products to return",
					});
					return;
				}
				if (this.purchaseReturn.returnDate == null || this.purchaseReturn.returnDate == '') {
					Swal.fire({
						icon: "error",
						text: "Enter date",
					});
					return;
				}

				let data = {
					invoice: this.selectedInvoice,
					purchaseReturn: this.purchaseReturn,
					cart: filteredCart
				}

				let url = '/add_purchase_return';
				if (this.purchaseReturn.returnId != 0) {
					url = '/update_purchase_return';
				}
				this.save_disabled = true;
				axios.post(url, data).then(async res => {
					let r = res.data;
					if (r.success) {
						let conf = confirm('Success. Do you want to view invoice?');
						if (conf) {
							window.open('/purchase_return_invoice/' + r.id, '_blank');
							await new Promise(r => setTimeout(r, 1000));
							window.location = '/purchaseReturns';
						} else {
							window.location = '/purchaseReturns';
						}
					}
				})
			},

			getReturn() {
				axios.post('/get_purchase_returns', {
					id: this.purchaseReturn.returnId
				}).then(async res => {
					let purchaseReturn = res.data.returns[0];
					this.selectedInvoice = {
						PurchaseMaster_SlNo: purchaseReturn.PurchaseMaster_SlNo,
						PurchaseMaster_InvoiceNo: purchaseReturn.PurchaseMaster_InvoiceNo,
						Supplier_SlNo: purchaseReturn.Supplier_IDdNo,
						invoice_text: `${purchaseReturn.PurchaseMaster_InvoiceNo} - ${purchaseReturn.Supplier_Name}`,
						Supplier_Name: purchaseReturn.Supplier_Name,
						Supplier_Address: purchaseReturn.Supplier_Address,
						Supplier_Mobile: purchaseReturn.Supplier_Mobile,
						supplierType: purchaseReturn.supplierType
					}

					this.purchaseReturn.returnDate = purchaseReturn.PurchaseReturn_ReturnDate;
					this.purchaseReturn.total = purchaseReturn.PurchaseReturn_ReturnAmount;
					this.purchaseReturn.note = purchaseReturn.PurchaseReturn_Description;

					await this.getPurchaseDetailsForReturn();

					this.returnDetails = res.data.returnDetails;

					this.cart.map(product => {
						let returnDetail = this.returnDetails.find(rd => rd.PurchaseReturnDetailsProduct_SlNo == product.Product_IDNo);
						product.return_quantity = returnDetail?.PurchaseReturnDetails_ReturnQuantity;
						product.returned_quantity = +product.returned_quantity - (returnDetail?.PurchaseReturnDetails_ReturnQuantity ?? 0);
						product.return_amount = returnDetail?.PurchaseReturnDetails_ReturnAmount;
						product.returned_amount = +product.returned_amount - (returnDetail?.PurchaseReturnDetails_ReturnAmount ?? 0);
						return product;
					})
				})
			}
		}
	})
</script>