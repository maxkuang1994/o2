<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('functions.php');
$Comments = new Comments();
$maxlength = $Comments->config('maxlength');
$base_url = $Comments->config('base_url');

//Include asssets
if ( $Comments->config('bootstrap') )
	echo '<link rel="stylesheet" type="text/css" href="'.$base_url.'assets/css/bootstrap.min.css">';

echo '<link rel="stylesheet" type="text/css" href="'.$base_url.'assets/css/comments.css">';

if ( $Comments->config('jquery') ) 
	echo '<script src="'.$base_url.'assets/js/jquery-1.9.1.min.js"></script>';
echo '<script src="assets/js/jquery-ui-1.9.2.custom.min.js"></script>'
?>
<script>
	var commentsConfig = {
		max_depth  : <?php echo $Comments->config('max_depth'); ?>,
		per_page   : <?php echo $Comments->config('comments_per_page'); ?>,
		page       : '<?php echo $Comments->config('comment_page') ? $Comments->config('comment_page') : $Comments->currentURL(); ?>',
		page_title : '<?php echo $Comments->config('page_title'); ?>',
		lang       : <?php echo json_encode( $Comments->config('js_lang') ); ?>,
		ajaxurl    : '<?php echo $Comments->config('ajaxurl'); ?>'
	};
</script>
<?php
echo '<script src="'.$base_url.'assets/js/mustache.min.js"></script>
	<script src="'.$base_url.'assets/js/jquery-linkify.min.js"></script>
	<script src="'.$base_url.'assets/js/comments.js"></script>';
?>

<!-- Comments Template -->
<script id="commentsTemplate" type="text/template">
	<li id="comment-{{id}}" class="{{hidden}}">
		{{#author_url}}
			<a href="{{author_url}}" class="avatar pull-left" target="_blank"><img src="{{avatar}}" height="50" width="50"></a>
		{{/author_url}}
		{{^author_url}}
			<span class="avatar pull-left"><img src="{{avatar}}" height="50" width="50"></span>
		{{/author_url}}
		<div class="right">
			<div class="bubble"></div>
			<div class="box highlight">
				<div class="header">
					{{#author_url}}
						<a href="{{author_url}}" target="_blank" class="author">{{author}}</a>
					{{/author_url}}
					{{^author_url}}
						<span class="author">{{author}}</span>
					{{/author_url}}
					<a href="#page/__paged__/comment/{{id}}" class="date" title="{{date}}">
						{{short_date}}
					</a>
					{{#status}}
						<span class="pending label label-warning">{{_status}}</span>
					{{/status}}
					{{#admin}}
						<a href="<?php echo $base_url; ?>admin/?page=comments#edit-{{id}}" class="actions">Edit</a>
					{{/admin}}
				</div>
				<div class="content">{{{comment}}}</div>
				{{#reply}}
					{{#depth}}{{/depth}}
					{{^depth}}
						<a href="#" class="reply" data-id="{{id}}">Reply</a>
					{{/depth}}
				{{/reply}}
			</div>
		</div>
		<div class="reply-box"></div>
		<ul class="list replies">
			{{{replies}}}
		</ul>
	</li>
</script>

<!-- Pagination Template -->
<script id="paginationTemplate" type="text/template">
	<div class="pagination pagination-small pagination-centered">
	  <ul>
	    <li><a href="#page/{{prev}}" data-paged="{{prev}}" title="Previous">&laquo;</a></li>
	    {{#pages}}
	    	<li><a href="#page/{{.}}" data-paged="{{.}}">{{.}}</a></li>
	    {{/pages}}
	    <li><a href="#page/{{next}}" data-paged="{{next}}" title="Next">&raquo;</a></li>
	  </ul>
	</div>
</script>

<div class="comments" >
	<?php if ( !$Comments->config('logged_only') or ($Comments->config('logged_only') and com_is_logged()) ) : ?>
		<h4 id="comment">Leave a comment</h4>
		<div class="add-comment">
			<form action="" method="post" class="form hidden" id="commentform">
			<?php if ( !com_is_logged() ) : ?>
				<div>
					<div class="input">
				      	<input type="text" name="author" id="commentAuthor" placeholder="Name">
				    	<span class="help-inline"></span>
				    </div>
				    <div class="input">
				      	<input type="text" name="email" id="commentEmail" placeholder="Email">
				    	<span class="help-inline"></span>
				    </div>
					 <div class="input">
				      	<input type="text" name="url" id="commentUrl" placeholder="Website">
				    	<span class="help-inline"></span>
				    </div>
				    <?php
				    if ( $Comments->config('comments_captcha') ) :
				    	$Comments->captcha();
				    ?>
					    <div class="input">
					      	<input type="text" name="captcha" id="commentCaptcha" placeholder="Captcha">
					    	<img src="includes/captcha.php" class="captcha-image" title="Click to reload Captcha">
					    	<span class="help-inline"></span>
					    </div>
				    <?php endif; ?>
					<textarea name="comment" id="commentContent" wrap="hard"<?php echo ($maxlength)?' maxlength="'.$maxlength.'"':''; ?> placeholder="Leave a message..."></textarea>
					<div class="pull-left">
						<input type="submit" class="btn btn-success btn-small add" name="post" value="Add comment">
						<button type="button" class="btn btn-small cancel">Cancel</button>
						<span class="ajax-loader hidden"></span>
					</div>
					<?php if ($maxlength) : ?>
						<div class="remaining pull-right"><span><?php echo $maxlength; ?></span> remaining</div>
					<?php endif; ?>
					<div class="alert response hidden"></div>
				</div>
			<?php  else : ?>
				<?php
			    if ( $Comments->config('comments_captcha_logged') ) :
			    	$Comments->captcha();
			    ?>
				    <div class="input">
				      	<input type="text" name="captcha" id="commentCaptcha" placeholder="Captcha">
				    	<img src="includes/captcha.php" class="captcha-image" title="Click to reload Captcha">
				    	<span class="help-inline"></span>
				    </div>
			    <?php endif; ?>
			   	<a href="#" class="avatar pull-left" target="_blank">
					<img src="<?php echo $Comments->user_avatar(); ?>" height="50" width="50">
				</a>
				<div class="block">
					<textarea name="comment" id="commentContent" wrap="hard"<?php echo ($maxlength)?' maxlength="'.$maxlength.'"':''; ?> placeholder="Leave a message..."></textarea>
					<div class="pull-left">
						<input type="submit" class="btn btn-success btn-small add" name="post" value="Add comment">
						<button type="button" class="btn btn-small cancel">Cancel</button>
						<span class="ajax-loader hidden"></span>
					</div>
					<?php if ($maxlength) { ?>
						<div class="remaining pull-right"><span><?php echo $maxlength; ?></span> remaining</div>
					<?php } ?>
					<div class="alert response hidden"></div>
				</div>
			<?php endif; ?>
				<br clear="all">
				<input type="hidden" id="commentReply" name="reply" value="0">
			</form>
			<div class="placeholder">Leave a message...</div>
		</div>
	<?php else : ?>
		<p class="alert">You must be logged to leave a comment.</p>
	<?php endif; ?>
	<span class="ajax-loader loading-comments hidden" title="Loading comments..."></span>
	<!-- Comments -->
	<div id="comments" class="hidden"></div>
</div>