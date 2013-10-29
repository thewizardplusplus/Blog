function publishing(link, action) {
	jQuery('#point_list').yiiGridView('update', {
		type: 'POST',
		url: '?r=point/update&id=' + id,
		data: { 'Point[published]': jQuery(link).attr('href').slice(1) },
		success: function(data) {
			jQuery('#point_list').yiiGridView('update');
		}
	});
	jQuery(link).attr('href', '#');
}
