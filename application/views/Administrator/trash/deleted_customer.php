<div id="customerListReport">
    <div class="row">
        <div class="col-md-12" style="margin: 0;">
            <fieldset class="scheduler-border scheduler-search">
                <legend class="scheduler-border">Deleted Customer List</legend>
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
                            <th>
                                <input type="checkbox" @click="allCheck">
                            </th>
                            <th>Sl</th>
                            <th>Customer Id</th>
                            <th>Customer Name</th>
                            <th>Address</th>
                            <th>Contact No.</th>
                            <th>AddedBy</th>
                            <th>DeletedBy</th>
                            <th>Deleted Time</th>
                        </thead>
                        <tbody>
                            <tr v-for="(customer, sl) in customers">
                                <td>
                                    <input type="checkbox" v-model="customer.checkStatus">
                                </td>
                                <td>{{ sl + 1 }}</td>
                                <td>{{ customer.Customer_Code }}</td>
                                <td>{{ customer.Customer_Name }}</td>
                                <td>{{ customer.Customer_Address }} {{ customer.District_Name }}</td>
                                <td>{{ customer.Customer_Mobile }}</td>
                                <td>{{ customer.added_by }}</td>
                                <td>{{ customer.deleted_by }}</td>
                                <td>{{ customer.DeletedTime | dateFormat('DD-MM-YYYY, h:mm:ss a') }}</td>
                            </tr>
                            <tr v-if="customers.filter(item => item.checkStatus == true).length > 0">
                                <td colspan="10" style="text-align: right;">
                                    <button type="button" @click="storeCustomer" style="margin: 0;" class="btn btn-success btn-sm">Store Customer</button>
                                </td>
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
        filters: {
            dateFormat(dt, format) {
                return moment(dt).format(format);
            },
        },
        methods: {
            getCustomers() {
                axios.post('/get_customers', {
                    customerType: this.searchType,
                    status: 'd'
                }).then(res => {
                    this.customers = res.data.map(item => {
                        item.checkStatus = false;
                        return item;
                    });
                })
            },

            allCheck() {
                if (event.target.checked) {
                    this.customers = this.customers.map(item => {
                        item.checkStatus = true;
                        return item;
                    })
                } else {
                    this.customers = this.customers.map(item => {
                        item.checkStatus = false;
                        return item;
                    })
                }
            },

            storeCustomer() {
                let customers = this.customers.filter(item => item.checkStatus == true).length;
                if (customers == 0) {
                    Swal.fire({
                        icon: "error",
                        text: "Select customer",
                    });
                    return;
                }
                axios.post('/restore_customer', {
                        customers: this.customers
                    })
                    .then(res => {
                        alert(res.data);
                        this.getCustomers();
                    })
            },

            async printCustomerList() {
                let printContent = `
                    <div class="container">
                        <h4 style="text-align:center">Deleted Customer List</h4 style="text-align:center">
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