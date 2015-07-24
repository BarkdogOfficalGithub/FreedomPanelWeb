<?php
require_once('../global/global.php');

// If the user is accessing this page, check if they have a valid cookie, if so, log them in straight away
$users->autoLogin();
?>
<!DOCTYPE html>
<html>
<head>
<!-- All the files that are required -->
  <link rel="stylesheet" href="../dist/css/login.css">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link href='https://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
  <script src="../dist/js/login.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <title><?php echo $config['page_title']; ?> Login</title>
</head>

<body>
<!-- Where all the magic happens -->
<!-- LOGIN FORM -->
<center>
<div class="text-center" style="padding:50px 0">
	<div class="logo"><?php echo $config['server_name']; ?> Login</div>
	<!-- Main Form -->
	<div class="login-form-1">
		<form id="login-form" class="text-left" method='post' action='../global/scripts/useractions.php?action=login'>
			<div class="login-form-main-message"></div>
			<div class="main-login-form">
				<div class="login-group">
					<div class="form-group">
						<label for="username" class="sr-only">Username</label>
						<input type="text" class="form-control" id="username" name="username" placeholder="username">
					</div>
					<div class="form-group">
						<label for="password" class="sr-only">Password</label>
						<input type="password" class="form-control" id="password" name="password" placeholder="password">
					</div>
					<div class="form-group login-group-checkbox">
						<input type="checkbox" id="remember" name="remember">
						<label for="remember">Remember me</label>
					</div>
				</div>
				<button type="submit" class="login-button"><i class="fa fa-chevron-right"></i></button>
			</div>
			<div class="etc-login-form">
				<!--<p>forgot your password? <a href="#">click here</a></p>
				<p>new user? <a href="#">create new account</a></p>-->
			</div>
		</form>
	</div>
	<!-- end:Main Form -->
</div>

<!--
<div class="text-center" style="padding:50px 0">
	<div class="logo">register</div>
	<div class="login-form-1">
		<form id="register-form" class="text-left">
			<div class="login-form-main-message"></div>
			<div class="main-login-form">
				<div class="login-group">
					<div class="form-group">
						<label for="reg_username" class="sr-only">Email address</label>
						<input type="text" class="form-control" id="reg_username" name="reg_username" placeholder="username">
					</div>
					<div class="form-group">
						<label for="reg_password" class="sr-only">Password</label>
						<input type="password" class="form-control" id="reg_password" name="reg_password" placeholder="password">
					</div>
					<div class="form-group">
						<label for="reg_password_confirm" class="sr-only">Password Confirm</label>
						<input type="password" class="form-control" id="reg_password_confirm" name="reg_password_confirm" placeholder="confirm password">
					</div>

					<div class="form-group">
						<label for="reg_email" class="sr-only">Email</label>
						<input type="text" class="form-control" id="reg_email" name="reg_email" placeholder="email">
					</div>
					<div class="form-group">
						<label for="reg_fullname" class="sr-only">Full Name</label>
						<input type="text" class="form-control" id="reg_fullname" name="reg_fullname" placeholder="full name">
					</div>

					<div class="form-group login-group-checkbox">
						<input type="radio" class="" name="reg_gender" id="male" placeholder="username">
						<label for="male">male</label>

						<input type="radio" class="" name="reg_gender" id="female" placeholder="username">
						<label for="female">female</label>
					</div>

					<div class="form-group login-group-checkbox">
						<input type="checkbox" class="" id="reg_agree" name="reg_agree">
						<label for="reg_agree">i agree with <a href="#">terms</a></label>
					</div>
				</div>
				<button type="submit" class="login-button"><i class="fa fa-chevron-right"></i></button>
			</div>
			<div class="etc-login-form">
				<p>already have an account? <a href="#">login here</a></p>
			</div>
		</form>
	</div>
</div>

<div class="text-center" style="padding:50px 0">
	<div class="logo">forgot password</div>
	<div class="login-form-1">
		<form id="forgot-password-form" class="text-left">
			<div class="etc-login-form">
				<p>When you fill in your registered email address, you will be sent instructions on how to reset your password.</p>
			</div>
			<div class="login-form-main-message"></div>
			<div class="main-login-form">
				<div class="login-group">
					<div class="form-group">
						<label for="fp_email" class="sr-only">Email address</label>
						<input type="text" class="form-control" id="fp_email" name="fp_email" placeholder="email address">
					</div>
				</div>
				<button type="submit" class="login-button"><i class="fa fa-chevron-right"></i></button>
			</div>
			<div class="etc-login-form">
				<p>already have an account? <a href="#">login here</a></p>
				<p>new user? <a href="#">create new account</a></p>
			</div>
		</form>
	</div>
</div>-->
</center>
</body>
</html>