<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
if ( !isset($_GET['status']) or !in_array($_GET['status'], array('all', 'approved', 'unapproved', 'spam')) )
	$_GET['status'] = 'all';

if ( empty($_GET['paged']) or !is_numeric($_GET['paged']))
	$_GET['paged'] = 1;

if (isset($_GET['action'])) {
	if (is_array($_GET['comments'])) {
		foreach ($_GET['comments'] as $id)
			if (is_numeric($id) and $id>0) {
				switch ($_GET['action']) {
					case 'approve': case 'not-spam':
						$Comments->update_comment( array('status'=>1, 'id'=>$id) );
					break;
					case 'unapprove':
						$Comments->update_comment( array('status'=>0, 'id'=>$id) );
					break;
					case 'spam':
						$Comments->update_comment( array('status'=>2, 'id'=>$id) );
					break;
					case 'delete':
						$Comments->delete_comment($id);
					break;
				}
			}
		
	}
	header('Location: index.php?page=comments&status='.$_GET['status']);
}
$per_page = 10;
$Comments->set_config( array('full_date_format' => 'Y/m/d \a\t  h:i a', 'comment_reply' => false) );
$data = array('rows'=>'*', 'order'=>'DESC', 'per_page'=>$per_page, 'paged'=>$_GET['paged'], 'order'=>'DESC');

if ($_GET['status']=='all')
	$data['_where'] = '(status=0 OR status=1)';
else $data['status'] = $_GET['status']=='unapproved' ? 0 : ($_GET['status']=='approved' ? 1 : 2);

$_comments = $Comments->get_comments( $data );

?>
<h2>Comments</h2>		

<div class="status">
	<a href="?page=comments&status=all"<?php echo $_GET['status']=='all'?' class="active"':''; ?>>All</a> |
	<a href="?page=comments&status=approved"<?php echo $_GET['status']=='approved'?' class="active"':''; ?>>Approved</a> |
	<a href="?page=comments&status=unapproved" class="unapproved<?php echo $_GET['status']=='unapproved'?' active':''; ?>">Pending</a> |
	<a href="?page=comments&status=spam" class="spam<?php echo $_GET['status']=='spam'?' active':''; ?>">Spam</a>
</div>

<form action="" method="GET">
	<input type="hidden" name="page" value="comments">
	<input type="hidden" name="status" value="<?php echo $_GET['status']; ?>">
	<div class="actions">
		<select name="action" class="form-control bulk-actions" >
			<option value="0">Bulk Actions</option>
			<?php 
			switch ($_GET['status']) {
				case 'all':
					echo'<option value="approve">Approve</option>
					<option value="unapprove">Unapprove</option>
					<option value="spam">Mark as Spam</option>';
				break;
				case'approved':
					echo'<option value="unapprove">Unapprove</option>
					<option value="spam">Mark as Spam</option>';
				break;
				case'unapproved':
					echo'<option value="approve">Approve</option>
					<option value="spam">Mark as Spam</option>';
				break;
				case 'spam':
					echo'<option value="not-spam">Not Spam</option>';
				break;
			}
			?>
			<option value="delete">Delete</option>
		</select>
		<button type="submit" class="btn btn-mini btn-primary">Apply</button>
	</div>

	<table class="table table-striped" id="comments">
		<thead>
			<tr>
				<th class="column-check"><input type="checkbox" name="select_all" value="1" class="select-all"> </th>
				<th class="column-author">Author</th>
				<th class="column-comment">Comment</th>
				<th class="column-page">Page</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			if (!empty($_comments['comments']))
				foreach($_comments['comments'] as $v) : ?>
					<tr class="<?php echo $v['status']==0&&$_GET['status']!='unapproved'?'warning':''; ?>" id="<?php echo $v['id']; ?>">
						<td><input type="checkbox" name="comments[]" value="<?php echo $v['id']; ?>" class="ckbox"></td>
						<td class="column-author">
							<strong>
								<img src="<?php echo $v['avatar']; ?>" width="32" width="32">
								<span class="name"><?php echo $v['author']; ?></span>
								<?php if (!empty($v['user_id'])) echo '<span class="user_id"></span>'; ?>
							</strong><br>
							<a href="mailto:<?php echo $v['author_email']; ?>" class="email"><?php echo $v['author_email']; ?></a><br>
							<span style="<?php echo empty($v['author_url']) ? 'display:none;' : ''; ?>">
								<a href="<?php echo $v['author_url'];?>" target="_blank" class="url"><?php echo $v['author_url'];?></a><br>
							</span>
							<span class="btn-link" data-toggle="tooltip" data-original-title="<?php echo $v['agent'] ?>"><?php echo $v['author_ip'] ?></span>
						</td>
						<td class="column-comment">
							<div class="submitted-on">
								Submitted on <a href="<?php echo com_get_comment_page($v['page']).'#page/'.$Comments->get_paged($v['page'], $v['id']).'/comment/'.$v['id']; ?>"><?php echo $v['date']; ?></a>
							</div>
							<p class="comment"><?php echo $v['comment'] ?></p>
							<div class="row-actions">
								<?php if ($_GET['status']!='spam') { ?>
									<span class="approve"<?php echo $v['status']!=1?'':' style="display:none;"'; ?>><a class="btn-link" title="Approve this comment">Approve</a></span>
									<span class="unapprove" <?php echo $v['status']!=0?'':' style="display:none;"'; ?>><a class="btn-link" title="Unapprove this comment">Unapprove</a></span>
									<span class="edit"> | <a class="btn-link" title="Edit comment">Edit</a></span>
									<span class="spam"> | <a class="btn-link" title="Mark this comment as spam">Spam</a></span>
								<?php } else { ?>
									<span class="not-spam"><a class="btn-link">Not Spam</a></span>
									<span class="edit"> | <a class="btn-link" title="Edit comment">Edit</a></span>
								<?php } ?>
								<span class="delete"> | <a class="btn-link" title="Delete this comment as spam">Delete</a></span>
								<input type="hidden" class="status" value="<?php echo $v['status']; ?>">
							</div>
						</td>
						<td> <a href="<?php echo com_get_comment_page($v['page']); ?>">View</a> </td>
					</tr>
				<?php endforeach; ?>
		</tbody>
	</table>

