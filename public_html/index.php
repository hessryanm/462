<?php
session_start();
require_once "mysql.php";

$users_query = mysql_query("SELECT uname FROM users");
$users = array();
while($user = mysql_fetch_array($users_query)){
	array_push($users, $user[0]);
}
?>	

<html>
<head>
	<title>Foursquare Integration</title>
	<link rel="stylesheet" type="text/css" href="css/index.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>
<body>
<div class="header">
	<?php
	if (isset($_SESSION['uname'])){
	?>
	<a href="/profile.php?uname=<?php echo $_SESSION['uname']; ?>" class="header_link">
		<h2><?php echo $_SESSION['uname']; ?></h2>
	</a>
	<a href="/logout.php" class="header_link">
		<h4>Logout</h4>
	</a>
	<?php } else { ?>
	<a href="/login.php?redirect=/" class="header_link">
		<h3>Log In</h3>
	</a>
	<?php } ?>
</div>
<div class="body">
	<h1>Users:</h1>
	<?php foreach($users as $user){ ?>
	<div class="user">
		<a href="/profile.php?uname=<?php echo $user; ?>">
			<?php echo $user; ?>
		</a>
	</div>
	<?php } ?>
</div>
</body>
</html>
