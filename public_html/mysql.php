<?php
$con = mysql_connect('localhost', 'root', 'root') or die("Could not connect: ".mysql_error());
mysql_select_db('cs462', $con) or die("Cannot use db: ".mysql_error());