function publishing(url, publish) {
	jQuery('#post_list').yiiGridView('update', {
		type: 'POST',
		url: url,
		data: { 'Post[published]': publish ? 1 : 0 },
		success: function(data) {
			jQuery('#post_list').yiiGridView('update');
		}
	});

	return false;
}
