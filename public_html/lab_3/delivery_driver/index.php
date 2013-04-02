<?php

require_once("../mysql.php");

if(isset($_SESSION['uname'])){
	$auth = true;
	$id = $_SESSION['id'];
	$query = "SELECT e.esl, s.name, s.lat, s.lng FROM flower_shop_esl as e LEFT JOIN flower_shop as s ON s.id = e.shop_id WHERE e.driver_id = '$id' ";
	$mysql = mysql_query($query) or die(mysql_error());
}

?>
<html>
<head>
	<title>Driver Site</title>
</head>
<body>

<h1>Drivers</h1>


</body>