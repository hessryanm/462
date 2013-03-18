<?php
require_once("mysql.php");

if (isset($_POST['pickup_time'])) $pickup_time = $_POST['pickup_time'];
else $pickup_time = time();

if (isset($_POST['delivery_time'])) $delivery_time = $_POST['delivery_time'];
else $delivery_time = time() + (3 * 60 * 60);

$delivery_address = $_POST['delivery_address'];

if (isset($_POST['delivered'])) $delivered = $_POST['delivered'];
else $delivered = 0;

if (isset($_POST['id'])){
	$id = $_POST['id'];
	mysql_query("UPDATE delivery SET pickup_time = '$pickup_time', delivery_time = '$delivery_time', delivery_address = '$delivery_address', delivered = '$delivered' WHERE id = '$id'") or die(mysql_error());
} else mysql_query("INSERT INTO delivery (pickup_time, delivery_time, delivery_address, delivered) VALUES ('$pickup_time', '$delivery_time', '$delivery_address', '$delivered')") or die(mysql_error());

die("Delivery Saved");
?>