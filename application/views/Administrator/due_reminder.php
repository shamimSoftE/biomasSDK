<style>
    .v-select{
		margin-top:-2.5px;
        float: right;
        min-width: 180px;
        margin-left: 5px;
	}
	.v-select .dropdown-toggle{
		padding: 0px;
        height: 25px;
	}
	.v-select input[type=search], .v-select input[type=search]:focus{
		margin: 0px;
	}
	.v-select .vs__selected-options{
		overflow: hidden;
		flex-wrap:nowrap;
	}
	.v-select .selected-tag{
		margin: 2px 0px;
		white-space: nowrap;
		position:absolute;
		left: 0px;
	}
	.v-select .vs__actions{
		margin-top:-5px;
	}
	.v-select .dropdown-menu{
		width: auto;
		overflow-y:auto;
	}
	#searchForm select{
		padding:0;
		border-radius: 4px;
	}
	#searchForm .form-group{
		margin-right: 5px;
	}
	#searchForm *{
		font-size: 13px;
	}
	.record-table{
		width: 100%;
		border-collapse: collapse;
	}
	.record-table thead{
		background-color: #0097df;
		color:white;
	}
	.record-table th, .record-table td{
		padding: 3px;
		border: 1px solid #454545;
	}
    .record-table th{
        text-align: center;
    }
</style>
<div id="salesRecord">
	



	<div class="row" style="margin-top:15px;">
		<div class="col-md-12" style="margin-bottom: 10px;">
			<a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
		</div>
		
		<div class="col-md-12">
			<div class="table-responsive" id="reportContent" >
				<table class="record-table" style="margin-top:5px;display:none;" v-bind:style="{display: dueremiander.length > 0 ? '' : 'none'}">
					<thead>
						<tr>
							<th>Customer Code</th>
							<th>Customer Name</th>
							<th>Customer Mobile</th>
							<th>Customer Address</th>
							<th>Due Amount</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dueremianders in dueremiander" style="background:#effcff">
							<td>{{ dueremianders.Customer_Name }}</td>
							<td>{{ dueremianders.Customer_Name }}</td>							
							<td>{{ dueremianders.Customer_Mobile }}</td>
							<td>{{ dueremianders.Customer_Address }}</td>
							<td style="text-align: right;">{{ dueremianders.dueAmount }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-md-12  text-center" style="margin-top:15px;display:none;" v-bind:style="{display: dueremiander.length > 0 ? 'none' : ''}">
				<h3>No Data Found</h3>
		</div>	
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lodash.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#salesRecord',
		data(){
			return {
				dueremiander : [],
				
			}
		},
		async created() {
			await this.getSalesRecord();
		},
		methods: {
		
		
		async getSalesRecord(){

				let url = '/get_customer_due_remainder';
			

				await axios.post(url)
				.then(res => {
						this.dueremiander = res.data;
						
				})
				.catch(error => {
					if(error.response){
						alert(`${error.response.status}, ${error.response.statusText}`);
					}
				})
			},
		
			async print(){
				


				let reportContent = `
					<div class="container">
						<div class="row">
							<div class="col-xs-12 text-center">
								<h3>Due Reminder List</h3>
							</div>
						</div>
					
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
							</div>
						</div>
					</div>
				`;

				var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}`);
				reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php');?>
				`);

				reportWindow.document.head.innerHTML += `
					<style>
						.record-table{
							width: 100%;
							border-collapse: collapse;
						}
						.record-table thead{
							background-color: #0097df;
							color:white;
						}
						.record-table th, .record-table td{
							padding: 3px;
							border: 1px solid #454545;
						}
						.record-table th{
							text-align: center;
						}
					</style>
				`;
				reportWindow.document.body.innerHTML += reportContent;

				if(this.searchType == '' || this.searchType == 'user'){
					let rows = reportWindow.document.querySelectorAll('.record-table tr');
					rows.forEach(row => {
						row.lastChild.remove();
					})
				}


				reportWindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				reportWindow.print();
				reportWindow.close();
			}
		}
	})
</script>