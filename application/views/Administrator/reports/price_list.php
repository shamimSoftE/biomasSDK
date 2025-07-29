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
<div id="priceList">
    <div class="row">
        <div class="col-md-12" style="margin: 0;">
            <fieldset class="scheduler-border scheduler-search">
                <legend class="scheduler-border">Product Price List</legend>
                <div class="control-group">
                    <form class="form-inline" @submit.prevent="getProducts">
                        <div class="form-group">
                            <label>Search Type</label>
                            <select class="form-select" style="height: 26px;padding:0 6px;width:150px;" v-model="searchType">
                                <option value="">All</option>
                                <option value="category">By Category</option>
                            </select>
                        </div>

                        <div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'category' ? '' : 'none'}">
                            <label>Category</label>
                            <v-select v-bind:options="categories" v-model="selectedCategory" label="ProductCategory_Name"></v-select>
                        </div>

                        <div class="form-group" style="margin-top: -1px;">
                            <input type="submit" value="Search">
                        </div>
                    </form>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="row" style="display:none;" v-bind:style="{display: products.length > 0 ? '' : 'none'}">
        <div class="col-md-12 text-right">
            <a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
        </div>
        <div class="col-md-12">
            <div class="table-responsive" id="reportContent">
                <table class="table table-bordered table-hover" id="priceListTable">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Purchase Price</th>
                            <th>Sale Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(product, sl) in products">
                            <td>{{ sl + 1 }}</td>
                            <td>{{ product.Product_Code }}</td>
                            <td style="text-align: start;">{{ product.Product_Name }}</td>
                            <td style="text-align: start;">{{ product.ProductCategory_Name }}</td>
                            <td style="text-align:right;">{{ product.Product_Purchase_Rate }}</td>
                            <td style="text-align:right;">{{ product.Product_SellingPrice }}</td>
                        </tr>
                    </tbody>
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
        el: '#priceList',
        data() {
            return {
                searchType: '',
                products: [],
                selectedProduct: null,
                categories: [],
                selectedCategory: null
            }
        },
        created() {
            this.getCategories();
        },
        methods: {
            getCategories() {
                axios.get('/get_categories').then(res => {
                    this.categories = res.data;
                })
            },
            getProducts() {
                let categoryId = '';
                if (this.searchType == 'category' && this.selectedCategory != null) {
                    categoryId = this.selectedCategory.ProductCategory_SlNo;
                }

                let data = {
                    categoryId: categoryId
                }
                axios.post('/get_products', data).then(res => {
                    this.products = res.data;
                })
            },
            async print() {
                let reportContent = `
					<div class="container">
                        <div class="row">
                            <div class="col-xs-12">
                                <h3 style="text-align:center">Product Price List</h3>
                            </div>
                        </div>
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
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
            }
        }
    })
</script>