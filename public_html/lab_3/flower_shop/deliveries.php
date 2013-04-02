<?php
session_start();
if (!isset($_SESSION['uname'])) header("Location: login.php?redirect=profile.php");
if ($_SESSION['uname'] != 'admin') header("Location: index.php");
require_once("mysql.php");
$deliveries = array();
$delivery_query = mysql_query("SELECT * FROM delivery WHERE delivered = 0 ORDER BY delivery_time ASC") or die(mysql_error());
while($delivery = mysql_fetch_array($delivery_query)){
	$delivery_id = $delivery['id'];
	$delivery['bids'] = array();
	$bids_query = mysql_query("SELECT * FROM bid WHERE delivery_id = '$delivery_id'") or die("can't select bids: ".mysql_error());
	while($bid = mysql_fetch_array($bids_query)){
		$driver_id = $bid['driver_id'];
		$user_query = mysql_query("SELECT uname FROM users WHERE id = '$driver_id' LIMIT 1") or die("can't select user: ".mysql_error());
		$uname = mysql_fetch_row($user_query);
		$uname = $uname[0];
		$profile_query = mysql_query("SELECT name FROM profile WHERE uname = '$uname' LIMIT 1") or die("Can't select profile: ".mysql_error());
		$name = mysql_fetch_row($profile_query);
		$name = $name[0];
		$bid['name'] = $name;
		array_push($delivery['bids'], $bid);
	}
	
	array_push($deliveries, $delivery);
} 
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
	<link rel="stylesheet" type="text/css" href="css/deliveries.css" />
</head>
<body>
	<a href="index.php">Back to Home</a>
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
	<table class="deliveries">
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
		<tr class="delivery">
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
		<tr class="popdown">
			<td colspan=3>
				<table>
					<tr>
						<th colspan=4>
							Bids
						</th>
					</tr>
					<tr>
						<th>
							Driver Name
						</th>
						<th>
							Price
						</th>
						<th>
							Est. Delivery
						</th>
						<th></th>
					</tr>
					<?php foreach($delivery['bids'] as $bid){ ?>
					<tr>
						<td>
							<?php echo $bid['name']; ?>
						</td>
						<td>
							$<?php echo number_format($bid['price'], 2); ?>
						</td>
						<td>
							<?php echo date("H:i:s d/m/Y", $bid['delivery_time']); ?>
						</td>
						<td>
							<input type="hidden" value="<?php echo $bid['id']; ?>" />
							<input type="button" value="Select" class="select_bid" />
						</td>
					</tr>
					<?php } ?>
				</table>
			</td>
		</tr>
		<?php } ?>
	</table>
	
	<script type="text/javascript" src="js/delivery.js"></script>
	<script type="text/javascript" src="js/deliveries.js"></script>
</body>