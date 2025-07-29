<div class="col-md-12" style="width:500px;height:200px;">
	<div class="col-md-12 text-center" style="border-bottom:2px #ccc solid;margin-bottom:30px;">
		<h4 style="margin-top:20px;"> Are you sure !!! You want to access this branch ? </h4>
	</div>
	<form method="post" class="form-horizontal">
		<input name="branch_id" type="hidden" id="branch_id" value="<?php echo $branch_id; ?>" />

		<div class="form-group">
			<div class="col-md-4 col-md-offset-2">
				<button type="button" class="btn btn-md btn-success" onclick="Submitdata()" name="btnSubmit">
					Access
					<i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
				</button>
			</div>

			<div class="col-md-4">
				<button type="button" class="btn btn-md btn-danger" onclick="canceltdata()" name="btnSubmit">
					Cancel
					<i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
				</button>
			</div>
		</div>
	</form>
</div>
<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/fancyBox/js/jquery.fancybox.js?v=2.1.5"></script>
<script type="text/javascript">
	function Submitdata() {
		var branch_id = $('#branch_id').val();
		var succes = "";
		if (succes == "") {
			var inputdata = 'branch_id=' + branch_id;
			var urldata = "<?php echo base_url(); ?>Administrator/login/branch_access_main_admin";
			$.ajax({
				type: "POST",
				url: urldata,
				data: inputdata,
				success: function(data) {
					location.reload();
				}
			});
		}
	}

	function canceltdata() {
		location.reload();
	}
</script>