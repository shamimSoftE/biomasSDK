<style scoped>
    .v-select {
        width: 100%;
        float: right;
        background: #fff;
        margin-left: 5px;
        border-radius: 4px !important;
        margin-top: -2px;
        margin-bottom: 4px;
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

    .article {
        min-height: 65px;
        max-height: 100px;
        float: left;
        writing-mode: tb-rl;
        line-height: 0;
        font-weight: 700;
        transform: rotate(180deg);
    }

    .content {
        width: 120px;
        float: left;
        padding: 2px;
    }

    .name {
        height: auto;
        width: 120px;
        font-size: 11px;
    }

    .img {
        height: 60px;
        width: 120px;
    }

    .pid {
        height: 15px;
        width: 120px;
    }

    .price {
        height: 10px;
        width: 120px;
    }

    .date {
        height: 90px;
        width: 20px;
        float: right;
        writing-mode: tb-rl;
    }

    .mytext {
        height: 25px !important;
        padding: 2px;
    }
</style>
<div id="multiproductBarcode">
    <form @submit.prevent="addToCart">
        <div class="row" style="margin:0;display:flex;justify-content:center;">
            <div class="col-md-10 col-xs-12" style="padding: 0;">
                <fieldset class="scheduler-border bg-of-skyblue">
                    <legend class="scheduler-border">BarCode Information</legend>
                    <div class="control-group">
                        <div class="col-md-6" style="padding: 0;">
                            <div class="form-group clearfix">
                                <label class="control-label col-xs-4 col-md-4">Product:</label>
                                <div class=" col-xs-8 col-md-7">
                                    <v-select id="products" :options="products" v-model="selectedProduct" label="display_text" @input="onChangeProduct" @search="onSearchProduct"></v-select>
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <label class="control-label col-xs-4 col-md-4">Sale Rate:</label>
                                <div class="col-xs-8 col-md-7">
                                    <input type="number" min="0" step="0.001" id="price" v-model="selectedProduct.Product_SellingPrice" class="form-control" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <label class="control-label col-xs-4 col-md-4">Quantity:</label>
                                <div class="col-xs-8 col-md-7">
                                    <input type="number" min="0" step="1" id="quantity" v-model="selectedProduct.quantity" class="form-control" autocomplete="off">
                                </div>
                            </div>

                        </div>

                        <div class="col-md-6" style="padding: 0;">
                            <div class="form-group clearfix">
                                <label class="control-label col-xs-4 col-md-4">Product Code:</label>
                                <div class="col-xs-8 col-md-7">
                                    <input type="text" @input="onChangeCode($event)" class="form-control" id="code" v-model="selectedProduct.Product_Code" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <label class="control-label col-xs-4 col-md-4">Product Name:</label>
                                <div class="col-xs-8 col-md-7">
                                    <input type="text" class="form-control" id="name" v-model="selectedProduct.Product_Name" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <label class="control-label col-xs-4 col-md-4">Article:</label>
                                <div class="col-xs-8 col-md-7">
                                    <input type="text" class="form-control" @input="onChangeCode($event)" id="article" v-model="selectedProduct.article" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <label class="col-md-4"></label>
                                <div class="col-md-7 text-right">
                                    <button :disabled="onProgress" type="submit" class="btnSave">Add To Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </form>
    <div class="table-responsive" style="display: none;" :style="{display: carts.length > 0 ? '' : 'none'}" v-if="carts.length > 0">
        <table class="table table-bordered" style="color:#000;margin-bottom: 5px;">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Code</th>
                    <th>Product Name</th>
                    <th>Article</th>
                    <th>Rate</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, sl) in carts">
                    <td>{{sl + 1}}</td>
                    <td>{{item.code}}</td>
                    <td style="text-align: left;">{{item.name}}</td>
                    <td>{{item.article}}</td>
                    <td>{{item.sale_rate}}</td>
                    <td>{{item.quantity}}</td>
                    <td>
                        <i class="btnDelete fa fa-trash" @click="removeCart(sl)"></i>
                    </td>
                </tr>
                <tr>
                    <th colspan="5">Total</th>
                    <th>{{carts.reduce((prev, curr) => {return prev + parseFloat(curr.quantity)},0)}}</th>
                    <th></th>
                </tr>
                <tr>
                    <td colspan="6">
                        <div style="display: flex;align-items: center;justify-content: center;gap: 15px;">
                            <label for="single" style="cursor:pointer;display: flex;align-items: center;gap: 5px;justify-content: center;margin-top: 6px;">
                                <input type="checkbox" style="cursor:pointer;margin: 0;width: 14px;height: 15px;" id="single" v-model="is_single"> Single
                            </label>
                            <div v-if="is_single" style="display: inline-block;justify-content:center;">
                                |
                                <label style="margin: 0;">
                                    Width: <input type="text" v-model="xAxis" style="width: 60px;">
                                </label>
                                <label style="margin: 0;">
                                    Height: <input type="text" v-model="yAxis" style="width: 60px;">
                                </label>
                            </div>
                        </div>
                    </td>
                    <td><button type="button" @click="generateBarcodes" class="btnEdit" style="margin: 2px 0px;border: 0;background: #009100c9;color: white;padding: 3px 12px;border-radius: 5px;">Generate</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script>
    Vue.component('v-select', VueSelect.VueSelect);
    let app = new Vue({
        el: '#multiproductBarcode',
        data() {
            return {
                carts: [],

                products: [],
                selectedProduct: {
                    Product_SlNo: "",
                    Product_Code: "",
                    display_text: "",
                    Product_Name: "",
                    Product_SellingPrice: 0,
                    quantity: 0,
                    article: "",
                },

                onProgress: false,
                showReport: null,
                is_single: false,
                xAxis: 1.5,
                yAxis: 1,
            }
        },

        created() {
            this.getProducts();
        },

        methods: {
            onChangeCode(event) {
                var article = $("#article");
                if (this.selectedProduct.Product_Code.length > 8) {
                    article.attr('disabled', true);
                    this.selectedProduct.article = "";
                } else {
                    article.removeAttr('disabled', true);
                }
                if (event.target.id == 'article') {
                    if (this.selectedProduct.article.length > 10) {
                        this.selectedProduct.article = this.selectedProduct.article.substring(0, 10);
                        Swal.fire({
                            icon: "error",
                            text: "You take only 10 characters",
                        });
                        return;
                    }
                }
            },
            getProducts() {
                axios.post("/get_products", {
                        forSearch: 'yes'
                    })
                    .then(res => {
                        let r = res.data;
                        this.products = r.filter(item => item.status == 'a');
                    })
            },

            onChangeProduct() {
                if (this.selectedProduct == null) {
                    this.selectedProduct = {
                        Product_SlNo: "",
                        Product_Code: "",
                        display_text: "",
                        Product_Name: "",
                        Product_SellingPrice: 0,
                        quantity: 0,
                        article: "",
                    }
                    return;
                }

                if (this.selectedProduct.Product_SlNo != "") {
                    document.querySelector("#quantity").focus();
                    this.onChangeCode(event)
                }

            },

            async onSearchProduct(val, loading) {
                if (val.length > 2) {
                    loading(true);
                    await axios.post("/get_products", {
                            name: val,
                        })
                        .then(res => {
                            let r = res.data;
                            this.products = r.filter(item => item.status == 'a')
                            loading(false)
                        })
                } else {
                    loading(false)
                    await this.getProducts();
                }
            },

            addToCart() {
                if (this.selectedProduct.quantity == "" || this.selectedProduct.quantity == 0 || this.selectedProduct.quantity == undefined) {
                    Swal.fire({
                        icon: "error",
                        text: "Please fill quantity",
                    });
                    document.querySelector("#products [type='search']").focus();
                    return;
                }
                if (this.selectedProduct.sale_rate == "" || this.selectedProduct.sale_rate == 0) {
                    Swal.fire({
                        icon: "error",
                        text: "Please fill price",
                    });
                    return;
                }

                let product = {
                    id: this.selectedProduct.Product_SlNo,
                    code: this.selectedProduct.Product_Code,
                    name: this.selectedProduct.Product_Name,
                    sale_rate: this.selectedProduct.Product_SellingPrice,
                    quantity: this.selectedProduct.quantity,
                    article: this.selectedProduct.article == undefined ? "" : this.selectedProduct.article
                }

                let findIndex = this.carts.findIndex(item => item.id == product.id);
                if (findIndex > -1) {
                    this.carts.splice(findIndex, 1);
                }
                this.carts.push(product);
                this.clearForm();
            },

            removeCart(sl) {
                this.carts.splice(sl, 1);
            },

            clearForm() {
                this.selectedProduct = {
                    Product_SlNo: "",
                    Product_Code: "",
                    display_text: "",
                    Product_Name: "",
                    Product_SellingPrice: 0,
                    quantity: 0,
                }
            },

            generateBarcodes() {
                let filter = {
                    products: this.carts,
                    single: this.is_single,
                    xAxis: this.xAxis,
                    yAxis: this.yAxis,
                }
                axios.post('/multibarcodeStore', filter)
                    .then(res => {
                        if (res.data.status) {
                            window.open("/multibarcodePrint", "_blank");
                        }
                    })
            },
        },
    })
</script>