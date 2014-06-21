$(document).ready(function() {
	var markdown_editor = ace.edit('editor');
	markdown_editor.setTheme('ace/theme/twilight');
	markdown_editor.getSession().setMode('ace/mode/markdown');
	markdown_editor.setShowInvisibles(true);

	$('#post-form').submit(function() {
		$('#Post_text').val(markdown_editor.getValue());
	});

	function ConvertMarkdown() {
		var text = marked(markdown_editor.getValue());
		text = text.replace(/<img\s/g, '<img class = "img-responsive" ');
		text = text.replace(
			'<table>',
			'<div class = "table-responsive">'
				+ '<table class = "table table-bordered table-striped">'
		);
		text = text.replace('</table>', '</table></div>');

		return text;
	}

	var preview_mode = false;
	var editor_element = $('#editor');
	var preview_element = $('#preview');
	var editor_switch_button = $('.editor-switch-button');
	var editor_switch_button_icon = $('span:first-child', editor_switch_button);
	var editor_switch_button_text = $('span:last-child', editor_switch_button);
	editor_switch_button.click(
		function() {
			if (!preview_mode) {
				preview_element.html(ConvertMarkdown());
				editor_element.hide();
				preview_element.show();

				editor_switch_button_icon
					.removeClass('glyphicon-eye-open')
					.addClass('glyphicon-eye-close');
				editor_switch_button_text.text('Редактор');

				preview_mode = true;
			} else {
				editor_element.show();
				preview_element.hide();

				editor_switch_button_icon
					.removeClass('glyphicon-eye-close')
					.addClass('glyphicon-eye-open');
				editor_switch_button_text.text('Предпросмотр');

				preview_mode = false;
			}

			return false;
		}
	);
});
