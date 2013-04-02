<?php
session_start();
require_once("mysql.php");

$uname = $_SESSION['uname'];
$foursquare_query = mysql_query("SELECT * FROM foursquare WHERE uname = '$uname' LIMIT 1") or die("can't select from foursquare: ".mysql_error());
$foursquare = mysql_fetch_array($foursquare_query);
$authenticated = false;
if ($foursquare['auth'] === 0 || $foursquare['auth'] === "0") $authenticated = false;
?>
<html>
<head>
	<title>Driver Site</title>
</head>
<body>

<?php if (!isset($_SESSION['uname'])) { ?>
<h1>Drivers</h1>
<a href="login.php">Log In</a>
<?php } else { ?>
<h1><?php echo $_SESSION['uname']; ?></h1>
<a href="/logout.php?redirect=lab_3/delivery_driver">Log Out</a><br/>
<a href="shops.php">Flower Shops</a><br/>
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
</body>