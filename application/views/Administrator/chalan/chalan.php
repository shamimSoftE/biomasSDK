<div id="chalan">
    <div class="row" style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
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
                            Chalan
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <strong>Customer Id:</strong> {{ chalan.Customer_Code }}<br>
                        <strong>Customer Name:</strong> {{ chalan.Customer_Name }}<br>
                        <strong>Customer Address:</strong> {{ chalan.Customer_Address }}<br>
                        <strong>Customer Mobile:</strong> {{ chalan.Customer_Mobile }}
                    </div>
                    <div class="col-xs-4 text-right">
                        <strong>Chalan by:</strong> {{ chalan.AddBy }}<br>
                        <strong>Invoice No.:</strong> {{ chalan.SaleMaster_InvoiceNo }}<br>
                        <strong>Date:</strong> {{ chalan.SaleMaster_SaleDate }} {{ formatDateTime(chalan.AddTime, 'h:mm a') }}
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
                                    <td>Note</td>
                                    <td>Qnty</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(product, sl) in cart">
                                    <td>{{ sl + 1 }}</td>
                                    <td>{{ product.Product_Name }}</td>
                                    <td>{{ product.product_note }}</td>
                                    <td>{{ product.SaleDetails_TotalQuantity }} {{ product.Unit_Name }}</td>
                                </tr>
                                <!-- <tr>
                                    <td colspan="2" style="text-align: right;"> Total: </td>
                                    <td> {{ cart.reduce((prev, curr) => { return prev + parseFloat(curr.SaleDetails_TotalQuantity)}, 0) }} </td>
                                </tr> -->
                            </tbody>
                        </table>
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
        el: '#chalan',
        data() {
            return {
                chalan: {},
                cart: [],
                style: null,
                companyProfile: null,
                currentBranch: null
            }
        },
        created() {
            this.setStyle();
            this.getChalans();
            this.getCompanyProfile();
            this.getCurrentBranch();
        },
        methods: {
            getChalans(){
                axios.post('/get_chalans', {chalanId: '<?php echo $chalanId;?>'}).then(res=>{
                    this.chalan = res.data.chalans[0];
                    this.cart = res.data.chalanDetails;
                })
            },

           
            getCompanyProfile() {
                axios.get('/get_company_profile').then(res => {
                    this.companyProfile = res.data;
                })
            },
            getCurrentBranch() {
                axios.get('/get_current_branch').then(res => {
                    this.currentBranch = res.data;
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
                table[_t92sadbc2]{
                    width: 100%;
                }
                table[_t92sadbc2] td{
                    padding: 2px;
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
                reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

                reportWindow.document.body.innerHTML += reportContent;

                if (this.searchType == '' || this.searchType == 'user') {
                    let rows = reportWindow.document.querySelectorAll('.record-table tr');
                    rows.forEach(row => {
                        row.lastChild.remove();
                    })
                }

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