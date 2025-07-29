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
			<div class="col-xs-12 col-md-6">
				<fieldset class="scheduler-border">
					<legend class="scheduler-border">LC Expense Entry</legend>
					<div class="control-group">
						
						<div class="form-group row">
							<div class="col-md-5">
								<div class="col-xs-12">
									<v-select v-bind:options="lcPurchases" style="margin: 0;" v-model="selectedLCPurchase" placeholder="Select LC" label="PurchaseMaster_InvoiceNo" v-on:input="lcOnChange"></v-select>
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
									<button type="button" v-on:click="addToCartExp" style="padding: 3px 21px;" class="btnCart pull-right">Add</button>
								</div>
							</div>
						</div>
						
					</div>
				</fieldset>
			</div>
			<div class="col-xs-12 col-md-6">
				<fieldset class="scheduler-border">
					<legend class="scheduler-border">LC Expense Cart</legend>
					<div class="control-group">
						<div class="row">
							<div class="col-xs-12">
								<div class="table-responsive">
									<table class="table table-bordered table-hover" style="color:#000;margin-bottom: 5px;">
										<thead>
											<tr>
												<th style="width:4%;color:#000;">SL</th>
												<th style="width:30%;color:#000;">Exp Name</th>
												<th style="width:13%;color:#000;">Total</th>
												<th style="width:5%;color:#000;">Action</th>
											</tr>
										</thead>
										<tbody style="display:none; min-height:55px" v-bind:style="{display: expcart.length > 0 ? '' : 'none'}">
											<tr v-for="(expense, sl) in expcart">
												<td>{{ sl + 1}}</td>
												<td style="text-align: left;padding-left:3px;">{{ expense.name }}</td>
												<td>{{ expense.total }}</td>
												<td><a href="" v-on:click.prevent="removeFromExpCart(sl)"><i class="fa fa-trash"></i></a></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

							<div class="col-xs-6">
								<span style="margin:15px">Cart Total : {{ cartTotal }}</span>
							</div>
							<div class="col-xs-6">
								<input type="submit" class="btn btn-sm" value="Save" 
								style="width: 35%;background: green !important;border: 0px;border-radius: 5px;float: right;margin-right: 5px;" 
								v-bind:disabled="saleOnProgress ? true : false" />	
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
							<datatable :columns="columns" :data="expneses" :filter-by="filter" style="margin-bottom: 5px;">
								<template scope="{ row }">
									<tr>
										<td>{{ row.sl }}</td>
										<td>{{ row.name }}</td>
										<td>{{ row.note }}</td>
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
				expneses: [],
				selectedExpense: null,
				lcPurchases: [],
				selectedLCPurchase: null,
				expcart: [],
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
						label: 'Description',
						field: 'note',
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

			addToCartExp() {
				if (this.selectedExpense == null) {
					Swal.fire({
						icon: "error",
						text: "Select Expense",
					});
					return;
				}

				let cartInd = this.expcart.findIndex(p => p.expId == this.selectedExpense.id);
				if (cartInd > -1) {
					this.expcart.splice(cartInd, 1);
				}

				let expense = {
					expId: this.selectedExpense.id,
					name: this.selectedExpense.name,
					total: this.exp_amount
				}

				if (expense.exp_amount == 0 || expense.exp_amount == '') {
					Swal.fire({
						icon: "error",
						text: "Enter Amount",
					});
					return;
				}
				this.expcart.push(expense);

				this.cartTotal = this.expcart.reduce((sl, item) => { return sl + parseFloat(item.total) }, 0).toFixed(2);
				// this.clearCart();
				this.selectedExpense = null;
				this.exp_amount = 0;
			},

			removeFromExpCart(ind) {
				this.expcart.splice(ind, 1);
			},

			lcOnChange() {
				if (this.expcart.length > 0) {
					let conf = confirm("If you change the LC No. Then it will clear the cart items!")
					if (conf) {
						this.expcart = [];
					}
				}
			},

			saveExpense() {
				if (this.expense.name == '') {
					Swal.fire({
						icon: "error",
						text: "Expense name is empty!",
					});
					return;
				}
				this.saleOnProgress = true;

				let url = '/add_lc_expanse';
				if (this.expense.id != 0) {
					url = '/update_lc_expanse';
				}

				axios.post(url, this.expense).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.status) {
						this.resetForm();
						this.getExpneses();
					}
					this.saleOnProgress = false;
				})
			},
			editExp(Exp) {
				let keys = Object.keys(this.expense);
				keys.forEach(key => {
					this.expense[key] = Exp[key];
				})
			},
			deleteExp(expId) {
				if (confirm('Are you sure?')) {
					axios.post('/delete_lc_expanse', {
						expId: expId
					}).then(res => {
						let r = res.data;
						alert(r.message);
						if (r.status) {
							this.getExpneses();
						}
					})
				}
			},
			resetForm() {
				this.expense = {
					id: 0,
					name: '',
					note: '',
				}
			}
		}
	})
</script>