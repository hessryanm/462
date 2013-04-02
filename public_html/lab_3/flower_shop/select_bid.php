<?php

require_once("mysql.php");

if (!isset($_POST['bid'])) die("No Bid Sent");

$bid = $_POST['bid'];

$bid_query = mysql_query("SELECT * FROM bid WHERE id = '$bid' LIMIT 1") or die("can't select bid: ".mysql_error());
$bid_id = $bid;
$bid = mysql_fetch_array($bid_query);

$delivery_id = $bid['delivery_id'];
mysql_query("UPDATE delivery SET delivered = 1, selected_bid = '$bid_id' WHERE id = '$delivery_id'") or die("can't update delivery: ".mysql_error());

die("done");
?>