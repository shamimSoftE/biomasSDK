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
<div id="reOrderList">
    <div class="row">
        <div class="col-md-12" style="margin: 0;">
            <fieldset class="scheduler-border scheduler-search">
                <legend class="scheduler-border">Reorder List</legend>
                <div class="control-group">
                    <form class="form-inline" @submit.prevent="getProductStock">
                        <div class="form-group">
                            <label>Search Type</label>
                            <select class="form-select" @change="onChangeSearch" style="height: 26px;margin:0 6px;width:150px;" v-model="searchType">
                                <option value="">All</option>
                                <option value="category">By Category</option>
                            </select>
                        </div>

                        <div class="form-group" v-if="searchType == 'category'">
                            <label for="">Category</label>
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
    <div style="display:none;" v-bind:style="{display: reOrderList.length > 0 ? '' : 'none'}">
        <div class="row">
            <div class="col-md-12 text-right">
                <a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" id="reportContent">
                <table class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>Product Id</th>
                            <th>Product Name</th>
                            <th>Category Name</th>
                            <th>Re Order Level</th>
                            <th>Current Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in reOrderList">
                            <td>{{ product.Product_Code }}</td>
                            <td>{{ product.Product_Name }}</td>
                            <td>{{ product.ProductCategory_Name }}</td>
                            <td style="text-align: right;">{{ product.Product_ReOrederLevel }} {{ product.Unit_Name }}</td>
                            <td style="text-align: right;">{{ product.current_quantity }} {{ product.Unit_Name }}</td>
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

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#reOrderList',
        data() {
            return {
                searchType: '',
                reOrderList: [],

                categories: [],
                selectedCategory: null,
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
            onChangeSearch(){
                this.selectedCategory = null;
            },
            getProductStock() {
                let filter = {
                    stockType: 'low',
                    categoryId: this.selectedCategory == null ? '' : this.selectedCategory.ProductCategory_SlNo
                }
                axios.post('/get_current_stock', filter).then(res => {
                    this.reOrderList = res.data.stock;
                })
            },
            async print() {
                let reportContent = `
					<div class="container">
						<h4 style="text-align:center">Re order list</h4 style="text-align:center">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
							</div>
						</div>
					</div>
				`;

                var mywindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}`);
                mywindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

                mywindow.document.body.innerHTML += reportContent;

                mywindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                mywindow.print();
                mywindow.close();
            }
        }
    })
</script>