<?php
session_start();
header('Content-Type: application/json');

// Check the request type 
if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH']!="XMLHttpRequest")
	die('0');

// Require an action parameter
if (empty($_REQUEST['action']))
	die('0');

require_once('../includes/Comments.class.php');
require_once('../includes/functions.php');

//Check if is logged as admin (you may uncomment this..)
if (!isset($_SESSION['com_is_logged_admin']))
	die('0');

$Comments = new Comments();

// POST actions
if (isset($_POST['action'])) {
	switch ($_POST['action']) {
		case 'approve':
			$Comments->update_comment( array('status' => 1, 'id'=>$_POST['id']) );
		break;
		case 'unapprove':
			$Comments->update_comment( array('status' => 0, 'id'=>$_POST['id']) );
		break;
		case 'spam':
			$Comments->update_comment( array('status' => 2, 'id'=>$_POST['id']) );
		break;
		case 'delete':
			$Comments->delete_comment($_POST['id']);
		break;
		case 'get-comment':
			$Comments->set_config( array('comment_reply' => false) );
			$data = $Comments->get_comments( array('id' => $_POST['id'], 'rows'=> '*' ));
			if ($data)
				echo json_encode(array('success'=>true, 'data'=> $data));
			else echo 0;
		break;
		case 'update':
			unset($_POST['action']);
			$_POST = $Comments->xss($_POST);
			$Comments->update_comment( $_POST );
		break;
	}
}