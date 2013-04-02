<?php
session_start();
require_once("mysql.php");
?>
<html>
<head>
	<title>Flower Shop</title>
</head>
<body>
	<h1>Flower Shop</h1>
	<?php  ?>
	<?php if (isset($_SESSION['uname'])) { ?>
	<h3><?php echo $_SESSION['uname']; ?></h3>
	<a href="/logout.php?redirect=lab_3/flower_shop">Log Out</a><br/>
	<?php if ($_SESSION['uname'] == 'admin') { ?>
	<a href="deliveries.php">Deliveries</a>
	<?php } else { ?>
	<a href="profile.php">Profile</a>
	<?php } }  else { ?>
	<a href="login.php">Log In</a>
	<?php } ?>
</body>
</html>