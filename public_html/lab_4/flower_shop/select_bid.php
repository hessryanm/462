<?php
session_start();
if (!isset($_SESSION['uname'])) die("Login Required");
require_once("mysql.php");
require_once("/lab_4/send_event.php");

if (!isset($_POST['bid'])) die("No Bid Sent");

$bid = $_POST['bid'];

$bid_query = mysql_query("SELECT * FROM bid WHERE id = '$bid' LIMIT 1") or die("can't select bid: ".mysql_error());
$bid_id = $bid;
$bid = mysql_fetch_array($bid_query);

$delivery_id = $bid['delivery_id'];
mysql_query("UPDATE delivery SET status = '2', selected_bid = '$bid_id' WHERE id = '$delivery_id'") or die("can't update delivery: ".mysql_error());

$uname = $_SESSION['uname'];
$shop = mysql_query("SELECT * FROM users WHERE uname = '$uname' LIMIT 1") or die("Can't select shop: ".mysql_error());
$shop = mysql_fetch_array($shop);

$data = array("_domain" => "rfq", "_name" => "bid_awarded", "driver_id" => $bid['driver_id'], "delivery_id" => $delivery_id, "shop_id" => $shop['unique_id']);
send_event($guild_esl, $data);

die("done");
?>