<?php
session_start();
if (!isset($_SESSION['uname'])) header("Location: login.php?redirect=profile.php");
require_once("mysql.php");
$uname = $_SESSION['uname'];
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$name = mysql_real_escape_string($_POST['name']);
	$esl = mysql_real_escape_string($_POST['esl']);
	mysql_query("UPDATE profile SET name = '$name', esl = '$esl' WHERE uname = '$uname'") or die(mysql_error());
	echo "<script type='text/javascript'>alert('Changes Saved');</script>";
}
$user = mysql_query("SELECT * FROM profile WHERE uname = '$uname' LIMIT 1");
$user = mysql_fetch_array($user);
$user_id = mysql_query("SELECT id FROM users WHERE uname = '$uname' LIMIT 1");
$user_id = mysql_fetch_row($user_id);
$user['id'] = $user_id[0];
?>
<html>
<head>
	<title>Profile</title>
</head>
<body>
	<a href="index.php">Back to Home</a>
	<h2><?php echo $uname; ?></h2>
	<form method="POST">
	<table>
		<tr>
			<th>
				Username
			</th>
			<th>
				Name
			</th>
			<th>
				Your Site's ESL
			</th>
		</tr>
		<tr>
			<td>
				<?php echo $uname; ?>
			</td>
			<td>
				<input type="text" name="name" value="<?php echo $user['name']; ?>"/>
			</td>
			<td>
				<input type="text" name="esl" value="<?php echo $user['esl']; ?>" />
			</td>
		</tr>
	</table>
	<input type="submit" value="Submit Changes" />
	</form>
	Your ESL for this site is:<br/>
	/lab_3/flower_shop/event_consumer.php?d=<?php echo $user['id']; ?>
	<h2>Flower Shop Info</h2>
	<table>
		<tr>
			<td>
				Name:
			</td>
			<td>
				Ryan's Flower Shop Extraordinaire
			</td>
		</tr>
		<tr>
			<td>
				Latitude:
			</td>
			<td>
				40.3337
			</td>
		</tr>
		<tr>
			<td>
				Longitude:
			</td>
			<td>
				-111.713
			</td>
		</tr>
	</table>
</body>
</html>