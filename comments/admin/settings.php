<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<h2>Settings</h2>
<?php
require_once('Options.class.php');
$Options = new Options();
$Options->table = $Comments->config('db_options_table');

if ( !$Options->installed() ) {
	if (isset($_POST['install'])) {
		$Options->install();
		header('Location: index.php?page=settings');
	}
	?>
	<p class="alert">The options table is not installed yet. Click install to create the options table.</p>
	<form action="" method="POST">
		<button type="submit" name="install" class="btn btn-primary">Install</button>
	</form>
	<?php
} else { 
	if ( isset($_POST['save']) ) {
		$_POST = $Comments->xss($_POST);
		$_options = array(
			'base_url' => $_POST['base_url'],
			'logged_only' => isset($_POST['logged_only']) ? 1 : 0,
			'comment_status' => $_POST['comment_status'],
			'maxlength' => is_numeric($_POST['maxlength'])&&$_POST['maxlength']>-1 ? $_POST['maxlength'] : 500,
			'comments_per_page' => is_numeric($_POST['comments_per_page'])&&$_POST['comments_per_page']>0 ? $_POST['comments_per_page'] : 10,
			'comment_reply' => isset($_POST['comment_reply']) ? 1 : 0,
			'max_depth' => is_numeric($_POST['max_depth'])&&$_POST['max_depth']>0 ? $_POST['max_depth'] : 3,
			'comments_order' => $_POST['comments_order']=='asc'||$_POST['comments_order']=='desc'?$_POST['comments_order']:'desc',
			'comments_captcha' => isset($_POST['comments_captcha']) ? 1 : 0,
			'comments_captcha_logged'=> isset($_POST['comments_captcha_logged']) ? 1 : 0,
			'comments_limit' => is_numeric($_POST['comments_limit'])&&$_POST['comments_limit']>0 ? $_POST['comments_limit'] : 10,
			'mark_comment_as' => in_array($_POST['mark_comment_as'], array(0,2,3)) ? $_POST['mark_comment_as'] : 2,
			'moderation_keys' => $_POST['moderation_keys'],
			'blacklist_keys' => $_POST['blacklist_keys'],
			'default_avatar' => $_POST['default_avatar'],
			'comment_notification' => $_POST['comment_notification'],
			'reply_notification' => isset($_POST['reply_notification']) ? 1 : 0,
			'from_name' => $_POST['from_name'],
			'from_email' => $_POST['from_email']
		);
		if (isset($_POST['use_gmail']) and !empty($_POST['gmail_username']) and !empty($_POST['gmail_password'])) {
			$_options['gmail_username'] = $_POST['gmail_username'];
			$_options['gmail_password'] = $_POST['gmail_password'];
		} else $_options['gmail_username'] = $_options['gmail_password'] = '';
		
		if (!empty($_POST['admin_user']))
			$_options['admin_user'] = $_POST['admin_user'];
		if ( !empty($_POST['admin_pass1']) ) {
			if ($_POST['admin_pass1']==$_POST['admin_pass2'])
				$_options['admin_pass'] = $_POST['admin_pass1'];
			else echo '<p class="alert alert-danger">Passwords are not matching. <span class="close" data-dismiss="alert">&times;</span></p>';
		}
		foreach ($_options as $name => $value)
			$Options->update($name, $value);

		echo '<p class="alert alert-success">Settings saved. <span class="close" data-dismiss="alert">&times;</span></p>';
	}
	$option = $Options->get();
	?>
	<form action="" method="POST" autocomplete="off">
		<table class="form-table">
			<tbody>
				<tr>
					<th>General settings</th>
					<td>
						<label>Base URL <input type="text" name="base_url" class="form-control" size="35" value="<?php echo @$option['base_url']; ?>"></label> <br>
						<label><input type="checkbox" name="logged_only" value="1" <?php echo empty($option['logged_only'])?'':'checked'; ?>> Only logged users can post comments</label> <br>
						<label>Default comment status
							<select name="comment_status" class="form-control">
								<option value="1" <?php echo @$option['comment_status']==1?'selected':''; ?>>Approved</option>
								<option value="0" <?php echo isset($option['comment_status'])&&$option['comment_status']==0?'selected':''; ?>>Unapproved</option>
								<option value="2" <?php echo @$option['comment_status']==2?'selected':''; ?>>Spam</option>
							</select>
						</label> <br>
						<label>
							Maximum characters allowed for comments
							<input type="number" min="0" step="10" class="form-control" name="maxlength" value="<?php echo @$option['maxlength']; ?>"></label> <br>
						<label>
							Comments displayed per page
							<input type="number" min="1" step="1" class="form-control" name="comments_per_page" value="<?php echo @$option['comments_per_page']; ?>"></label> <br>
						<label>
							<input type="checkbox" name="comment_reply" value="1" <?php echo empty($option['comment_reply'])?'':'checked'; ?>> Enable threaded (nested) comments</label>
							<select name="max_depth" class="form-control">
								<?php
								for($i=1;$i<=10;$i++)
									echo '<option value="'.$i.'" '.(isset($option['max_depth'])&&$option['max_depth']==$i?'selected':'').'>'.$i.'</option>';
								?>
							</select>
							levels deep
						 <div style="margin-bottom:5px;"></div> 
						<label>Comments should be displayed with the 
							<select name="comments_order" class="form-control">
								<option value="asc" <?php echo @$option['comments_order']=='asc'?'selected':''; ?>>older</option>
								<option value="desc" <?php echo @$option['comments_order']=='desc'?'selected':''; ?>>newer</option>
							</select>
							comments at the top of each page</label>
					</td>
				</tr>
				<tr>
					<th>Captcha settings</th>
					<td>
						<label>
							<input type="checkbox" name="comments_captcha" value="1" <?php echo empty($option['comments_captcha'])?'':'checked'; ?>>
							Enable captcha for not logged users
						</label> <br>
						<label>
							<input type="checkbox" name="comments_captcha_logged" value="1" <?php echo empty($option['comments_captcha_logged'])?'':'checked'; ?>>
							Enable captcha for logged users
					</label>
					</td>
				</tr>
				<tr>
					<th>Comment Moderation</th>
					<td>
						<label>Maximum comments allowed per minute
							<input type="number" min="0" step="1" class="form-control" name="comments_limit" value="<?php echo @$option['comments_limit']; ?>"></label>
						before the comments is 
						<select name="mark_comment_as" class="form-control">
							<option value="3" <?php echo @$option['mark_comment_as']==3?'selected':''; ?>>Rejected</option>
							<option value="0" <?php echo isset($option['mark_comment_as'])&&$option['mark_comment_as']==0?'selected':''; ?>>Unapproved</option>
							<option value="2" <?php echo @$option['mark_comment_as']==2?'selected':''; ?>>Spam</option>
						</select>
						<br>
						<label for="moderation_keys">When a comment contains any of these words in its content, name, URL, e-mail, or IP it will be held in the <a href="?page=comments&status=unapproved">moderation queue</a> (separate with comma).</label> <br>
						<textarea name="moderation_keys" id="moderation_keys" class="form-control"><?php echo @$option['moderation_keys']; ?></textarea>
					</td>
				</tr>
				<tr>
					<th>Comment Blacklist</th>
					<td>
						<label for="blacklist_keys">When a comment contains any of these words in its content, name, URL, e-mail, or IP, it will be marked as spam (separate with comma).</label> <br>
						<textarea name="blacklist_keys" id="blacklist_keys" class="form-control"><?php echo @$option['blacklist_keys']; ?></textarea>
					</td>
				</tr>
				<tr>
					<th>Default Avatar</th>
					<td>
						For users without a custom avatar of their own, you can either display a generic logo or a generated one based on their e-mail address. <br>
						<label><input type='radio' name='default_avatar' value='mystery' <?php echo empty($option['default_avatar'])||$option['default_avatar']=='mystery'?'checked':''; ?> /> <img src='http://0.gravatar.com/avatar/66883277456b0bfaa3d167bcb4f3d1a0?s=32&amp;d=http%3A%2F%2F0.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D32&amp;r=G&amp;forcedefault=1' height='32' width='32' /> Mystery Man</label><br />
						<label><input type='radio' name='default_avatar' value='gravatar_default' <?php echo @$option['default_avatar']=='gravatar_default'?'checked':''; ?> /> <img src='http://0.gravatar.com/avatar/66883277456b0bfaa3d167bcb4f3d1a0?s=32&amp;d=&amp;r=G&amp;forcedefault=1' height='32' width='32' /> Gravatar Logo</label><br />
						<label><input type='radio' name='default_avatar' value='identicon' <?php echo @$option['default_avatar']=='identicon'?'checked':''; ?>/> <img src='http://0.gravatar.com/avatar/66883277456b0bfaa3d167bcb4f3d1a0?s=32&amp;d=identicon&amp;r=G&amp;forcedefault=1' height='32' width='32' /> Identicon (Generated)</label><br />
						<label><input type='radio' name='default_avatar' value='wavatar' <?php echo @$option['default_avatar']=='wavatar'?'checked':''; ?>/> <img src='http://0.gravatar.com/avatar/66883277456b0bfaa3d167bcb4f3d1a0?s=32&amp;d=wavatar&amp;r=G&amp;forcedefault=1' height='32' width='32' /> Wavatar (Generated)</label><br />
						<label><input type='radio' name='default_avatar' value='monsterid' <?php echo @$option['default_avatar']=='monsterid'?'checked':''; ?>/> <img src='http://0.gravatar.com/avatar/66883277456b0bfaa3d167bcb4f3d1a0?s=32&amp;d=monsterid&amp;r=G&amp;forcedefault=1' height='32' width='32' /> MonsterID (Generated)</label><br />
						<label><input type='radio' name='default_avatar' value='retro' <?php echo @$option['default_avatar']=='retro'?'checked':''; ?>/> <img src='http://0.gravatar.com/avatar/66883277456b0bfaa3d167bcb4f3d1a0?s=32&amp;d=retro&amp;r=G&amp;forcedefault=1' height='32' width='32' /> Retro (Generated)</label>
					</td>
				</tr>
				<tr>
					<th>E-mail notifications</th>
					<td>
						<label>E-mail me when comments are posted @ <input type="text" name="comment_notification" value="<?php echo @$option['comment_notification']; ?>" class="form-control" size="35"></label> <br>
						<label><input type="checkbox" name="reply_notification" value="1" <?php echo empty($option['reply_notification'])?'':'checked'; ?>> E-mail comment author when someone replies</label>
					</td>
				</tr>
				<tr>
					<th>E-mail settings</th>
					<td>
						<label for="from_name">From</label>  <input type="text" name="from_name" id="from_name" value="<?php echo @$option['from_name']; ?>" class="form-control" size="30" placeholder="Name">
						<input type="text" name="from_email" value="<?php echo @$option['from_email']; ?>" class="form-control" size="30" style="margin-left: 1px;" placeholder="E-mail"> <br>
						<div style="margin-bottom:5px;"></div>
						<label><input type="checkbox" name="use_gmail" value="1" <?php echo empty($option['gmail_username'])?'':'checked'; ?>> Use my Gmail account to send emails </label>
							<input type="text" name="gmail_username" value="<?php echo @$option['gmail_username']; ?>" class="form-control" size="30" placeholder="Gmail username">
							<input type="text" name="gmail_password" value="<?php echo @$option['gmail_password']; ?>" class="form-control" size="30" placeholder="Gmail password">
					</td>
				</tr>
				<tr>
					<th>Admin Login</th>
					<td>
						<label for="admin_user">Username</label> <input type="text" name="admin_user" id="admin_user" class="form-control" value="<?php echo @$option['admin_user']; ?>"> <br>
						<div style="margin-bottom:5px;"></div>
						Change Password <br>
						<input type="password" name="admin_pass1" id="admin_pass1" class="form-control" placeholder="New Password" value="" autocomplete="off">
						<input type="password" name="admin_pass2" id="admin_pass2" class="form-control" placeholder="Repeat Password" value="" autocomplete="off">
					</td>
				</tr>
			</tbody>
		</table>
	    <button type="submit" name="save" class="btn btn-primary">Save changes</button>
	</form>
	<?php
}
?>

<style>
	.form-table td {
		padding-bottom: 20px;
	}
	.form-table th {
		width: 200px;
		vertical-align: top;
		text-align: left;
	}
	.form-table label {
		font-weight: normal;
		cursor: pointer;
		margin-bottom: 3px;
	}
	.form-table input[type="checkbox"] {
		vertical-align: text-top;
		margin: 1px 0 0;
	}
	input[type="number"] {
		width: 60px;
	}
	.form-control {
		display: inline-block;
		width: auto;
		border-radius: 3px;
		height: 25px;
		padding: 3px 5px;
		font-size: 12px;
	}
	select.form-control {
		padding: 3px 3px;
	}
	textarea.form-control {
		height: 75px;
		width: 570px;
	}
</style>