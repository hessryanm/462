<?php

require_once("mysql.php");

$user = $_POST['uname'];
$number = $_POST['number'];

mysql_query("UPDATE users SET phone_number = '$number' WHERE uname = '$user'") or die("can't update: ".mysql_error());

die("done");
?>