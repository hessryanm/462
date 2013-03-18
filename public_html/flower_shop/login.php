<?php
session_start();
if (isset($_POST['uname']) && isset($_POST['pass'])){
	require_once("mysql.php");
	$uname = mysql_real_escape_string($_POST['uname']);
	$pass = md5($_POST['pass']);
	$query = mysql_query("SELECT uname FROM users WHERE uname = '$uname' AND pass = '$pass' LIMIT 1");
	
	if(mysql_num_rows($query) > 0) {
		$_SESSION['uname'] = $uname;
		if (isset($_REQUEST['redirect'])) echo "<script type='text/javascript'>window.location.href='".$_REQUEST['redirect']."';</script>";
		else echo "<script type='text/javascript'>window.location.href='/flower_shop';</script>";
	}
}

?>
<html>
<head>
	<title>Log In</title>
	<link rel="stylesheet" type="text/css" href="css/login.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript" src="js/login.js"></script>
</head>
<body>
	<h1>Please Log In</h1>
	<div class="login error"></div>
	<form action method="POST">
		<input type="text" placeholder="Username" name="uname" /><br/>
		<input type="password" placeholder="Password" name="pass" /><br/>
		<input type="button" value="Submit" id="login_submit" />
	</form>
	<br/><br/>
	<h4>Create an Account</h4>
	<div class="add error"></div>
	<form>
		<input type="text" placeholder="Username" name="uname" /><br/>
		<input type="password" placeholder="Password" name="pass" /><br/>
		<input type="password" placeholder="Confirm Password" name="confirm" /><br/>
		<input type="button" value="Submit" id="add_submit" />
	</form>
</body>