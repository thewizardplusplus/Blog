jQuery(document).ready(function() {
	var editor = ace.edit('editor');
	editor.setTheme('ace/theme/twilight');
	editor.getSession().setMode('ace/mode/markdown');

	jQuery('#post_form').submit(function() {
		jQuery('#Post_text').val(editor.getValue());
	});
});
