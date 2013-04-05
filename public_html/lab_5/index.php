
<?php

if (!isset($_REQUEST['query'])) $query = '"test query"';
else $query = '"'.$_REQUEST['query'].'"';

$query = urlencode(strtolower($query));

$url = "http://search.twitter.com/search.json?rpp=20&q=".$query;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
if ($result === false) echo curl_error($ch);
curl_close($ch);

$result = json_decode($result);
$tweets = $result->results;

$tweet_text = array();

foreach($tweets as $tweet){
	array_push($tweet_text, $tweet->text);
}

$response = json_encode($tweet_text);
die($response);

?>