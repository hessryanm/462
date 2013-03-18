<?php
session_start();
if (!isset($_SESSION['uname'])) header("Location: login.php?redirect=profile.php");
require_once("mysql.php");
$uname = $_SESSION['uname'];
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$name = mysql_real_escape_string($_POST['name']);
	$esl = mysql_real_escape_string($_POST['esl']);
	mysql_query("UPDATE profile SET name = '$name', esl = '$esl' WHERE uname = '$uname'") or die(mysql_error());
}
$user = mysql_query("SELECT * FROM profile WHERE uname = '$uname' LIMIT 1");
$user = mysql_fetch_array($user);
?>
<html>
<head>
	<title>Profile</title>
</head>
<body>
	<a href="/flower_shop/">Back to Home</a>
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
				ESL
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
</body>
</html>