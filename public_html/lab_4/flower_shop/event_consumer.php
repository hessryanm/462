<?php

require_once("mysql.php");

if (isset($_REQUEST['_name']) && $_REQUEST['_name'] == "bid_ready" && isset($_REQUEST['_domain']) && $_REQUEST['_domain'] == "rfq"){
	$driver_id = $_REQUEST['driver_id'];
	$delivery_id = $_REQUEST['delivery_id'];
	$price = floatval($_REQUEST['bid_amount']);
	$time = intval($_REQUEST['delivery_time']);
	
	mysql_query("UPDATE delivery SET status = '1' WHERE id = '$delivery_id'") or die("can't update delivery: ".mysql_error());

	$exists_query = mysql_query("SELECT * FROM bid WHERE delivery_id = '$delivery_id' AND driver_id = '$driver_id'") or die("Can't check if exists: ".mysql_error());
	if (mysql_num_rows($exists_query) > 0) mysql_query("UPDATE bid SET price = '$price', delivery_time = '$time' WHERE delivery_id = '$delivery_id' AND driver_id = '$driver_id'") or die("can't update: ".mysql_error());
	else mysql_query("INSERT INTO bid (delivery_id, driver_id, price, delivery_time) VALUES ('$delivery_id', '$driver_id', '$price', '$time')") or die("can't insert: ".mysql_error());
	die("Bid Received");
} else if (isset($_REQUEST['_name']) && $_REQUEST['_name'] == "complete" && isset($_REQUEST['_domain']) && $_REQUEST['_domain'] == "delivery"){
	$delivery_id = $_REQUEST['delivery_id'];
	require_once("../send_event.php");
	
	mysql_query("UPDATE delivery SET status = '4' WHERE id = '$delivery_id'") or die("can't update delivery: ".mysql_error());
	
	$shop_id = mysql_query("SELECT shop_id FROM delivery WHERE id = '$delivery_id' LIMIT 1") or die("Can't select shop: ".mysql_error());
	$shop_unique = mysql_query("SELECT unique_id FROM users WHERE id = '$shop_id' LIMIT 1") or die("can't get unique id: ".mysql_error());
	$shop_unique = mysql_fetch_row($shop_unique);
	$shop_unique = $shop_unique[0];
	
	$now = time();
	$data = array("_domain" => "delivery", "_name" => "complete", "delivery_id" => $delivery_id, "shop_id" => $shop_unique, "time" => $now);
	
	send_event($guild_esl, $data);

} else die("Invalid Event");

?>