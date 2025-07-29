<style>
    .widgets {
        width: 100%;
        padding: 8px;
        box-shadow: 0px 1px 2px #454545;
        border-radius: 3px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .widgets .widget-icon {
        width: 40px;
        height: 40px;
        padding-top: 8px;
        border-radius: 50%;
        color: white;
    }

    .widgets .widget-content {
        flex-grow: 2;
        font-weight: bold;
    }

    .widgets .widget-content .widget-text {
        font-size: 13px;
        color: #6f6f6f;
    }

    .widgets .widget-content .widget-value {
        font-size: 16px;
    }

    .custom-table-bordered,
    .custom-table-bordered>tbody>tr>td,
    .custom-table-bordered>tbody>tr>th,
    .custom-table-bordered>tfoot>tr>td,
    .custom-table-bordered>tfoot>tr>th,
    .custom-table-bordered>thead>tr>td,
    .custom-table-bordered>thead>tr>th {
        border: 1px solid #224079;
    }

    .overAllData {
        height: 290px;
        border: 2px solid #5a4692;
    }

    .graphData {
        height: 305px;
        border: 2px solid #5a4692;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .topData {
        height: 305px;
        border: 2px solid #5a4692;
    }

    @media only screen and (max-width: 600px) and (min-width: 320px) {
        .overAllData {
            height: auto;
        }

        .graphData {
            height: auto;
        }

        .topData {
            height: auto;
        }
    }
</style>
<div id="graph">
    <div class="row overAllData">
        <div v-if="showOverallData" style="display:none;" v-bind:style="{ display: showOverallData ? '' : 'none' }">
            <div class="col-md-12 no-padding">
                <marquee scrollamount="3" onmouseover="this.stop();" onmouseout="this.start();" direction="left" height="30" bgcolor="#224079" style="color:white;padding-top:5px;margin-bottom: 15px;">{{ salesText }}</marquee>
            </div>

            <div class="col-md-2 col-xs-6">
                <a target="_blank" href="<?php echo base_url(); ?>salesrecord">
                    <div class="widgets" style="border-top: 5px solid #1c8dff;">
                        <div class="widget-icon" style="background-color: #1c8dff;text-align:center;">
                            <i class="fa fa-shopping-cart fa-2x"></i>
                        </div>
    
                        <div class="widget-content">
                            <div class="widget-text">Today's Sale</div>
                            <div class="widget-value"><?php echo $this->session->userdata('Currency_Name'); ?> {{ todaysSale | decimal }}</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-2 col-xs-6">
                <a target="_blank" href="<?php echo base_url(); ?>customerPaymentPage">
                    <div class="widgets" style="border-top: 5px solid #666633;">
                        <div class="widget-icon" style="background-color: #666633;text-align:center;">
                            <i class="fa fa-money fa-2x"></i>
                        </div>

                        <div class="widget-content">
                            <div class="widget-text">Collection</div>
                            <div class="widget-value"><?php echo $this->session->userdata('Currency_Name'); ?> {{ todaysCollection | decimal }}</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-2 col-xs-6">
                <a target="_blank" href="<?php echo base_url(); ?>salesrecord">
                    <div class="widgets" style="border-top: 5px solid #008241;">
                        <div class="widget-icon" style="background-color: #008241;text-align:center;">
                            <i class="fa fa-shopping-cart fa-2x"></i>
                        </div>

                        <div class="widget-content">
                            <div class="widget-text">Monthly Sale</div>
                            <div class="widget-value"><?php echo $this->session->userdata('Currency_Name'); ?> {{ thisMonthSale | decimal }}</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-2 col-xs-6">
                <a target="_blank" href="<?php echo base_url(); ?>customerDue">
                    <div class="widgets" style="border-top: 5px solid #ff8000;">
                        <div class="widget-icon" style="background-color: #ff8000;text-align:center;">
                            <i class="fa fa-reply fa-2x"></i>
                        </div>
                        <div class="widget-content">
                            <div class="widget-text">Customer Due</div>
                            <div class="widget-value"><?php echo $this->session->userdata('Currency_Name'); ?> {{ customerDue | decimal }}</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-xs-6">
                <a target="_blank" href="<?php echo base_url(); ?>cash_view">
                    <div class="widgets" style="border-top: 5px solid #ae0000;">
                        <div class="widget-icon" style="background-color: #ae0000;text-align:center;">
                            <i class="fa fa-dollar fa-2x"></i>
                        </div>

                        <div class="widget-content">
                            <div class="widget-text">Cash Balance</div>
                            <div class="widget-value"><?php echo $this->session->userdata('Currency_Name'); ?> {{ cashBalance | decimal }}</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-xs-6">
                <a target="_blank" href="<?php echo base_url(); ?>balance_sheet">
                    <div class="widgets" style="border-top: 5px solid #663300;">
                        <div class="widget-icon" style="background-color: #663300;text-align:center;">
                            <i class="fa fa-dollar fa-2x"></i>
                        </div>
                        <div class="widget-content">
                            <div class="widget-text">Bank Balance</div>
                            <div class="widget-value"><?php echo $this->session->userdata('Currency_Name'); ?> {{ bankBalance | decimal }}</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-xs-6" style="margin-top:5px;">
                <a target="_blank" href="<?php echo base_url(); ?>currentStock">
                    <div class="widgets" style="border-top: 5px solid #1c8dff;">
                        <div class="widget-icon" style="background-color: #1c8dff;text-align:center;">
                            <i class="fa fa-home fa-2x"></i>
                        </div>

                        <div class="widget-content">
                            <div class="widget-text">Stock Value</div>
                            <div class="widget-value"><?php echo $this->session->userdata('Currency_Name'); ?> {{ stockValue | decimal }}</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-xs-6" style="margin-top:5px;">
                <a target="_blank" href="<?php echo base_url(); ?>assets_report">
                    <div class="widgets" style="border-top: 5px solid #666633;">
                        <div class="widget-icon" style="background-color: #666633;text-align:center;">
                            <i class="fa fa-building fa-2x"></i>
                        </div>

                        <div class="widget-content">
                            <div class="widget-text">Asset Value</div>
                            <div class="widget-value"><?php echo $this->session->userdata('Currency_Name'); ?> {{ assetValue | decimal }}</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-xs-6" style="margin-top:5px;">
                <a target="_blank" href="<?php echo base_url(); ?>supplierDue">
                    <div class="widgets" style="border-top: 5px solid #008241;">
                        <div class="widget-icon" style="background-color: #008241;text-align:center;">
                            <i class="fa fa-reply fa-2x"></i>
                        </div>

                        <div class="widget-content">
                            <div class="widget-text">Supplier Due</div>
                            <div class="widget-value"><?php echo $this->session->userdata('Currency_Name'); ?> {{ supplierDue | decimal }}</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-xs-6" style="margin-top:5px;">
                <a target="_blank" href="<?php echo base_url(); ?>investment_view">
                    <div class="widgets" style="border-top: 5px solid #ff8000;">
                        <div class="widget-icon" style="background-color: #ff8000;text-align:center;">
                            <i class="fa fa-dollar fa-2x"></i>
                        </div>

                        <div class="widget-content">
                            <div class="widget-text">Invest Balance</div>
                            <div class="widget-value"><?php echo $this->session->userdata('Currency_Name'); ?> {{ investBalance | decimal }}</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-xs-6" style="margin-top:5px;">
                <a target="_blank" href="<?php echo base_url(); ?>loan_view">
                    <div class="widgets" style="border-top: 5px solid #ae0000;">
                        <div class="widget-icon" style="background-color: #ae0000;text-align:center;">
                            <i class="fa fa-dollar fa-2x"></i>
                        </div>

                        <div class="widget-content">
                            <div class="widget-text">Loan Balance</div>
                            <div class="widget-value"><?php echo $this->session->userdata('Currency_Name'); ?> {{ loanBalance | decimal }}</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-xs-6" style="margin-top:5px;">
                <a target="_blank" href="<?php echo base_url(); ?>profitLoss">
                    <div class="widgets" style="border-top: 5px solid #663300;">
                        <div class="widget-icon" style="background-color: #663300;text-align:center;">
                            <i class="fa fa-line-chart fa-2x"></i>
                        </div>

                        <div class="widget-content">
                            <div class="widget-text">Monthly Profit</div>
                            <div class="widget-value"><?php echo $this->session->userdata('Currency_Name'); ?> {{ thisMonthProfit | decimal }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div style="display:none;" v-bind:style="{display: showOverallData == false ? '' : 'none'}">
            <div class="col-md-12 text-center">
                <img src="/assets/loader.gif" alt="">
            </div>
        </div>
    </div>

    <div class="row graphData">
        <div v-if="showGraphData" style="display:none;" v-bind:style="{ display: showGraphData ? '' : 'none' }">
            <div class="col-md-12 col-xs-12 no-padding">
                <div style="background: #224079;width:100%;height:30px;display: flex;align-items: center;gap: 5px;">
                    <div style="color: white;">
                        Select Year
                    </div>
                    <div>
                        <input type="month" class="form-control" v-model="monthYear">
                    </div>
                    <div>
                        <button @click="getGraphData" style="margin: 0;margin-top: -2px;" type="button">Submit</button>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12" v-if="salesGraph == 'monthly'">
                <h3 class="text-center" style="margin: 0;">This Month's Sale</h3>
                <sales-chart type="ColumnChart" :data="salesData" :options="salesChartOptions" />
            </div>
            <div class="col-md-12 col-xs-12" v-else>
                <h3 class="text-center" style="margin: 0;">This Year's Sale</h3>
                <sales-chart type="ColumnChart" :data="yearlySalesData" :options="yearlySalesChartOptions" />
            </div>
            <div class="col-md-12 col-xs-12 text-center">
                <div class="btn-group" role="group" aria-label="...">
                    <button type="button" class="btn btn-primary" @click="salesGraph = 'monthly'">Monthly</button>
                    <button type="button" class="btn btn-warning" @click="salesGraph = 'yearly'">Yearly</button>
                </div>
            </div>
        </div>
        <div style="display:none;" v-bind:style="{display: showGraphData == false ? '' : 'none'}">
            <div class="col-md-12 text-center">
                <img src="/assets/loader.gif" alt="">
            </div>
        </div>
    </div>
    <div class="row topData">
        <div v-if="showTopData" style="display:none;" v-bind:style="{ display: showTopData ? '' : 'none' }">
            <div class="col-md-12 col-xs-12 no-padding">
                <div style="background: #224079;width:100%;height:30px;display: flex;align-items: center;gap: 5px;">
                    <div style="color: white;">
                        Select Date
                    </div>
                    <div>
                        <input type="date" class="form-control" v-model="dateFrom">
                    </div>
                    <div style="color: white;">
                        TO
                    </div>
                    <div>
                        <input type="date" class="form-control" v-model="dateTo">
                    </div>
                    <div>
                        <button @click="getTopData('yes')" style="margin: 0;margin-top: -2px;" type="button">Submit</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xs-12">
                <h3 class="text-center">Top Sold Products</h3>
                <top-product-chart type="PieChart" :data="topProducts" :options="topProductsOptions" />
            </div>
            <div class="col-md-4 col-xs-12 col-md-offset-2">
                <table class="table custom-table-bordered" style="margin: 0;margin-top:20px;">
                    <thead>
                        <tr>
                            <td class="text-center" colspan="2" style="background-color: #224079;color: white;font-weight: 900;">Top Customers</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="customer in topCustomers">
                            <td width="75%">{{customer.customer_name}}</td>
                            <td width="25%">{{customer.amount}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div style="display:none;" v-bind:style="{display: showTopData == false ? '' : 'none'}">
            <div class="col-md-12 text-center">
                <img src="/assets/loader.gif" alt="">
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/components/vue-google-charts.browser.js"></script>

<script>
    let googleChart = VueGoogleCharts.GChart;
    new Vue({
        el: '#graph',
        components: {
            'sales-chart': googleChart,
            'top-product-chart': googleChart,
            'top-customer-chart': googleChart
        },
        filters: {
            decimal(value) {
                return value == null || value == '' ? '0.00' : parseFloat(value).toFixed(2);
            }
        },
        data() {
            return {
                dateFrom: moment().format('YYYY-MM-DD'),
                dateTo: moment().format('YYYY-MM-DD'),
                monthYear: moment().format('YYYY-MM'),
                salesData: [
                    ['Date', 'Sales']
                ],
                salesChartOptions: {
                    chart: {
                        title: 'Sales',
                        subtitle: "This month's sales data",
                    }
                },
                yearlySalesData: [
                    ['Month', 'Sales']
                ],
                yearlySalesChartOptions: {
                    chart: {
                        title: 'Sales',
                        subtitle: "This year's sales data",
                    }
                },
                topProducts: [
                    ['Product', 'Quantity']
                ],
                topProductsOptions: {
                    chart: {
                        title: 'Top Sold Products',
                        subtitle: "Top sold products"
                    }
                },
                topCustomers: [],
                salesText: '',
                todaysSale: 0,
                thisMonthSale: 0,
                todaysCollection: 0,
                cashBalance: 0,
                customerDue: 0,
                bankBalance: 0,
                thisMonthProfit: 0,
                stockValue: 0,
                assetValue: 0,
                supplierDue: 0,
                investBalance: 0,
                loanBalance: 0,
                showOverallData: null,
                showGraphData: null,
                showTopData: null,
                salesGraph: 'monthly'
            }
        },
        created() {
            this.getOverallData();
            this.getGraphData();
            this.getTopData();
        },
        methods: {
            getOverallData() {
                this.showOverallData = false
                axios.get('/get_overall_data').then(res => {
                    this.todaysSale = res.data.todays_sale;
                    this.thisMonthSale = res.data.this_month_sale;
                    this.todaysCollection = res.data.todays_collection;
                    this.cashBalance = res.data.cash_balance;
                    this.customerDue = res.data.customer_due;
                    this.bankBalance = res.data.bank_balance;
                    this.thisMonthProfit = res.data.this_month_profit;
                    this.stockValue = res.data.stock_value;
                    this.assetValue = res.data.asset_value;
                    this.supplierDue = res.data.supplier_due;
                    this.investBalance = res.data.invest_balance;
                    this.loanBalance = res.data.loan_balance;

                    this.salesText = res.data.sales_text.map(sale => {
                        return sale.sale_text;
                    }).join(' | ');

                    this.showOverallData = true;
                });
            },
            getGraphData() {
                let filter = {
                    monthYear: this.monthYear
                }
                this.showGraphData = false;
                axios.post('/get_graph_data', filter).then(res => {
                    this.salesData = [
                        ['Date', 'Sales']
                    ]
                    res.data.monthly_record.forEach(d => {
                        this.salesData.push(d);
                    })

                    this.yearlySalesData = [
                        ['Month', 'Sales']
                    ]
                    res.data.yearly_record.forEach(d => {
                        this.yearlySalesData.push(d);
                    })

                    this.showGraphData = true;
                })
            },

            getTopData(fromSubmit = '') {
                let filter = {
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo,
                    fromSubmit: fromSubmit
                }
                this.showTopData = false;
                axios.post('/get_top_data', filter).then(res => {
                    this.topCustomers = res.data.top_customers;

                    this.topProducts = [
                        ['Product', 'Quantity']
                    ]
                    res.data.top_products.forEach(p => {
                        this.topProducts.push([p.product_name, parseFloat(p.sold_quantity)]);
                    })

                    this.showTopData = true
                })
            }
        }
    })
</script>