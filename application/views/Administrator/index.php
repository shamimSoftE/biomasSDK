<?php
$companyInfo = $this->db->query("select * from tbl_company c order by c.Company_SlNo desc limit 1")->row();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<title><?php echo $companyInfo->Company_Name; ?> || <?php echo $title; ?></title>

	<meta name="description" content="Static &amp; Dynamic Tables" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

	<!-- bootstrap & fontawesome -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/font-awesome/4.5.0/css/font-awesome.min.css" />

	<!-- page specific plugin styles -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery-ui.custom.min.css" />
	<!-- text fonts -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/fonts.googleapis.com.css" />

	<!-- ace styles -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/responsive.css" />

	<!--[if lte IE 9]>
			<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace-rtl.min.css" />
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/fancyBox/css/jquery.fancybox.css?v=2.1.5" media="screen" />

	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace-skins.min.css" />

	<!-- inline styles related to this page -->

	<!-- ace settings handler -->
	<script src="<?php echo base_url(); ?>assets/js/ace-extra.min.js"></script>

	<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"></script>

	<link rel="icon" type="image/x-icon" href="<?php echo base_url(); ?>uploads/favicon.png">

</head>

<body class="skin-2">
	<div id="navbar" class="navbar navbar-default ace-save-state navbar-fixed-top" style="background:#3e2e6b !important;">
		<div class="navbar-container ace-save-state" id="navbar-container">
			<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
				<span class="sr-only">Toggle sidebar</span>

				<span class="icon-bar"></span>

				<span class="icon-bar"></span>

				<span class="icon-bar"></span>
			</button>

			<div class="navbar-header pull-left">
				<a href="<?php echo base_url(); ?>" class="navbar-brand">
					<small>
						<i class="fa fa-leaf"></i>
						<?php echo $companyInfo->Company_Name; ?> <span style="color:#000;font-weight:700;letter-spacing:1px;font-size:16px;"> </span>
					</small>
				</a>
			</div>

			<div class="navbar-buttons navbar-header pull-right" role="navigation">
				<ul class="nav ace-nav">
					<?php
					$userID =  $this->session->userdata('userId');
					$CheckSuperAdmin = $this->db->where('UserType', 'm')->or_where('UserType', 'a')->where('User_SlNo', $userID)->get('tbl_user')->row();
					if (isset($CheckSuperAdmin)) :
					?>
					<?php
										$date = date("Y-m-d");
										$reminders = $this->db->query("
													 select * from( select 
													c.Customer_SlNo,
													(select ifnull(sum(sm.SaleMaster_TotalSaleAmount), 0.00) + ifnull(c.previous_due, 0.00)
														from tbl_salesmaster sm 
														where sm.SalseCustomer_IDNo = c.Customer_SlNo
														" . ($date == null ? "" : " and sm.SaleMaster_SaleDate < '$date'") . "
														and sm.status = 'a') as billAmount,

													(select ifnull(sum(sm.SaleMaster_PaidAmount), 0.00)
														from tbl_salesmaster sm
														where sm.SalseCustomer_IDNo = c.Customer_SlNo
														" . ($date == null ? "" : " and sm.SaleMaster_SaleDate < '$date'") . "
														and sm.status = 'a') as invoicePaid,

													(select ifnull(sum(cp.CPayment_amount), 0.00) 
														from tbl_customer_payment cp 
														where cp.CPayment_customerID = c.Customer_SlNo 
														and cp.CPayment_TransactionType = 'CR'
														" . ($date == null ? "" : " and cp.CPayment_date < '$date'") . "
														and cp.status = 'a') as cashReceived,

													(select ifnull(sum(cp.CPayment_amount), 0.00) 
														from tbl_customer_payment cp 
														where cp.CPayment_customerID = c.Customer_SlNo 
														and cp.CPayment_TransactionType = 'CP'
														" . ($date == null ? "" : " and cp.CPayment_date < '$date'") . "
														and cp.status = 'a') as paidOutAmount,

													(select ifnull(sum(sr.SaleReturn_ReturnAmount), 0.00) 
														from tbl_salereturn sr 
														join tbl_salesmaster smr on smr.SaleMaster_InvoiceNo = sr.SaleMaster_InvoiceNo 
														where smr.SalseCustomer_IDNo = c.Customer_SlNo 
														" . ($date == null ? "" : " and sr.SaleReturn_ReturnDate < '$date'") . "
													) as returnedAmount,

													(select invoicePaid + cashReceived) as paidAmount,

													(select (billAmount + paidOutAmount) - (paidAmount + returnedAmount)) as dueAmount
													
													from tbl_customer c
													where c.branch_id = ?
													and c.Customer_remainder_day  = ?
													) as tbl
													 where 1=1
													 and dueAmount > 0

										", [$this->session->userdata('BRANCHid'),$date])->result();
										
										$totalCustomerReminder = count($reminders);
										
									?>
									<li>
										<a href="<?php echo base_url(); ?>due_reminder" style="background-color: #224079;">
											<i class="ace-icon fa fa-bell icon-animated-bell"></i>Due Remainder
											<span class="badge badge-important"><?php echo $totalCustomerReminder; ?></span>
										</a>
									</li>
						<li class="light-blue dropdown-modal">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								<big>Branch Acess</big>
								<i class="ace-icon fa fa-caret-down"></i>
							</a>

							<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
							
								<?php
								$sql = $this->db->query("SELECT * FROM tbl_branch where status = 'a' order by Branch_name asc ");
								$row = $sql->result();
								foreach ($row as $row) { ?>
									<li>
										<a class="btn-add fancybox fancybox.ajax" href="<?php echo base_url(); ?>brachAccess/<?php echo $row->branch_id; ?>">
											<i class="ace-icon fa fa-bank"></i>
											<?php echo $row->Branch_name; ?>
										</a>
									</li>
								<?php } ?>
							</ul>
						</li>
					<?php endif; ?>

					<li class="clock_li">
						<a class="clock" style="background:#3e2e6b !important;">
							<span style="font-size:20px;"><i class="ace-icon fa fa-clock-o"></i></span> <span style="font-size:15px;"><?php date_default_timezone_set('Asia/Dhaka');
																																		echo date("l, d F Y"); ?>,&nbsp;<span id="timer" style="font-size:15px;"></span></span>
						</a>
					</li>



					<li class="light-blue dropdown-modal">
						<a data-toggle="dropdown" href="#" class="dropdown-toggle">
							<?php if (!empty($this->session->userdata('user_image'))) { ?>

								<img class="nav-user-photo" src="<?php echo base_url(); ?><?php echo $this->session->userdata('user_image'); ?>" alt="<?php echo $this->session->userdata('FullName'); ?>" />
							<?php } else { ?>

								<img class="nav-user-photo" src="<?php echo base_url(); ?>uploads/no_user.png" alt="<?php echo $this->session->userdata('FullName'); ?>" />
							<?php } ?>
							<span class="user-info">
								<small>Welcome,</small>
								<?php echo $this->session->userdata('FullName'); ?>
							</span>

							<i class="ace-icon fa fa-caret-down"></i>
						</a>

						<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
							<li>
								<a href="<?php echo base_url(); ?>profile">
									<i class="ace-icon fa fa-user"></i>
									Profile
								</a>
							</li>

							<li class="divider"></li>

							<li>
								<a href="<?php echo base_url(); ?>Login/logout">
									<i class="ace-icon fa fa-power-off"></i>
									Logout
								</a>
							</li>
						</ul>
					</li>

				</ul>
			</div>
		</div><!-- /.navbar-container -->
	</div>

	<div class="main-container ace-save-state">
		<script type="text/javascript">
			try {
				ace.settings.loadState('main-container')
			} catch (e) {}
		</script>

		<div id="sidebar" class="sidebar responsive ace-save-state sidebar-fixed sidebar-scroll">
			<script type="text/javascript">
				try {
					ace.settings.loadState('sidebar')
				} catch (e) {}
			</script>

			<div class="sidebar-shortcuts" id="sidebar-shortcuts">
				<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
					<a href="/graph" class="btn btn-success">
						<i class="ace-icon fa fa-signal"></i>
					</a>

					<a href="/module/AccountsModule" class="btn btn-info">
						<i class="ace-icon fa fa-pencil"></i>
					</a>

					<a href="/module/HRPayroll" class="btn btn-warning">
						<i class="ace-icon fa fa-users"></i>
					</a>

					<a href="/module/Administration" class="btn btn-danger">
						<i class="ace-icon fa fa-cogs"></i>
					</a>
				</div>

				<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
					<span class="btn btn-success"></span>

					<span class="btn btn-info"></span>

					<span class="btn btn-warning"></span>

					<span class="btn btn-danger"></span>
				</div>
			</div><!-- /.sidebar-shortcuts -->

			<?php include('menu.php'); ?>

			<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
				<i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
			</div>
		</div>

		<div class="main-content">
			<div class="main-content-inner">
				<div class="breadcrumbs ace-save-state" id="breadcrumbs">
					<ul class="breadcrumb">
						<li>
							<i class="ace-icon fa fa-home home-icon"></i>
							<a href="#">Home</a>
						</li>

						<li>
							<a href="#"><?php echo $title; ?></a>
						</li>

					</ul><!-- /.breadcrumb -->

					<div class="nav-search" id="nav-search">
						<span style="font-weight: bold; color: #972366; font-size: 16px;">
							<?php echo $this->session->userdata('Branch_name');  ?>
						</span>
					</div><!-- /.nav-search -->
				</div>

				<div class="page-content">
					<div id="loader" hidden style="position: fixed; z-index: 1000; margin: auto; height: 100%; width: 100%; background:rgba(255, 255, 255, 0.72);;">
						<img src="<?php echo base_url(); ?>assets/loader.gif" style="top: 30%; left: 50%; opacity: 1; position: fixed;">
					</div>
					<?php echo $content; ?>


				</div><!-- /.page-content -->
				<div class="row" style="display:none;">
					<table id="dynamic-table" class="table table-striped table-bordered table-hover">
					</table>
				</div>
			</div>
		</div><!-- /.main-content -->

		<div class="footer">
			<div class="footer-inner">
				<div class="footer-content">
					<div class="row">
						<div class="col-md-9" style="padding-right: 0;">
							<marquee scrollamount="2" onmouseover="this.stop();" onmouseout="this.start();" direction="left" height="30" style="padding-top: 3px;color: red;margin-bottom: -10px;font-size: 15px;" id="linkup_api"></marquee>
						</div>
						<!-- <div class="col-md-3" style="padding: 4px 0;background-color: #3e2e6b;color:white; margin-bottom: -1px;">
							<span style="font-size: 12px;">
								Developed by <span class="blue bolder"><a href="http://linktechbd.com/" target="_blank" style="color: white;text-decoration: underline;font-weight: normal;">Link-Up Technology</a></span>
							</span>
						</div> -->
					</div>

				</div>
			</div>
		</div>

		<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
			<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
		</a>
	</div><!-- /.main-container -->

	<!-- basic scripts -->
	<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"></script>
	<script type="text/javascript">
		if ('ontouchstart' in document.documentElement) document.write("<script src='<?php echo base_url(); ?>assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
	</script>
	<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>

	<!-------------------  profile script end   --------------------->
	<script src="<?php echo base_url(); ?>assets/js/jquery-ui.custom.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

	<script src="<?php echo base_url(); ?>assets/js/jquery-typeahead.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>assets/fancyBox/js/jquery.fancybox.js?v=2.1.5"></script>
	<!-- ace scripts -->
	<script src="<?php echo base_url(); ?>assets/js/ace-elements.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/ace.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/sweetalert2.min.js"></script>

	<!-- inline scripts related to this page -->

	<script type="text/javascript">
		setInterval(function() {

			var currentTime = new Date();

			var currentHours = currentTime.getHours();

			var currentMinutes = currentTime.getMinutes();

			var currentSeconds = currentTime.getSeconds();

			currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;

			currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;

			var timeOfDay = (currentHours < 12) ? "AM" : "PM";

			currentHours = (currentHours > 12) ? currentHours - 12 : currentHours;

			currentHours = (currentHours == 0) ? 12 : currentHours;

			var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;

			document.getElementById("timer").innerHTML = currentTimeString;

		}, 1000);
	</script>
	<script type="text/javascript">
		$(document).ready(function() {

			$.ajax({
				method: 'get',
				url: '/get_mother_api_content',
				success: function(res) {
					$('#linkup_api').text(res);
				}
			})

		});

		$(".fancybox").fancybox({
			padding: 0,
			transitionIn: 'elastic',
			transitionOut: 'elastic',
			loop: true
		});
	</script>
</body>

</html>