</form>

<div class="pull-left"><i><span class="total-comments"><?php echo isset($_comments['total']) ? $_comments['total'] : 0; ?></span> comments</i> </div>

<!-- pagination -->
<?php 
$count = isset($_comments['count']) ? $_comments['count'] : 0;
if ($count > $per_page ) {
	$pages = ceil($count/$per_page);
	echo '<ul class="pull-right pagination">';
		if ($_GET['paged'] == 1)
			echo'<li class="disabled"><span>&laquo;</span></li>';
		else echo'<li><a href="?page=comments&status='.$_GET['status'].'&paged='.($_GET['paged']-1).'" title="Previous">&laquo;</a></li>';
		for($i=1; $i<=$pages; $i++)
			echo'<li'.($_GET['paged']==$i?' class="active"':'').'><a href="?page=comments&status='.$_GET['status'].'&paged='.$i.'">'.$i.'</a></li>';
		if ($_GET['paged'] == $pages)
			echo'<li class="disabled"><span>&raquo;</span></li>';
		else echo'<li><a href="?page=comments&status='.$_GET['status'].'&paged='.($_GET['paged']+1).'" title="Next">&raquo;</a></li>';
	echo'</ul>';
}
?>
<br clear="all">

<!-- edit comment modal -->
<div class="modal fade" id="editComment">
	<div class="modal-dialog">
	  	<div class="modal-content">
	    	<div class="modal-header">
	      		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	     		<h4 class="modal-title">Edit Comment</h4>
	    	</div>
	    	<div class="modal-body">
	      		<form class="edit-comment">
	      			<div class="form-group">
				    	<label for="author-name">Name</label>
				      	<input type="text" class="form-control" id="author-name">
				    </div>
				    <div class="form-group">
				    	<label for="author-email">Email</label>
				      	<input type="text" class="form-control" id="author-email">
				    </div>
				    <div class="form-group">
				    	<label for="author-url">URL</label>
				      	<input type="text" class="form-control" id="author-url">
				    </div>
				    <div class="form-group">
				    	<label for="comment-status">Status</label>
				      	<select id="comment-status" class="form-control">
				      		<option value="1">Approved</option>
				      		<option value="0">Unapproved</option>
				      		<option value="2">Spam</option>
				      	</select>
				    </div>
				    <div class="form-group">
				    	<label for="comment">Comment</label>
				      	<textarea id="comment" class="form-control" rows="5"></textarea>
				    </div>
				    <button type="button" class="btn btn-danger delete btn-small">Delete Comment</button>
				    <input type="hidden" id="commentID" value="0">
	      		</form>
	    	</div>
	    	<div class="modal-footer">
       			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        		<button type="button" class="btn btn-primary save" data-dismiss="modal">Save changes</button>
      		</div>
		</div>
	</div>
</div>

<script>
	var commentStatus = '<?php echo $_GET['status']; ?>';
</script>