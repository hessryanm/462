<?php

$con = mysql_connect("localhost", "root", "root") or die("can't connect: ".mysql_error());
mysql_select_db("lab_3_shop", $con) or die("can't select db: ".mysql_error());

?>
