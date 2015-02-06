<?php
session_start();
$link   = mysql_connect('pineappletele.com', 'maxkuang1994', 'Pineapple110');
$status = explode('  ', mysql_stat($link));
print_r($status);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Comment System</title>
	<style type="text/css">
		body {
			margin: 0;
			font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
			line-height: 20px;
			color: #333;
			background: #e9e9e9 url(assets/images/white-texture.png) repeat;
		}
		.container {
			min-height: 300px;
			min-width: 300px;
			max-width: 700px;
			margin: 2em auto;
			padding: 1em 2em;
			float: none;
			background: rgba(255, 255, 255, 0.7);
			border: 1px solid #D8D8D8;
			-webkit-border-radius: 6px;
			-moz-border-radius: 6px;
			border-radius: 6px;
			-webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, .065);
			-moz-box-shadow: 0 1px 4px rgba(0,0,0,.065);
			box-shadow: 0 1px 4px rgba(0, 0, 0, .065);
		}
	</style>
</head>
<body>
	<div class="container">
		<?php
		//Simple login
		echo '<div style="float:right;">';
		if (isset($_SESSION['user']))
			echo 'Logged as <a href="account.php">'.$_SESSION['user']['name'].'</a> | <a href="account.php?page=logout">Logout</a>';
		else echo '<a href="account.php?page=login" class="btn">Login</a> <a href="account.php?page=signup" class="btn">Sign up</a>';
		echo'</div>';
		?>
		<a href="admin">Admin Dashboard Here !</a> | <a href="index2.php">Template #2</a> <br>
		<?php 

		//Include Comments Class
 		require_once('includes/Comments.class.php');
		$Comments = new Comments();
		
		//Set some custom config. NOTE: Not all config options will work here ! Read the documentation.
		$config = array(
			'page_title' => 'My Page', //This is your page title, it's optional
		);
		$Comments->set_config($config);
		
		//Display comments
		$Comments->display_comments();

		?>
	</div>
</body>
</html>