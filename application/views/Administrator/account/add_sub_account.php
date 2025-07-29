<style>
    .v-select {
		margin-bottom: 5px;
		background: #fff;
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
    #accountForm select {
        padding: 0 !important;
    }

    #accountsTable .button {
        width: 25px;
        height: 25px;
        border: none;
        color: white;
    }

    #accountsTable .edit {
        background-color: #7bb1e0;
    }

    #accountsTable .delete {
        background-color: #ff6666;
    }
</style>

<div id="accounts">
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">Sub Account Entry Form</legend>
        <div class="control-group">
            <div class="row">
                <div class="col-md-12">
                    <form id="accountForm" class="form-horizontal" @submit.prevent="saveSubAccount">
                        <div class="row">
                            <div class="col-md-6 col-md-offset-2">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Sub Account Id</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" v-model="subaccount.Sub_Acc_Code" required readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-4">Account Name</label>
                                    <div class="col-md-8">
                                        <v-select v-bind:options="accounts" v-model="selectedAccounts" label="account_name" placeholder="Select Account"></v-select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-4">Sub Account Name</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" v-model="subaccount.Sub_Acc_Name" required>
                                    </div>
                                </div>

                                <!-- <div class="form-group">
                                    <label class="control-label col-md-4">Description</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" v-model="account.Acc_Description"></textarea>
                                    </div>
                                </div> -->

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
            <div id="accountsTable" class="table-responsive">
                <datatable :columns="columns" :data="subaccounts" :filter-by="filter">
                    <template scope="{ row }">
                        <tr>
                            <td>{{ row.Sub_Acc_Code }}</td>
                            <td>{{ row.account_name }}</td>
                            <td>{{ row.Sub_Acc_Name }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <i class="btnEdit fa fa-pencil" @click="editSubAccount(row)"></i>
                                    <i class="btnDelete fa fa-trash" @click="deleteSubAccount(row.id)"></i>
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

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#accounts',
        data() {
            return {
                subaccount: {
                    id: null,
                    Sub_Acc_Code: '<?php echo $subaccountCode; ?>',
                    // Acc_Tr_Type: '',
                    account_id: '',
                    Sub_Acc_Name: '',
                    Sub_Acc_Description: ''
                },
                subaccounts: [],
                accounts: [],
                selectedAccounts:null,


                columns: [{
                        label: 'Account Id',
                        field: 'Sub_Acc_Code',
                        align: 'center'
                    },
                    {
                        label: 'Account Name',
                        field: 'account_name',
                        align: 'center'
                    },
                    {
                        label: 'Sub Account Name',
                        field: 'Sub_Acc_Name',
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
            this.getSubAccounts();
        },
        methods: {
            getSubAccounts() {
                axios.get('/get_sub_accounts').then(res => {
                    this.subaccounts = res.data;
                })
            },
            getAccounts() {
                axios.get('/get_accounts').then(res => {
                    this.accounts = res.data;
                })
            },

            saveSubAccount() {
                let url = '/add_sub_account';
                if (this.subaccount.id != null) {
                    url = '/update_sub_account';
                }

                this.subaccount.account_id = this.selectedAccounts.Acc_SlNo;

                axios.post(url, this.subaccount).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.resetForm();
                        this.subaccount.Sub_Acc_Code = r.newSubAccountCode;
                        this.getSubAccounts();
                    }
                })
            },

            editSubAccount(subaccount) {
                Object.keys(this.subaccount).forEach(key => {
                    this.subaccount[key] = subaccount[key];
                })
                this.selectedAccounts = {
					Acc_SlNo: subaccount.account_id,
					account_name: subaccount.account_name,
				}
            },

            deleteSubAccount(subaccountId) {
                let confirmation = confirm("Are you sure?");
                if (confirmation == false) {
                    return;
                }
                axios.post('/delete_sub_account', {
                    subaccountId: subaccountId
                })
                .then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getSubAccounts();
                    }
                })
            },

            resetForm() {
                this.account = {
                    Acc_SlNo: null,
                    // Acc_Tr_Type: '',
                    // Acc_Name: '',
                    // Acc_Description: '',
                    // Sub_Acc_Name: ''
                };
                this.subaccount = {
                    id: null,
                    Sub_Acc_Name: ''
                };
                this.selectedAccounts = null;
            }
        }
    })
</script>