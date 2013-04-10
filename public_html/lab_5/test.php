<?php




?>

$eventJSON = json_encode($event);
$ch = curl_init($targeturl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $eventJSON);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
       'Content-Type: application/json',
       'Content-Length: ' . strlen($eventJSON))
);
$result = curl_exec($ch);