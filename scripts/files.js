var FileList = (function() {
	var exists_files = [];

	return {
		hasExistsFile: function(exists_file) {
			return $.inArray(exists_file, exists_files) > -1;
		},
		setExistsFiles: function(new_exists_files) {
			exists_files = new_exists_files;
		},
		addExistsFile: function(exists_file) {
			exists_files.push(exists_file);
		},
		removeExistsFile: function(exists_file) {
			var index = $.inArray(exists_file, exists_files);
			if (index > -1) {
				exists_files.splice(index, 1);
			}
		}
	};
})();

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
		var RequestToFileList = function(url, data, callback) {
			file_list.yiiGridView(
				'update',
				{
					url: url,
					data: data,
					success: function() {
						callback();
						UpdateFileList();
					}
				}
			);
		};

		var file_upload_form = $('#file-upload');
		var file_select_input = $('#files', file_upload_form);
		var file_upload_error_block = $(
			'.file-upload-error-view',
			file_upload_form
		);
		var file_upload_form_submit_button = $(
			'input[type=submit]',
			file_upload_form
		);
		var collisions = [];
		var TestCollisions = function() {
			var original_input = file_select_input.get(0);

			collisions = [];
			for (var i = 0; i < original_input.files.length; ++i) {
				var filename = original_input.files[i].name;
				if (FileList.hasExistsFile(filename)) {
					collisions.push(filename);
				}
			}

			if (!collisions.length) {
				file_upload_error_block.hide();
				file_upload_form_submit_button.prop('disabled', false);
			} else {
				file_upload_error_block.text(
					(collisions.length > 1
						? 'Эти файлы'
						: 'Этот файл')
						+ ' уже есть в текущей директории: «'
						+ collisions.join('», «')
						+ '».'
				);
				file_upload_error_block.show();
				file_upload_form_submit_button.prop('disabled', true);
			}
		};
		file_select_input.change(TestCollisions);

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

		$.extend(
			FileList,
			{
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
						RequestToFileList(
							url,
							{},
							function() {
								FileList.removeExistsFile(filename);
								TestCollisions();
							}
						);
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
									callback: function(new_filename) {
										var update_url =
											$(this).data('update-url');
										var old_filename =
											$
											.url(update_url)
											.param('old_filename');
										FileList.removeExistsFile(old_filename);
										FileList.addExistsFile(new_filename);
										TestCollisions();
										UpdateFileList();
									},
									onerror: function(settings, original) {
										alert('Не удалось переименовать файл.');
										original.reset();
									}
								}
							);
						}
					);
				}
			}
		);

		FileList.initialize();
	}
);
