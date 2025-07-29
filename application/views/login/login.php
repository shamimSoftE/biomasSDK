<!DOCTYPE html>
<html lang="en">
<?php
$companyInfo = $this->db->query("select * from tbl_company c order by c.Company_SlNo desc limit 1")->row();
?>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>
		<?php echo $companyInfo->Company_Name; ?> || Login Page
	</title>
	<link rel="stylesheet" type="text/css" href="/assets/login/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/assets/login/css/style.css">
	<link rel="icon" type="image/x-icon" href="<?php echo base_url(); ?>uploads/favicon.png">
</head>

<body>
	<div class="content">
		<h2 class="headding">
			<span id="typed"></span>
		</h2>
		<div class="login">
			<div class="left-cont">
				<div class="company-feature">
					<div class="com-image">
						<?php if ($companyInfo->Company_Logo_thum == "") { ?>
							<img src="/assets/login/img/images.jpg" style="width: 100%; height: 125px; border-radius: 15px; margin-top: 10px; padding: 5px; border: 1px solid white;">
						<?php } else { ?>
							<img src="/<?php echo $companyInfo->Company_Logo_thum; ?>" style="width: 100%; height: 125px; border-radius: 15px; margin-top: 10px; padding: 5px; border: 1px solid white;">
						<?php } ?>
					</div>

				</div>
				<!-- </div> -->
				<div class="company-info">
					<h4>
						<?php echo $companyInfo->Company_Name; ?>
					</h4>
					<div class="com-add">
						<div class="com-profile">
							<strong>Address</strong> :
							<?php echo $companyInfo->Repot_Heading; ?> <br>
						</div>
					</div>
				</div>
				<div class="corcel">
					<div class="round">
						<div class="inner-round">
							<div class="inner-logo"></div>
						</div>
					</div>
				</div>

			</div>
			<div class="right-cont">
				<div class="login-form">
					<div class="form">
						<h4>Sign In Form</h4>
						<p style="color:red;">
							<?php if (isset($message)) {
								echo $message;
							} ?>
						</p>
						<form method="post" action="<?php echo base_url(); ?>Login/procedure">
							<div class="form-group">
								<?php echo form_error('user_name'); ?>
								<input type="text" name="user_name" class="form-control" placeholder="User Name" autofocus autocomplete="off"/>
							</div>
							<div class="form-group">
								<?php echo form_error('password'); ?>
								<input type="password" name="password" class="form-control" placeholder="Password">
							</div>
							<div class="form-group">
								<input type="submit" name="submit" class="btn btn-info btn-block" value="Login">
							</div>
						</form>
					</div>
				</div>
				<div class="develop_by"><strong style="font-size: 10px">Develop By </strong> <a href="http://linktechbd.com">Link-Up Technology Ltd.</a></div>
			</div>
			<div class="clr"></div>
		</div>
	</div>

	<script src="/assets/login/js/jquery.min.js"></script>
	<script src="/assets/login/js/bootstrap.min.js"></script>
	<script src="/assets/js/typed.js"></script>
	<script>
		$(function() {
			var typed = new Typed('#typed', {
				strings: ['Welcome to Online ERP Software'],
				typeSpeed: 100,
				backSpeed: 100,
				loop: true
			});
		});
	</script>
</body>

</html>