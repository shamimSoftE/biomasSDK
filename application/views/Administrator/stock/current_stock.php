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

<div id="stock">
	<div class="row">
		<div class="col-xs-12 col-md-12 col-lg-12" style="margin: 0;">
			<fieldset class="scheduler-border scheduler-search">
				<legend class="scheduler-border">Stock Report</legend>
				<div class="control-group">
					<div class="form-group">
						<label class="col-md-1 control-label no-padding-right" style="font-size: 13px;"> Select Type </label>
						<div class="col-md-2">
							<v-select v-bind:options="searchTypes" v-model="selectedSearchType" label="text" v-on:input="onChangeSearchType"></v-select>
						</div>
					</div>

					<div class="form-group" v-if="selectedSearchType.value == 'category'">
						<div class="col-md-2">
							<v-select v-bind:options="categories" v-model="selectedCategory" label="ProductCategory_Name"></v-select>
						</div>
					</div>

					<div class="form-group" v-if="selectedSearchType.value == 'product'">
						<div class="col-md-2">
							<v-select v-bind:options="products" v-model="selectedProduct" label="display_text"></v-select>
						</div>
					</div>

					<div class="form-group" v-if="selectedSearchType.value == 'brand'">
						<div class="col-md-2">
							<v-select v-bind:options="brands" v-model="selectedBrand" label="brand_name"></v-select>
						</div>
					</div>

					<div class="form-group" v-if="selectedSearchType.value != 'current'">
						<div class="col-md-2">
							<input type="date" class="form-control" v-model="date">
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-2">
							<input type="button" style="padding: 1px 15px;" value="Show" v-on:click="getStock">
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="row" v-if="searchType != null" style="display:none" v-bind:style="{display: searchType == null ? 'none' : ''}">
		<div class="col-md-6 text-left">
			<label style="margin-right: 5px;">Sort By: </label>
			<select v-model="sortRecord" @change="sorting" style="height: 24px; padding: 5px; border: 1px solid #ccc;">
				<option value="">Select an option</option>
				<option value="asc">A to Z</option>
				<option value="desc">Z to A</option>
			</select>
		</div>
		<div class="col-md-6 text-right">
			<a href="" v-on:click.prevent="print">
				<i class="fa fa-print"></i> Print
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive" id="stockContent">
				<table class="table table-bordered table-hover" v-if="searchType == 'current'" style="display:none" v-bind:style="{display: searchType == 'current' ? '' : 'none'}">
					<thead>
						<tr>
							<th>Sl</th>
							<th>Code</th>
							<th>Product Name</th>
							<th>Category</th>
							<th>Current Quantity</th>
							<th>Rate</th>
							<th>Stock Value</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(product, sl) in stock">
							<td>{{ sl + 1 }}</td>
							<td>{{ product.Product_Code }}</td>
							<td style="text-align: left;padding-left:4px;">{{ product.Product_Name }}</td>
							<td style="text-align: left;">{{ product.ProductCategory_Name }}</td>
							<td style="text-align: right;">{{ product.current_quantity }} {{ product.Unit_Name }}</td>
							<td style="text-align: right;">{{ product.Product_Purchase_Rate | decimal }}</td>
							<td style="text-align: right;">{{ product.stock_value | decimal }}</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="4" style="text-align:right;">Total</th>
							<th>{{ stock.reduce((prev, curr) => {return prev + parseFloat(curr.current_quantity)}, 0) | decimal }}</th>
							<th></th>
							<th style="text-align: right;">{{ totalStockValue | decimal }}</th>
						</tr>
					</tfoot>
				</table>

				<table class="table table-bordered table-hover" v-if="searchType != 'current' && searchType != null" style="display:none;" v-bind:style="{display: searchType != 'current' && searchType != null ? '' : 'none'}">
					<thead>
						<tr>
							<th>Sl</th>
							<th style="text-align: left;">Code</th>
							<th style="width: 28%;">Product Name</th>
							<th style="width: 20%;">Category</th>
							<th colspan="4">Purchase</th>
							<th colspan="2">Sale</th>
							<th colspan="2">Transfer</th>
							<th style="width: 15%;">Current Qty</th>
							<th>Rate</th>
							<th>Stock Value</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="4"></td>
							<td style="font-size: 11px;padding: 2px 6px;font-weight: 900;">Purchase</td>
							<td style="font-size: 11px;padding: 2px 6px;font-weight: 900;">LC Purchase</td>
							<td style="font-size: 11px;padding: 2px 6px;font-weight: 900;">Return</td>
							<td style="font-size: 11px;padding: 2px 6px;font-weight: 900;">Damage</td>
							<td style="font-size: 11px;padding: 2px 6px;font-weight: 900;">Sold</td>
							<td style="font-size: 11px;padding: 2px 6px;font-weight: 900;">Return</td>
							<td style="font-size: 11px;padding: 2px 6px;font-weight: 900;">In</td>
							<td style="font-size: 11px;padding: 2px 6px;font-weight: 900;">Out</td>
							<td colspan="3"></td>
						</tr>
						<tr v-for="(product, sl) in stock">
							<td>{{ sl + 1 }}</td>
							<td style="text-align: left;">{{ product.Product_Code }}</td>
							<td style="padding-left: 4px;text-align:left;">{{ product.Product_Name }}</td>
							<td style="text-align: left;">{{ product.ProductCategory_Name }}</td>
							<td style="text-align: right;">{{ product.purchased_quantity }}</td>
							<td style="text-align: right;">{{ product.lc_purchase_quantity }}</td>
							<td style="text-align: right;">{{ product.purchase_returned_quantity }}</td>
							<td style="text-align: right;">{{ product.damaged_quantity }}</td>
							<td style="text-align: right;">{{ product.sold_quantity }}</td>
							<td style="text-align: right;">{{ product.sales_returned_quantity }}</td>
							<td style="text-align: right;">{{ product.transferred_to_quantity}}</td>
							<td style="text-align: right;">{{ product.transferred_from_quantity}}</td>
							<td style="text-align: right;">{{ product.current_quantity }} {{ product.Unit_Name }}</td>
							<td style="text-align: right;">{{ product.Product_Purchase_Rate | decimal }}</td>
							<td style="text-align: right;">{{ product.stock_value | decimal }}</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="12" style="text-align:right;">Total</th>
							<th>{{ stock.reduce((prev, curr) => {return prev + parseFloat(curr.current_quantity)}, 0) | decimal }}</th>
							<th></th>
							<th>{{ totalStockValue | decimal }}</th>
						</tr>
					</tfoot>
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
		el: '#stock',
		data() {
			return {
				searchTypes: [{
						text: 'Current Stock',
						value: 'current'
					},
					{
						text: 'Total Stock',
						value: 'total'
					},
					{
						text: 'Category Wise Stock',
						value: 'category'
					},
					{
						text: 'Product Wise Stock',
						value: 'product'
					},
					//{text: 'Brand Wise Stock', value: 'brand'}
				],
				selectedSearchType: {
					text: 'Current Stock',
					value: 'current'
				},
				searchType: null,
				date: moment().format('YYYY-MM-DD'),
				categories: [],
				selectedCategory: null,
				products: [],
				selectedProduct: null,
				brands: [],
				selectedBrand: null,
				selectionText: '',

				stock: [],
				totalStockValue: 0.00,
				sortRecord: ''
			}
		},
		filters: {
			decimal(value) {
				return value == null ? '0.00' : parseFloat(value).toFixed(2);
			}
		},
		created() {},
		methods: {
			sorting() {
				if (this.sortRecord === 'asc') {
					this.stock.sort((a, b) => a.Product_Name.localeCompare(b.Product_Name));
				} else if (this.sortRecord === 'desc') {
					this.stock.sort((a, b) => b.Product_Name.localeCompare(a.Product_Name));
				}
			},
			getStock() {
				this.searchType = this.selectedSearchType.value;
				let url = '';
				let parameters = {};

				if (this.searchType == 'current') {
					url = '/get_current_stock';
				} else {
					url = '/get_total_stock';
					parameters.date = this.date;
				}

				this.selectionText = "";

				if (this.searchType == 'category' && this.selectedCategory == null) {
					alert('Select a category');
					return;
				} else if (this.searchType == 'category' && this.selectedCategory != null) {
					parameters.categoryId = this.selectedCategory.ProductCategory_SlNo;
					this.selectionText = "Category: " + this.selectedCategory.ProductCategory_Name;
				}

				if (this.searchType == 'product' && this.selectedProduct == null) {
					alert('Select a product');
					return;
				} else if (this.searchType == 'product' && this.selectedProduct != null) {
					parameters.productId = this.selectedProduct.Product_SlNo;
					this.selectionText = "product: " + this.selectedProduct.display_text;
				}

				if (this.searchType == 'brand' && this.selectedBrand == null) {
					alert('Select a brand');
					return;
				} else if (this.searchType == 'brand' && this.selectedBrand != null) {
					parameters.brandId = this.selectedBrand.brand_SiNo;
					this.selectionText = "Brand: " + this.selectedBrand.brand_name;
				}


				axios.post(url, parameters).then(res => {
					if (this.searchType == 'current') {
						this.stock = res.data.stock.filter((pro) => pro.current_quantity != 0);
					} else {
						this.stock = res.data.stock;
					}
					this.totalStockValue = res.data.totalValue;
				})
			},
			onChangeSearchType() {
				if (this.selectedSearchType.value == 'category' && this.categories.length == 0) {
					this.getCategories();
				} else if (this.selectedSearchType.value == 'brand' && this.brands.length == 0) {
					this.getBrands();
				} else if (this.selectedSearchType.value == 'product' && this.products.length == 0) {
					this.getProducts();
				}
			},
			getCategories() {
				axios.get('/get_categories').then(res => {
					this.categories = res.data;
				})
			},
			getProducts() {
				axios.post('/get_products', {
					isService: 'false'
				}).then(res => {
					this.products = res.data;
				})
			},
			getBrands() {
				axios.get('/get_brands').then(res => {
					this.brands = res.data;
				})
			},
			async print() {
				let reportContent = `
					<div class="container-fluid">
						<h4 style="text-align:center">${this.selectedSearchType.text} Report</h4 style="text-align:center">
						<h6 style="text-align:center">${this.selectionText}</h6>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#stockContent').innerHTML}
							</div>
						</div>
					</div>
				`;

				var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}, left=0, top=0`);
				reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

				reportWindow.document.body.innerHTML += reportContent;

				reportWindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				reportWindow.print();
				reportWindow.close();
			},
		}
	})
</script>