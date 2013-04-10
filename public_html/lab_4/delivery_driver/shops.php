<?php

session_start();
if (!isset($_SESSION['uname'])) header("Location: login.php?redirect=shops.php");

require_once("mysql.php");

$uname = $_SESSION['uname'];
$driver_query = mysql_query("SELECT id FROM users WHERE uname = '$uname' LIMIT 1") or die("can't select driver id: ").mysql_error();
$driver = mysql_fetch_row($driver_query);
$driver_id = $driver[0];

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST"){
	$name = mysql_real_escape_string($_POST['name']);
	$lat = $_POST['lat'];
	$lng = $_POST['lng'];
	$esl = mysql_real_escape_string($_POST['esl']);
	
	if(isset($_POST['id'])){
		$id = $_POST['id'];
		mysql_query("UPDATE flower_shop SET name = '$name', lat = '$lat', lng = '$lng' WHERE id = '$id'") or die("Can't update flower shop: ".mysql_error());
	} else{
		mysql_query("INSERT INTO flower_shop (name, lat, lng) VALUES ('$name', '$lat', '$lng')") or die("can't insert flower shop: ".mysql_error());
		$id = mysql_insert_id();
	}
	$esl_query = mysql_query("SELECT esl FROM flower_shop_esl WHERE driver_id = '$driver_id' AND shop_id = '$id' LIMIT 1") or die("can't check if esl already set: ".mysql_error());
	if(mysql_num_rows($esl_query) == 0){
		mysql_query("INSERT INTO flower_shop_esl (driver_id, shop_id, esl) VALUES ('$driver_id', '$id', '$esl')") or die("can't insert esl: ".mysql_error());
	} else{
		mysql_query("UPDATE flower_shop_esl SET esl = '$esl' WHERE driver_id = '$driver_id' AND shop_id = '$id'") or die("can't update esl: ".mysql_error());
	}
	echo "<script type='text/javascript'>alert('Changes Saved');</script>";
}

$shops_query = mysql_query("SELECT * FROM flower_shop") or die("can't select shops: ".mysql_error());
$shops = array();
while($shop = mysql_fetch_array($shops_query)){
	$shop_id = $shop['id'];
	$esl_query = mysql_query("SELECT esl FROM flower_shop_esl WHERE driver_id = '$driver_id' AND shop_id = '$shop_id' LIMIT 1") or die("can't get esl: ".mysql_error());
	$esl = mysql_fetch_row($esl_query);
	$shop['esl'] = $esl[0];
	array_push($shops, $shop);
}

?>
<html>
<head>
	<title>Flower Shops</title>
</head>
<body>
	<a href="index.php">Back to Home</a>
	<h2>Flower Shops</h2>
	<table>
		<tr>
			<th>
				Name
			</th>
			<th>
				Latitude
			</th>
			<th>
				Longitude
			</th>
			<th>
				Shop's ESL
			</th>
			<th></th>
			<th>
				ESL to Give to Shop
			</th>
		</tr>
		<?php foreach($shops as $shop){ ?>
		<form method="POST">
		<input type="hidden" name="id" value="<?php echo $shop['id']; ?>" />
		<tr>
			<td>
				<input type="text" name="name" value="<?php echo $shop['name']; ?>" />
			</td>
			<td>
				<input type="text" name="lat" value="<?php echo $shop['lat']; ?>" />
			</td>
			<td>
				<input type="text" name="lng" value="<?php echo $shop['lng']; ?>" />
			</td>
			<td>
				<input type="text" name="esl" value="<?php echo $shop['esl']; ?>" />
			</td>
			<td>
				<input type="submit" value="Save Changes" />
			</td>
			<td>
				/lab_3/delivery_driver/event_consumer.php?s=<?php echo $shop['id']; ?>&d=<?php echo $driver_id; ?>
			</td>
		</tr>
		</form>
		<?php } ?>
	</table>
	<br/>
	<h2>Add New Shop</h2>
	<form method="POST">
		<label for="new_name">Name</label>
		<input type="text" id="new_name" name="name" /><br/>
		<label for="new_lat">Latitude</label>
		<input type="text" id="new_lat" name="lat" /><br/>
		<label for="new_lng">Longitude</label>
		<input type="text" id="new_lng" name="lng" /><br/>
		<label for="new_esl">Shop's ESL</label>
		<input type="text" id="new_esl" name="esl" /><br/>
		<input type="submit" />
	</form>
</body>
</html>