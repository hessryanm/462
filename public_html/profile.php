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
	
	$last_time = mysql_query("SELECT time_added FROM checkins WHERE user = '$view_user' ORDER BY time_added DESC LIMIT 1") or trigger_error("can't select: ".mysql_error(), E_USER_WARNING);
	if (mysql_num_rows($last_time) > 0){
		$last_time = mysql_fetch_row($last_time);
		$last_time = strtotime($last_time[0]);
		$params['afterTimestamp'] = $last_time;
	}
	
	$response = $foursquare->GetPrivate("users/self/checkins",$params);
	$response = json_decode($response);
	$response = $response->response->checkins;
	foreach($response->items as $item){
		$time = $item->createdAt;
		$name = $item->venue->name;
		$lat = $item->venue->location->lat;
		$lng = $item->venue->location->lng;
		$address = $item->venue->location->address;
		$city = $item->venue->location->city;
		$state = $item->venue->location->state;
		$postal = $item->venue->location->postalCode;
		
		mysql_query("INSERT INTO checkins (user, time, name, lat, lng, address, city, state, postal) VALUES ('$view_user', '$time', '$name', '$lat', '$lng', '$address', '$city', '$state', '$postal')") or trigger_error("Can't Insert: ".mysql_error(), E_USER_WARNING);
	}
	
	if ($view_own) $query_string = "SELECT * FROM checkins WHERE user = '$view_user' ORDER BY time DESC LIMIT 10";
	else $query_string = "SELECT time, name, lat, lng FROM checkins WHERE user = '$view_user' ORDER BY time DESC LIMIT 1";
	
	$checkins_query = mysql_query($query_string) or trigger_error("Can't Select: ".mysql_error(), E_USER_WARNING);
	$checkins = array();
	while($checkin = mysql_fetch_assoc($checkins_query)){
		array_push($checkins, $checkin);
	}
}
?>

<html>
<head>
	<title><?php if($view_own) echo "Your"; else echo $view_user."'s" ?> Profile</title>
	<link rel="stylesheet" type="text/css" href="css/profile.css" />
</head>
<body>
	<?php if ($view_own && !$authorized) { ?>
	<a href="/foursquare.php">Authorize Foursquare</a>
	<?php } else if (!$view_own && !$authorized) { ?>
	<h2><?php echo $view_user; ?> has not authorized foursquare for their account</h2>
	<?php } else if ($view_own) { ?>
	<table class="checkin">
		<tr>
			<th>
				Location
			</th>
			<th>
				Time
			</th>
			<th>
				Address
			</th>
			<th>
				Lat/Long
			</th>
			<th>
				Map
			</th>
		</tr>
	<?php foreach($checkins as $checkin){ ?>
		<tr>
			<td>
				<?php echo $checkin['name']; ?>
			</td>
			<td>
				<?php echo date("H:i:s m/d/y", $checkin['time']); ?>
			</td>
			<td>
				<?php echo $checkin['address']."<br/>".$checkin['city'].", ".$checkin['state']." ".$checkin['postal']; ?>
			</td>
			<td>
				<?php echo $checkin['lat']." / ".$checkin['lng']; ?>
			</td>
			<td>
				<?php echo "<img src='http://maps.googleapis.com/maps/api/staticmap?zoom=12&size=300x300&maptype=roadmap&markers=color:red%7C".$checkin['lat'].",".$checkin['lng']."&sensor=false' alt='Map' />"; ?>
			</td>
		</tr>
	<?php } ?>
	</table>
	<?php } else { ?>
	<table>
		<tr>
			<td>
				<?php echo $checkins[0]['name']; ?>
			</td>
			<td>
				<?php echo date("H:i:s m/d/y", $checkin[0]['time']); ?>
			</td>
			<td>
				<?php echo "<img src='http://maps.googleapis.com/maps/api/staticmap?zoom=12&size=300x300&maptype=roadmap&markers=color:red%7C".$checkin[0]['lat'].",".$checkin[0]['lng']."&sensor=false' alt='Map' />"; ?>
			</td>
		</tr>
	</table>
	<?php } ?>
</body>
</html>
	
