<div id="months">
	<form @submit.prevent="saveMonth">
		<div class="row" style="margin: 0;">
			<fieldset class="scheduler-border">
				<legend class="scheduler-border">Month Entry Form</legend>
				<div class="control-group">
					<div class="col-xs-12 col-md-6 col-md-offset-3">
						<div class="form-group clearfix">
							<label class="control-label col-xs-4 col-md-4">Month Name:</label>
							<div class="col-xs-8 col-md-8">
								<input type="month" class="form-control" v-model="month.month_name">
							</div>
						</div>
						<div class="form-group clearfix">
							<div class="col-xs-12 col-md-12 text-right">
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
				<datatable :columns="columns" :data="months" :filter-by="filter" style="margin-bottom: 5px;">
					<template scope="{ row }">
						<tr>
							<td>{{ row.sl }}</td>
							<td>{{ row.month_name }}</td>
							<td>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<i class="btnEdit fa fa-pencil" @click="editMonth(row)"></i>
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
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#months',
		data() {
			return {
				month: {
					month_id: 0,
					month_name: moment().format("YYYY-MM"),
				},
				months: [],

				columns: [{
						label: 'Sl',
						field: 'sl',
						align: 'center'
					},
					{
						label: 'Month Name',
						field: 'month_name',
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
			this.getMonths();
		},
		methods: {
			getMonths() {
				axios.get('/get_months').then(res => {
					this.months = res.data.map((item, index) => {
						item.sl = index + 1;
						return item;
					});
				})
			},
			saveMonth() {
				if (this.month.month_name == '') {
					alert("Month name is empty");
					return;
				}
				this.month.month_name = moment(this.month.month_name).format('MMMM-YYYY');
				let url = '/add_month';
				if (this.month.month_id != 0) {
					url = '/update_month';
				}

				axios.post(url, this.month).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.status) {
						this.resetForm();
						this.getMonths();
					}else{
						this.month.month_name = moment(this.month.month_name).format('YYYY-MM');
					}
				})
			},
			editMonth(month) {
				let keys = Object.keys(this.month);
				keys.forEach(key => {
					this.month[key] = month[key];
				})
				this.month.month_name = moment(this.month.month_name).format('YYYY-MM');
			},
			resetForm() {
				this.month = {
					month_id: 0,
					month_name: moment().format("YYYY-MM"),
				}
			}
		}
	})
</script>