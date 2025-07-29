const orderInvoice = Vue.component('order-invoice', {
    template: `
        <div>
            <div class="row">
                <div class="col-xs-12 text-right">
                    <a href="" v-on:click.prevent="print"><i class="fa fa-print"></i> Print</a>
                </div>
            </div>
            
            <div id="invoiceContent">
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <div _h098asdh>
                            Order Invoice
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-7">
                        <strong> Id:</strong> {{ sales.Customer_Code }}<br>
                        <strong> Name:</strong> {{ sales.Customer_Name }}<br>
                        <strong> Address:</strong> {{ sales.Customer_Address }}<br>
                        <strong> Mobile:</strong> {{ sales.Customer_Mobile }}
                    </div>
                    <div class="col-xs-5 text-right">
                        <strong>Order by:</strong> {{ sales.added_by }}<br>
                        <strong>Order No.:</strong> {{ sales.SaleMaster_InvoiceNo }}<br>
                        <strong>Order Date:</strong> {{ sales.SaleMaster_SaleDate }} {{ sales.AddTime | formatDateTime('h:mm a') }}
                        <span v-if="sales.employee_id != null">
                            <br>
                            <strong>Employee:</strong> {{ sales.Employee_Name }}
                        </span>
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
                                    <td>{{ product.SaleDetails_TotalQuantity }}</td>
                                    <td>{{ product.Unit_Name }}</td>
                                    <td>{{ product.SaleDetails_Rate }}</td>
                                    <td align="right">{{ product.SaleDetails_TotalAmount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <br>
                        <table class="pull-left" style="display:none;" :style="{display: currentBranch.dueStatus == 'true' ? '' : 'none'}">
                            <tr>
                                <td><strong>Previous Due:</strong></td>
                                
                                <td style="text-align:right">{{ sales.SaleMaster_Previous_Due == null ? '0.00' : sales.SaleMaster_Previous_Due  }}</td>
                            </tr>
                            <tr>
                                <td><strong>Current Due:</strong></td>
                                
                                <td style="text-align:right">{{ sales.SaleMaster_DueAmount == null ? '0.00' : sales.SaleMaster_DueAmount  }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="border-bottom: 1px solid #ccc;"></td>
                            </tr>
                            <tr>
                                <td><strong>Total Due:</strong></td>
                                
                                <td style="text-align:right">{{ (parseFloat(sales.SaleMaster_Previous_Due) + parseFloat(sales.SaleMaster_DueAmount == null ? 0.00 : sales.SaleMaster_DueAmount)).toFixed(2) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-xs-6">
                        <table _t92sadbc2>
                            <tr>
                                <td><strong>Sub Total:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_SubTotalAmount }}</td>
                            </tr>
                            <tr>
                                <td><strong>VAT:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_TaxAmount }}</td>
                            </tr>
                            <tr>
                                <td><strong>Discount:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_TotalDiscountAmount }}</td>
                            </tr>
                            <tr>
                                <td><strong>Transport Cost:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_Freight }}</td>
                            </tr>
                            <tr><td colspan="2" style="border-bottom: 1px solid #ccc"></td></tr>
                            <tr>
                                <td><strong>Total:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_TotalSaleAmount }}</td>
                            </tr>
                            <tr>
                                <td><strong>Cash Paid:</strong></td>
                                <td style="text-align:right">{{ sales.cash_paid }}</td>
                            </tr>
                            <tr>
                                <td><strong>Bank Paid:</strong></td>
                                <td style="text-align:right">{{ sales.bank_paid }}</td>
                            </tr>
                             <tr><td colspan="2" style="border-bottom: 1px solid #ccc"></td></tr>
                            <tr>
                                <td><strong>Total Paid:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_PaidAmount }}</td>
                            </tr>
                            <tr><td colspan="2" style="border-bottom: 1px solid #ccc"></td></tr>
                            <tr>
                                <td><strong>Due:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_DueAmount }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <strong>In Word: </strong> {{ withDecimal(sales.SaleMaster_TotalSaleAmount) }}<br><br>
                        <p style="margin:0;">
                            <strong>Note: </strong>
                            {{ sales.SaleMaster_Description ? sales.SaleMaster_Description : currentBranch.InvoiceNote }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    `,
    props: ['sales_id'],
    data(){
        return {
            sales:{
                SaleMaster_InvoiceNo: null,
                SalseCustomer_IDNo: null,
                SaleMaster_SaleDate: null,
                Customer_Name: null,
                Customer_Address: null,
                Customer_Mobile: null,
                SaleMaster_TotalSaleAmount: null,
                SaleMaster_TotalDiscountAmount: null,
                SaleMaster_TaxAmount: null,
                SaleMaster_Freight: null,
                SaleMaster_SubTotalAmount: null,
                SaleMaster_PaidAmount: null,
                SaleMaster_DueAmount: null,
                SaleMaster_Previous_Due: null,
                SaleMaster_Description: null,
                AddBy: null
            },
            cart: [],
            style: null,
            companyProfile: {},
            currentBranch: {}
        }
    },
    filters: {
        formatDateTime(dt, format) {
            return dt == '' || dt == null ? '' : moment(dt).format(format);
        }
    },
    created(){
        this.getCurrentBranch();
        this.setStyle();
        this.getSales();
    },
    methods:{
        getSales(){
            axios.post('/get_sales', {salesId: this.sales_id,sale_type:'order',status:'p'}).then(res=>{
                this.sales = res.data.sales[0];
                this.cart = res.data.saleDetails;
            })
        },
        getCurrentBranch() {
            axios.get('/get_current_branch').then(res => {
                this.currentBranch = res.data;
            })
        },
        setStyle(){
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
                return whole + this.currentBranch.Currency_Name + ' ' + fraction + this.currentBranch.SubCurrency_Name +" only";

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
        async print(){
            let invoiceContent = document.querySelector('#invoiceContent').innerHTML;
            let printWindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}, left=0, top=0`);
            if (this.currentBranch.print_type == '2') {
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
                            html, body{
                                width:500px!important;
                            }
                            body, table{
                                font-size: 13px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="row">
                            <div class="col-xs-2"><img src="/${this.currentBranch.Company_Logo_thum}" alt="Logo" style="height:80px;" /></div>
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
                        <div class="row">
                            <div class="col-xs-12">
                                ${invoiceContent}
                            </div>
                        </div>
                    </body>
                    </html>
				`);
            } else {
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
                    <body>
                        <div class="container">
                            <table style="width:100%;">
                                <thead>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-xs-2"><img src="/${this.currentBranch.Company_Logo_thum}" alt="Logo" style="height:80px;" /></div>
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
                                        Print Date: ${moment().format('DD-MM-YYYY h:mm a')}, Printed by: ${this.sales.added_by}
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
            }
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