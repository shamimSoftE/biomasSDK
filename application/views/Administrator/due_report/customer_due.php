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

<div id="customerDueList">
	<div class="row" style="margin: 0;">
		<fieldset class="scheduler-border scheduler-search">
			<legend class="scheduler-border">Search Customer Due</legend>
			<div class="control-group">
				<div class="col-md-12">
					<form class="form-inline">
						<div class="form-group">
							<label>Search Type</label>
							<select class="form-select" style="margin:0;height:26px;width:150px;" v-model="searchType" v-on:change="onChangeSearchType" style="padding:0px;">
								<option value="all">All</option>
								<option value="customer">By Customer</option>
								<option value="area">By Area</option>
							</select>
						</div>
						<div class="form-group" style="display: none" v-bind:style="{display: searchType == 'customer' ? '' : 'none'}">
							<label>Select Customer</label>
							<v-select v-bind:options="customers" v-model="selectedCustomer" label="display_name" placeholder="Select customer"></v-select>
						</div>
						<div class="form-group" style="display: none" v-bind:style="{display: searchType == 'area' ? '' : 'none'}">
							<label>Select Area</label>
							<v-select v-bind:options="areas" v-model="selectedArea" label="District_Name" placeholder="Select area"></v-select>
						</div>

						<div class="form-group" style="margin-top: -1px;">
							<input type="button" value="Show Report" v-on:click="getDues">
						</div>
					</form>
				</div>
			</div>
		</fieldset>
	</div>

	<div class="row" style="display: none" v-bind:style="{display: dues.length > 0 ? '' : 'none'}">
		<div class="col-md-12">
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
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Customer Id</th>
							<th >Customer Name</th>
							<th>Owner Name</th>
							<th>Address</th>
							<th>Customer Mobile</th>
							<th>Due Amount</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="data in dues">
							<td style="text-align: start;">{{ data.Customer_Code }}</td>
							<td style="text-align: start;">{{ data.Customer_Name }}</td>
							<td style="text-align: start;">{{ data.owner_name }}</td>
							<td style="text-align: start;">{{ data.Customer_Address }}</td>
							<td style="text-align: right;">{{ data.Customer_Mobile }}</td>
							<td style="text-align:right">{{ parseFloat(data.dueAmount).toFixed(2) }}</td>
						</tr>
					</tbody>
					<tfoot>
						<tr style="font-weight:bold;">
							<td colspan="5" style="text-align:right">Total Due</td>
							<td style="text-align:right">{{ parseFloat(totalDue).toFixed(2) }}</td>
						</tr>
					</tfoot>
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
		el: '#customerDueList',
		data() {
			return {
				searchType: 'all',
				customers: [],
				selectedCustomer: null,
				areas: [],
				selectedArea: null,
				dues: [],
				totalDue: 0.00,
				sortRecord: ''
			}
		},
		created() {

		},
		methods: {
			sorting() {
				if (this.sortRecord === 'asc') {
					this.dues.sort((a, b) => a.Customer_Name.localeCompare(b.Customer_Name));
				} else if (this.sortRecord === 'desc') {
					this.dues.sort((a, b) => b.Customer_Name.localeCompare(a.Customer_Name));
				}
			},
			onChangeSearchType() {
				if (this.searchType == 'customer' && this.customers.length == 0) {
					this.getCustomers();
				} else if (this.searchType == 'area' && this.areas.length == 0) {
					this.getAreas();
				}
				if (this.searchType == 'all') {
					this.selectedCustomer = null;
					this.selectedArea = null;
				}
			},
			getCustomers() {
				axios.get('/get_customers').then(res => {
					this.customers = res.data;
				})
			},
			getAreas() {
				axios.get('/get_areas').then(res => {
					this.areas = res.data;
				})
			},
			getDues() {
				if (this.searchType == 'customer' && this.selectedCustomer == null) {
					alert('Select customer');
					console.log(this.selectedCustomer);
					return;
				}

				let customerId = this.selectedCustomer == null ? null : this.selectedCustomer.Customer_SlNo;
				let districtId = this.selectedArea == null ? null : this.selectedArea.District_SlNo;
				axios.post('/get_customer_due', {
					customerId: customerId,
					districtId: districtId
				}).then(res => {
					if (this.searchType == 'customer') {
						this.dues = res.data;
					} else {
						this.dues = res.data.filter(d => parseFloat(d.dueAmount) != 0);
					}
					this.totalDue = this.dues.reduce((prev, cur) => {
						return prev + parseFloat(cur.dueAmount)
					}, 0);
				})
			},
			async print() {
				let reportContent = `
					<div class="container">
						<h4 style="text-align:center">Customer due report</h4 style="text-align:center">
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