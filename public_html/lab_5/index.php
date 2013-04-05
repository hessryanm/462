
<?php

$con = mysql_connect("localhost", "root", "password") or die("can't connect: ".mysql_error());
mysql_select_db("lab_5", $con) or die("can't select db: ".mysql_error());

$request = mysql_real_escape_string(json_encode($_REQUEST));
$server = mysql_real_escape_string(json_encode($_SERVER));

mysql_query("INSERT INTO request (request, server) VALUES ('$request', '$server')");
$request_id = mysql_insert_id();

if (isset($_REQUEST["_domain"]) && $_REQUEST['_domain'] == "rft" && isset($_REQUEST['_name']) && $_REQUEST['_name'] == "tweet_request"){

	if (!isset($_REQUEST['query'])) $query = '"test query"';
	else $query = $_REQUEST['query'];
	
	if (!isset($_REQUEST['responseESL'])) die("No ESL Provided");

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
	
	$ch = curl_init($_REQUEST['responseESL']);
	$data = array("_name" => "tweets_found", "_domain" => "rft", "callbackNumber" => $_REQUEST['callbackNumber'], "results" => $response);
	
	mysql_query("UPDATE request SET response = 'json_encode($data)' WHERE id = '$request_id'");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	$result = curl_exec($ch);
	if ($result === false) echo curl_error($ch);
	else {
		// echo "<br/><br/>";
		// print_r(curl_getinfo($ch));
	}
	curl_close($ch);

} else die("Invalid Request");

?>