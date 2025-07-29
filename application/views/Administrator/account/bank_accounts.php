<style>
    .v-select {
        margin-bottom: 5px;
    }

    .v-select .dropdown-toggle {
        padding: 0px;
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

    .edit {
        background-color: #7bb1e0;
    }

    .active {
        background-color: rgb(252, 89, 89);
    }
</style>

<div id="bankAccounts">
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">Bank Account Entry Form</legend>
        <div class="control-group">
            <div class="row">
                <div class="col-md-12">
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

                                <div class="form-group" style="display: none;">
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
                                        <input type="button" value="Reset" @click="resetForm" class="btnReset">
                                        <input type="submit" value="Save" class="btnSave">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </fieldset>

    <div class="row">
        <div class="col-md-12 form-inline">
            <label for="filter" class="sr-only">Filter</label>
            <input type="text" class="form-control" v-model="filter" placeholder="Filter">
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="accounts" :filter-by="filter">
                    <template scope="{ row }">
                        <tr :style="{background: row.status == 1 ? '' : '#ffe5b6'}">
                            <td>{{ row.account_name }}</td>
                            <td>{{ row.account_number }}</td>
                            <td>{{ row.account_type }}</td>
                            <td>{{ row.bank_name }}</td>
                            <td>{{ row.branch_name }}</td>
                            <td>{{ row.status_text }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                        <i class="btnEdit fa fa-pencil" @click="editAccount(row)"></i>
                                        <i v-bind:class="row.status == 1 ? 'btnDelete fa fa-ban' : 'fa fa-check'" style="cursor: pointer;" :style="{color: row.status == 0 ? 'green' : ''}" @click="changestatus(row)"></i>
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
        el: '#bankAccounts',
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
                axios.get('/get_bank_accounts').then(res => {
                    this.accounts = res.data;
                })
            },

            saveAccount() {
                if (this.account.account_name == '') {
					Swal.fire({
						icon: "error",
						text: "Account name is empty!",
					});
					return;
				}
                if (this.account.account_number == '') {
					Swal.fire({
						icon: "error",
						text: "Account name is empty!",
					});
					return;
				}
                if (this.account.bank_name == '') {
					Swal.fire({
						icon: "error",
						text: "Bank name is empty!",
					});
					return;
				}
                let url = '/add_bank_account';
                if (this.account.account_id != 0) {
                    url = '/update_bank_account';
                }
                axios.post(url, this.account).then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.resetForm();
                            this.getAccounts();
                        }
                    })
            },

            editAccount(account) {
                Object.keys(this.account).forEach(key => {
                    this.account[key] = account[key];
                })
            },

            changestatus(account) {
                axios.post('/change_account_status', {
                        account: account
                    }).then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.getAccounts();
                        }
                    })
            },

            resetForm() {
                this.account = {
                    account_id: 0,
                    account_number: '',
                    account_name: '',
                    account_type: '',
                    bank_name: '',
                    branch_name: '',
                    initial_balance: 0.00
                }
            }
        }
    })
</script>