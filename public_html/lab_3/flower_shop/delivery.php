<?php
function send_delivery_ready($url, $id, $pickup_time, $delivery_time, $delivery_address){
	if (strpos($url, "http") === false) $url = "http://".$_SERVER['HTTP_HOST'].$url;
	$ch = curl_init($url);
	$data = array("_name" => "delivery_ready", "_domain" => "rfq", "delivery_id" => $id, "pickup_time" => $pickup_time, "delivery_time" => $delivery_time, "delivery_address" => $delivery_address);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$result = curl_exec($ch);
	if ($result === false) echo curl_error($ch);
	curl_close($ch);
}

require_once("mysql.php");

if (!isset($_POST['delivery_address'])) $_POST['delivery_address'] = "address";

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
} else{
	mysql_query("INSERT INTO delivery (pickup_time, delivery_time, delivery_address, delivered) VALUES ('$pickup_time', '$delivery_time', '$delivery_address', '$delivered')") or die(mysql_error());
	$id = mysql_insert_id();

	$esl_query = mysql_query("SELECT esl FROM profile") or die("can't select esls: ".mysql_error());
	while($esl = mysql_fetch_row($esl_query)){
		send_delivery_ready($esl[0], $id, $pickup_time, $delivery_time, $delivery_address);
	}
}
die("Delivery Saved");
?>