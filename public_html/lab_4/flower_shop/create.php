<?php
session_start();
require_once "mysql.php";
$uname = mysql_real_escape_string($_POST['uname']);
$pass = md5($_POST['pass']);

$query = mysql_query("SELECT uname FROM users WHERE uname = '$uname'") or die("can't select");
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

$uuid = uniqid("hess_shop");

mysql_query("INSERT INTO users (uname, pass, unique_id) VALUES ('$uname', '$pass', '$uuid')") or die("can't insert");
$toReturn->result = true;
die(json_encode($toReturn));

?>