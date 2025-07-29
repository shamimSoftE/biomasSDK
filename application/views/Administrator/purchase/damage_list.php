<div id="damageList">
	<div class="row" style="margin:0;">
		<fieldset class="scheduler-border scheduler-search">
			<legend class="scheduler-border">Product Damage List</legend>
			<div class="control-group">
				<div class="col-md-12">
					<form class="form-inline" id="searchForm" @submit.prevent="getDamageList">
						<div class="form-group">
							<label>Record Type</label>
							<select class="form-control" v-model="recordType">
								<option value="without_details">Without Details</option>
								<option value="with_details">With Details</option>
							</select>
						</div>
						<div class="form-group">
							<input type="date" class="form-control" v-model="dateFrom">
						</div>

						<div class="form-group">
							<input type="date" class="form-control" v-model="dateTo">
						</div>

						<div class="form-group" style="margin-top: -5px;">
							<input type="submit" value="Search">
						</div>
					</form>
				</div>
			</div>
		</fieldset>
	</div>
	<div style="display:none;" v-bind:style="{display: damages.length > 0 ? '' : 'none'}">
		<div class="row">
			<div class="col-md-12 text-right">
				<a href="" v-on:click.prevent="print">
					<i class="fa fa-print"></i> Print
				</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="table-responsive" id="reportTable">
					<table class="table table-bordered table-hover" style="display: none;" :style="{display: recordType == 'without_details' ? '' : 'none'}">
						<thead>
							<tr>
								<th>Sl</th>
								<th>Invoice</th>
								<th>Date</th>
								<th>Note</th>
								<th>Damage Amount</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(item, sl) in damages">
								<td style="text-align:center;">{{ sl + 1 }}</td>
								<td>{{ item.Damage_InvoiceNo }}</td>
								<td>{{ item.Damage_Date }}</td>
								<td style="text-align:right;">{{ item.Damage_Description }}</td>
								<td>{{ item.damage_amount }}</td>
								<td>
									<a href="" title="Invoice" v-bind:href="`/damage_invoice/${item.Damage_SlNo}`" target="_blank"><i class="fa fa-file-o"></i></a>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="table table-bordered table-hover" style="display: none;" :style="{display: recordType == 'with_details' ? '' : 'none'}">
						<thead>
							<tr>
								<th>Sl</th>
								<th>Invoice</th>
								<th>Date</th>
								<th>Product Name</th>
								<th>Quantity</th>
								<th>Rate</th>
								<th>Total</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<template v-for="(item, index) in damages">
								<tr>
									<td style="text-align:center;">{{ index + 1 }}</td>
									<td>{{ item.Damage_InvoiceNo }}</td>
									<td>{{ item.Damage_Date }}</td>
									<td>{{ item.damageDetail[0].Product_Name }}</td>
									<td>{{ item.damageDetail[0].DamageDetails_DamageQuantity }}</td>
									<td>{{ item.damageDetail[0].damage_rate }}</td>
									<td>{{ item.damageDetail[0].damage_amount }}</td>
									<td>
										<a href="" title="Invoice" v-bind:href="`/damage_invoice/${item.Damage_SlNo}`" target="_blank"><i class="fa fa-file-o"></i></a>
									</td>
								</tr>
								<tr v-for="(product, sl) in item.damageDetail.slice(1)">
									<td colspan="3" v-bind:rowspan="item.damageDetail.length - 1" v-if="sl == 0"></td>
									<td>{{ product.Product_Name }}</td>
									<td>{{ product.DamageDetails_DamageQuantity }}</td>
									<td>{{ product.damage_rate }}</td>
									<td>{{ product.damage_amount }}</td>
								</tr>
								<tr>
									<th colspan="4" style="text-align: left;">Total</th>
									<th>{{item.damageDetail.reduce((prev, curr) => {return prev + parseFloat(curr.DamageDetails_DamageQuantity)},0)}}</th>
									<th></th>
									<th>{{item.damageDetail.reduce((prev, curr) => {return prev + parseFloat(curr.damage_amount)},0)}}</th>
								</tr>
							</template>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	new Vue({
		el: '#damageList',
		data() {
			return {
				dateFrom: moment().format("YYYY-MM-DD"),
				dateTo: moment().format("YYYY-MM-DD"),
				recordType: "without_details",
				damages: [],
				products: [],
			}
		},
		created() {
			this.getProducts();
		},
		methods: {
			getProducts() {
				axios.get('/get_products').then(res => {
					this.products = res.data;
				})
			},
			getDamageList() {
				let filter = {
					dateFrom: this.dateFrom,
					dateTo: this.dateTo,
				}
				axios.post('/get_damages', filter).then(res => {
					this.damages = res.data;
				})
			},
			async print() {
				let reportContent = `
					<div class="container">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4 style="text-align:center">Product Damage List</h4 style="text-align:center">
                            </div>
                        </div>
					</div>
					<div class="container">
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