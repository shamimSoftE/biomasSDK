<div id="units">
	<form @submit.prevent="saveUnit">
		<div class="row" style="margin: 0;">
			<fieldset class="scheduler-border">
				<legend class="scheduler-border">Unit Entry Form</legend>
				<div class="control-group">
					<div class="col-xs-12 col-md-6 col-md-offset-3">
						<div class="form-group clearfix">
							<label class="control-label col-xs-4 col-md-4">Unit Name:</label>
							<div class="col-xs-8 col-md-8">
								<input type="text" class="form-control" v-model="unit.Unit_Name" required>
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
				<datatable :columns="columns" :data="units" :filter-by="filter" style="margin-bottom: 5px;">
					<template scope="{ row }">
						<tr>
							<td>{{ row.sl }}</td>
							<td>{{ row.Unit_Name }}</td>
							<td>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<i class="btnEdit fa fa-pencil" @click="editUnit(row)"></i>
									<i class="btnDelete fa fa-trash" @click="deleteUnit(row.Unit_SlNo)"></i>
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
		el: '#units',
		data() {
			return {
				unit: {
					Unit_SlNo: 0,
					Unit_Name: '',
				},
				units: [],

				columns: [{
						label: 'Sl',
						field: 'sl',
						align: 'center'
					},
					{
						label: 'Unit Name',
						field: 'Unit_Name',
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
			this.getUnits();
		},
		methods: {
			getUnits() {
				axios.get('/get_units').then(res => {
					this.units = res.data.map((item, index) => {
						item.sl = index + 1;
						return item;
					});
				})
			},
			saveUnit() {
				let url = '/add_unit';
				if (this.unit.Unit_SlNo != 0) {
					url = '/update_unit';
				}

				axios.post(url, this.unit).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.status) {
						this.resetForm();
						this.getUnits();
					}
				})
			},
			editUnit(unit) {
				let keys = Object.keys(this.unit);
				keys.forEach(key => {
					this.unit[key] = unit[key];
				})
			},
			deleteUnit(unitId) {
				if (confirm('Are you sure?')) {
					axios.post('/delete_unit', {
						unitId: unitId
					}).then(res => {
						let r = res.data;
						alert(r.message);
						if (r.status) {
							this.getUnits();
						}
					})
				}
			},
			resetForm() {
				this.unit = {
					Unit_SlNo: 0,
					Unit_Name: '',
				}
			}
		}
	})
</script>