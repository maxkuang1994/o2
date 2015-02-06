<?php
session_start();
ob_start();

require_once('config.php');
require_once('includes/Database.class.php');

$db = new Database();
if ( !empty($database['user']) )
	$db->connect( $database );

$link   = mysql_connect('pineappletele.com', 'maxkuang1994', 'Pineapple110');
$status = explode('  ', mysql_stat($link));
print_r($status);

function xss($val) { return htmlspecialchars(trim($val), ENT_QUOTES); }

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Comment System</title>
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
	<style type="text/css">
		body {
			margin: 0;
			font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
			line-height: 20px;
			color: #333;
			background: #e9e9e9 url(assets/images/white-texture.png) repeat;
		}
		.container {
			width: 700px;
			min-height: 300px;
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
		<a href="index.php">&larr; Back to comments</a> <a href="?page=logout" class="pull-right">Logout</a>
		<?php
		 echo $status;
		if (!isset($_GET['page'])) $_GET['page'] = '';
		switch ($_GET['page']) {
		 	default:
		 		echo '<h3>Account Settings</h3>';
		 		if (!isset($_SESSION['user']))
		 			header('Location: account.php?page=login');
		 		else {
		 			$db = new Database();
		 			if (isset($_POST['save'])) {
		 				$_POST['name'] = trim($_POST['name']);
		 				$_POST['npassword'] = trim($_POST['npassword']);
		 				if (preg_match('/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i', $_POST['email']) &&
		 					!$db->select('users', 'id', 'email="'.$_POST['email'].'" AND id!="'.$_SESSION['user']['id'].'"', null, 1))
		 					$data['email'] = $_POST['email'];
		 				if (!empty($_POST['name']))
		 					$data['name'] = xss($_POST['name']);
		 				if (preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $_POST['url']))
		 					$data['url'] = xss($_POST['url']);
		 				if (!empty($_POST['npassword']))
		 					$data['password'] = md5($_POST['npassword']);
		 				if (!empty($data))
		 					$db->update('users', $data, 'id="'.$_SESSION['user']['id'].'"', 1);
		 				echo '<p class="alert alert-success">Successfully saved.</p>';
		 			}
		 			if ($db->select('users', '*', 'id="'.$_SESSION['user']['id'].'"', null,1)) {
						$res = $db->getResult();
						?><form action="" method="POST">
							<label for="email">Email</label>
							<input type="text" name="email" id="email" value="<?php echo $res[1]['email']; ?>">
							<label for="name">Name</label>
							<input type="text" name="name" id="name" value="<?php echo $res[1]['name']; ?>">
							<label for="url">URL</label>
							<input type="text" name="url" id="url" value="<?php echo $res[1]['url']; ?>">
							<label for="password">New password</label>
							<input type="password" name="npassword" id="password"><br>
							<button type="submit" name="save" class="btn btn-primary">Save changes</button>
						</form><?php
					}
		 		}
			break;

			//Login
			case 'login':
				echo '<h3>Login</h3>';
				if (isset($_SESSION['user']))
					header('Location: index.php');
				if (isset($_POST['login'])) {
					if (empty($_POST['email']) or empty($_POST['password']))
						echo '<p class="alert alert-danger">Enter your email & password.</p>';
					else {
						$db = new Database();
						if ($db->select('users', 'id,name,email,url', 'email="'.xss($_POST['email']).'" AND password="'.md5($_POST['password']).'"', null,1)){
							$res = $db->getResult();
							$_SESSION['user'] = array(
								'id'   => $res[1]['id'],
								'name' => $res[1]['name'],
								'email'=> $res[1]['email'],
								'url'  => $res[1]['url']
							);
							header('Location: index.php');
						}
						else echo '<p class="alert alert-danger">Wrong email or password.</p>';
					}
				}
				?><form action="" method="POST">
					<label for="email">Email</label>
					<input type="text" name="email" id="email">
					<label for="password">Password</label>
					<input type="password" name="password" id="password">
					<br>
					<button type="submit" name="login" class="btn btn-primary">Login</button>
				</form>
				<a href="?page=signup">Sign up</a><?php
			break;

			//Signup
			case 'signup':
				echo '<h3>Sign up</h3>';
				if (isset($_SESSION['user']))
					header('Location: index.php');
				if (isset($_POST['signup'])) {
					$_POST['name'] = trim($_POST['name']);
		 			$_POST['password'] = trim($_POST['password']);
					if (empty($_POST['email']) or empty($_POST['password']) or empty($_POST['name']))
						echo '<p class="alert alert-danger">Please complete all fields !</p>';
					else {
						if (!preg_match('/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i', $_POST['email']))
							echo '<p class="alert alert-danger">Enter a valid email address.</p>';
						else {
							$db = new Database();
							if ($db->select('users', 'id', 'email="'.xss($_POST['email']).'"', null,1))
								echo '<p class="alert alert-danger">An account with this email already exists !</p>';
							else {
								$data = array(
									'email' => $_POST['email'],
									'name' => xss($_POST['name']),
									'password'=>md5($_POST['password'])
								);
								if ($db->insert('users', $data)) {
									echo '<p class="alert alert-success">Account created. <a href="?page=login">Login</a> </p>';
									unset($_POST);
								} else echo '<p>Unexpected error. Try again.</p>';
							}
						}
					}
				}
				?><form action="" method="POST">
					<label for="email">Email</label>
					<input type="text" name="email" id="email" value="<?php echo xss(@$_POST['email']); ?>">
					<label for="name">Name</label>
					<input type="text" name="name" id="name" value="<?php echo xss(@$_POST['name']); ?>">
					<label for="password">Password</label>
					<input type="password" name="password" id="password"><br>
					<button type="submit" name="signup" class="btn btn-primary">Sign up</button>
				</form>
				<a href="?page=login">Login</a><?php
			break;

			case 'logout':
				session_destroy();
				header('Location: index.php');
			break;
		}
		?>
	</div>
</body>
</html>