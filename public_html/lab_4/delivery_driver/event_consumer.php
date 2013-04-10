<?php

require_once("mysql.php");
require_once("../send_event.php");
require_once("Services/Twilio.php");
$AccountSid = "AC16abe3540ad6bf17260a27ac7e8f9cfc";
$AuthToken = "e70d34e6dc245344f74ad34a6fcece8d";

$request_json = mysql_real_escape_string(json_encode($_REQUEST));
mysql_query("INSERT INTO request VALUES ('$request_json')");

function save_error($error){
	$error = mysql_real_escape_string($error);
	mysql_query("INSERT INTO error VALUES ('$error')");
}

function send_bid($url, $delivery_id, $price, $time, $driver_id){
	
	$data = array("_name" => "bid_ready", "_domain" => "rfq", "delivery_id" => $delivery_id, "bid_amount" => $price, "delivery_time" => $time, "driver_id" => $driver_id);

	send_event($url, $data);
}

function vincentyGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000){
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $lonDelta = $lonTo - $lonFrom;
  $a = pow(cos($latTo) * sin($lonDelta), 2) +
    pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
  $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

  $angle = atan2(sqrt($a), $b);
  return $angle * $earthRadius;
}

$client = new Services_Twilio($AccountSid, $AuthToken);

if (isset($_REQUEST['_name']) && $_REQUEST['_name'] == "delivery_ready" && isset($_REQUEST['_domain']) && $_REQUEST['_domain'] == "rfq"){

	//	$data = array("_name" => "delivery_ready", "_domain" => "rfq", "delivery_id" => $delivery_id, "pickup_time" => $pickup_time, "delivery_time" => $delivery_time, "delivery_address" => $delivery_address, "shop_name" => $shop_name, "shop_lat" => $shop_lat, "shop_lng" => $shop_lng, "shop_esl" => $shop_esl, "shop_id" => $shop_id);

	$user = $_REQUEST['d'];
	$driver_id = $_REQUEST['driver_id'];
	$delivery_id = $_REQUEST['delivery_id'];
	$pickup_time = intval($_REQUEST['pickup_time']);
	$delivery_time = intval($_REQUEST['delivery_time']);
	$delivery_address = $_REQUEST['delivery_address'];
	$shop_name = $_REQUEST['shop_name'];
	$shop_lat = floatval($_REQUEST['shop_lat']);
	$shop_lng = floatval($_REQUEST['shop_lng']);
	$shop_esl = $_REQUEST['shop_esl'];
	$escaped_esl = mysql_real_escape_string($shop_esl);
	$shop_id = $_REQUEST['shop_id'];

	$driver_query = mysql_query("SELECT uname FROM users WHERE id = '$user' LIMIT 1") or die("can't select driver: ".mysql_error());
	$uname = mysql_fetch_row($driver_query);
	$uname = $uname[0];
	
	$driver_loc_query = mysql_query("SELECT lat, lng FROM foursquare WHERE uname = '$uname' LIMIT 1") or die("can't select foursquare info: ".mysql_error());
	$driver_loc = mysql_fetch_array($driver_loc_query);
    	
	$number_query = mysql_query("SELECT phone_number FROM users WHERE id = '$user' LIMIT 1") or die("can't select phone number: ".mysql_error());
	$number = mysql_fetch_row($number_query);
	$number = $number[0];
	
	mysql_query("INSERT INTO delivery (user_id, driver_id, delivery_id, shop_id, shop_esl, status) VALUES ('$user', '$driver_id', '$delivery_id', '$shop_id', '$escaped_esl', '0')") or die("can't insert: ".mysql_error());
	$driver_delivery_id = mysql_insert_id();

	if ($driver_loc['lat'] == 0 || $driver_loc['lng'] == 0 || vincentyGreatCircleDistance($driver_loc['lat'], $driver_loc['lng'], $shop_lat, $shop_lng) > 1609.34){
    	
    	$sms_body = "Delivery from ".$shop_name."; Lat: ".$shop_lat."; Lng: ".$shop_lng.". PT: ".date("H:i:s m/d", $pickup_time)."; DT: ".date("H:i:s m/d", $delivery_time)."; DA: ".$delivery_address."; ID: ".$driver_delivery_id;
    		
	} else{
		
		send_bid($shop_esl, $delivery_id, "5.00", time() + (30*60), $driver_id);
		mysql_query("UPDATE delivery SET status = '1' WHERE id = '$driver_delivery_id'") or die("Can't update: ".mysql_error());
		$sms_body = "Bid Sent to ".$shop_name."; Lat: ".$shop_lat."; Lng: ".$shop_lng.". PT: ".date("H:i:s m/d", $pickup_time)."; DT: ".date("H:i:s m/d", $delivery_time)."; DA: ".$delivery_address."; ID: ".$driver_delivery_id;
	}
	
	if($number != 0){
		$sms = $client->account->sms_messages->create("801-921-4507", $number, $sms_body);
	}

	echo("Delivery Received");

} else if (isset($_REQUEST['_name']) && $_REQUEST['_name'] == "bid_awarded" && isset($_REQUEST['_domain']) && $_REQUEST['_domain'] == "rfq"){
	
	//{"d":"5","_domain":"rfq","_name":"bid_awarded","delivery_id":"41","shop_id":"hess_shop516457af0c106"}

	$user_id = $_REQUEST['d'];
	$delivery_id = $_REQUEST['delivery_id'];
	$shop_id = $_REQUEST['shop_id'];
	
	$user_query = mysql_query("SELECT phone_number FROM users WHERE id = '$user_id' LIMIT 1") or save_error("Can't select phone number: ".mysql_error());
	$phone_number = mysql_fetch_row($user_query);
	$phone_number = $phone_number[0];
	
	$delivery_query = mysql_query("SELECT id FROM delivery WHERE user_id = '$user_id' AND shop_id = '$shop_id' AND delivery_id = '$delivery_id' LIMIT 1") or save_error("can't get delivery id: ".mysql_error());
	$driver_delivery_id = mysql_fetch_row($delivery_query);
	$driver_delivery_id = $driver_delivery_id[0];
	
	$sms_body = "Bid Awarded: ".$driver_delivery_id;
	$sms = $client->account->sms_messages->create("801-921-4507", $phone_number, $sms_body);
	
	mysql_query("UPDATE delivery SET status = '2' WHERE id = '$driver_delivery_id'") or save_error("can't update status: ".mysql_error());

} else if(isset($_REQUEST['source']) && $_REQUEST['source'] == "foursquare"){
	if($_REQUEST['secret'] != "YSCWQ1VWN10LHUSH31F422DB45XODZBCXH1FQ5UHUM5O3LYE" || !isset($_REQUEST['checkin'])) die();
	$checkin = $_REQUEST['checkin'];
	$checkin = json_decode($checkin);
	$user_id = $checkin->user->id;
	$time = $checkin->createdAt;
	$name = mysql_real_escape_string($checkin->venue->name);
	$lat = $checkin->venue->location->lat;
	$lng = $checkin->venue->location->lng;
	
	mysql_query("UPDATE foursquare SET lat = '$lat', lng = '$lng', time = '$time', name = '$name' WHERE foursquare_id = '$user_id'") or die("can't update foursquare: ".mysql_error());
	
	die("Checkin Received");
} else if(isset($_REQUEST['source']) && $_REQUEST['source'] == "twilio"){
	if (strpos($_REQUEST['From'], "+") == 0) $_REQUEST['From'] = substr($_REQUEST['From'], 1);
	$_REQUEST['From'] = str_replace("-", "", $_REQUEST['From']);
	if (strpos($_REQUEST['from'], "1") == 0) $_REQUEST['From'] = substr($_REQUEST['From'], 1);
	$from = intval(trim($_REQUEST['From']));
	$body = trim(strtolower($_REQUEST['Body']));
	$body = str_replace("'", "", $body);
	
	$user_query = mysql_query("SELECT id FROM users WHERE phone_number = '$from' LIMIT 1") or save_error("can't select user: ".mysql_error());
	$user_id = mysql_fetch_row($user_query);
	$user_id = $user_id[0];
	
	if($body == "bid anyway"){
		
		$last_delivery_query = mysql_query("SELECT * FROM delivery WHERE user_id = '$user_id' AND status = '0' ORDER BY time_added DESC LIMIT 1") or save_error("Can't select last delivery: ".mysql_error());
		if (mysql_num_rows($last_delivery_query) == 0) die();
		$delivery = mysql_fetch_array($last_delivery_query);
		$delivery_id = $delivery['id'];

		send_bid($delivery['shop_esl'], $delivery['delivery_id'], "5.00", time() + (30 * 60), $delivery['driver_id']);
		
		mysql_query("UPDATE delivery SET status = '1' WHERE id = '$delivery_id'") or die("Can't update: ".mysql_error());
		
	} else if($body == "dont bid"){
		$last_delivery_query = mysql_query("SELECT * FROM delivery WHERE user_id = '$user_id' AND status = '0' ORDER BY time_added DESC LIMIT 1") or save_error("Can't select last delivery: ".mysql_error());
		if (mysql_num_rows($last_delivery_query) == 0) die();
		$delivery = mysql_fetch_array($last_delivery_query);
		$delivery_id = $delivery['id'];
		
		mysql_query("DELETE FROM delivery WHERE id = '$delivery_id'") or die("can't delete: ".mysql_error());
	} else {

		if(strpos($body, "complete") === 0){
		
		$body_info = explode(" ", $body);
		
		$last = end($body_info);
		if (is_numeric($last)) {
			$driver_delivery_id = intval($last);
			$delivery_query = mysql_query("SELECT id, delivery_id, shop_esl FROM delivery WHERE id = '$driver_delivery_id' LIMIT 1") or save_error("can't select delivery by id: ".mysql_error());
		} else {
			$delivery_query = mysql_query("SELECT id, delivery_id, shop_esl FROM delivery WHERE user_id = '$user_id' AND status = '2' ORDER BY time_added DESC LIMIT 1") or save_error("Can't select last delivery: ".mysql_error());
		}
		
		$delivery = mysql_fetch_array($delivery_query);
		$driver_delivery_id = $delivery['id'];
		
		$data = array("_domain" => "delivery", "_name" => "complete", "delivery_id" => $delivery['delivery_id']);
		send_event($delivery['shop_esl'], $data);
		}
	}
} else die("Invalid Event");






?>