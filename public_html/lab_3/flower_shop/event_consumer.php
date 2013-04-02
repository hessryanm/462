<?php

require_once("mysql.php");

if (isset($_REQUEST['_name']) && $_REQUEST['_name'] == "bid_ready" && isset($_REQUEST['_domain']) && $_REQUEST['_domain'] == "rfq"){
	$driver = $_REQUEST['d'];
	$delivery = $_REQUEST['delivery_id'];
	$price = $_REQUEST['bid_amount'];
	$time = $_REQUEST['delivery_time'];

	$exists_query = mysql_query("SELECT * FROM bid WHERE delivery_id = '$delivery' AND driver_id = '$driver'") or die("Can't check if exists: ".mysql_error());
	if (mysql_num_rows($exists_query) > 0) mysql_query("UPDATE bid SET price = '$price', delivery_time = '$time' WHERE delivery_id = '$delivery' AND driver_id = '$driver'") or die("can't update: ".mysql_error());
	else mysql_query("INSERT INTO bid (delivery_id, driver_id, price, delivery_time) VALUES ('$delivery', '$driver', '$price', '$time')") or die("can't insert: ".mysql_error());
	die("Bid Received");
} else die("Invalid Event");

?>