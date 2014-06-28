var PostList = {};

$(document).ready(
	function() {
		var UpdatePostList = function() {
			$.fn.yiiListView.update(
				'post-list',
				{
					url:
						location.pathname
							+ location.search
							+ location.hash
				}
			);
		};
		var RequestToPostList = function(url, data) {
			$.fn.yiiListView.update(
				'post-list',
				{
					type: 'POST',
					url: url,
					data: data,
					success: function() {
						UpdatePostList();
						UpdateTagCloud();
					}
				}
			);
		};

		PostList = {
			initialize: function() {
				$('.publishing-post-button').click(
					function() {
						var publishing_url = $(this).attr('href');
						var published = $(this).data('published');

						RequestToPostList(
							publishing_url,
							{ 'Post[published]': published ? 1 : 0 }
						);

						return false;
					}
				);
				$('.delete-post-button').click(
					function() {
						var deleting_url = $(this).attr('href');
						var post_title = $(this).data('post-title');

						var result = confirm(
							'Ты точно хочешь удалить пост «' + post_title + '»?'
						);
						if (result) {
							RequestToPostList(deleting_url, {});
						}

						return false;
					}
				);
			}
		};

		PostList.initialize();
	}
);
