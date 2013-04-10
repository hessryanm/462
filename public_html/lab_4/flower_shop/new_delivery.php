<?php
session_start();
if (!isset($_SESSION['uname'])) die("Login Required");
require_once("/lab_4/send_event.php");

function send_delivery_ready($shop_lat, $shop_lng, $shop_name, $shop_id, $shop_esl, $delivery_id, $pickup_time, $delivery_time, $delivery_address){
	global $guild_esl;
	
	$data = array("_name" => "delivery_ready", "_domain" => "rfq", "delivery_id" => $delivery_id, "pickup_time" => $pickup_time, "delivery_time" => $delivery_time, "delivery_address" => $delivery_address, "shop_name" => $shop_name, "shop_lat" => $shop_lat, "shop_lng" => $shop_lng, "shop_id" => $shop_id, "shop_esl" => $shop_esl);
	
	send_event($guild_esl, $data);
}

require_once("mysql.php");

$address = mysql_real_escape_string($_POST['address']);
$pu_time = intval($_POST['pickup_time']);
$de_time = intval($_POST['delivery_time']);

$uname = $_SESSION['uname'];

$shop = mysql_query("SELECT * FROM users WHERE uname = '$uname' LIMIT 1") or die("Can't select user: ".mysql_error());
$shop = mysql_fetch_array($shop);
$shop_id = $shop['id'];

mysql_query("INSERT INTO delivery (shop_id, pickup_time, delivery_time, delivery_address) VALUES ('$shop_id', '$pu_time', '$de_time', '$address')") or die("can't insert delivery: ".mysql_error());

send_delivery_ready($shop['lat'], $shop['lng'], $shop['shop_name'], $shop['unique_id'], "/lab_4/flower_shop/event_consumer.php", mysql_insert_id(), $pu_time, $de_time, $address);

die("Delivery Saved");
?>