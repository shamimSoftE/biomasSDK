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

<div id="supplierDue">
	<div class="row" style="margin: 0;">
		<fieldset class="scheduler-border scheduler-search">
			<legend class="scheduler-border">Supplier Due</legend>
			<div class="control-group">
				<div class="col-xs-12 col-md-12 col-lg-12">
					<form class="form-inline">
						<div class="form-group">
							<label for="searchType"> Search Type </label>
							<select id="searchType" class="form-select" style="margin:0;width:150px;height:26px;" v-model="searchType" v-on:change="onChangeSearchType">
								<option value="all"> All </option>
								<option value="supplier"> By Supplier </option>
							</select>
						</div>

						<div class="form-group" style="display:none" v-bind:style="{display: searchType == 'supplier' ? '' : 'none'}">
							<label> Suppliers </label>
							<v-select v-bind:options="suppliers" v-model="selectedSupplier" label="Supplier_Name"></v-select>
						</div>

						<div class="form-group" style="margin-top: -1px;">
							<input type="button" value="Show Report" v-on:click="getDues">
						</div>
					</form>
				</div>
			</div>
		</fieldset>
	</div>
	<div class="row" style="display:none;" v-bind:style="{display: dueList.length > 0 ? '' : 'none'}">
		<div class="col-md-12 text-right">
			<!-- <a href="" v-on:click.prevent="print">
				<i class="fa fa-print"></i> Print
			</a> -->
			<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
				<div style="display: flex; align-items: center;">
					<label style="margin-right: 5px;">Sort By: </label>
					<select v-model="sortRecord" @change="sorting" style="height: 24px; padding: 5px; border: 1px solid #ccc;">
						<option value="">Select an option</option>
						<option value="asc">A to Z</option>
						<option value="desc">Z to A</option>
					</select>
				</div>
				<div>
					<a href="" v-on:click.prevent="print">
						<i class="fa fa-print"></i> Print
					</a>
				</div>
			</div>
			<div class="table-responsive" id="reportTable">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th>Supplier Code</th>
							<th>Supplier Name</th>
							<th>Owner Name</th>
							<th>Address</th>
							<th>Mobile</th>
							<th>Due</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="due in dueList">
							<td>{{ due.Supplier_Code }}</td>
							<td style="text-align: start;">{{ due.Supplier_Name }}</td>
							<td style="text-align: start;">{{ due.contact_person }}</td>
							<td style="text-align: start;">{{ due.Supplier_Address }}</td>
							<td>{{ due.Supplier_Mobile }}</td>
							<td><?php echo $this->session->userdata('Currency_Name'); ?> {{ due.due }}</td>
						</tr>
					</tbody>
					<tbody>
						<tr style="font-weight:bold">
							<td colspan="5" style="text-align:right">Total due</td>
							<td>
								<?php echo $this->session->userdata('Currency_Name'); ?> {{ total.toFixed(2) }}
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
		el: '#supplierDue',
		data() {
			return {
				searchType: 'all',
				suppliers: [],
				selectedSupplier: {
					Supplier_SlNo: null,
					Supplier_Name: 'Select Supplier'
				},
				dueList: [],
				total: 0.00,
				sortRecord: ''
			}
		},
		methods: {
			sorting() {
				if (this.sortRecord === 'asc') {
					this.dueList = this.dueList.sort((a, b) => a['Supplier_Name'].localeCompare(b['Supplier_Name']));
				} else if (this.sortRecord === 'desc') {
					this.dueList = this.dueList.sort((a, b) => b['Supplier_Name'].localeCompare(a['Supplier_Name']));
				}
			},
			getSuppliers() {
				axios.get('/get_suppliers').then(res => {
					this.suppliers = res.data;
				})
			},
			onChangeSearchType() {
				if (this.searchType == 'supplier' && this.suppliers.length == 0) {
					this.getSuppliers();
				} else if (this.searchType == 'all') {
					this.selectedSupplier.Supplier_SlNo = null;
				}
			},
			getDues() {
				if (this.searchType == 'supplier' && this.selectedSupplier.Supplier_SlNo == null) {
					alert('Select supplier');
					return;
				}

				axios.post('/get_supplier_due', {
					supplierId: this.selectedSupplier.Supplier_SlNo
				}).then(res => {
					if (this.searchType == 'supplier') {
						this.dueList = res.data;
					} else {
						this.dueList = res.data.filter(d => parseFloat(d.due) != 0);
					}
					this.total = this.dueList.reduce((prev, curr) => {
						return prev + parseFloat(curr.due)
					}, 0);
				})
			},
			async print() {
				let reportContent = `
					<div class="container">
						<h4 style="text-align:center">Supplier due report</h4 style="text-align:center">
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