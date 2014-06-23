function UpdateCommentsCounters() {
	var ids = [];
	$('a[data-disqus-identifier]').each(
		function() {
			var identifier = $(this).data('disqus-identifier');
			ids.push('ident:' + identifier);
		}
	);

	$.get(
		'http://disqus.com/api/3.0/threads/set.json',
		{ api_key: disqus_api_key, forum : disqus_shortname, thread : ids },
		function(data) {
			$(data.response).each(
				function() {
					var identifier = this.identifiers[0];
					var counter = this.posts;
					var text = counter != 0 ? counter.toString() : 'нет';
					$('a[data-disqus-identifier=' + identifier + ']').text(
						'Комментариев: ' + text
					);
				}
			);
		},
		'json'
	);
}

UpdateCommentsCounters();
