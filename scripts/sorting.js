$(document).ready(
	function() {
		var create_sort_button = $('.create-sort-button');
		var modify_sort_button = $('.modify-sort-button');
		var SortPostList = function(order) {
			$.fn.yiiListView.update(
				'post-list',
				{ data: { sort: order } }
			)
		};

		create_sort_button.click(
			function() {
				var button = $(this);
				if (!button.hasClass('active')) {
					button.addClass('active');
					modify_sort_button.removeClass('active');

					SortPostList('create');
				}
			}
		);
		modify_sort_button.click(
			function() {
				var button = $(this);
				if (!button.hasClass('active')) {
					create_sort_button.removeClass('active');
					button.addClass('active');

					SortPostList('modify');
				}
			}
		);
	}
);
