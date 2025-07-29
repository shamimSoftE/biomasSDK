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

    .active {
        background-color: rgb(252, 89, 89);
    }
</style>

<div id="fdrAccounts">
    <div class="row">
        <div class="col-md-12" style="margin: 0;">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">FDR Account Entry Form</legend>
                <div class="control-group">
                    <form action="" class="form-horizontal" @submit.prevent="saveAccount">
                        <div class="row">
                            <div class="col-md-5 col-md-offset-1">
                                <div class="form-group">
                                    <label for="" class="control-label col-md-4">Account Name</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" v-model="account.account_name" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="control-label col-md-4">Account No.</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" v-model="account.account_number" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="control-label col-md-4">Account Type</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" v-model="account.account_type" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="control-label col-md-4">Bank Name</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" v-model="account.bank_name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="" class="control-label col-md-4">Branch Name</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" v-model="account.branch_name">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="control-label col-md-4">Initial Balance</label>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control" v-model="account.initial_balance">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="control-label col-md-4">Description</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" v-model="account.description"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-8 col-md-offset-4 text-right">
                                        <input type="submit" value="Save" class="btnSave">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </fieldset>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12 form-inline">
            <label for="filter" class="sr-only">Filter</label>
            <input type="text" class="form-control" v-model="filter" placeholder="Filter">
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="accounts" :filter-by="filter">
                    <template scope="{ row }">
                        <tr v-bind:style="{background: row.status == 1 ? '' : '#ffd486'}">
                            <td>{{ row.account_name }}</td>
                            <td>{{ row.account_number }}</td>
                            <td>{{ row.account_type }}</td>
                            <td>{{ row.bank_name }}</td>
                            <td>{{ row.branch_name }}</td>
                            <td>{{ row.initial_balance }}</td>
                            <td>{{ row.status_text }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <i class="btnEdit fa fa-pencil" @click="editAccount(row)"></i>
                                    <i :class="row.status == 1 ? 'btnDelete fa fa-trash' : 'btnEdit fa fa-check'" :style="{color: row.status == 1 ? '' : 'green'}" @click="changestatus(row)"></i>
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
        el: '#fdrAccounts',
        data() {
            return {
                account: {
                    account_id: 0,
                    account_name: '',
                    account_number: '',
                    account_type: '',
                    bank_name: '',
                    branch_name: '',
                    initial_balance: 0.00,
                    description: ''
                },
                accounts: [],
                columns: [{
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
                        label: 'Account Type',
                        field: 'account_type',
                        align: 'center'
                    },
                    {
                        label: 'Bank Name',
                        field: 'bank_name',
                        align: 'center'
                    },
                    {
                        label: 'Branch Name',
                        field: 'branch_name',
                        align: 'center'
                    },
                    {
                        label: 'Initial Balance',
                        field: 'initial_balance',
                        align: 'center'
                    },
                    {
                        label: 'status',
                        field: 'status_text',
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
                filter: ''
            }
        },
        created() {
            this.getAccounts();
        },
        methods: {
            getAccounts() {
                axios.get('/get_fdr_accounts').then(res => {
                    this.accounts = res.data;
                })
            },

            saveAccount() {
                let url = '/add_fdr_account';
                if (this.account.account_id != 0) {
                    url = '/update_fdr_account';
                }
                axios.post(url, this.account).then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.resetForm();
                            this.getAccounts();
                        }
                    })
                    .catch(error => {
                        if (error.response) {
                            alert(`${error.response.status}, ${error.response.statusText}`);
                        }
                    })
            },

            editAccount(account) {
                Object.keys(this.account).forEach(key => {
                    this.account[key] = account[key];
                });
                this.account.account_id = account.fdr_account_id;
            },

            changestatus(account) {
                axios.post('/change_loan_account_status', {
                        account: account
                    }).then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.getAccounts();
                        }
                    })
                    .catch(error => {
                        if (error.response) {
                            alert(`${error.response.status}, ${error.response.statusText}`);
                        }
                    })
            },

            resetForm() {
                this.account = {
                    account_name: '',
                    account_type: '',
                    description: '',
                    account_id: 0,
                    account_number: '',
                    bank_name: '',
                    branch_name: '',
                    initial_balance: 0.00
                }
            }
        }
    })
</script>