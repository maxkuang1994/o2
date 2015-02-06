<?php
session_start();
define('BASEPATH', TRUE);

//Include Comments Class
 require_once('../includes/Comments.class.php');
$Comments = new Comments();

require_once('functions.php');

//Check if the user is logged
if ( !isset($_SESSION['com_is_logged_admin']) ) {
	require_once('login.php');
	die();
}

//Logout action
if (isset($_GET['logout'])) {
	session_destroy();
	header('Location: index.php');
}

//Set this for ajax.php
$_SESSION['com_is_logged_admin'] = TRUE;

//Get the current page from url
$page = (!empty($_GET['page']))  ?  $_GET['page'] : '';
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Comments Admin</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" media="all">
		<link rel="stylesheet" type="text/css" href="css/style.css" media="all">
		<script src="js/jquery-1.9.1.min.js"></script>
		<script src="js/jquery-ui-1.9.2.custom.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/script.js"></script>
	</head>
	<body>
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
				<a href="../" class="navbar-brand" title="Visit site">&larr;</a>	  
				<ul class="nav navbar-nav">
					<li<?php echo $page==''?' class="active"':''; ?>><a href="index.php">Dashboard</a></li>
					<li<?php echo $page=='comments'?' class="active"':''; ?>><a href="?page=comments">Comments</a></li>
					<li<?php echo $page=='settings'?' class="active"':''; ?>><a href="?page=settings">Settings</a></li>
				</ul>
				<ul class="nav navbar-nav pull-right">
		        	<li><a href="?logout">Logout</a></li>
		    	</ul>
			</div>
		</div>
		<div class="container">
			<?php
			//Switch by the page and require the corresponding file
			switch ($page) {
				case '': default:
					?>
					<h2>Dashboard</h1>
						<p>Welcome to Ajax Comment System Admin. <br> Here you can view comments, edit, delete and change settings. <br> If you have any troubles contact me @ <a href="mailto:hazzardweb@gmail.com">hazzardweb@gmail.com</a>. </p>
					<?php
				break;
				case 'comments':
					require_once('comments.php');
				break;
				case 'settings':
					require_once('settings.php');
				break;
			}
			?>
			<hr> <p>&copy; HazzardWeb 2013</p>
		</div>
	</body>
</html>