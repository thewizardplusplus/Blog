function publishing(url, publish) {
	$('#post_list').yiiGridView('update', {
		type: 'POST',
		url: url,
		data: { 'Post[published]': publish ? 1 : 0 },
		success: function(data) {
			$('#post_list').yiiGridView('update');
		}
	});

	return false;
}
