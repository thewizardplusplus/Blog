$(document).ready(
	function() {
		var UPDATE_DELAY = 300;

		var update_timeout = null;
		var search_input = $('.search-input');
		search_input.keyup(
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

		$('.clear-search-input-button').click(
			function() {
				if (search_input.val().length) {
					search_input.val('');
					search_input.keyup();
				}

				search_input.focus();

				return false;
			}
		);
	}
);
