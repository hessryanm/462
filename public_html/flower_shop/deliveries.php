<?php
session_start();
if (!isset($_SESSION['uname'])) header("Location: login.php?redirect=profile.php");
if ($_SESSION['uname'] != 'admin') header("Location: /flower_shop/");
require_once("mysql.php");
$deliveries = array();
$delivery_query = mysql_query("SELECT * FROM delivery WHERE delivered = 0 ORDER BY delivery_time ASC") or die(mysql_error());
while($delivery = mysql_fetch_array($delivery_query)) array_push($deliveries, $delivery);
$esls = array();
$esl_query = mysql_query("SELECT esl FROM profile WHERE esl != ''") or die(mysql_error());
while ($esl = mysql_fetch_row($esl_query)) array_push($esls, $esl[0]);
?>
<html>
<head>
	<title>Devlieries</title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript" src="js/underscore.js"></script>
	<script type="text/javascript">
	var esls = [];
	<?php foreach($esls as $esl) { ?>
		esls.push('<?php echo $esl; ?>');
	<?php } ?>
	</script>
</head>
<body>
	<a href="/flower_shop/">Back to Home</a>
	<h1>Deliveries</h1>
	<h3>New Delivery</h3>
	<form id="new_delivery">
	<table>
		<tr>
			<td>
				Pickup Time:
			</td>
			<td>
				<input type="text" id="pickup_time" />
			</td>
		</tr>
		<tr>
			<td>
				Delivery Time:
			</td>
			<td>
				<input type="text" id="delivery_time" />
			</td>
		</tr>
		<tr>
			<td>
				Delivery Address:
			</td>
			<td>
				<input type="text" id="delivery_address" />
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<input type="button" value="Submit" onclick="send_events()" />
			</td>
		</tr>
	</table>
	</form>
	
	<h3>Undelivered Deliveries</h3>
	<table>
		<tr>
			<th>
				Delivery Time
			</th>
			<th>
				Delivery Address
			</th>
			<th>
				Pickup Time
			</th>
		</tr>
		<?php foreach($deliveries as $delivery){ ?>
		<tr>
			<td>
				<?php echo $delivery['delivery_time']; ?>
			</td>
			<td>
				<?php echo $delivery['delivery_address']; ?>
			</td>
			<td>
				<?php echo $delivery['pickup_time']; ?>
			</td>
		</tr>
		<?php } ?>
	</table>
	
	<script type="text/javascript" src="js/delivery.js"></script>
</body>