<style>
    .v-select {
        margin-bottom: 5px;
        width: 250px;
    }
    .v-select .dropdown-toggle {
        padding: 0px;
    }
    .v-select input[type=search],
    .v-select input[type=search]:focus {
        margin: 0px;
    }
    .v-select .selected-tag {
        margin: 0px;
    }
</style>

<div id="salesInvoiceReport" class="row">
    <div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;margin-bottom:5px;">
        
        <div class="form-group">
            <label class="col-sm-2 control-label no-padding-right"> Purchase Invoice </label>
            <label class="col-sm-1 control-label no-padding-right"> : </label>
            <div class="col-sm-3">
                <v-select v-bind:options="invoices" label="invoice_text" v-model="selectedInvoice" v-on:input="viewInvoice" placeholder="Select Invoice"></v-select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-2">
                <input type="button" class="btn btn-primary" value="Show Report" v-on:click="viewInvoice" style="margin-top:0px;width:150px;display: none;">
            </div>
        </div>
    </div>
    <div class="col-md-8 col-md-offset-2">
        <br>
        <costing-invoice v-bind:purchase_id="selectedInvoice.PurchaseMaster_SlNo" v-if="showInvoice && searchType == 'purchase'"></costing-invoice>
        <costing-material-invoice v-bind:material_id="selectedMaterialInvoice.purchase_id" v-if="showInvoice && searchType == 'material'"></costing-material-invoice>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/components/costingInvoice.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#salesInvoiceReport',
        data() {
            return {
                searchType: 'purchase',
                invoices: [],
                selectedInvoice: null,
                materialinvoices: [],
                selectedMaterialInvoice: null,
                showInvoice: false
            }
        },
        created() {
            this.getPurchases();
        },
        methods: {
            // getLC() {
            //     axios.get("/get_LC").then(res => {
            //         this.invoices = res.data.lc;
            //     })
            // },

            onChangeSearchType() {
                this.viewInvoice()
            },

            getPurchases() {
                axios.get("/get_purchases").then(res => {
                    this.invoices = res.data.purchases;
                })
            },

            getMaterialPurchase() {
                axios.get("/get_material_purchase").then(res => {
                    this.materialinvoices = res.data.purchases;
                })
            },
            
            async viewInvoice() {
                this.showInvoice = false;
                await new Promise(r => setTimeout(r, 500));
                this.showInvoice = true;
            }
        }
    })
</script>