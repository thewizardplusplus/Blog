$(document).ready(
	function() {
		$('.create-backup-button').click(
			function() {
				var create_backup_button = $(this);
				var processing_animation_image = $('img', create_backup_button);
				var backup_icon = $('span', create_backup_button);

				create_backup_button.prop('disabled', true);
				processing_animation_image.show();
				backup_icon.hide();

				var backup_list = $('#backup-list');
				var create_backup_url = create_backup_button.data(
					'create-backup-url'
				);
				var get_log_url = create_backup_button.data('get-log-url');
				var FinishAnimation = function() {
					create_backup_button.prop('disabled', false);
					processing_animation_image.hide();
					backup_icon.show();
				};
				if (backup_list.length) {
					backup_list.yiiGridView(
						'update',
						{
							type: 'POST',
							url: create_backup_url,
							success: function() {
								FinishAnimation();

								backup_list.yiiGridView(
									'update',
									{
										url:
											location.pathname
												+ location.search
												+ location.hash
									}
								);

								$.get(
									get_log_url,
									function(data) {
										$('.log').text(data);
									}
								);
							}
						}
					);
				} else {
					$.post(create_backup_url, FinishAnimation).fail(
						function() {
							alert('Не удалось создать бекап.');
						}
					);
				}
			}
		);
	}
);
