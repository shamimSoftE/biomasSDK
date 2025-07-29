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

    .active-button {
        background-color: rgb(252, 89, 89);
    }

    .transaction-deposit {
        background-color: #f0f4f0;
    }

    .transaction-withdraw {
        background-color: #fff4f4;
    }

    .add-button {
		padding: 2.8px;
		width: 100%;
		background-color: #d15b47;
		display: block;
		text-align: center;
		color: white;
		cursor: pointer;
		border-radius: 3px;
	}
	.add-button:hover {
		color: white;
	}
</style>

<div id="fdrTransactions">
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">FDR Transaction Form</legend>
        <div class="control-group">
            <form action="" class="form-horizontal" @submit.prevent="saveTransaction">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="" class="control-label col-md-4">Transaction Date</label>
                            <div class="col-md-8">
                                <input type="date" style="margin-bottom: 4px;" class="form-control" v-model="transaction.transaction_date" required @change="getTransactions">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label col-md-4">Transaction Type</label>
                            <div class="col-md-8">
                                <select class="form-control" v-model="transaction.transaction_type" required style="padding:0px;">
                                    <option value="">Select Type</option>
                                    <option value="deposit">Deposit</option>
                                    <!-- <option value="Interest">Interest</option> -->
                                    <option value="withdraw">Withdraw</option>
                                </select>
                            </div>
                        </div>

                        <!-- <div class="form-group">
                            <label for="" class="control-label col-md-4">Bank Account</label>
                            <div class="col-md-8" style="display: flex;align-items:center;">
                                <div style="width: 86%;">
                                    <v-select v-bind:options="filteredAccounts" v-model="selectedAccount" label="display_text" placeholder="Select account" @input="getLoanBalance"></v-select>
                                </div>
                                <div style="width: 13%;margin-left: 2px;margin-top: -5px;">
                                    <a href="<?= base_url('bank_accounts') ?>" class="add-button" target="_blank" title="Add New "><i class="fa fa-plus" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div> -->

                        <div class="form-group">
                            <label class="col-md-4 control-label">Bank Account</label>
                            <div class="col-md-8">
                                <v-select v-bind:options="bankBalances" v-model="selectedBankBalance" @input="bankOnchange" label="display_txt" placeholder="Select account"></v-select>
                            </div>
                        </div>                        

                        <div class="form-group">
                            <label for="" class="control-label col-md-4">FDR Account</label>
                            <div class="col-md-8" style="display: flex;align-items:center;">
                                <div style="width: 100%;" >
                                    <v-select v-bind:options="filteredFDRAccounts" v-model="selectedFDRAccount" label="display_text" placeholder="Select account"></v-select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- <div class="form-group">
                            <label class="col-md-4 control-label">Payment Type</label>
                        
                            <div class="col-md-8">
                                <select class="form-control" required v-model="transaction.payment_type" @change="paymentOnChange()">
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" v-if="transaction.payment_type == 'Bank'">
                            <label class="col-md-4 control-label">Bank</label>
                            <div class="col-md-8">
                                <v-select v-bind:options="bankAccounts" v-model="selectedBankAccount" label="display_txt" placeholder="Select account"></v-select>
                            </div>
                        </div> -->
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="" class="control-label col-md-4">Amount</label>
                            <div class="col-md-8">
                                <input type="number" class="form-control" v-model="transaction.amount" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label col-md-4">Note</label>
                            <div class="col-md-8">
                                <textarea class="form-control" rows="3" cols="20" v-model="transaction.note"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <input type="button" @click="resetForm" value="Reset" class="btnReset">
                            </div>
                            <div class="col-md-3">
                                <input type="submit" value="Save FDR" v-bind:disabled="onProgress ? true : false" class="btnSave">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 text-center" style="display:none;" v-bind:style="{display: selectedBankBalance == null || selectedBankBalance.account_id == undefined ? 'none' : ''}">
                        <div style="width: 90%;min-height: 120px;padding:10px 3px;background: #eeeeee;border: 1px solid #cdcdcd;">
                            <i class="fa fa-dollar fa-2x"></i>
                            <h5>Current Balance</h5>
                            <h3 style="color: green;">{{ accountBalance }}</h3>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </fieldset>

    <div class="row">
        <div class="col-md-12 form-inline">
            <label for="filter" class="sr-only">Filter</label>
            <input type="text" class="form-control" v-model="filter" placeholder="Filter">
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="transactions" :filter-by="filter">
                    <template scope="{ row }">
                        <tr v-bind:class="[ row.transaction_type == 'Payment' ? 'transaction-deposit' : 'transaction-withdraw']">
                            <td>{{ row.transaction_date }}</td>
                            <td>{{ row.account_name }}</td>
                            <td>{{ row.account_number }}</td>
                            <td>{{ row.bankName }}</td>
                            <td>{{ row.transaction_type}}</td>
                            <td>{{ row.note }}</td>
                            <td>{{ row.amount }}</td>
                            <td>{{ row.AddBy }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <i class="btnEdit fa fa-pencil" @click="editTransaction(row)"></i>
                                    <i class="btnDelete fa fa-trash" @click="removeTransaction(row)"></i>
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
        el: '#fdrTransactions',
        data() {
            return {
                transaction: {
                    fdr_transaction_id: 0,
                    fdr_account_id: '',
                    transaction_date: moment().format('YYYY-MM-DD'),
                    transaction_type: 'deposit',
                    payment_type: 'Bank',
                    bank_account_id: '',
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
                        label: 'Account Name',
                        field: 'account_name',
                        align: 'center'
                    },
                    {
                        label: 'Account Number',
                        field: 'account_number',
                        align: 'center'
                    },
                    {
                        label: 'Bank Name',
                        field: 'bankName',
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
                accountBalance: 0.00,
                fdrAccounts: [],
                selectedFDRAccount: null,
                bankBalances:[],
                selectedBankBalance: 0,
                bankAccounts: [],
				selectedBankAccount: null,
                onProgress: false
            }
        },
        computed: {
            filteredFDRAccounts() {
                let accounts = this.fdrAccounts.filter(account => account.status == '1');
                return accounts.map(account => {
                    account.display_text = `${account.account_name} - ${account.account_number} (${account.bank_name})`;
                    return account;
                })
            },
        },
        created() {
            this.getBankBalance();
            this.getFDRAccounts();
            this.getTransactions();
        },
        methods: {
            // getAccounts() {
            //     axios.get('/get_loan_accounts')
            //         .then(res => {
            //             this.accounts = res.data;
            //         })
            // },

            getBankBalance(){
                axios.get('/get_bank_balance').then(res => {
                    this.bankBalances = res.data.map(item => {
                        item.display_txt = item.bank_name+ '-' + item.branch_name + '('+ item.account_number+')';
                        return item;
                    });
                });
            },

            getFDRAccounts() {
                axios.get('/get_fdr_accounts')
                    .then(res => {
                        this.fdrAccounts = res.data;
                    })
            },

            getTransactions() {
                let data = {
                    dateFrom: this.transaction.transaction_date,
                    dateTo: this.transaction.transaction_date
                }
                axios.post('/get_fdr_transactions', data)
                    .then(res => {
                        this.transactions = res.data;
                    })
            },
            // getBankAccounts(){
            //     axios.get('/get_bank_accounts')
            //     .then(res => {
            //         let bankAcc = res.data.filter(bank_a => bank_a.status == '1');
            //         this.bankAccounts = bankAcc.map(bank => {
            //             bank.display_txt = `${bank.account_name} - ${bank.branch_name} (${bank.account_number})`;
            //             return bank;
            //         })
            //     })
            // },

            paymentOnChange(){
                if(this.transaction.payment_type == 'Bank') {
					this.getBankAccounts();
				}
            },

            saveTransaction() {
                if (this.selectedFDRAccount == null) {
                    alert('Select an Account');
                    return;
                }else{
                    this.transaction.fdr_account_id = this.selectedFDRAccount.fdr_account_id;
                }

                // if (this.transaction.payment_type == 'Bank') {
                if (this.selectedBankBalance == null) {
                    alert('Select a bank account');
                    return;
                }else{
                    this.transaction.bank_account_id = this.selectedBankBalance.account_id;
                }
				// }

                let url = '/add_fdr_transaction';
                if (this.transaction.fdr_transaction_id != 0) {
                    url = '/update_fdr_transaction';
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

                this.selectedFDRAccount = {
                    fdr_account_id: transaction.fdr_account_id,
                    account_number: transaction.account_number,
                    bank_name: transaction.bank_name,
                    display_text: `${transaction.account_number} (${transaction.bank_name})`
                }

                this.selectedBankBalance = this.bankBalances.find(item => item.account_id == transaction.bank_account_id);

                this.paymentOnChange();
                // this.selectedBankAccount = {
                //     display_txt: `${transaction.bank_accountName} - ${transaction.bank_branchName} (${transaction.bank_accountNumber})`,
                //     account_id:transaction.bank_account_id,
                //     account_number:transaction.bank_accountNumber,
                // }
            },

            removeTransaction(transaction) {
                let confirmation = confirm('Are you sure?');
                if (confirmation == false) {
                    return;
                }

                axios.post('/remove_fdr_transaction', transaction)
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

            bankOnchange(){
                if (this.selectedBankBalance == null) {
                    return
                }else{
                    this.accountBalance = parseFloat(this.selectedBankBalance.balance).toFixed(2);
                }
            },

            // getLoanBalance() {
            //     if (this.selectedAccount == null || this.selectedAccount.account_id == undefined) {
            //         return;
            //     }
            //     axios.post('/get_loan_balance', {
            //         accountId: this.selectedAccount.account_id
            //     }).then(res => {
            //         this.accountBalance = res.data[0].balance;
            //     })
            // },

            resetForm() {
                this.transaction.fdr_transaction_id = '';
                this.transaction.fdr_account_id = '';
                this.transaction.bank_account_id = '';
                this.transaction.transaction_type = '';
                this.transaction.payment_type = 'Bank';
                this.transaction.amount = '';
                this.transaction.note = '';

                this.selectedFDRAccount = null;
                this.selectedBankBalance = null;
            }
        }
    })
</script>