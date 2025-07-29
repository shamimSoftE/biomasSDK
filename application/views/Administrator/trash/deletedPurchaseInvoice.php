<div id="deletedPurchaseInvoice" class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="row">
            <div class="col-xs-12 text-right">
                <a href="" v-on:click.prevent="print"><i class="fa fa-print"></i> Print</a>
            </div>
        </div>

        <div id="invoiceContent">
            <div class="row">
                <div class="col-xs-12 text-center">
                    <div _h098asdh>
                        Purchase Invoice
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-7">
                    <strong>Supplier Id:</strong> {{ purchase.Supplier_Code }}<br>
                    <strong>Supplier Name:</strong> {{ purchase.Supplier_Name }}<br>
                    <strong>Supplier Address:</strong> {{ purchase.Supplier_Address }}<br>
                    <strong>Supplier Mobile:</strong> {{ purchase.Supplier_Mobile }}
                </div>
                <div class="col-xs-5 text-right">
                    <strong>Purchase by:</strong> {{ purchase.added_by }}<br>
                    <strong>Invoice No.:</strong> {{ purchase.PurchaseMaster_InvoiceNo }}<br>
                    <strong>Purchase Date:</strong> {{ purchase.PurchaseMaster_OrderDate }} {{ moment(purchase.AddTime).format('h:mm a') }}
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
                                <td>Unit</td>
                                <td>Unit Price</td>
                                <td style="text-align:right;">Total</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(product, sl) in cart">
                                <td>{{ sl + 1 }}</td>
                                <td style="text-align:left;">{{ product.Product_Name }} - {{ product.Product_Code }}</td>
                                <td>{{ product.PurchaseDetails_TotalQuantity }}</td>
                                <td>{{ product.Unit_Name }}</td>
                                <td>{{ product.PurchaseDetails_Rate }}</td>
                                <td align="right">{{ product.PurchaseDetails_TotalAmount }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <br>
                    <table class="pull-left">
                        <tr>
                            <td><strong>Previous Due:</strong></td>

                            <td style="text-align:right">{{ purchase.previous_due == null ? '0.00' : purchase.previous_due  }}</td>
                        </tr>
                        <tr>
                            <td><strong>Current Due:</strong></td>

                            <td style="text-align:right">{{ purchase.PurchaseMaster_DueAmount == null ? '0.00' : purchase.PurchaseMaster_DueAmount  }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-bottom: 1px solid #ccc;"></td>
                        </tr>
                        <tr>
                            <td><strong>Total Due:</strong></td>

                            <td style="text-align:right">{{ (parseFloat(purchase.previous_due) + parseFloat(purchase.PurchaseMaster_DueAmount == null ? 0.00 : purchase.PurchaseMaster_DueAmount)).toFixed(2) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-xs-6">
                    <table _t92sadbc2>
                        <tr>
                            <td><strong>Sub Total:</strong></td>
                            <td style="text-align:right">{{ purchase.PurchaseMaster_SubTotalAmount }}</td>
                        </tr>
                        <tr>
                            <td><strong>VAT:</strong></td>
                            <td style="text-align:right">{{ purchase.PurchaseMaster_Tax }}</td>
                        </tr>
                        <tr>
                            <td><strong>Discount:</strong></td>
                            <td style="text-align:right">{{ purchase.PurchaseMaster_DiscountAmount }}</td>
                        </tr>
                        <tr>
                            <td><strong>Transport Cost:</strong></td>
                            <td style="text-align:right">{{ purchase.PurchaseMaster_Freight }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-bottom: 1px solid #ccc"></td>
                        </tr>
                        <tr>
                            <td><strong>Total:</strong></td>
                            <td style="text-align:right">{{ purchase.PurchaseMaster_TotalAmount }}</td>
                        </tr>
                        <tr>
                            <td><strong>Paid:</strong></td>
                            <td style="text-align:right">{{ purchase.PurchaseMaster_PaidAmount }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-bottom: 1px solid #ccc"></td>
                        </tr>
                        <tr>
                            <td><strong>Due:</strong></td>
                            <td style="text-align:right">{{ purchase.PurchaseMaster_DueAmount }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <strong>In Word: </strong> {{ withDecimal(purchase.PurchaseMaster_TotalAmount) }}<br><br>
                    <strong>Note: </strong>
                    <p style="white-space: pre-line">{{ purchase.PurchaseMaster_Description }}</p>
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
        el: '#deletedPurchaseInvoice',
        data() {
            return {
                purchase: {
                    PurchaseMaster_InvoiceNo: null,
                    SalseSupplier_IDNo: null,
                    PurchaseMaster_OrderDate: null,
                    Supplier_Name: null,
                    Supplier_Address: null,
                    Supplier_Mobile: null,
                    PurchaseMaster_TotalAmount: null,
                    PurchaseMaster_DiscountAmount: null,
                    PurchaseMaster_Tax: null,
                    PurchaseMaster_Freight: null,
                    PurchaseMaster_SubTotalAmount: null,
                    PurchaseMaster_PaidAmount: null,
                    PurchaseMaster_DueAmount: null,
                    previous_due: null,
                    PurchaseMaster_Description: null,
                    AddBy: null
                },
                cart: [],
                style: null,
                companyProfile: {},
                currentBranch: {}
            }
        },
        created() {
            this.setStyle();
            this.getPurchase();
            this.getCurrentBranch();
        },
        methods: {
            getPurchase() {
                axios.post('/get_purchases', {
                    purchaseId: "<?php echo $purchaseId; ?>",
                    status: 'd'
                }).then(res => {
                    this.purchase = res.data.purchases[0];
                    this.cart = res.data.purchaseDetails;
                })
            },
            getCurrentBranch() {
                axios.get('/get_current_branch').then(res => {
                    this.currentBranch = res.data;
                })
            },
            setStyle() {
                this.style = document.createElement('style');
                this.style.innerHTML = `
                div[_h098asdh]{
                    /*background-color:#e0e0e0;*/
                    font-weight: bold;
                    font-size:15px;
                    margin-bottom:15px;
                    padding: 5px;
                    border-top: 1px dotted #454545;
                    border-bottom: 1px dotted #454545;
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
            withDecimal(n) {
                n = n == undefined ? 0 : parseFloat(n).toFixed(2);
                var nums = n.toString().split('.')
                var whole = this.convertNumberToWords(nums[0])
                if (nums.length == 2 && nums[1] > 0) {
                    var fraction = this.convertNumberToWords(nums[1])
                    return whole + this.currentBranch.Currency_Name + ' ' + fraction + this.currentBranch.SubCurrency_Name + " only";

                } else {
                    return whole + this.currentBranch.Currency_Name + " only";
                }
            },
            convertNumberToWords(amount) {
                var words = new Array();
                words[0] = '';
                words[1] = 'One';
                words[2] = 'Two';
                words[3] = 'Three';
                words[4] = 'Four';
                words[5] = 'Five';
                words[6] = 'Six';
                words[7] = 'Seven';
                words[8] = 'Eight';
                words[9] = 'Nine';
                words[10] = 'Ten';
                words[11] = 'Eleven';
                words[12] = 'Twelve';
                words[13] = 'Thirteen';
                words[14] = 'Fourteen';
                words[15] = 'Fifteen';
                words[16] = 'Sixteen';
                words[17] = 'Seventeen';
                words[18] = 'Eighteen';
                words[19] = 'Nineteen';
                words[20] = 'Twenty';
                words[30] = 'Thirty';
                words[40] = 'Forty';
                words[50] = 'Fifty';
                words[60] = 'Sixty';
                words[70] = 'Seventy';
                words[80] = 'Eighty';
                words[90] = 'Ninety';
                amount = amount.toString();
                var atemp = amount.split(".");
                var number = atemp[0].split(",").join("");
                var n_length = number.length;
                var words_string = "";
                if (n_length <= 9) {
                    var n_array = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0);
                    var received_n_array = new Array();
                    for (var i = 0; i < n_length; i++) {
                        received_n_array[i] = number.substr(i, 1);
                    }
                    for (var i = 9 - n_length, j = 0; i < 9; i++, j++) {
                        n_array[i] = received_n_array[j];
                    }
                    for (var i = 0, j = 1; i < 9; i++, j++) {
                        if (i == 0 || i == 2 || i == 4 || i == 7) {
                            if (n_array[i] == 1) {
                                n_array[j] = 10 + parseInt(n_array[j]);
                                n_array[i] = 0;
                            }
                        }
                    }
                    value = "";
                    for (var i = 0; i < 9; i++) {
                        if (i == 0 || i == 2 || i == 4 || i == 7) {
                            value = n_array[i] * 10;
                        } else {
                            value = n_array[i];
                        }
                        if (value != 0) {
                            words_string += words[value] + " ";
                        }
                        if ((i == 1 && value != 0) || (i == 0 && value != 0 && n_array[i + 1] == 0)) {
                            words_string += "Crores ";
                        }
                        if ((i == 3 && value != 0) || (i == 2 && value != 0 && n_array[i + 1] == 0)) {
                            words_string += "Lakhs ";
                        }
                        if ((i == 5 && value != 0) || (i == 4 && value != 0 && n_array[i + 1] == 0)) {
                            words_string += "Thousand ";
                        }
                        if (i == 6 && value != 0 && (n_array[i + 1] != 0 && n_array[i + 2] != 0)) {
                            words_string += "Hundred and ";
                        } else if (i == 6 && value != 0) {
                            words_string += "Hundred ";
                        }
                    }
                    words_string = words_string.split("  ").join(" ");
                }
                return words_string;
            },
            async print() {
                let invoiceContent = document.querySelector('#invoiceContent').innerHTML;
                let printWindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}, left=0, top=0`);

                printWindow.document.write(`
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta http-equiv="X-UA-Compatible" content="ie=edge">
                    <title>Invoice</title>
                    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
                    <style>
                        body, table{
                            font-size: 13px;
                        }
                    </style>
                </head>
                <body style="position:relative;">
                        <p style="margin: 0; position: absolute; top: 60%; left: 33%; transform: translate(-50%, -50%); font-weight: 900; color: red; z-index: 1000;">Deleted Invoice</p>
                        <p style="margin: 0; position: absolute; bottom: 0; left: 33%; transform: translate(-50%, -50%); font-weight: 900; color: red; z-index: 1000;">Deleted Invoice</p>
                        <div class="container">
                            <table style="width:100%;">
                                <thead>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-xs-2"><img src="/uploads/company_profile_thum/${this.currentBranch.Company_Logo_org}" alt="Logo" style="height:80px;" /></div>
                                                <div class="col-xs-10" style="padding-top:20px;">
                                                    <strong style="font-size:18px;">${this.currentBranch.Company_Name}</strong><br>
                                                    <p style="white-space:pre-line;">${this.currentBranch.Repot_Heading}</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div style="border-bottom: 4px double #454545;margin-top:7px;margin-bottom:7px;"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    ${invoiceContent}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>
                                            <div style="width:100%;height:50px;">&nbsp;</div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>                           
                            <div style="position:fixed;left:0;bottom:15px;width:100%;">
                                <div class="row" style="border-bottom:1px solid #ccc;margin-bottom:0;padding-bottom:20px;">
                                    <div class="col-xs-6">
                                        <span style="text-decoration:overline;">Received by</span>
                                    </div>
                                    <div class="col-xs-6 text-right">
                                        <span style="text-decoration:overline;">Authorized by</span>
                                    </div>
                                </div>
                                <div class="row" style="font-size:12px;">
                                    <div class="col-xs-6">
                                        Print Date: ${moment().format('DD-MM-YYYY h:mm a')}, Printed by: ${this.purchase.added_by}
                                    </div>
                                    <div class="col-xs-6 text-right">
                                        Developed by: Link-Up Technologoy, Contact no: 01911978897
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </body>
                </html>
            `);
                let invoiceStyle = printWindow.document.createElement('style');
                invoiceStyle.innerHTML = this.style.innerHTML;
                printWindow.document.head.appendChild(invoiceStyle);
                printWindow.moveTo(0, 0);

                printWindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                printWindow.print();
                printWindow.close();
            }
        }
    })
</script>