<?php

session_start();
if (!isset($_SESSION['uname'])) header("Location: login.php?redirect=profile.php");
if ($_SESSION['uname'] != 'admin') header("Location: index.php");
require_once("mysql.php");


?>