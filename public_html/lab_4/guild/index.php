<?php
session_start();
require_once("mysql.php");

if (isset($_SESSION['uname'])){
	$uname = $_SESSION['uname'];
	
	if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST"){
		$esl = mysql_real_escape_string($_POST['esl']);
		
		mysql_query("UPDATE users SET esl = '$esl' WHERE uname = '$uname'") or die("Can't update user esl: ".mysql_error());
		echo "<script type='text/javascript'>alert('ESL Saved');</script>";
	}
	
	$user = mysql_query("SELECT * FROM users WHERE uname = '$uname' LIMIT 1") or die("Can't select user: ".mysql_error());
	$user = mysql_fetch_array($user);
}
?>
<html>
<head>
	<title>Driver's Guild</title>
</head>
<body>
	<h1>Driver's Guild</h1>
	<?php if (isset($_SESSION['uname'])) { ?>
	<h3><?php echo $_SESSION['uname']; ?></h3>
	<a href="/logout.php?redirect=lab_4/guild">Log Out</a><br/>
	<br/>
	<form method="POST">
	<label for="driver_esl">Your ESL: </label><input type="text" id="driver_esl" name="esl" value="<?php echo $user['esl']; ?>" /><br/>
	<input type="submit" value="Save Changes" />
	</form>
	<?php }  else { ?>
	<a href="login.php">Log In</a>
	<?php } ?>
</body>
</html>