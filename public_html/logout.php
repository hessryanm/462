<?php
session_start();
unset($_SESSION["uname"]);
if (isset($_REQUEST['redirect'])) header("Location: /".$_REQUEST['redirect']);
else header("Location: /");
?>