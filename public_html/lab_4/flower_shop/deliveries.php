<?php
session_start();
if (!isset($_SESSION['uname'])) header("Location: login.php?redirect=deliveries.php");

$user_query = mysql_query("SELECT id FROM users WHERE uname = '{$_SESSION['uname']}' LIMIT 1") or die("can't select user: ".mysql_error());
$user_id = mysql_fetch_row($user_query);
$user_id = $user_id[0];

require_once("mysql.php");
$no_bids = array();
$has_bids = array();
$bid_selected = array();
$picked_up = array();
$completed = array();

$no_bids_query = mysql_query("SELECT * FROM delivery WHERE status = '0' AND shop_id = '$user_id' ORDER BY delivery_time ASC") or die("can't get no bids: ".mysql_error());
$has_bids_query = mysql_query("SELECT * FROM delivery WHERE status = '1' AND shop_id = '$user_id' ORDER BY delivery_time ASC") or die("can't get has bids: ".mysql_error());
$bid_selected_query = mysql_query("SELECT * FROM delivery WHERE status = '2' AND shop_id = '$user_id' ORDER BY delivery_time ASC") or die("can't get bid selected: ".mysql_error());
$picked_up_query = mysql_query("SELECT * FROM delivery WHERE status = '3' AND shop_id = '$user_id' ORDER BY delivery_time ASC") or die("can't get picked up: ".mysql_error());
$completed_query = mysql_query("SELECT * FROM delivery WHERE status = '4' AND shop_id = '$user_id' ORDER BY delivery_time ASC") or die("can't get completed: ".mysql_error());

while($delivery = mysql_fetch_array($no_bids_query)) array_push($no_bids, $delivery);

while($delivery = mysql_fetch_array($has_bids_query)){
	$delivery_id = $delivery['id'];
	$bids = array();
	
	$bid_query = mysql_query("SELECT id, price, delivery_time FROM bid WHERE delivery_id = '$delivery_id'") or die("can't get bids: ".mysql_error());
	
	while($bid = mysql_fetch_array($bid_query)) array_push($bids, $bid);
	
	$delivery['bids'] = $bids;
	
	array_push($has_bids, $delivery);
}

while($delivery = mysql_fetch_array($bid_selected_query)){
	$bid_id = $delivery['selected_bid'];
	
	$bid_query = mysql_query("SELECT id, price, delivery_time FROM bid WHERE id = '$bid_id' LIMIT 1") or die("can't get selected bid: ".mysql_error());
	$delivery['bid'] = mysql_fetch_array($bid_query);
	array_push($bid_selected, $delivery);
}

while($delivery = mysql_fetch_array($picked_up_query)) array_push($picked_up, $delivery);

while($delivery = mysql_fetch_array($completed_query)) array_push($completed, $delivery);
?>
<html>
<head>
	<title>Devlieries</title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript" src="js/underscore.js"></script>
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
				<input type="button" value="Submit" onclick="save_delivery()" />
			</td>
		</tr>
	</table>
	</form>
	
	<h3>Deliveries with No Bids</h3>
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
		<?php foreach($no_bids as $delivery){ ?>
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
		<?php } ?>
	</table>
	
	<h3>Deliveries with Unselected Bids</h3>
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
		<?php foreach($has_bids as $delivery){ ?>
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
						<th colspan=3>
							Bids
						</th>
					</tr>
					<tr>
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
	
	<h3>Deliveries with Selected Bids</h3>
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
		<?php foreach($bid_selected as $delivery){ ?>
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
						<th colspan=3>
							Bids
						</th>
					</tr>
					<tr>
						<th>
							Price
						</th>
						<th>
							Est. Delivery
						</th>
						<th></th>
					</tr>
					<tr>
						<td>
							$<?php echo number_format($delivery['bid']['price'], 2); ?>
						</td>
						<td>
							<?php echo date("H:i:s d/m/Y", $delivery['bid']['delivery_time']); ?>
						</td>
						<td>
							<input type="hidden" value="<?php echo $delivery['id']; ?>" />
							<input type="button" value="Picked Up" class="delivery_picked_up" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php } ?>
	</table>
	
	<h3>Deliveries Picked Up</h3>
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
		<?php foreach($picked_up as $delivery){ ?>
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
		<?php } ?>
	</table>
	
	<h3>Deliveries Completed</h3>
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
		<?php foreach($completed as $delivery){ ?>
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
		<?php } ?>
	</table>

	<script type="text/javascript" src="js/deliveries.js"></script>
</body>