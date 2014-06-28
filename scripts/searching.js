$(document).ready(
	function() {
		var UPDATE_DELAY = 300;

		var update_timeout = null;
		$('.search-input').keyup(
			function() {
				var text = encodeURIComponent($(this).val());

				clearTimeout(update_timeout);
				update_timeout = setTimeout(
					function() {
						$.fn.yiiListView.update(
							'post-list',
							{ data: { search: text } }
						)
					},
					UPDATE_DELAY
				);
			}
		);
	}
);
