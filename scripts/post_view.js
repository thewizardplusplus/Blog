$(document).ready(
	function() {
		var publishing_button = $('.publishing-post-button');
		var publishing_url = publishing_button.attr('href');
		var published = !publishing_button.data('published');
		var publishing_button_icon = $('span', publishing_button);
		var publishing_button_processing_icon = $(
			'<img src = "'
				+ publishing_button.data('processing-icon')
				+ '" alt = "..."/>'
		);
		publishing_button_processing_icon.hide();
		publishing_button.append(publishing_button_processing_icon);

		publishing_button.click(
			function() {
				publishing_button_icon.hide();
				publishing_button_processing_icon.show();

				$.post(
					publishing_url,
					{ 'Post[published]': published ? 0 : 1 },
					function() {
						publishing_button_icon.show();
						publishing_button_processing_icon.hide();

						if (published) {
							publishing_button_icon
								.removeClass('glyphicon-eye-open')
								.addClass('glyphicon-eye-close');
							publishing_button.attr('title', 'Опубликовать');
							publishing_button.data('published', 'true');

							published = false;
						} else {
							publishing_button_icon
								.removeClass('glyphicon-eye-close')
								.addClass('glyphicon-eye-open');
							publishing_button.attr('title', 'Скрыть');
							publishing_button.data('published', 'false');

							published = true;
						}
					}
				);

				return false;
			}
		);

		var deleting_button = $('.delete-post-button');
		var deleting_url = deleting_button.attr('href');
		var redirect_url = deleting_button.data('redirect-url');
		var deleting_button_icon = $('span', deleting_button);
		var deleting_button_processing_icon = $(
			'<img src = "'
				+ deleting_button.data('processing-icon')
				+ '" alt = "..."/>'
		);
		deleting_button_processing_icon.hide();
		deleting_button.append(deleting_button_processing_icon);

		deleting_button.click(
			function() {
				var result = confirm('Ты точно хочешь удалить этот пост?');
				if (result) {
					deleting_button_icon.hide();
					deleting_button_processing_icon.show();

					$.post(
						deleting_url,
						function() {
							window.location.href = redirect_url;
						}
					);
				}

				return false;
			}
		);
	}
);
