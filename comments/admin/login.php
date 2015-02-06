<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <title>Sign in &middot; Comments Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" media="all">
        <style type="text/css">
            body {
                padding-top: 40px;
                padding-bottom: 40px;
                background-color: #f5f5f5;
            }
            .form-signin {
                max-width: 300px;
                padding: 19px 29px 29px;
                margin: 0 auto 20px;
                background-color: #fff;
                border: 1px solid #e5e5e5;
                -webkit-border-radius: 4px;
                -moz-border-radius: 4px;
                border-radius: 4px;
                -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
            }
            input {  margin-bottom: 15px; }
        </style>
    </head>
    <body>
        <div class="container">
            <form method="post" action="" class="form-signin">
                <h2 class="form-signin-heading">Please sign in</h2>
                <?php
                if (isset($_POST['signin'], $_POST['user'], $_POST['pass']))
                    if ( $_POST['user'] == $Comments->config('admin_user') and  $_POST['pass'] == $Comments->config('admin_pass')  ) {
                        $_SESSION['com_is_logged_admin'] = true;
                        header('Location: index.php');
                    } else echo '<p class="alert alert-danger">Error ! Try again.</p>';
                ?>
                <input type="text" name="user" class="form-control" placeholder="Username">
                <input type="password" name="pass" class="form-control" placeholder="Password">
                <button class="btn btn-primary" type="submit" name="signin">Sign in</button>
            </form>
        </div>
    </body>
</html>
