<style>
	.v-select {
		margin-bottom: 5px;
		background: #fff;
		border-radius: 3px;
	}

	.v-select.open .dropdown-toggle {
		border-bottom: 1px solid #ccc;
	}

	.v-select .dropdown-toggle {
		padding: 0px;
		height: 25px;
		border: none;
	}

    .v-select input[type=search],
    .v-select input[type=search]:focus {
        margin: 0px;
    }

    .v-select .vs__selected-options {
        overflow: hidden;
        flex-wrap: nowrap;
    }

    .v-select .selected-tag {
        margin: 2px 0px;
        white-space: nowrap;
        position: absolute;
        left: 0px;
    }

    .v-select .vs__actions {
        margin-top: -5px;
    }

    .v-select .dropdown-menu {
        width: auto;
        overflow-y: auto;
    }

    .button {
        width: 25px;
        height: 25px;
        border: none;
        color: white;
    }

    .active-button {
        background-color: rgb(252, 89, 89);
    }

    .transaction-deposit {
        background-color: #f0f4f0;
    }

    .transaction-withdraw {
        background-color: #fff4f4;
    }
</style>

<div id="bankTransactions">
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">Investment Transaction Form</legend>
        <div class="control-group">
            <div class="row">
                <div class="col-md-6 col-md-offset-1">
                    <form action="" class="form-horizontal" @submit.prevent="saveTransaction">
                        <div class="form-group">
                            <label for="" class="control-label col-md-4">Transaction Date</label>
                            <div class="col-md-8">
                                <input type="date" style="margin-bottom: 4px;" class="form-control" v-model="transaction.transaction_date" required @change="getTransactions">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label col-md-4">Account</label>
                            <div class="col-md-8">
                                <v-select v-bind:options="filteredAccounts" v-model="selectedAccount" label="display_text" placeholder="Select account" @input="getAccountBalance"></v-select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label col-md-4">Transaction Type</label>
                            <div class="col-md-8">
                                <select class="form-control" v-model="transaction.transaction_type" required style="padding:0px;">
                                    <option value="">Select Type</option>
                                    <option value="Receive">Receive</option>
                                    <option value="Profit">Profit</option>
                                    <option value="Payment">Payment</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label no-padding-right" style="margin:0;">Payment Type</label>
                            <div class="col-md-8">
                                <select name="" id="" class="form-control" v-model="transaction.payment_type" >
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="display:none" v-bind:style="{display: transaction.payment_type == 'Bank' ? '' : 'none'}">
                            <label class="col-md-4 control-label no-padding-right" style="margin:0;">Bank Account</label>
                            <div class="col-md-8">
                                <v-select v-bind:options="filteredBankAccounts" v-model="selectedBankAccount" label="display_text" placeholder="Select Account"></v-select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label col-md-4">Amount</label>
                            <div class="col-md-8">
                                <input type="number" class="form-control" v-model="transaction.amount" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label col-md-4">Note</label>
                            <div class="col-md-8">
                                <textarea class="form-control" v-model="transaction.note"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4 text-right">
                                <input type="button" @click="resetForm" value="Reset" class="btnReset">
                                <input type="submit" value="Save Transaction" v-bind:disabled="onProgress ? true : false" class="btnSave">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-2 col-md-offset-1 text-center" style="display:none;" v-bind:style="{display: selectedAccount == null || selectedAccount.Acc_SlNo == undefined ? 'none' : ''}">
                    <div style="width: 100%;min-height: 150px;padding:15px 5px;background: #eeeeee;border: 1px solid #cdcdcd;margin-top: 15px;">
                        <i class="fa fa-dollar fa-2x"></i>
                        <h5>Current Balance</h5>
                        <h3 style="color: green;">{{ accountBalance }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>


    <div class="row">
        <div class="col-md-12 form-inline">
            <div class="form-group">
                <label for="filter" class="sr-only">Filter</label>
                <input type="text" class="form-control" v-model="filter" placeholder="Filter">
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="transactions" :filter-by="filter">
                    <template scope="{ row }">
                        <tr v-bind:class="[row.transaction_type == 'Payment' ? 'transaction-deposit' : 'transaction-withdraw']">
                            <td>{{ row.transaction_date }}</td>
                            <td>{{ row.Acc_Code }}</td>
                            <td>{{ row.Acc_Name }}</td>
                            <td>{{ row.bank_name }} - {{ row.account_number }}</td>
                            <td>{{ row.transaction_type }}</td>
                            <td>{{ row.note }}</td>
                            <td>{{ row.amount }}</td>
                            <td>{{ row.AddBy }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                        <i class="btnEdit fa fa-pencil" @click="editTransaction(row)"></i>
                                        <i class="btnDelete fa fa-trash"  @click="removeTransaction(row)"></i>
                                <?php } ?>
                            </td>
                        </tr>
                    </template>
                </datatable>
                <datatable-pager v-model="page" type="abbreviated" :per-page="per_page"></datatable-pager>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#bankTransactions',
        data() {
            return {
                transaction: {
                    transaction_id: 0,
                    account_id: '',
                    bank_account_id: '',
                    transaction_date: moment().format('YYYY-MM-DD'),
                    transaction_type: '',
                    payment_type: 'Cash',
                    amount: '',
                    note: ''
                },
                transactions: [],
                columns: [{
                        label: 'Transaction Date',
                        field: 'transaction_date',
                        align: 'center'
                    },
                    {
                        label: 'Account Code',
                        field: 'Acc_Code',
                        align: 'center'
                    },
                    {
                        label: 'Account Name',
                        field: 'Acc_Name',
                        align: 'center'
                    },
                    {
                        label: 'Bank Account',
                        field: 'bank_name',
                        align: 'center'
                    },
                    {
                        label: 'Transaction Type',
                        field: 'transaction_type',
                        align: 'center'
                    },
                    {
                        label: 'Note',
                        field: 'note',
                        align: 'center'
                    },
                    {
                        label: 'Amount',
                        field: 'amount',
                        align: 'center'
                    },
                    {
                        label: 'Saved By',
                        field: 'AddBy',
                        align: 'center'
                    },
                    {
                        label: 'Action',
                        align: 'center',
                        filterable: false
                    }
                ],
                page: 1,
                per_page: 100,
                filter: '',
                accounts: [],
                selectedAccount: null,

                bankAccounts: [],
				selectedBankAccount: null,

                accountBalance: null,
                onProgress: false
            }
        },
        computed: {
            filteredAccounts() {
                let accounts = this.accounts;
                return accounts.map(account => {
                    account.display_text = `${account.Acc_Code} - ${account.Acc_Name}`;
                    return account;
                })
            },
            filteredBankAccounts() {
                let accounts = this.bankAccounts.filter(account => account.status == '1');
                return accounts.map(account => {
                    account.display_text = `${account.account_name} - ${account.account_number} (${account.bank_name})`;
                    return account;
                })
            },
        },
        created() {
            this.getAccounts();
            this.getTransactions();
            this.getBankAccounts();
        },
        methods: {
            getAccounts() {
                axios.get('/get_investment_accounts')
                    .then(res => {
                        this.accounts = res.data;
                    })
            },

            getBankAccounts() {
                axios.get('/get_bank_accounts')
                    .then(res => {
                        this.bankAccounts = res.data;
                    })
            },

            getTransactions() {
                let data = {
                    dateFrom: this.transaction.transaction_date,
                    dateTo: this.transaction.transaction_date
                }
                axios.post('/get_investment_transactions', data)
                    .then(res => {
                        this.transactions = res.data;
                    })
            },

            saveTransaction() {
                if (this.selectedAccount == null) {
                    alert('Select an Account');
                    return;
                }

                if (this.selectedBankAccount != null) {
                    this.transaction.bank_account_id = this.selectedBankAccount.account_id;
                }
                

                this.transaction.account_id = this.selectedAccount.Acc_SlNo;
                

                let url = '/add_investment_transaction';
                if (this.transaction.transaction_id != 0) {
                    url = '/update_investment_transaction';
                }

                this.onProgress = true;
                axios.post(url, this.transaction)
                    .then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.resetForm();
                            this.getTransactions();
                            this.onProgress = false;
                        }
                    })
                    .catch(error => {
                        if (error.response) {
                            alert(`${error.response.status}, ${error.response.statusText}`)
                        }
                    })
            },

            editTransaction(transaction) {
                let keys = Object.keys(this.transaction);
                keys.forEach(key => this.transaction[key] = transaction[key]);
                this.selectedAccount = {
                    Acc_SlNo: transaction.account_id,
                    Acc_Name: transaction.Acc_Name,
                    Acc_Code: transaction.Acc_Code,
                    display_text: `${transaction.Acc_Code} - ${transaction.Acc_Name}`
                }

                if (transaction.payment_type == 'Bank') {
                    this.selectedBankAccount = {
                        account_id : transaction.bank_account_id,
                        display_text: transaction.account_name+'-'+transaction.account_number+'('+transaction.bank_name+')',
                    }
                }

            },

            removeTransaction(transaction) {
                let confirmation = confirm('Are you sure?');
                if (confirmation == false) {
                    return;
                }

                axios.post('/remove_investment_transaction', transaction)
                    .then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.getTransactions();
                        }
                    })
                    .catch(error => {
                        if (error.response) {
                            alert(`${error.response.status}, ${error.response.statusText}`)
                        }
                    })
            },

            getAccountBalance() {
                if (this.selectedAccount == null || this.selectedAccount.Acc_SlNo == undefined) {
                    return;
                }

                axios.post('/get_investment_balance', {
                    accountId: this.selectedAccount.Acc_SlNo
                }).then(res => {
                    this.accountBalance = res.data[0].balance;
                })
            },

            resetForm() {
                this.transaction.transaction_id = '';
                this.transaction.account_id = '';
                this.transaction.transaction_type = '';
                this.transaction.amount = '';
                this.transaction.note = '';

                this.selectedAccount = null;
                this.selectedBankAccount = null;
            }
        }
    })
</script>