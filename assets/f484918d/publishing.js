function publishing(link, action) {
	jQuery('#post_list').yiiGridView('update', {
		type: 'POST',
		//url: '?r=post/update&id=' + jQuery(link).attr('href').slice(1),
		url: 'post/' + jQuery(link).attr('href').slice(1) + '/update',
		data: { 'Post[published]': action == 'publish' ? 1 : 0 },
		success: function(data) {
			jQuery('#post_list').yiiGridView('update');
		}
	});
	jQuery(link).attr('href', '#');

	return false;
}
