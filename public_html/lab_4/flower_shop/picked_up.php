<?php
session_start();
if (!isset($_SESSION['uname'])) die("Login Required");
require_once("mysql.php");
require_once("../send_event.php");

if (!isset($_POST['delivery'])) die("No Delivery Sent");

$delivery = $_POST['delivery'];

mysql_query("UPDATE delivery SET status = '3' WHERE id = '$delivery'") or die("can't update delivery: ".mysql_error());

$uname = $_SESSION['uname'];
$shop = mysql_query("SELECT * FROM users WHERE uname = '$uname' LIMIT 1") or die("Can't select shop: ".mysql_error());
$shop = mysql_fetch_array($shop);

$data = array("_domain" => "delivery", "_name" => "picked_up", "time" => time(), "delivery_id" => $delivery_id, "shop_id" => $shop['unique_id']);
send_event($guild_esl, $data);

die("done");
?>