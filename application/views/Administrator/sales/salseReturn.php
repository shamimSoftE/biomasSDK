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
</style>

<div class="row" id="salesReturn" style="margin: 0;">
	<fieldset class="scheduler-border scheduler-search">
		<legend class="scheduler-border">Sales Return</legend>
		<div class="control-group">
			<div class="col-md-12">
				<form class="form-inline">
					<div class="form-group" style="display:none;" v-bind:style="{display: customers.length > 0 ? '' : 'none'}">
						<label>Customer</label>
						<v-select v-bind:options="customers" label="display_name" v-model="selectedCustomer" v-on:input="getInvoices" @search="onSearchCustomer" v-bind:disabled="salesReturn.returnId == 0 ? false : true"></v-select>
					</div>

					<div class="form-group">
						<label>Invoice</label>
						<v-select v-bind:options="invoices" label="SaleMaster_InvoiceNo" v-model="selectedInvoice" @input="onChangeInvoice" @search="onSearchInvoice" v-bind:disabled="salesReturn.returnId == 0 ? false : true"></v-select>
					</div>
					<div class="form-group" style="margin-top: -3px;">
						<button type="button" style="padding: 1px 15px;" @click="getSaleDetailsForReturn" v-bind:disabled="salesReturn.returnId == 0 ? false : true">View</button>
					</div>
				</form>
			</div>
		</div>
	</fieldset>
	<div style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
		<div class="col-xs-12 col-md-12 col-lg-12">
			<br>
			<div class="col-md-6">
				Return date: <input type="date" v-model="salesReturn.returnDate" v-bind:disabled="userType == 'u' ? true : false"><br><br>
				Invoice Discount: {{ selectedInvoice.SaleMaster_TotalDiscountAmount }}
			</div>
			<div class="col-md-6 text-right">
				<h4 style="margin:0px;padding:0px;">Customer Information</h4>
				Name: {{ selectedInvoice.Customer_Name }}<br>
				Address: {{ selectedInvoice.Customer_Address }}<br>
				Mobile: {{ selectedInvoice.Customer_Mobile }}
			</div>
			<div class="col-md-12">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th style="width: 6%;">Sl</th>
								<th>Product</th>
								<th>Quantity</th>
								<th>Amount</th>
								<th>Already returned quantity</th>
								<th>Already returned amount</th>
								<th>Return Quantity</th>
								<th>Return Rate</th>
								<th style="width: 10%;">Return Amount</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(product, sl) in cart">
								<td>{{ sl + 1 }}</td>
								<td style="width: 30%;text-align:left;padding-left:4px;">{{ product.Product_Name }}</td>
								<td>{{ product.SaleDetails_TotalQuantity }}</td>
								<td>{{ product.SaleDetails_TotalAmount }}</td>
								<td>{{ product.returned_quantity }}</td>
								<td>{{ product.returned_amount }}</td>
								<td><input type="text" style="padding: 2px 5px;margin:2px 0;font-size: 13px;" v-model="product.return_quantity" v-on:input="productReturnTotal(sl)"></td>
								<td><input type="text" style="padding: 2px 5px;margin:2px 0;font-size: 13px;" v-model="product.return_rate" v-on:input="productReturnTotal(sl)"></td>
								<td>{{ product.return_amount }}</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="5" style="text-align:right;padding-top:15px;">Note</td>
								<td colspan="2">
									<textarea style="margin:2px 5px;" v-model="salesReturn.note"></textarea>
								</td>
								<td>
									<button class="btnSave pull-left" style="margin:15px 48px;" v-on:click="saveSalesReturn">Save</button>
								</td>
								<td>Total: {{ salesReturn.total }}</td>
							</tr>
						</tfoot>
					</table>
				</div>

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
		el: '#salesReturn',
		data() {
			return {
				customers: [],
				selectedCustomer: null,
				invoices: [],
				selectedInvoice: {
					SaleMaster_InvoiceNo: '',
					SalseCustomer_IDNo: null,
					Customer_Name: '',
					Customer_Mobile: '',
					Customer_Address: '',
					SaleMaster_TotalDiscountAmount: 0
				},
				cart: [],
				salesReturn: {
					returnId: parseInt('<?php echo $returnId; ?>'),
					returnDate: moment().format('YYYY-MM-DD'),
					total: 0.00,
					note: ''
				},
				userType: '<?php echo $this->session->userdata("accountType"); ?>'
			}
		},
		created() {
			this.getCustomers();
			this.getInvoices();
			if (this.salesReturn.returnId != 0) {
				this.getReturn();
			}
		},
		methods: {
			getCustomers() {
				axios.post('/get_customers', {
					forSearch: 'yes'
				}).then(res => {
					this.customers = res.data;
					this.customers.unshift({
						Customer_SlNo: null,
						Customer_Name: 'Cash Customer',
						Customer_Type: 'G',
						display_name: 'Cash Customer'
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
			getInvoices() {
				let arg = {};
				if (this.selectedCustomer != null) {
					if (this.selectedCustomer.Customer_Type == 'G') {
						arg = {
							customerType: 'G'
						}
					} else {
						arg = {
							customerId: this.selectedCustomer.Customer_SlNo,
							forSearch: 'yes'
						}
					}
				}

				axios.post('/get_sales', arg).then(res => {
					this.invoices = res.data.sales;
				})
			},
			async onSearchInvoice(val, loading) {
				if (val.length > 3) {
					loading(true);
					await axios.post("/get_sales", {
							name: val,
						})
						.then(res => {
							let r = res.data;
							this.invoices = r.sales
							loading(false)
						})
				} else {
					loading(false)
					await this.getInvoices();
				}
			},
			onChangeInvoice() {
				if (this.selectedInvoice == null) {
					this.selectedInvoice = {
						SaleMaster_InvoiceNo: '',
						SalseCustomer_IDNo: null,
						Customer_Name: '',
						Customer_Mobile: '',
						Customer_Address: '',
						SaleMaster_TotalDiscountAmount: 0
					};
					this.selectedCustomer = null;
					return;
				}
				if (this.selectedInvoice.SaleMaster_InvoiceNo != '') {
					this.selectedCustomer = {
						Customer_SlNo: this.selectedInvoice.customerType == 'G' ? "" : this.selectedInvoice.SalseCustomer_IDNo,
						Customer_Name: this.selectedInvoice.Customer_Name,
						display_name: this.selectedInvoice.customerType == 'G' ? 'Cash Customer' : `${this.selectedInvoice.Customer_Name} - ${this.selectedInvoice.Customer_Code} - ${this.selectedInvoice.Customer_Mobile}`,
					}
				}
			},
			async getSaleDetailsForReturn() {
				if (this.selectedInvoice.SaleMaster_InvoiceNo == '') {
					return;
				}
				await axios.post('/get_saledetails_for_return', {
					salesId: this.selectedInvoice.SaleMaster_SlNo
				}).then(res => {
					this.cart = res.data;
				})
			},
			productReturnTotal(ind) {
				if (this.cart[ind].return_quantity > (this.cart[ind].SaleDetails_TotalQuantity - this.cart[ind].returned_quantity)) {
					Swal.fire({
						icon: "error",
						text: "Return quantity is not valid!",
					});
					this.cart[ind].return_quantity = '';
					return;
				}
				if (parseFloat(this.cart[ind].return_rate) > parseFloat(this.cart[ind].SaleDetails_Rate)) {
					Swal.fire({
						icon: "error",
						text: "Rate is not valid!",
					});
					this.cart[ind].return_rate = '';
					return;
				}
				this.cart[ind].return_amount = parseFloat(this.cart[ind].return_quantity) * parseFloat(this.cart[ind].return_rate);
				this.calculateTotal();
			},
			calculateTotal() {
				this.salesReturn.total = this.cart.reduce((prev, cur) => {
					return prev + (cur.return_amount ? parseFloat(cur.return_amount) : 0.00)
				}, 0);
			},
			saveSalesReturn() {
				// let filteredCart = this.cart.filter(product => product.return_quantity > 0 && product.return_rate > 0);
				let filteredCart = this.cart.filter(product => product.return_quantity > 0 );

				if (filteredCart.length == 0) {
					Swal.fire({
						icon: "error",
						text: "No products to return",
					});
					return;
				}
				if (this.salesReturn.returnDate == null || this.salesReturn.returnDate == '') {
					Swal.fire({
						icon: "error",
						text: "Enter date",
					});
					return;
				}

				let data = {
					invoice: this.selectedInvoice,
					salesReturn: this.salesReturn,
					cart: filteredCart
				}

				let url = '/add_sales_return';
				if (this.salesReturn.returnId != 0) {
					url = '/update_sales_return';
				}

				axios.post(url, data).then(async res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						let conf = confirm('Success. Do you want to view invoice?');
						if (conf) {
							window.open('/sale_return_invoice/' + r.id, '_blank');
							await new Promise(r => setTimeout(r, 1000));
							window.location = '/salesReturn';
						} else {
							window.location = '/salesReturn';
						}
					}
				})
			},
			async getReturn() {
				let returnData = await axios.post('/get_sale_returns', {
					id: this.salesReturn.returnId
				}).then(res => {
					return res.data;
				})

				let saleReturn = returnData.returns?.[0];

				this.selectedCustomer = {
					Customer_SlNo: saleReturn.Customer_SlNo,
					Customer_Name: saleReturn.Customer_Name,
					Customer_Code: saleReturn.Customer_Code,
					Customer_Mobile: saleReturn.Customer_Mobile,
					display_name: saleReturn.Customer_SlNo == null ? 'Cash Customer' : `${saleReturn.Customer_Name} - ${saleReturn.Customer_Code} - ${saleReturn.Customer_Mobile}`,
					Customer_Type: saleReturn.Customer_Type
				}

				this.selectedInvoice = {
					SaleMaster_SlNo: saleReturn.SaleMaster_SlNo,
					SaleMaster_InvoiceNo: saleReturn.SaleMaster_InvoiceNo,
					SalseCustomer_IDNo: saleReturn.Customer_SlNo,
					Customer_Name: saleReturn.Customer_Name,
					Customer_Mobile: saleReturn.Customer_Mobile,
					Customer_Address: saleReturn.Customer_Address,
					Customer_Type: saleReturn.Customer_Type,
					SaleMaster_TotalDiscountAmount: saleReturn.SaleMaster_TotalDiscountAmount
				}

				this.salesReturn.returnDate = saleReturn.SaleReturn_ReturnDate;
				this.salesReturn.total = saleReturn.SaleReturn_ReturnAmount;
				this.salesReturn.note = saleReturn.SaleReturn_Description;

				await this.getSaleDetailsForReturn();

				this.cart.map(product => {
					let returnDetail = returnData.returnDetails.find(rd => rd.SaleReturnDetailsProduct_SlNo == product.Product_IDNo);
					product.return_quantity = returnDetail?.SaleReturnDetails_ReturnQuantity;
					product.returned_quantity = product.returned_quantity - (returnDetail?.SaleReturnDetails_ReturnQuantity ?? 0);
					product.return_amount = returnDetail?.SaleReturnDetails_ReturnAmount;
					product.returned_amount = product.returned_amount - (returnDetail?.SaleReturnDetails_ReturnAmount ?? 0);
					return product;
				})

			}
		}
	})
</script>