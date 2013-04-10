<?php

$guild_esl = "/lab_4/guild/event_consumer.php";

function send_event($url, $data = array()){
	if (strpos($url, "http") === false){
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") $url = "https://".$_SERVER['HTTP_HOST'].$url;
		else $url = "http://".$_SERVER['HTTP_HOST'].$url;
	} 
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	$result = curl_exec($ch);
	if ($result === false) echo curl_error($ch);
	curl_close($ch);
}

?>