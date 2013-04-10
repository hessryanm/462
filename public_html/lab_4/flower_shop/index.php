<?php
session_start();
require_once("mysql.php");

if (isset($_SESSION['uname'])) {
	$uname = $_SESSION['uname'];
	
	if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST"){
		$name = mysql_real_escape_string($_POST['name']);
		$lat = floatval($_POST['lat']);
		$lng = floatval($_POST['lng']);
		
		mysql_query("UPDATE users SET shop_name = '$name', lat = '$lat', lng = '$lng' WHERE uname = '$uname'") or die("Can't update shop info: ".mysql_error());
		echo "<script type='text/javascript'>alert('Changes Saved');</script>";
	}
	
	$shop_info = mysql_query("SELECT * FROM users WHERE uname = '$uname' LIMIT 1") or die("can't select user: ".mysql_error());
	$shop_info = mysql_fetch_assoc($shop_info);
}
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
	<a href="/logout.php?redirect=lab_4/flower_shop">Log Out</a><br/>
	<a href="deliveries.php">Deliveries</a><br/>
	<h4>Shop Info</h4>
	<form method="POST">
	<label for="shop_name">Shop Name: </label><input type="text" id="shop_name" name="name" value="<?php echo $shop_info['shop_name']; ?>" /><br/>
	<label for="shop_lat">Latitude: </label><input type="text" id="shop_lat" name="lat" value="<?php echo $shop_info['lat']; ?>" /><br/>
	<label for="shop_lng">Longitude: </label><input type="text" id="shop_lng" name="lng" value="<?php echo $shop_info['lng']; ?>" /><br/>
	<input type="submit" value="Save Changes" />
	</form>
	<?php }  else { ?>
	<a href="login.php">Log In</a>
	<?php } ?>
</body>
</html>