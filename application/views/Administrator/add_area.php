<div id="areas">
	<form @submit.prevent="saveArea">
		<div class="row" style="margin: 0;">
			<fieldset class="scheduler-border">
				<legend class="scheduler-border">Area Entry Form</legend>
				<div class="control-group">
					<div class="col-xs-12 col-md-6 col-md-offset-3">
						<div class="form-group clearfix">
							<label class="control-label col-xs-4 col-md-4">Area Name:</label>
							<div class="col-xs-8 col-md-8">
								<input type="text" class="form-control" v-model="area.District_Name">
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
				<datatable :columns="columns" :data="areas" :filter-by="filter" style="margin-bottom: 5px;">
					<template scope="{ row }">
						<tr>
							<td>{{ row.sl }}</td>
							<td>{{ row.District_Name }}</td>
							<td>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<i class="btnEdit fa fa-pencil" @click="editArea(row)"></i>
									<i class="btnDelete fa fa-trash" @click="deleteArea(row.District_SlNo)"></i>
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
		el: '#areas',
		data() {
			return {
				area: {
					District_SlNo: 0,
					District_Name: '',
				},
				areas: [],

				columns: [{
						label: 'Sl',
						field: 'sl',
						align: 'center'
					},
					{
						label: 'Area Name',
						field: 'District_Name',
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
			this.getAreas();
		},
		methods: {
			getAreas() {
				axios.get('/get_areas').then(res => {
					this.areas = res.data.map((item, index) => {
						item.sl = index + 1;
						return item;
					});
				})
			},
			saveArea() {
				if (this.area.District_Name == '') {
					Swal.fire({
						icon: "error",
						text: "Area name is empty!",
					});
					return;
				}
				let url = '/add_area';
				if (this.area.District_SlNo != 0) {
					url = '/update_area';
				}

				axios.post(url, this.area).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.status) {
						this.resetForm();
						this.getAreas();
					}
				})
			},
			editArea(area) {
				let keys = Object.keys(this.area);
				keys.forEach(key => {
					this.area[key] = area[key];
				})
			},
			deleteArea(areaId) {
				if (confirm('Are you sure?')) {
					axios.post('/delete_area', {
						areaId: areaId
					}).then(res => {
						let r = res.data;
						alert(r.message);
						if (r.status) {
							this.getAreas();
						}
					})
				}
			},
			resetForm() {
				this.area = {
					District_SlNo: 0,
					District_Name: '',
				}
			}
		}
	})
</script>