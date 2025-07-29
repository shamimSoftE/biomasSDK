<div id="sms">
    <div class="row">
        <div class="col-md-6">
            <fieldset class="scheduler-border scheduler-search" style="height:190px;">
                <legend class="scheduler-border">Search Type</legend>
                <div class="control-group">
                    <form v-on:submit.prevent="getData">
                        <div class="form-group">
                            <label for="customer">
                                <input type="radio" id="customer" value="customer" @change="onChangeSearchType" v-model="searchType"> By Customer
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="supplier">
                                <input type="radio" id="supplier" value="supplier" @change="onChangeSearchType" v-model="searchType"> By Supplier
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="employee">
                                <input type="radio" id="employee" value="employee" @change="onChangeSearchType" v-model="searchType"> By Employee
                            </label>
                        </div>
                        <div class="form-group" style="margin-top: 50px;">
                            <button type="submit" class="btn btn-primary btn-xs pull-right"> Submit </button>
                        </div>
                    </form>
                </div>
            </fieldset>
        </div>
        <div class="col-md-6">
            <fieldset class="scheduler-border scheduler-search">
                <legend class="scheduler-border">Send SMS</legend>
                <div class="control-group">
                    <form v-on:submit.prevent="sendSms">
                        <div class="form-group">
                            <label for="smsText">SMS Text</label>
                            <textarea class="form-control" id="smsText" v-model="smsText" v-on:input="checkSmsLength" style="height: 100px;"></textarea>
                            <p style="display:none" v-bind:style="{display: smsText.length > 0 ? '' : 'none'}">{{ smsText.length }} | {{ smsLength - smsText.length }} Remains | Max: {{ smsLength }} characters</p>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-xs pull-right" v-bind:style="{display: onProgress ? 'none' : ''}"> <i class="fa fa-send"></i> Send </button>
                            <button type="button" class="btn btn-primary btn-xs pull-right" disabled style="display:none" v-bind:style="{display: onProgress ? '' : 'none'}"> Please Wait .. </button>
                        </div>
                    </form>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="row" style="margin-top: 25px;">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered" style="display: none;" v-if="searchType =='customer' && customers.length > 0" :style="{display: searchType =='customer' && customers.length > 0 ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Select All &nbsp; <input type="checkbox" v-on:click="selectAll"></th>
                            <th>Customer Code</th>
                            <th>Customer Name</th>
                            <th>Mobile</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody style="display:none" v-bind:style="{display: customers.length > 0 ? '' : 'none'}">
                        <tr v-for="customer in customers">
                            <td><input type="checkbox" v-bind:value="customer.Customer_Mobile" v-model="selectedCustomers" v-if="customer.Customer_Mobile.match(regexMobile)"></td>
                            <td>{{ customer.Customer_Code }}</td>
                            <td>{{ customer.Customer_Name }}</td>
                            <td><span class="label label-md arrowed" v-bind:class="[customer.Customer_Mobile.match(regexMobile) ? 'label-info' : 'label-danger']">{{ customer.Customer_Mobile }}</span></td>
                            <td>{{ customer.Customer_Address }}</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered" style="display: none;" v-if="searchType =='supplier' && suppliers.length > 0" :style="{display: searchType =='supplier' && suppliers.length > 0 ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Select All &nbsp; <input type="checkbox" v-on:click="selectAll"></th>
                            <th>Supplier Code</th>
                            <th>Supplier Name</th>
                            <th>Mobile</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody style="display:none" v-bind:style="{display: suppliers.length > 0 ? '' : 'none'}">
                        <tr v-for="supplier in suppliers">
                            <td><input type="checkbox" v-bind:value="supplier.Supplier_Mobile" v-model="selectedSuppliers" v-if="supplier.Supplier_Mobile.match(regexMobile)"></td>
                            <td>{{ supplier.Supplier_Code }}</td>
                            <td>{{ supplier.Supplier_Name }}</td>
                            <td><span class="label label-md arrowed" v-bind:class="[supplier.Supplier_Mobile.match(regexMobile) ? 'label-info' : 'label-danger']">{{ supplier.Supplier_Mobile }}</span></td>
                            <td>{{ supplier.Supplier_Address }}</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered" style="display: none;" v-if="searchType =='employee' && employees.length > 0" :style="{display: searchType =='employee' && employees.length > 0 ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Select All &nbsp; <input type="checkbox" v-on:click="selectAll"></th>
                            <th>Employee Code</th>
                            <th>Employee Name</th>
                            <th>Mobile</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody style="display:none" v-bind:style="{display: employees.length > 0 ? '' : 'none'}">
                        <tr v-for="employee in employees">
                            <td><input type="checkbox" v-bind:value="employee.Employee_ContactNo" v-model="selectedEmployees" v-if="employee.Employee_ContactNo.match(regexMobile)"></td>
                            <td>{{ employee.Employee_ID }}</td>
                            <td>{{ employee.Employee_Name }}</td>
                            <td><span class="label label-md arrowed" v-bind:class="[employee.Employee_ContactNo.match(regexMobile) ? 'label-info' : 'label-danger']">{{ employee.Employee_ContactNo }}</span></td>
                            <td>{{ employee.Employee_PrasentAddress }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>

<script>
    new Vue({
        el: '#sms',
        data() {
            return {
                searchType: 'customer',
                customers: [],
                selectedCustomers: [],
                suppliers: [],
                selectedSuppliers: [],
                employees: [],
                selectedEmployees: [],
                smsText: '',
                smsLength: 306,
                onProgress: false,
                regexMobile: /^01[13-9][\d]{8}$/
            }
        },
        methods: {
            getCustomers() {
                axios.get('/get_customers').then(res => {
                    this.customers = res.data.map(customer => {
                        customer.Customer_Mobile = customer.Customer_Mobile.trim();
                        return customer;
                    });
                })
            },
            getSuppliers() {
                axios.get('/get_suppliers').then(res => {
                    this.suppliers = res.data.map(supplier => {
                        supplier.Supplier_Mobile = supplier.Supplier_Mobile.trim();
                        return supplier;
                    });
                })
            },
            getEmployees() {
                axios.get('/get_employees').then(res => {
                    this.employees = res.data.map(employee => {
                        employee.Employee_ContactNo = employee.Employee_ContactNo.trim();
                        return employee;
                    });
                })
            },
            onChangeSearchType() {
                this.customers = [];
                this.suppliers = [];
                this.employees = [];
            },
            getData() {
                if (this.searchType == 'customer') {
                    this.getCustomers();
                } else if (this.searchType == 'supplier') {
                    this.getSuppliers();
                } else {
                    this.getEmployees();
                }
            },
            selectAll() {
                let checked = event.target.checked;
                if (checked) {
                    this.selectedCustomers = [...new Set(this.customers.map(v => v.Customer_Mobile))].filter(mobile => mobile.match(this.regexMobile));
                    this.selectedSuppliers = [...new Set(this.suppliers.map(v => v.Supplier_Mobile))].filter(mobile => mobile.match(this.regexMobile));
                    this.selectedEmployees = [...new Set(this.employees.map(v => v.Employee_ContactNo))].filter(mobile => mobile.match(this.regexMobile));
                } else {
                    this.selectedCustomers = [];
                    this.selectedSuppliers = [];
                    this.selectedEmployees = [];
                }
            },
            checkSmsLength() {
                if (this.smsText.length > this.smsLength) {
                    this.smsText = this.smsText.substring(0, this.smsLength);
                }
            },
            sendSms() {
                if (this.searchType == 'customer' && this.selectedCustomers.length == 0) {
                    alert('Select customer');
                    return;
                }
                if (this.searchType == 'supplier' && this.selectedSuppliers.length == 0) {
                    alert('Select supplier');
                    return;
                }

                if (this.smsText.length == 0) {
                    alert('Enter sms text');
                    return;
                }

                let data = {
                    smsText: this.smsText,
                }
                if (this.searchType == 'customer') {
                    data.numbers = this.selectedCustomers;
                }
                if (this.searchType == 'supplier') {
                    data.numbers = this.selectedSuppliers;
                }
                if (this.searchType == 'employee') {
                    data.numbers = this.selectedEmployees;
                }

                this.onProgress = true;
                axios.post('/send_bulk_sms', data).then(res => {
                    let r = res.data;
                    alert(r.message);
                    this.onProgress = false;
                })
            }
        }
    })
</script>