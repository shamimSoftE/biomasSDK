<style>
    .v-select {
        margin-bottom: 5px;
        background: #fff;
        border-radius: 3px;
    }

    .v-select.open .dropdown-toggle {
        border-bottom: 1px solid #ccc;
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
<div id="damages">
    <div class="row" style="margin: 0;">
        <div class="col-xs-12 col-md-12 col-lg-12 no-padding">
            <fieldset class="scheduler-border entryFrom">
                <div class="control-group">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-1"> Code: </label>
                            <div class="col-md-2">
                                <input type="text" placeholder="Code" class="form-control" v-model="damage.Damage_InvoiceNo" required readonly />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-1"> Date: </label>
                            <div class="col-md-3">
                                <input type="date" placeholder="Date" class="form-control" v-model="damage.Damage_Date" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-1"> Description: </label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" placeholder="Description" v-model="damage.Damage_Description">
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">Damage Product Information</legend>
            <div class="control-group">
                <div class="col-md-4">
                    <form class="form" @submit.prevent="addToCart">
                        <div class="form-group">
                            <label class="col-md-3 control-label no-padding-right"> Product: </label>
                            <div class="col-md-9">
                                <v-select v-bind:options="products" label="display_text" v-model="selectedProduct" placeholder="Select Product" v-on:input="productOnChange"></v-select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label no-padding-right"> Quantity: </label>
                            <div class="col-md-9">
                                <input type="number" id="quantity" placeholder="Quantity" class="form-control" v-model="selectedProduct.quantity" required v-on:input="productTotal" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label no-padding-right"> Rate: </label>
                            <div class="col-md-9">
                                <input type="number" min="0" step="0.01" placeholder="Rate" class="form-control" v-model="selectedProduct.Product_Purchase_Rate" required v-on:input="productTotal" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label no-padding-right"> Amount: </label>
                            <div class="col-md-9">
                                <input type="number" min="0" step="any" placeholder="Amount" class="form-control" v-model="selectedProduct.total" required disabled />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label no-padding-right"></label>
                            <div class="col-md-9 text-right">
                                <button type="submit" class="btnCart">
                                    Add To Cart
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-1 no-padding">
                    <div style="background: #fdfdfd;width:100%;height:110px;display: flex;flex-direction: column;align-items: center;justify-content: center;">
                        <p v-if="productStock > 0" style="color:green;margin:0;font-size:9px;margin-bottom:8px;">Stock Available</p>
                        <p v-else style="color:red;margin:0;font-size:9px;margin-bottom:8px;">Stock Unavailable</p>
                        <strong :style="{color: productStock > 0 ? 'green' : 'red'}">{{productStock}}</strong>
                        <strong>{{productUnit}}</strong>
                    </div>
                </div>
                <div class="col-md-7">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, sl) in carts">
                                <td>{{sl + 1}}</td>
                                <td>{{item.productName}} - {{item.productCode}}</td>
                                <td>{{item.quantity}}</td>
                                <td>{{item.rate}}</td>
                                <td>{{item.total}}</td>
                                <td>
                                    <i class="fa fa-trash btnDelete" @click="removeCart(sl)"></i>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot style="display: none;" :style="{display: carts.length > 0 ? '' : 'none'}" v-if="carts.length > 0">
                            <tr>
                                <th colspan="4">Total</th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="4">{{damage.damage_amount}}</th>
                                <th>
                                    <button type="button" class="btnReset" @click="resetForm">Reset</button>
                                </th>
                                <th>
                                    <button type="button" class="btnSave" @click="addDamage">Save</button>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </fieldset>
    </div>
    <div class="row">
        <div class="col-md-12 form-inline">
            <div class="form-group">
                <label for="filter" class="sr-only">Filter</label>
                <input type="text" class="form-control" v-model="filter" placeholder="Filter">
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="damages" :filter-by="filter">
                    <template scope="{ row }">
                        <tr>
                            <td>{{ row.sl }}</td>
                            <td>{{ row.Damage_InvoiceNo }}</td>
                            <td>{{ row.Damage_Date }}</td>
                            <td>{{ row.damage_amount }}</td>
                            <td>{{ row.Damage_Description }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <i class="fa fa-pencil btnEdit" @click="editDamage(row)"></i>
                                    <i class="fa fa-trash btnDelete" @click="deleteDamage(row.Damage_SlNo)"></i>
                                <?php } ?>
                            </td>
                        </tr>
                    </template>
                </datatable>
                <datatable-pager v-model="page" type="abbreviated" :per-page="per_page"></datatable-pager>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#damages',
        data() {
            return {
                damage: {
                    Damage_SlNo: 0,
                    Damage_InvoiceNo: '<?php echo $damageCode; ?>',
                    Damage_Date: moment().format('YYYY-MM-DD'),
                    Damage_Description: '',
                    damage_amount: 0,
                    damageFor: "<?php echo $this->session->userdata('BRANCHid'); ?>"
                },
                products: [],
                selectedProduct: {
                    Product_SlNo: "",
                    Product_Code: "",
                    Product_Name: "",
                    display_text: '',
                    quantity: 0,
                    Product_Purchase_Rate: 0,
                    total: 0,
                },
                carts: [],
                productStock: 0,
                productUnit: '',
                damages: [],
                columns: [{
                        label: 'Sl',
                        field: 'sl',
                        align: 'center'
                    },
                    {
                        label: 'Code',
                        field: 'Damage_InvoiceNo',
                        align: 'center',
                        filterable: false
                    },
                    {
                        label: 'Date',
                        field: 'Damage_Date',
                        align: 'center'
                    },
                    {
                        label: 'Damage Amount',
                        field: 'damage_amount',
                        align: 'center'
                    },
                    {
                        label: 'Description',
                        field: 'Damage_Description',
                        align: 'center'
                    },
                    {
                        label: 'Action',
                        align: 'center',
                        filterable: false
                    }
                ],
                page: 1,
                per_page: 100,
                filter: ''
            }
        },
        created() {
            this.getProducts();
            this.getDamages();
        },
        methods: {
            getProducts() {
                axios.post('/get_products', {
                    isService: 'false'
                }).then(res => {
                    this.products = res.data;
                })
            },

            async productOnChange() {
                if ((this.selectedProduct.Product_SlNo != '' || this.selectedProduct.Product_SlNo != 0)) {
                    this.productStock = await axios.post('/get_product_stock', {
                        productId: this.selectedProduct.Product_SlNo
                    }).then(res => {
                        this.productUnit = this.selectedProduct.Unit_Name
                        document.querySelector("#quantity").focus();
                        return res.data;
                    })
                }
            },

            productTotal() {
                this.selectedProduct.total = parseFloat(this.selectedProduct.Product_Purchase_Rate * this.selectedProduct.quantity).toFixed(2)
            },

            addToCart() {
                if (this.selectedProduct == null) {
                    Swal.fire({
                        icon: "error",
                        text: "Product is empty",
                    });
                    return;
                }
                let product = {
                    product_id: this.selectedProduct.Product_SlNo,
                    productCode: this.selectedProduct.Product_Code,
                    productName: this.selectedProduct.Product_Name,
                    quantity: this.selectedProduct.quantity,
                    rate: this.selectedProduct.Product_Purchase_Rate,
                    total: this.selectedProduct.total,
                }

                if (product.quantity > this.productStock) {
                    Swal.fire({
                        icon: "error",
                        text: "Stock unavailable",
                    });
                    return;
                }
                if (product.productName == '') {
                    Swal.fire({
                        icon: "error",
                        text: "Product name is empty",
                    });
                    return;
                }

                let findIndex = this.carts.findIndex(item => item.product_id == product.product_id);
                if (findIndex > -1) {
                    this.carts.splice(findIndex, 1);
                }

                this.carts.push(product);
                this.clearProduct();
                this.calculateTotal();
            },

            removeCart(sl) {
                this.carts.splice(sl, 1);
                this.calculateTotal();
            },

            clearProduct() {
                this.selectedProduct = {
                    Product_SlNo: "",
                    Product_Code: "",
                    Product_Name: "",
                    display_text: '',
                    quantity: 0,
                    Product_Purchase_Rate: 0,
                    total: 0,
                }
                this.productStock = 0;
            },

            addDamage() {
                if (this.carts.length == 0) {
                    Swal.fire({
                        icon: "error",
                        text: "Cart is empty",
                    });
                    return;
                }

                let url = '/add_damage';
                if (this.damage.Damage_SlNo != 0) {
                    url = '/update_damage'
                }
                let filter = {
                    damage: this.damage,
                    carts: this.carts
                }

                axios.post(url, filter).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.resetForm();
                        this.damage.Damage_InvoiceNo = r.damageCode;
                        this.getDamages();
                    } else {
                        if (r.branch_status == false) {
                            location.reload();
                        }
                    }
                })
            },

            editDamage(damage) {
                let keys = Object.keys(this.damage);
                keys.forEach(key => this.damage[key] = damage[key]);

                this.carts = [];
                damage.damageDetail.forEach(item => {
                    let product = {
                        product_id: item.Product_SlNo,
                        productCode: item.Product_Code,
                        productName: item.Product_Name,
                        quantity: item.DamageDetails_DamageQuantity,
                        rate: item.damage_rate,
                        total: item.damage_amount,
                    }
                    this.carts.push(product)
                })

            },

            calculateTotal() {
                this.damage.damage_amount = this.carts.reduce((prev, curr) => {
                    return prev + parseFloat(curr.total)
                }, 0).toFixed(2);
            },

            deleteDamage(damageId) {
                let deleteConfirm = confirm('Are you sure?');
                if (deleteConfirm == false) {
                    return;
                }
                axios.post('/delete_damage', {
                    damageId: damageId
                }).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getDamages();
                    }
                })
            },

            getDamages() {
                axios.get('/get_damages').then(res => {
                    this.damages = res.data.map((item, index) => {
                        item.sl = index + 1;
                        return item;
                    });
                })
            },

            resetForm() {
                this.damage.Damage_SlNo = '';
                this.damage.Damage_Description = '';
                this.damage.damage_amount = 0;
                this.damage.damageFor = "<?= $this->session->userdata('BRANCHid'); ?>";
                this.damage.Damage_InvoiceNo = "<?= $this->mt->generateDamageCode(); ?>";

                this.carts = [];

            }
        }
    })
</script>