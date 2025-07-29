<div id="expneses">
	<form @submit.prevent="saveExpense">
		<div class="row" style="margin: 0;">
			<fieldset class="scheduler-border">
				<legend class="scheduler-border">Expense Entry Form</legend>
				<div class="control-group">
					<div class="col-xs-12 col-md-6 col-md-offset-3">
						<div class="form-group clearfix">
							<label class="control-label col-xs-4 col-md-4">Expense Name:</label>
							<div class="col-xs-8 col-md-8">
								<input type="text" class="form-control" v-model="expense.name" required>
							</div>
						</div>
						<div class="form-group clearfix">
							<label class="control-label col-xs-4 col-md-4">Description:</label>
							<div class="col-xs-8 col-md-8">								
								<textarea class="form-control" v-model="expense.note"></textarea>
							</div>
						</div>
						<div class="form-group clearfix">
							<div class="col-xs-12 col-md-12 text-right">
								<input type="button" class="btnReset" value="Reset" @click="resetForm">
								<input type="submit" class="btnSave" value="Save">
							</div>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</form>

	<div class="row">
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
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	new Vue({
		el: '#expneses',
		data() {
			return {
				expense: {
					id: 0,
					name: '',
					note: '',
				},
				expneses: [],

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
				per_page: 100,
				filter: ''
			}
		},
		created() {
			this.getExpneses();
		},
		methods: {
			getExpneses() {
				axios.get('/get_lc_expanses').then(res => {
					this.expneses = res.data.map((item, index) => {
						item.sl = index + 1;
						return item;
					});
				})
			},
			saveExpense() {
				if (this.expense.name == '') {
					Swal.fire({
						icon: "error",
						text: "Expense name is empty!",
					});
					return;
				}
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