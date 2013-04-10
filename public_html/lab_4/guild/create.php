<?php
session_start();
require_once ("mysql.php");
$uname = mysql_real_escape_string($_POST['uname']);
$pass = md5($_POST['pass']);

$query = mysql_query("SELECT uname FROM users WHERE uname = '$uname'") or die("can't select: ".mysql_error());
$toReturn;
if (mysql_num_rows($query) > 0){
	$toReturn->result = false;
	$toReturn->error = "Username already exists";
	die(json_encode($toReturn));
} 

if ($uname == "" || $pass == ""){
	$toReturn->result = false;
	$toReturn->error = "Bad Request";
	die(json_encode($toReturn));
}

$rank = rand(-100, 100);
mysql_query("INSERT INTO users (uname, pass, ranking) VALUES ('$uname', '$pass', '$rank')") or die("can't insert: ".mysql_error());
$toReturn->result = true;
die(json_encode($toReturn));

?>