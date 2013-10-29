function publishing(link, action) {
	jQuery('#post_list').yiiGridView('update', {
		type: 'POST',
		url: jQuery(link).attr('href'),
		data: { 'Post[published]': action == 'publish' ? 1 : 0 },
		success: function(data) {
			jQuery('#post_list').yiiGridView('update');
		}
	});
	jQuery(link).attr('href', '#');

	return false;
}
