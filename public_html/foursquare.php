<?php 
	session_start();
	if (!isset($_SESSION['uname'])){
		echo "<script type='text/javascript> history.back(); </script>";
		die();
	} 
	require_once("FoursquareAPI.class.php");
	
	// This file is intended to be used as your redirect_uri for the client on Foursquare
	
	// Set your client key and secret
	$client_key = "ORFB0CXNQ52CARZBLRJWRCCDBMZAK5BBIHTC3F3GRNNTVXEO";
	$client_secret = "MDJ3AMMCTRUWTSQLVVP5PTFDWIUPKGZEAWNPQKU5QHAWMDXJ";
	$redirect_uri = "http://54.235.68.69/foursquare.php";
	
	// Load the Foursquare API library
	$foursquare = new FoursquareAPI($client_key,$client_secret);
	
	// If the link has been clicked, and we have a supplied code, use it to request a token
	if(array_key_exists("code",$_GET)){
		$token = $foursquare->GetToken($_GET['code'],$redirect_uri);
	}
	
	if(!isset($token)){ 
		header("Location: ".$foursquare->AuthenticationLink($redirect_uri));
	} else{
		$uname = $_SESSION['uname'];
		$add_token_query = mysql_query("UPDATE users SET foursquare_token = '$token' WHERE uname = '$uname'");
		header("Location: /profile.php?uname=".$uname);
	}
	
?>