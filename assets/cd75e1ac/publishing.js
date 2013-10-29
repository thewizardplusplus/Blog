function publishing(link, action) {
	var url = jQuery(link).attr('href').slice(1);
	alert(url);
	jQuery(link).attr('href', '#');
}
