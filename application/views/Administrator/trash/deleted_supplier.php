<style>
	.v-select {
		float: right;
		min-width: 200px;
		background: #fff;
		margin-left: 5px;
		border-radius: 4px !important;
		margin-top: -2px;
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
</style>

<div id="deletedSupplier">
	<div class="row" style="margin: 0;">
		<fieldset class="scheduler-border scheduler-search">
			<legend class="scheduler-border">Deleted Supplier List</legend>
			<div class="control-group">
				<div class="col-xs-12 col-md-12 col-lg-12">
					<form class="form-inline">
						<div class="form-group">
							<label for="searchType"> Search Type </label>
							<select id="searchType" class="form-select" style="margin:0;width:150px;height:26px;" v-model="searchType">
								<option value="all"> All </option>
							</select>
						</div>

						<div class="form-group" style="margin-top: -1px;">
							<input type="button" value="Show Report" v-on:click="getSuppliers">
						</div>
					</form>
				</div>
			</div>
		</fieldset>
	</div>
	<div class="row" style="display:none;" v-bind:style="{display: suppliers.length > 0 ? '' : 'none'}">
		<div class="col-md-12 text-right">
			<a href="" v-on:click.prevent="print">
				<i class="fa fa-print"></i> Print
			</a>
			<div class="table-responsive" id="reportTable">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th>
								<input type="checkbox" @click="allCheck">
							</th>
							<th>Supplier Code</th>
							<th>Supplier Name</th>
							<th>Owner Name</th>
							<th>Address</th>
							<th>Mobile</th>
							<th>AddedBy</th>
							<th>DeletedBy</th>
							<th>Deleted Time</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="supplier in suppliers">
							<td>
								<input type="checkbox" v-model="supplier.checkStatus">
							</td>
							<td>{{ supplier.Supplier_Code }}</td>
							<td>{{ supplier.Supplier_Name }}</td>
							<td>{{ supplier.contact_person }}</td>
							<td>{{ supplier.Supplier_Address }}</td>
							<td>{{ supplier.Supplier_Mobile }}</td>
							<td>{{ supplier.added_by }}</td>
							<td>{{ supplier.deleted_by }}</td>
							<td>{{ supplier.DeletedTime | dateFormat('DD-MM-YYYY, h:mm:ss a') }}</td>
						</tr>
						<tr v-if="suppliers.filter(item => item.checkStatus == true).length > 0">
							<td colspan="10" style="text-align: right;">
								<button type="button" @click="storeSupplier" style="margin: 0;" class="btn btn-success btn-sm">Store Supplier</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#deletedSupplier',
		data() {
			return {
				searchType: 'all',
				suppliers: [],
				selectedSupplier: {
					Supplier_SlNo: null,
					Supplier_Name: 'Select Supplier'
				},
				suppliers: [],
			}
		},
		filters: {
			dateFormat(dt, format) {
				return moment(dt).format(format);
			},
		},
		methods: {
			getSuppliers() {
				axios.post('/get_suppliers', {
					status: 'd'
				}).then(res => {
					this.suppliers = res.data.map(item => {
						item.checkStatus = false;
						return item;
					});;
				})
			},

			allCheck() {
				if (event.target.checked) {
					this.suppliers = this.suppliers.map(item => {
						item.checkStatus = true;
						return item;
					})
				} else {
					this.suppliers = this.suppliers.map(item => {
						item.checkStatus = false;
						return item;
					})
				}
			},

			storeSupplier() {
				let suppliers = this.suppliers.filter(item => item.checkStatus == true).length;
				if (suppliers == 0) {
					Swal.fire({
						icon: "error",
						text: "Select supplier",
					});
					return;
				}
				axios.post('/restore_supplier', {
						suppliers: this.suppliers
					})
					.then(res => {
						alert(res.data);
						this.getSuppliers();
					})
			},
			async print() {
				let reportContent = `
					<div class="container">
						<h4 style="text-align:center">Deleted Supplier List</h4 style="text-align:center">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportTable').innerHTML}
							</div>
						</div>
					</div>
				`;

				var mywindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}`);
				mywindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

				mywindow.document.body.innerHTML += reportContent;
				mywindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				mywindow.print();
				mywindow.close();
			}
		}
	})
</script>