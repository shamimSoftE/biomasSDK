const posInvoice = Vue.component("pos-invoice", {
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
                            <strong>Invoice #{{ sale.SaleMaster_InvoiceNo }}</strong>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">                    
                        <div class="col-xs-7" style="padding:0;">
                            <table style="width:100%;">
                                <tr>
                                    <td>Customer</td>
                                    <td>:</td>
                                    <td>{{ sale.Customer_Name }}</td>
                                </tr>
                                <tr>
                                    <td>Contact</td>
                                    <td>:</td>
                                    <td>{{ sale.Customer_Mobile }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-xs-5" style="padding:0;">
                            <table style="width:100%;">
                                <tr>
                                    <td style="text-align:right;">Date</td>
                                    <td>:</td>
                                    <td style="text-align:right;">{{ sale.SaleMaster_SaleDate | formatDateTime('DD-MM-YYYY') }}</td>
                                </tr>
                                <tr>
                                    <td style="text-align:right;">SavedBy</td>
                                    <td>:</td>
                                    <td style="text-align:right;">{{ sale.added_by }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <table _a584de>
                            <thead>
                                <tr>
                                    <td>Sl.</td>
                                    <td>Description</td>
                                    <td>Qty</td>
                                    <td>Rate</td>
                                    <td align="right">Total</td>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(item, sl) in cart">
                                <tr style="border-bottom:1px solid #fff !important;">
                                    <td>{{ sl + 1 }}</td>
                                    <td colspan="6" style="text-align:left;">{{ item.Product_Name}} - {{ item.Product_Code }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td>{{ parseFloat(item.SaleDetails_TotalQuantity).toFixed(2) }}</td>
                                    <td>{{ parseFloat(item.SaleDetails_Rate).toFixed(2) }}</td>                                    
                                    <td align="right">{{ parseFloat(item.SaleDetails_TotalAmount).toFixed(2) }}</td>
                                </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4"></div>
                    <div class="col-xs-8">
                        <table _t92sadbc2>
                            <tr>
                                <td><strong>Sub Total:</strong></td>
                                <td style="text-align:right">{{ parseFloat(sale.SaleMaster_SubTotalAmount).toFixed(2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>(-)Discount:</strong></td>
                                <td style="text-align:right">{{ parseFloat(sale.SaleMaster_TotalDiscountAmount).toFixed(2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>(+)VAT:</strong></td>
                                <td style="text-align:right">{{ parseFloat(sale.SaleMaster_TaxAmount).toFixed(2) }}</td>
                            </tr>
                            <tr><td colspan="2" style="border-bottom: 1px solid #ccc"></td></tr>
                            <tr>
                                <td><strong>Net Total:</strong></td>
                                <td style="text-align:right">{{ parseFloat(sale.SaleMaster_TotalSaleAmount).toFixed(2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Cash Received:</strong></td>
                                <td style="text-align:right">{{ parseFloat(sale.SaleMaster_PaidAmount).toFixed(2) }}</td>
                            </tr>
                            <tr><td colspan="2" style="border-bottom: 1px solid #ccc"></td></tr>
                            <tr>
                                <td><strong>Cash Return:</strong></td>
                                <td style="text-align:right">{{ parseFloat(0).toFixed(2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `,
  props: ["pos_id"],
  data() {
    return {
      sale: {
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
        AddBy: null,
      },
      cart: [],
      style: null,
      companyProfile: null,
      currentBranch: null,
    };
  },
  filters: {
    formatDateTime(dt, format) {
      return dt == "" || dt == null ? "" : moment(dt).format(format);
    },
  },
  created() {
    this.setStyle();
    this.getCurrentBranch();
    this.getSales();
  },
  methods: {
    getSales() {
      axios.post("/get_sales", { salesId: this.pos_id }).then((res) => {
        this.sale = res.data.sales[0];
        this.cart = res.data.saleDetails;
      });
    },

    getCurrentBranch() {
      axios.get("/get_current_branch").then((res) => {
        this.currentBranch = res.data;
      });
    },

    setStyle() {
      this.style = document.createElement("style");
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
                    margin-top:12px;
                }
                table[_a584de] thead tr {
                    border-top: 1px solid gray;
                    border-bottom: 1px solid gray;
                    border-left: 0;
                    border-right: 0;
                    font-weight: 900;
                }
                table[_a584de] tbody tr {
                    border-top: 1px solid gray;
                    border-bottom: 1px solid gray;
                    border-left: 0;
                    border-right: 0;
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
    convertNumberToWords(amountToWord) {
      var words = new Array();
      words[0] = "";
      words[1] = "One";
      words[2] = "Two";
      words[3] = "Three";
      words[4] = "Four";
      words[5] = "Five";
      words[6] = "Six";
      words[7] = "Seven";
      words[8] = "Eight";
      words[9] = "Nine";
      words[10] = "Ten";
      words[11] = "Eleven";
      words[12] = "Twelve";
      words[13] = "Thirteen";
      words[14] = "Fourteen";
      words[15] = "Fifteen";
      words[16] = "Sixteen";
      words[17] = "Seventeen";
      words[18] = "Eighteen";
      words[19] = "Nineteen";
      words[20] = "Twenty";
      words[30] = "Thirty";
      words[40] = "Forty";
      words[50] = "Fifty";
      words[60] = "Sixty";
      words[70] = "Seventy";
      words[80] = "Eighty";
      words[90] = "Ninety";
      amount = amountToWord == null ? "0.00" : amountToWord.toString();
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
          if (
            (i == 1 && value != 0) ||
            (i == 0 && value != 0 && n_array[i + 1] == 0)
          ) {
            words_string += "Crores ";
          }
          if (
            (i == 3 && value != 0) ||
            (i == 2 && value != 0 && n_array[i + 1] == 0)
          ) {
            words_string += "Lakhs ";
          }
          if (
            (i == 5 && value != 0) ||
            (i == 4 && value != 0 && n_array[i + 1] == 0)
          ) {
            words_string += "Thousand ";
          }
          if (
            i == 6 &&
            value != 0 &&
            n_array[i + 1] != 0 &&
            n_array[i + 2] != 0
          ) {
            words_string += "Hundred and ";
          } else if (i == 6 && value != 0) {
            words_string += "Hundred ";
          }
        }
        words_string = words_string.split("  ").join(" ");
      }
      return words_string + " only";
    },
    async print() {
      let invoiceContent = document.querySelector("#invoiceContent").innerHTML;
      let printWindow = window.open(
        "",
        "PRINT",
        `width=${screen.width}, height=${screen.height}, left=0, top=0`
      );

      printWindow.document.write(`
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <meta http-equiv="X-UA-Compatible" content="ie=edge">
                        <title>Sale Invoice</title>
                        <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
                        <style>
                            body, table{
                                font-size: 10px !important;
                            }
                        </style>
                    </head>
                    <body>
                    <div class="container">
                    <table style="width:100%;">
                        <thead>
                            <tr>
                                <td>
                                    <div class="row" style="margin-top:8px;">
                                      ${this.currentBranch.Company_Logo_org == null || this.currentBranch.Company_Logo_org == '' ? '' :` <div class="col-xs-12 text-center"><img src="/${this.currentBranch.Company_Logo_thum}" alt="Logo" style="height:40px;" /></div>`}                                               
                                        <div class="col-xs-12 text-center">
                                            <strong style="font-size:14px;">${this.currentBranch.Company_Name}</strong><br>
                                            <p style="white-space:pre-line;line-height:1;">${this.currentBranch.Repot_Heading}</p>
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
                                    <div class="row" style="margin-top:15px;">
                                        <div class="col-xs-12 text-center">${this.currentBranch.Company_Name}, Contact no: ${this.currentBranch.Branch_phone}</div>
                                        <div class="col-xs-12 text-center" style="padding:0;font-size:10px;border-top:1px solid gray;">Software by: Big Technology, Contact no: 01946-700300</div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                        
                    </body>
                    </html>
				`);
      let invoiceStyle = printWindow.document.createElement("style");
      invoiceStyle.innerHTML = this.style.innerHTML;
      printWindow.document.head.appendChild(invoiceStyle);
      printWindow.moveTo(0, 0);

      printWindow.focus();
      await new Promise((resolve) => setTimeout(resolve, 1000));
      printWindow.print();
      printWindow.close();
    },
  },
});
