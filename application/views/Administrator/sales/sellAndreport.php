<div id="salesInvoice">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<pos-invoice v-if="company_profile.print_type == 3" v-bind:pos_id="salesId"></pos-invoice>
			<sales-invoice v-if="company_profile.print_type != 3" v-bind:sales_id="salesId"></sales-invoice>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/components/salesInvoice.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/components/posInvoice.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>
<script>
	new Vue({
		el: '#salesInvoice',
		components: {
			salesInvoice,
			posInvoice
		},
		data(){
			return {
				salesId: parseInt('<?php echo $salesId;?>'),
				company_profile: {},
			}
		},

		created() {
			this.getCompanyProfile();
		},

		methods: {
			getCompanyProfile() {
				axios.get('/get_company_profile').then(res => {
					this.company_profile = res.data;
				})
			},
		},
	})
</script>

