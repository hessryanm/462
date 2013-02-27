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
	$checkins = json_decode($response);
	$checkins = $checkins->response->checkins;
	print_r($checkins);
}
?>

<html>
<head>
	<title><?php if($view_own) echo "Your"; else echo $view_user."'s" ?> Profile</title>
</head>
<body>
	<?php if ($view_own && !$authorized) { ?>
	<a href="/foursquare.php">Authorize Foursquare</a>
	<?php } else if ($view_own) { ?>
	Authorized!
	<?php } ?>
</body>
</html>
	
