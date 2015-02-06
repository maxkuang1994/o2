$(function() {
	var ajaxurl = 'ajax.php',
		hash = window.location.hash;

	//Bootstrap Tooltip
	$('[data-toggle="tooltip"]').tooltip();

	//Rows actions
	$('.row-actions').on('click', 'span', function() {
		var action = $(this).attr('class'),
				id = $(this).closest('tr').attr('id'),
				el = $('#'+id),
				status = commentStatus;
		switch(action) {
			case 'approve':
				if (status=='unapproved')
					el.find('td').effect("highlight", {color:'#dff0d8'}, 400, function(){
						$(this).fadeOut();
					});
				else {
					el.removeClass('warning')
					el.find('td').effect("highlight", {color:'#dff0d8'}, 400);
					el.find('.approve').hide();
					el.find('.unapprove').show();
				}
				el.find('.status').val(1);
			break;
			case 'not-spam':
				el.find('td').effect("highlight", {color:'#dff0d8'}, 400, function(){
					$(this).fadeOut();
				});
				action = 'approve';
			break;
			case 'unapprove':
				if (status=='approved')
					el.find('td').effect("highlight", {color:'#dff0d8'}, 400, function(){
						$(this).fadeOut();
					});
				else {	
					el.find('td').effect("highlight", {color:'#fcf8e3'}, 400, function() {
						el.addClass('warning');
					});
					el.find('.unapprove').hide();
					el.find('.approve').show();
					el.find('.status').val(0);
				}
			break;
			case 'spam':
				el.find('td').effect("highlight", {color:'#f2dede'}, 400, function() {
					$(this).fadeOut();
					el.find('.status').val(2);
				});
			break;
			case 'delete':
				el.find('td').effect("highlight", {color:'#f2dede'}, 400, function() {
					$(this).fadeOut();
				});
			break;
			case 'edit':
				showEditModal({
					id: id,
					url:  el.find('.column-author .url').text(),
					name: el.find('.column-author .name').text(),
					email: el.find('.column-author .email').text(),
					status: el.find('.status').val(),
					comment: el.find('.column-comment .comment').html(),
					user_id: el.find('.user_id').length ? 1 : 0
				});
			break;
		}
		if (action!='edit')
			$.post(ajaxurl, {action : action, id : id});
	});
	
	//Edit comment
	$('#editComment').on('click', '.save', function(){
		var 
			name = $.trim($('#author-name').val()),
			email = $('#author-email').val(),
			url = $('#author-url').val(),
			status = $('#comment-status').val(),
			comment = $('#comment').val(),
			id = $('#commentID').val(),
			el = $('#'+id),
			data = {status: status},
			email_pattern =/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/,
			url_pattern = new RegExp("^((http[s]?:\\/\\/(www\\.)?|ftp:\\/\\/(www\\.)?|www\\.){1})?([0-9A-Za-z-\\.@:%_\+~#=]+)+((\\.[a-zA-Z]{2,3})+)(/(.)*)?(\\?(.)*)?");

		if (name.length > 1) {
			el.find('.column-author .name').text(name);
			data.author = name;
		}
		if (email_pattern.test(email)) {
			el.find('.column-author .email').text(email).attr('href', 'mailto:'+email);
			data.author_email = email;
		}
		if (url_pattern.test(url)) {
			el.find('.column-author .url').parent().show();
			el.find('.column-author .url').text(url).attr('href', url);
			data.author_url = url;
		} else if ($.trim(url)=='') {
			el.find('.column-author .url').parent().hide();
			data.author_url = '';
		}
		if ($.trim(comment)!='') {
			el.find('.column-comment .comment').html(comment);
			data.comment = comment;
		}	
		if (Object.keys(data).length) {
			data.action = 'update';
			data.id = id;
			$.post(ajaxurl, data);
		}
		if (el.length && el.find('.status').val()!=status) 
			el.find('.row-actions .'+(status==1?'approve':status==2?'spam':'unapprove')).trigger('click');
	});

	//Delete comment from edit comment modal
	$('#editComment').on('click', '.delete', function(){
		var id = $('#commentID').val();
		$('#editComment').modal('hide');
		if ($('#'+id).length)
			$('#'+id+' .row-actions .delete').trigger('click');
		else $.post(ajaxurl, {action:'delete', id:id});
	});
	
	//Delete hash when modal is hidden
	$('#editComment').on('hidden', function () {
		window.location.hash = '';
	});

	//If #edit-{id} exists show edit modal
	if (hash) {
		var id = hash.substr(6, hash.length);
		if ( $('#'+id).length )
			$('#'+id+' .row-actions .edit').trigger('click');
		else {
			$.post(ajaxurl, {action: 'get-comment', id: id}, function(data) {
				if (data.success) {
					data = data.data;
					showEditModal({
						id: data.id,
						url:  data.author_url,
						name: data.author,
						email: data.author_email,
						status: data.status,
						comment: data.comment,
						user_id: data.user_id
					});
				}
				else window.location.hash='';
			},'json');
		}
	}

	//Checkbox select
	$('.select-all').click(function () {
          $('.ckbox').attr('checked', this.checked);
    });
    $('.ckbox').click(function(){
        if ($('.ckbox').length == $('.ckbox:checked').length)
			$('.select-all').attr('checked', 'checked');
        else $('.select-all').removeAttr('checked');
    });
});

//Edit modal
function showEditModal(data) {

	var name = $('#author-name'),
		email = $('#author-email'),
		url = $('#author-url');

	name.parent().show();
	email.parent().show();
	url.parent().show();

	if (parseInt(data.user_id)) {
		name.parent().hide();
		email.parent().hide();
		url.parent().hide();
	}

	name.val(data.name);
	email.val( data.email);
	url.val(data.url);
	$('#comment-status').val(data.status);
	$('#comment').val(data.comment);
	$('#commentID').val(data.id);
	$('#editComment').modal('show');
	window.location.hash = '#edit-'+data.id;
}