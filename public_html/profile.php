<?php
date_default_timezone_set("America/Denver");
session_start();

require_once("mysql.php");

if (isset($_GET['uname'])) $view_user = $_GET['uname'];
else header("Location: /");

if (isset($_SESSION['uname']) && $_SESSION['uname'] == $view_user) $view_own = true;
else $view_own = false;

$query_string="SELECT foursquare_token FROM users WHERE uname = '$view_user'";
$auth_token_query = mysql_query($query_string) or die("cannot select: ".mysql_error());
$auth_token = mysql_fetch_array($auth_token_query);
$auth_token = $auth_token[0];

$authorized = 1;
if ($auth_token === 0) $authorized = 0;

if ($authorized){
	require_once("FoursquareAPI.class.php");
	$client_key = "ORFB0CXNQ52CARZBLRJWRCCDBMZAK5BBIHTC3F3GRNNTVXEO";
	$client_secret = "MDJ3AMMCTRUWTSQLVVP5PTFDWIUPKGZEAWNPQKU5QHAWMDXJ";
	$foursquare = new FoursquareAPI($client_key,$client_secret);
	$foursquare->SetAccessToken($auth_token);
	
	$params = array("sort" => "newestfirst");
	
	$last_time = mysql_query("SELECT time FROM checkins WHERE uname = '$view_user' ORDER BY time DESC LIMIT 1");
	if (mysql_num_rows($last_time) > 0){
		$last_time = mysql_fetch_row($last_time);
		$last_time = $last_time[0];
		$params['afterTimestamp'] = $last_time;
	}
	
	$response = $foursquare->GetPrivate("users/self/checkins",$params);
	$response = json_decode($response);
	$response = $response->response->checkins;
	foreach($response->items as $item){
		$time = $item->createdAt;
		$name = $item->venue->name;
		$lat = $item->venue->lat;
		$lng = $item->venue->lng;
		$address = $item->venue->address;
		$city = $item->venue->city;
		$state = $item->venue->state;
		$postal = $item->venue->postalCode;
		
		mysql_query("INSERT INTO checkins (user, time, name, lat, lng, address, city, state, postal) VALUES ('$view_user', '$time', '$name', '$lat', '$lng', '$address', '$city', '$state', '$postal')") or trigger_error("Can't Insert: ".mysql_error(), E_USER_WARNING);
	}
	
	if ($view_own) $query_string = "SELECT * FROM checkins WHERE uname = '$view_user' ORDER BY time DESC LIMIT 10";
	else $query_string = "SELECT time, name, lat, lng FROM checkins WHERE uname = '$view_user' ORDER BY time DESC LIMIT 1";
	
	$checkins_query = mysql_query($query_string);
	$checkins = array();
	while($checkin = mysql_fetch_assoc($checkins_query)){
		array_push($checkins, $checkin);
	}
}
?>

<html>
<head>
	<title><?php if($view_own) echo "Your"; else echo $view_user."'s" ?> Profile</title>
</head>
<body>
	<?php if ($view_own && !$authorized) { ?>
	<a href="/foursquare.php">Authorize Foursquare</a>
	<?php } else if ($view_own) { print_r($checkins); ?>
	<!-- Authorized! -->
	<?php } ?>
</body>
</html>
	
