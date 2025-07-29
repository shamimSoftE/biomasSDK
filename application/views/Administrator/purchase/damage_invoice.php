<div id="damageInvoice">
    <div class="row" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
        <div class="col-md-8 col-md-offset-2">
            <div class="row">
                <div class="col-xs-12">
                    <a href="" v-on:click.prevent="print"><i class="fa fa-print"></i> Print</a>
                </div>
            </div>

            <div id="invoiceContent">
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <div _h098asdh>
                            Damage Invoice
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        <strong>Invoice No.:</strong> {{ damage.Damage_InvoiceNo }}<br>
                        <strong>Damage Date:</strong> {{ formatDateTime(damage.Damage_Date, 'DD-MM-YYYY') }} {{ formatDateTime(damage.AddTime, 'h:mm a') }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div _d9283dsc></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <table _a584de>
                            <thead>
                                <tr>
                                    <td>Sl.</td>
                                    <td>Description</td>
                                    <td>Qnty</td>
                                    <td>Damage Rate</td>
                                    <td>Damage Amount</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(product, sl) in cart">
                                    <td>{{ sl + 1 }}</td>
                                    <td>{{ product.Product_Name }}</td>
                                    <td>{{ product.DamageDetails_DamageQuantity }}</td>
                                    <td>{{ product.damage_rate }}</td>
                                    <td>{{ product.damage_amount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-12" style="margin-top: 25px;">
                        <span> <strong>Note:</strong> {{ damage.Damage_Description }}</span>
                    </div>
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
    new Vue({
        el: '#damageInvoice',
        data() {
            return {
                damage: {
                    Damage_SlNo: parseInt('<?php echo $damageId;?>'),
                    Damage_InvoiceNo: null,
                    Damage_Date: null,
                    damage_amount: null,
                    Damage_Description: null,
                    AddBy: null
                },
                cart: [],
                style: null,
                companyProfile: null,
                currentBranch: null
            }
        },
        created() {
            this.setStyle();
            this.getDamage();
            this.getCompanyProfile();
            this.getCurrentBranch();
        },
        methods: {
            getDamage() {
                axios.post('/get_damage', {
                    damageId: this.damage.Damage_SlNo
                }).then(res => {
                    this.damage = res.data.damage[0];
                    this.cart = res.data.damageDetails;
                });
            },
            getCompanyProfile() {
                axios.get('/get_company_profile').then(res => {
                    this.companyProfile = res.data;
                }).catch(err => {
                    console.error("Error fetching company profile: ", err);
                })
            },
            getCurrentBranch() {
                axios.get('/get_current_branch').then(res => {
                    this.currentBranch = res.data;
                }).catch(err => {
                    console.error("Error fetching current branch data: ", err);
                })
            },
            formatDateTime(datetime, format) {
                return moment(datetime).format(format);
            },
            setStyle() {
                this.style = document.createElement('style');
                this.style.innerHTML = `
                    div[_h098asdh]{
                        background-color:#e0e0e0;
                        font-weight: bold;
                        font-size:15px;
                        margin-bottom:15px;
                        padding: 5px;
                    }
                    div[_d9283dsc]{
                        padding-bottom:25px;
                        border-bottom: 1px solid #ccc;
                        margin-bottom: 15px;
                    }
                    table[_a584de]{
                        width: 100%;
                        text-align:center;
                    }
                    table[_a584de] thead{
                        font-weight:bold;
                    }
                    table[_a584de] td{
                        padding: 3px;
                        border: 1px solid #ccc;
                    }
                `;
                document.head.appendChild(this.style);
            },
            async print() {
                let reportContent = `
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-12">
                                ${document.querySelector('#invoiceContent').innerHTML}
                            </div>
                        </div>
                    </div>
                `;

                var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}`);
                reportWindow.document.write(`<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>`);
                reportWindow.document.body.innerHTML += reportContent;

                let invoiceStyle = reportWindow.document.createElement('style');
                invoiceStyle.innerHTML = this.style.innerHTML;
                reportWindow.document.head.appendChild(invoiceStyle);

                reportWindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                reportWindow.print();
                reportWindow.close();
            }
        }
    })


</script>