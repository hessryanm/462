<?php

require_once("mysql.php");

function send_bid($url, $delivery_id, $price, $time){
	if (strpos($url, "http") === false){
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") $url = "https://".$_SERVER['HTTP_HOST'].$url;
		else $url = "http://".$_SERVER['HTTP_HOST'].$url;
	} 
	$ch = curl_init($url);
	$data = array("_name" => "bid_ready", "_domain" => "rfq", "delivery_id" => $delivery_id, "bid_amount" => $price, "delivery_time" => $time);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	
	$result = curl_exec($ch);
	if ($result === false) echo curl_error($ch);
	curl_close($ch);
}

if (isset($_REQUEST['_name']) && $_REQUEST['_name'] == "delivery_ready" && isset($_REQUEST['_domain']) && $_REQUEST['_domain'] == "rfq"){

	$driver = $_REQUEST['d'];
	$shop = $_REQUEST['s'];
	$delivery_id = $_REQUEST['delivery_id'];
	$pickup_time = $_REQUEST['pickup_time'];
	$delivery_time = $_REQUEST['delivery_time'];
	$delivery_address = $_REQUEST['delivery_address'];

	$exists_query = mysql_query("SELECT * FROM delivery WHERE flower_shop_id = '$shop' AND driver_id = '$driver' AND delivery_id = '$delivery_id'") or die("can't see if exists: ".mysql_error());
	if (mysql_num_rows($exists_query) > 0) mysql_query("UPDATE delivery SET pickup_time = '$pickup_time', delivery_time = '$delivery_time', delivery_address = '$delivery_address' WHERE flower_shop_id = '$shop' AND driver_id = '$driver' AND delivery_id = '$delivery_id'") or die("can't update: ".mysql_error());
	else mysql_query("INSERT INTO delivery (flower_shop_id, driver_id, delivery_id, pickup_time, delivery_time, delivery_address) VALUES ('$shop', '$driver', '$delivery_id', '$pickup_time', '$delivery_time', '$delivery_address')") or die("can't insert: ".mysql_error());
	
	$shop_esl_query = mysql_query("SELECT esl FROM flower_shop_esl WHERE driver_id = '$driver' AND shop_id= '$shop' LIMIT 1") or die("can't get esl: ".mysql_error());
	$esl = mysql_fetch_row($shop_esl_query);
	$esl = $esl[0];
	send_bid($esl, $delivery_id, "5.00", time() + (30*60));

	echo("Delivery Received");

} else die("Invalid Event");






?>