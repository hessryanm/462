<?php
session_start();
require_once("mysql.php");

$uname = $_SESSION['uname'];
$foursquare_query = mysql_query("SELECT * FROM foursquare WHERE uname = '$uname' LIMIT 1") or die("can't select from foursquare: ".mysql_error());
$foursquare = mysql_fetch_array($foursquare_query);
$authenticated = 1;
if ($foursquare['auth'] === 0 || $foursquare['auth'] === "0") $authenticated = 0;
if (isset($_SESSION['uname'])) {
	$pn_query = mysql_query("SELECT phone_number, id FROM users WHERE uname = '$uname' LIMIT 1") or die("can't get phone number: ".mysql_error());
	$phone_number = mysql_fetch_row($pn_query);
	$user_id = $phone_number[1];
	$phone_number = $phone_number[0];
}
?>
<html>
<head>
	<title>Driver Site</title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>
<body>

<?php if (!isset($_SESSION['uname'])) { ?>
<h1>Drivers</h1>
<a href="login.php">Log In</a>
<?php } else { ?>
<h1><?php echo $_SESSION['uname']; ?></h1>
<a href="/logout.php?redirect=lab_4/delivery_driver">Log Out</a><br/>
<br/>
Your ESL is: /lab_4/delivery_driver/event_consumer.php?d=<?php echo $user_id; ?>
<br/><br/>
Phone Number: 
<input type="text" id="phone_number" value="<?php echo $phone_number; ?>" name="number" />
<input type="hidden" value="<?php echo $uname; ?>" id="uname" />
<input id="save_number" type="button" value="Save" /><br/>
<?php if($authenticated){ ?>
<h3>Last Checkin:</h3>
<table>
	<tr>
		<td>
			<?php echo $foursquare['name']; ?>
		</td>
		<td>
			<?php echo date("H:i:s m/d/Y", $foursquare['time']); ?>
		</td>
	</tr>
</table>
<?php } else { ?>
<a href="authorize_foursquare.php">Authorize Foursquare</a>
<?php } ?>
<?php } ?>
<script src="js/index.js" type="text/javascript"></script>
</body>