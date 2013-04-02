<?php 
	session_start();
	if (!isset($_SESSION['uname'])){
		echo "<script type='text/javascript> history.back(); </script>";
		die();
	} 
	require_once("FoursquareAPI.class.php");
	require_once("mysql.php");
	
	// This file is intended to be used as your redirect_uri for the client on Foursquare
	
	// Set your client key and secret
	$client_key = "ORFB0CXNQ52CARZBLRJWRCCDBMZAK5BBIHTC3F3GRNNTVXEO";
	$client_secret = "MDJ3AMMCTRUWTSQLVVP5PTFDWIUPKGZEAWNPQKU5QHAWMDXJ";
	$redirect_uri = "https://54.235.68.69/lab_3/delivery_driver/authorize_foursquare.php";
	
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
		//$add_token_query = mysql_query("UPDATE foursquare SET auth = '$token' WHERE uname = '$uname'") or die("cannot update".mysql_error());
		
		$user = new FoursquareAPI($client_key,$client_secret);
		$user->SetAccessToken($token);
		$user_response = $user->GetPrivate("users/self");
		$user_response = json_decode($user_response);
		echo "ID: ".$user_response->response->user->id;
		
		echo "<br/><br/>";
		$checkin = new FoursquareAPI($client_key,$client_secret);
		$checkin->SetAccessToken($token);
		$params = array("sort" => "newestfirst", "limit" => 1);
		$checkin_response = $checkin->GetPrivate("users/self/checkins",$params);
		$checkin_response = json_decode($checkin_response);
		$checkin_response = $checkin_response->response->checkins;
		print_r($checkin_response->items);
		//header("Location: index.php");
	}
	
?>
