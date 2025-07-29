<div id="assets">
	<div class="row">
		<div class="col-sm-6 ">
			<form @submit.prevent="saveAsset">
				<div class="row" style="margin: 0;">
					<fieldset class="scheduler-border">
						<legend class="scheduler-border">Asset Name Entry Form</legend>
						<div class="control-group">
							<div class="col-xs-12 col-sm-12">
								<div class="form-group clearfix">
									<label class="control-label col-xs-4 col-md-4">Asset Name:</label>
									<div class="col-xs-8 col-md-8">
										<input type="text" class="form-control" v-model="asset.name">
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
		</div>

		<div class="col-sm-6">
			<fieldset class="scheduler-border" style="padding: 5px 15px;">
				<legend class="scheduler-border">Asset Name List</legend>
				<div class="form-group form-inline">
					<label for="filter" class="sr-only">Filter</label>
					<input type="text" class="form-control" v-model="filter" placeholder="Filter">
				</div>
				<div class="table-responsive">
					<datatable :columns="columns" :data="assets" :filter-by="filter" style="margin-bottom: 5px;">
						<template scope="{ row }">
							<tr>
								<td>{{ row.sl }}</td>
								<td>{{ row.name }}</td>
								<td>
									<?php if ($this->session->userdata('accountType') != 'u') { ?>
										<i class="btnEdit fa fa-pencil" @click="editArea(row)"></i>
										<i class="btnDelete fa fa-trash" @click="deleteAsset(row.id)"></i>
									<?php } ?>
								</td>
							</tr>
						</template>
					</datatable>
					<datatable-pager v-model="page" type="abbreviated" :per-page="per_page" style="margin-bottom: 50px;"></datatable-pager>
				</div>
			</fieldset>
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
		el: '#assets',
		data() {
			return {
				asset: {
					id: 0,
					name: '',
				},
				assets: [],

				columns: [{
						label: 'Sl',
						field: 'sl',
						align: 'center'
					},
					{
						label: 'Asset Name',
						field: 'name',
						align: 'center'
					},
					{
						label: 'Action',
						align: 'center',
						filterable: false
					}
				],
				page: 1,
				per_page: 55,
				filter: ''
			}
		},
		created() {
			this.getAssets();
		},
		methods: {
			getAssets() {
				axios.get('/get_asset_name').then(res => {
					this.assets = res.data.map((item, index) => {
						item.sl = index + 1;
						return item;
					});
				})
			},
			saveAsset() {
				if (this.asset.name == '') {
					Swal.fire({
						icon: "error",
						text: "Asset name is empty!",
					});
					return;
				}
				let url = '/add_asset_name';
				if (this.asset.id != 0) {
					url = '/update_asset_name';
				}

				axios.post(url, this.asset).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.status) {
						this.resetForm();
						this.getAssets();
					}
				})
			},
			editArea(asset) {
				let keys = Object.keys(this.asset);
				keys.forEach(key => {
					this.asset[key] = asset[key];
				})
			},
			deleteAsset(assetId) {
				if (confirm('Are you sure?')) {
					axios.post('/delete_asset_name', {
						assetId: assetId
					}).then(res => {
						let r = res.data;
						alert(r.message);
						if (r.status) {
							this.getAssets();
						}
					})
				}
			},
			resetForm() {
				this.asset = {
					id: 0,
					name: '',
				}
			}
		}
	})
</script>