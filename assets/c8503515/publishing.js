function publishing(link, action) {
	jQuery('#point_list').yiiGridView('update', {
		type: 'POST',
		url: '?r=point/update&id=' + jQuery(link).attr('href').slice(1),
		data: { 'Point[published]': action == 'publish' ? 1 : 0 },
		success: function(data) {
			jQuery('#point_list').yiiGridView('update');
		}
	});
	jQuery(link).attr('href', '#');
}
