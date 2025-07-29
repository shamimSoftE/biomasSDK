<div id="chalanInvoice">
	<a href="/chalan_entry" title="" class="buttonAshiqe">Back To Chalan</a>

	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<chalan-invoice v-bind:chalan_id="chalanId"></chalan-invoice>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/components/chalanInvoice.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>
<script>
	new Vue({
		el: '#chalanInvoice',
		components: {
			chalanInvoice
		},
		data(){
			return {
				chalanId: parseInt('<?php echo $chalanId;?>')
			}
		}
	})
</script>

