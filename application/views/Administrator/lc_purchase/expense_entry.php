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

	#branchDropdown .vs__actions button {
		display: none;
	}

	#branchDropdown .vs__actions .open-indicator {
		height: 15px;
		margin-top: 7px;
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
</style>

<div id="expneses">
	<form @submit.prevent="saveLCExpense">
		<div class="row" style="margin: 0;">
			<div class="col-xs-12 col-md-8 col-md-offset-2">
				<fieldset class="scheduler-border">
					<legend class="scheduler-border">LC Expense Entry</legend>
					<div class="control-group">
						<div class="form-group row">
							<div class="col-md-5">
								<div class="col-xs-12">
									<v-select v-bind:options="lcPurchases" style="margin: 0;" v-model="selectedLCPurchase" placeholder="Select LC" label="PurchaseMaster_InvoiceNo" ></v-select>
								</div>
							</div>
							<div class="col-md-7">
								<div class="col-xs-12">
									<v-select v-bind:options="expneses" style="margin: 0;" v-model="selectedExpense" placeholder="Select Expense" label="name"></v-select>
								</div>
								<div class="col-xs-7" style="margin-top: 5px;">
									<input type="number" min="0" step="any" id="total" class="form-control" v-model="exp_amount" />
								</div>
								<div class="col-xs-5" style="margin-top: 5px;">
									<!-- <button type="button" v-on:click="addToCartExp" style="padding: 3px 21px;" class="btnCart pull-right">Add</button> -->
									<button type="submit" style="padding: 3px 21px;" v-bind:disabled="saleOnProgress ? true : false" class="btnCart pull-right">Save</button>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			
		</div>
	</form>


	<!-- LC expense record -->
	 <div class="row">
		<div class="col-sm-12" style="margin-top: 25px;">
			<fieldset class="">
				<legend class="scheduler-border" style=" width: 100%; background: #93d2f5; padding: 5px; "> LC Expense Record ↓</legend>
				<div class="row" style="padding: 5px;">
					<div class="col-sm-12 form-inline">
						<div class="form-group">
							<label for="filter" class="sr-only">Filter</label>
							<input type="text" class="form-control" v-model="filter" placeholder="Filter">
						</div>
					</div>
					<div class="col-md-12">
						<div class="table-responsive">
							<datatable :columns="columns" :data="LCPurchaseExpneses" :filter-by="filter" style="margin-bottom: 5px;">
								<template scope="{ row }">
									<tr>
										<td>{{ row.sl }}</td>
										<td>{{ row.name }}</td>
										<td>{{ row.amount }}</td>
										<td>{{ row.PurchaseMaster_InvoiceNo }}</td>
										<td>
											<?php if ($this->session->userdata('accountType') != 'u') { ?>
												<i class="btnEdit fa fa-pencil" @click="editExp(row)"></i>
												<i class="btnDelete fa fa-trash" @click="deleteExp(row.id)"></i>
											<?php } ?>
										</td>
									</tr>
								</template>
							</datatable>
							<datatable-pager v-model="page" type="abbreviated" :per-page="per_page" style="margin-bottom: 50px;"></datatable-pager>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	 </div>

</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#expneses',
		data() {
			return {
				lcInfo: {
					id: 0,
					lc_purchase_id: null,
					exp_id: null,
					amount: null,
				},
				expneses: [],
				selectedExpense: null,
				lcPurchases: [],
				selectedLCPurchase: null,
				LCPurchaseExpneses: [],
				exp_amount: 0,
				cartTotal: 0,
				saleOnProgress: false,
				columns: [{
						label: 'Sl',
						field: 'sl',
						align: 'center'
					},
					{
						label: 'Name',
						field: 'name',
						align: 'center'
					},
					{
						label: 'Amount',
						field: 'amount',
						align: 'center'
					},
					{
						label: 'LC Invoice',
						field: 'PurchaseMaster_InvoiceNo',
						align: 'center'
					},
					{
						label: 'Action',
						align: 'center',
						filterable: false
					}
				],
				page: 1,
				per_page: 51,
				filter: ''
			}
		},
		created() {
			this.getExpneses();
			this.getLCPurchase();
			this.getLCPurchaseExpneses();
		},
		methods: {
			getExpneses() {
				axios.get('/get_lc_expanses').then(res => {
					this.expneses = res.data;
				})
			},

			getLCPurchase() {
				axios.get('/get_lc_purchases').then(res => {
					this.lcPurchases = res.data.purchases;
				})
			},

			getLCPurchaseExpneses() {
				axios.get('/get_lc_purchase_expanses').then(res => {
					this.LCPurchaseExpneses = res.data.LCpurchaseExpense.map((item, index) => {
						item.sl = index + 1;
						return item;
					});
				})
			},

			saveLCExpense() {
				if (this.selectedLCPurchase == null) {
					Swal.fire({
						icon: "error",
						text: "Select LC!",
					});
					return;
				}
				if (this.selectedExpense == null) {
					Swal.fire({
						icon: "error",
						text: "Select expense!",
					});
					return;
				}
				if (this.exp_amount == 0) {
					Swal.fire({
						icon: "error",
						text: "Enter amount!",
					});
					return;
				}
				this.saleOnProgress = true;				

				this.lcInfo.lc_purchase_id = this.selectedLCPurchase.lc_purchase_master_id;
				this.lcInfo.exp_id = this.selectedExpense.id;
				this.lcInfo.amount = this.exp_amount;
				
				let url = '/add_expense_lc_purchase';
				if (this.lcInfo.id > 0) {
					url = '/update_expense_lc_purchase';
				}

				

				axios.post(url, this.lcInfo).then(res => {
					let r = res.data;
					alert(r.message);
					this.getLCPurchaseExpneses();
					if (r.success) {
						this.resetForm();
					}
					this.saleOnProgress = false;
				})
			},

			editExp(data) {
				this.lcInfo = {
					id: data.id,
					lc_purchase_id: data.lc_purchase_id,
					exp_id: data.exp_id,
					amount: data.amount,
				};

				this.selectedExpense = {
					id: data.exp_id,
					name: data.name,
				};

				this.selectedLCPurchase = {
					lc_purchase_master_id: data.lc_purchase_id,
					PurchaseMaster_InvoiceNo: data.PurchaseMaster_InvoiceNo,
				};
				this.exp_amount = data.amount;
			},

			deleteExp(expId) {
				if (confirm('Are you sure?')) {
					axios.post('/delete_lc_purchase_expanse', {
						expId: expId
					}).then(res => {
						let r = res.data;
						alert(r.message);
						if (r.success) {
							this.getLCPurchaseExpneses();
						}
					})
				}
			},

			resetForm() {
				this.lcInfo = {
					id: 0,
					lc_purchase_id: null,
					exp_id: null,
					amount: 0,
				};
				this.selectedLCPurchase = null;
				this.selectedExpense = null;
				this.exp_amount = 0;
			}
		}
	})
</script>