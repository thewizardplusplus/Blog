var FileList = {};

$(document).ready(
	function() {
		var file_list = $('#file-list');
		var UpdateFileList = function() {
			file_list.yiiGridView(
				'update',
				{
					url:
						location.pathname
							+ location.search
							+ location.hash
				}
			);
		};
		var RequestToFileList = function(url, data) {
			file_list.yiiGridView(
				'update',
				{
					url: url,
					data: data,
					success: UpdateFileList
				}
			);
		};

		$.editable.addInputType(
			'bootstrapped-line-edit',
			{
				element : function() {
					var block = $('<div class = "input-group"></div>');
					$(this).append(block);

					var input = $('<input class = "form-control" />');
					block.append(input);

					return input;
				},
				buttons: function(settings, original) {
					var form = this;
					var block = $(form).find('.input-group');
					var submit_button = $(
						'<a class = "input-group-addon" href = "#">'
							+ '<span class = "glyphicon glyphicon-floppy-disk">'
							+ '</span>'
						+ '</a>'
					);
					var cancel_button = $(
						'<a class = "input-group-addon" href = "#">'
							+ '<span class = "glyphicon glyphicon-remove">'
							+ '</span>'
						+ '</a>'
					);

					block.append(submit_button).append(cancel_button);
					submit_button.click(
						function() {
							if (submit_button.attr('type') != 'submit') {
								form.submit();
							}

							return false;
						}
					);
					cancel_button.click(
						function() {
							var reset = $.editable.types[settings.type].reset;
							if (!$.isFunction(reset)) {
								reset = $.editable.types['defaults'].reset;
							}

							reset.apply(form, [settings, original]);

							return false;
						}
					);
				}
			}
		);

		FileList = {
			rename: function(link) {
				var url = $(link).attr('href');
				var element_id = 'file-item' + $.url(url).param('file_id');
				$('#' + element_id).trigger(element_id + '-edit');

				return false;
			},
			removing: function(link) {
				var url = $(link).attr('href');
				var filename = $.url(url).param('filename');

				var answer = confirm(
					'Ты точно хочешь удалить файл «' + filename + '»?'
				);
				if (answer) {
					RequestToFileList(url, {});
				}

				return false;
			},
			initialize: function() {
				$('.file-item').each(
					function(id, item) {
						item = $(item);
						item.editable(
							item.data('update-url'),
							{
								type: 'bootstrapped-line-edit',
								event: item.attr('id') + '-edit',
								name: 'new_filename',
								onblur: 'ignore',
								indicator:
									'<img src = "'
										+ item.data('saving-icon-url')
										+ '" alt = "Сохранение..." />',
								placeholder: '',
								callback: UpdateFileList,
								onerror: function(settings, original) {
									alert('Не удалось переименовать файл.');
									original.reset();
								}
							}
						);
					}
				);
			}
		};

		FileList.initialize();
	}
);
