<div id="lcPurchaseInvoice">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<lc-invoice v-bind:purchase_id="purchaseId"></lc-invoice>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/components/lcpurchaseInvoice.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>
<script>
	new Vue({
		el: '#lcPurchaseInvoice',
		components: {
			lcPurchaseInvoice
		},
		data(){
			return {
				purchaseId: parseInt('<?php echo $purchaseId;?>')
			}
		}
	})
</script>

