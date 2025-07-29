<div id="customerListReport">
    <div class="row">
        <div class="col-md-12" style="margin: 0;">
            <fieldset class="scheduler-border scheduler-search">
                <legend class="scheduler-border">Customer List</legend>
                <div class="control-group">
                    <form class="form-inline" @submit.prevent="getCustomers">
                        <div class="form-group">
                            <label>Search Type</label>
                            <select class="form-select" style="height: 26px;padding:0 6px;width:150px;" v-model="searchType">
                                <option value="">All</option>
                                <option value="retail">Retail</option>
                                <option value="wholesale">Wholesale</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="submit" value="Search">
                        </div>
                    </form>
                </div>
            </fieldset>
        </div>
    </div>
    <div style="display:none;" v-bind:style="{display: customers.length > 0 ? '' : 'none'}">
        <div class="row">
            <div class="col-md-12 text-right">
                <a href="" @click.prevent="printCustomerList"><i class="fa fa-print"></i> Print</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive" id="printContent">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <th>Sl</th>
                            <th>Customer Id</th>
                            <th>Customer Name</th>
                            <th>Address</th>
                            <th>Contact No.</th>
                        </thead>
                        <tbody>
                            <tr v-for="(customer, sl) in customers">
                                <td>{{ sl + 1 }}</td>
                                <td>{{ customer.Customer_Code }}</td>
                                <td style="text-align: start;">{{ customer.Customer_Name }}</td>
                                <td style="text-align: start;">{{ customer.Customer_Address }} {{ customer.District_Name }}</td>
                                <td>{{ customer.Customer_Mobile }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div style="display:none;text-align:center;" v-bind:style="{display: customers.length > 0 ? 'none' : ''}">
        No records found
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>

<script>
    new Vue({
        el: '#customerListReport',
        data() {
            return {
                searchType: '',
                customers: []
            }
        },
        methods: {
            getCustomers() {
                axios.post('/get_customers', {
                    customerType: this.searchType
                }).then(res => {
                    this.customers = res.data;
                })
            },

            async printCustomerList() {
                let printContent = `
                    <div class="container">
                        <h4 style="text-align:center">Customer List</h4 style="text-align:center">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#printContent').innerHTML}
							</div>
						</div>
                    </div>
                `;

                let printWindow = window.open('', '', `width=${screen.width}, height=${screen.height}`);
                printWindow.document.write(`
                    <?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
                `);

                printWindow.document.body.innerHTML += printContent;
                printWindow.focus();
                await new Promise(r => setTimeout(r, 1000));
                printWindow.print();
                printWindow.close();
            }
        }
    })
</script>