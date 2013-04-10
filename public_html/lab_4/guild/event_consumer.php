<?php

require_once("mysql.php");
require_once("../send_event.php");

function save_error($error){
	$error = mysql_real_escape_string($error);
	mysql_query("INSERT INTO error VALUES ('$error')");
}

if (isset($_REQUEST['_name']) && $_REQUEST['_name'] == "delivery_ready" && isset($_REQUEST['_domain']) && $_REQUEST['_domain'] == "rfq"){
	
	$delivery_id = $_REQUEST['delivery_id'];
	$pickup_time = intval($_REQUEST['pickup_time']);
	$delivery_time = intval($_REQUEST['delivery_time']);
	$delivery_address = $_REQUEST['delivery_address'];
	$shop_name = $_REQUEST['shop_name'];
	$shop_lat = floatval($_REQUEST['shop_lat']);
	$shop_lng = floatval($_REQUEST['shop_lng']);
	$shop_id = $_REQUEST['shop_id'];
	$shop_esl = $_REQUEST['shop_esl'];

	mysql_query("INSERT INTO delivery (shop_delivery_id, pickup_time, delivery_time, shop_id) VALUES ('$delivery_id', '$pickup_time', '$delivery_time', '$shop_id')") or save_error("Can't insert delivery: ".mysql_error());
	
	$data = array("_name" => "delivery_ready", "_domain" => "rfq", "delivery_id" => $delivery_id, "pickup_time" => $pickup_time, "delivery_time" => $delivery_time, "delivery_address" => $delivery_address, "shop_name" => $shop_name, "shop_lat" => $shop_lat, "shop_lng" => $shop_lng, "shop_esl" => $shop_esl, "shop_id" => $shop_id);
	
	$driver_query = mysql_query("SELECT id, esl FROM users WHERE esl != '' ORDER BY ranking DESC LIMIT 3") or save_error("can't select top three drivers: ".mysql_error());
	while($driver = mysql_fetch_assoc($driver_query)){
		$data['driver_id'] = $driver['id'];
		$esl = mysql_real_escape_string($driver['esl']);
		$json_data = mysql_real_escape_string(json_encode($data));
		mysql_query("INSERT INTO event_send (url, data) VALUES ('$esl', '$json_data')");
		send_event($driver['esl'], $data);
	}
	
	die("Request Received");
} else if (isset($_REQUEST['_name']) && $_REQUEST['_name'] == "bid_awarded" && isset($_REQUEST['_domain']) && $_REQUEST['_domain'] == "rfq"){

	$driver_id = $_REQUEST['driver_id'];
	$delivery_id = $_REQUEST['delivery_id'];
	$shop_id = $_REQUEST['shop_id'];
	
	mysql_query("UPDATE delivery SET driver_chosen = '$driver_id' WHERE shop_delivery_id = '$delivery_id' AND shop_id = '$shop_id'") or save_error("Can't update delivery: ".mysql_error());
	
	$driver_query = mysql_query("SELECT esl FROM users WHERE id = '$driver_id' LIMIT 1") or save_error("can't get esl: ".mysql_error());
	$esl = mysql_fetch_row($driver_query);
	$esl = $esl[0];
	
	$data = array("_domain" => "rfq", "_name" => "bid_awarded", "delivery_id" => $delivery_id, "shop_id" => $shop_id);
	
	send_event($esl, $data);

	die("Update Received");
} else if (isset($_REQUEST['_name']) && $_REQUEST['_name'] == "picked_up" && isset($_REQUEST['_domain']) && $_REQUEST['_domain'] == "delivery"){

	$delivery_id = $_REQUEST['delivery_id'];
	$shop_id = $_REQUEST['shop_id'];
	$time = intval($_REQUEST['time']);
	
	$delivery_query = mysql_query("SELECT pickup_time, driver_chosen FROM delivery WHERE shop_delivery_id = '$delivery_id' AND shop_id = '$shop_id' LIMIT 1") or save_error("can't get delivery: ".mysql_error());
	$delivery = mysql_fetch_array($delivery_query);
	
	if ($time <= $delivery['pickup_time']){
		$rank_change = rand(1, 10);
	} else{
		$rank_change = rand(-10, -1);
	}
	
	$driver_id = $delivery['driver_chosen'];
	$driver_query = mysql_query("SELECT ranking FROM users WHERE id = '$driver_id' LIMIT 1") or save_error("Can't get driver: ".mysql_error());
	$driver = mysql_fetch_row($driver_query);
	$rank = $driver[0];
	$rank += $rank_change;
	
	mysql_query("UPDATE users SET ranking = '$rank' WHERE id = '$driver_id'") or save_error("can't update ranking: ".mysql_error());

	die("Update Received");
} else if (isset($_REQUEST['_name']) && $_REQUEST['_name'] == "complete" && isset($_REQUEST['_domain']) && $_REQUEST['_domain'] == "delivery"){
	
	$delivery_id = $_REQUEST['delivery_id'];
	$shop_id = $_REQUEST['shop_id'];
	$time = $_REQUEST['time'];
	
	$delivery_query = mysql_query("SELECT id, delivery_time, driver_chosen FROM delivery WHERE shop_delivery_id = '$delivery_id' AND shop_id = '$shop_id' LIMIT 1") or save_error("can't get delivery: ".mysql_error());
	$delivery = mysql_fetch_array($delivery_query);
	
	if ($time <= $delivery['delivery_time']){
		$rank_change = rand(1, 10);
	} else{
		$rank_change = rand(-10, -1);
	}
	
	$driver_id = $delivery['driver_chosen'];
	$driver_query = mysql_query("SELECT ranking FROM users WHERE id = '$driver_id' LIMIT 1") or save_error("Can't get driver: ".mysql_error());
	$driver = mysql_fetch_row($driver_query);
	$rank = $driver[0];
	$rank += $rank_change;
	
	mysql_query("UPDATE users SET ranking = '$rank' WHERE id = '$driver_id'") or save_error("can't update ranking: ".mysql_error());
	$guild_delivery_id = $delivery['id'];
	mysql_query("DELETE FROM delivery WHERE id = '$guild_delivery_id'") or save_error("Can't delete: ".mysql_error());

	die("Update Received");
} else die("Invalid Event");

?>