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

if (isset($_REQUEST['_name']) && $_REQUEST['_name'] == "delivery_ready" && isset($_REQUEST['_domain']) && $_REQUEST['_domain'] == "rfq"){

	$driver = $_REQUEST['d'];
	$shop = $_REQUEST['s'];
	$delivery_id = $_REQUEST['delivery_id'];
	$pickup_time = $_REQUEST['pickup_time'];
	$delivery_time = $_REQUEST['delivery_time'];
	$delivery_address = $_REQUEST['delivery_address'];

	$shop_query = mysql_query("SELECT lat, lng, name FROM flower_shop WHERE id = '$shop' LIMIT 1") or die("can't select flower shop: ".mysql_error());
	$shop_info = mysql_fetch_array($shop_query);
	
	$driver_query = mysql_query("SELECT uname FROM users WHERE id = '$driver' LIMIT 1") or die("can't select driver: ".mysql_error());
	$uname = mysql_fetch_row($driver_query);
	$uname = $uname[0];
	
	$driver_loc_query = mysql_query("SELECT lat, lng FROM foursquare WHERE uname = '$uname' LIMIT 1") or die("can't select foursquare info: ".mysql_error());
	$driver_loc = mysql_fetch_array($driver_loc_query);

	if ($driver_loc['lat'] == 0 || $driver_loc['lng'] == 0 || vincentyGreatCircleDistance($driver_loc['lat'], $driver_loc['lng'], $shop_info['lat'], $shop_info['lng']) > 1609.34){
		$exists_query = mysql_query("SELECT * FROM delivery WHERE flower_shop_id = '$shop' AND driver_id = '$driver' AND delivery_id = '$delivery_id'") or die("can't see if exists: ".mysql_error());
		if (mysql_num_rows($exists_query) > 0) mysql_query("UPDATE delivery SET pickup_time = '$pickup_time', delivery_time = '$delivery_time', delivery_address = '$delivery_address' WHERE flower_shop_id = '$shop' AND driver_id = '$driver' AND delivery_id = '$delivery_id'") or die("can't update: ".mysql_error());
		else mysql_query("INSERT INTO delivery (flower_shop_id, driver_id, delivery_id, pickup_time, delivery_time, delivery_address) VALUES ('$shop', '$driver', '$delivery_id', '$pickup_time', '$delivery_time', '$delivery_address')") or die("can't insert: ".mysql_error());
		
		require_once("Services/Twilio.php");
		$AccountSid = "AC16abe3540ad6bf17260a27ac7e8f9cfc";
    	$AuthToken = "e70d34e6dc245344f74ad34a6fcece8d";
    	
    	$client = new Services_Twilio($AccountSid, $AuthToken);
    	
    	$number_query = mysql_query("SELECT phone_number FROM users WHERE id = '$driver' LIMIT 1") or die("can't select phone number: ".mysql_error());
    	$number = mysql_fetch_row($number_query);
    	$number = $number[0];
    	
    	if($number != 0){
    		$sms_body = "Delivery from ".$shop_info['name'].". PT: ".date("H:i:s m/d", $pickup_time)."; DT: ".date("H:i:s m/d", $delivery_time)."; DA: ".$delivery_address;
    		$sms = $client->account->sms_messages->create("801-921-4507", $number, $sms_body);
    	}
	} else{
		$shop_esl_query = mysql_query("SELECT esl FROM flower_shop_esl WHERE driver_id = '$driver' AND shop_id= '$shop' LIMIT 1") or die("can't get esl: ".mysql_error());
		$esl = mysql_fetch_row($shop_esl_query);
		$esl = $esl[0];
		send_bid($esl, $delivery_id, "5.00", time() + (30*60));
	}

	echo("Delivery Received");

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
	
	if($body == "bid anyway"){
		$user_query = mysql_query("SELECT id FROM users WHERE phone_number = '$from' LIMIT 1") or die("can't select user: ".mysql_error());
		$user_id = mysql_fetch_row($user_query);
		$user_id = $user_id[0];
		
		$delivery_query = mysql_query("SELECT * FROM delivery WHERE driver_id = '$user_id' ORDER BY time_added DESC LIMIT 1") or die("Can't select delivery: ".mysql_error());
		if (mysql_num_rows($delivery_query) == 0) die();
		$delivery = mysql_fetch_array($delivery_query);
		$shop_id = $delivery['flower_shop_id'];
		
		$esl_query = mysql_query("SELECT esl FROM flower_shop_esl WHERE driver_id = '$user_id' AND shop_id = '$shop_id' LIMIT 1") or die("can't select esl: ".mysql_error());
		$esl = mysql_fetch_row($esl_query);
		$esl = $esl[0];
		
		send_bid($esl, $delivery['delivery_id'], "5.00", $delivery['delivery_time']);
		
		$delivery_id = $delivery['id'];
		mysql_query("DELETE FROM delivery WHERE id = '$delivery_id'") or die("Can't delete: ".mysql_error());
	}
	
	mysql_query("INSERT INTO twilio_message (`from`, `body`) VALUES ('$from', '$body')") or die("can't insert: ".mysql_error());
} else die("Invalid Event");






?>