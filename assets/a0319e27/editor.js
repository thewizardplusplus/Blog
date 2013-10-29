jQuery(document).ready(function() {
	var editor = ace.edit('editor');
	editor.setTheme('ace/theme/twilight');
	editor.getSession().setMode('ace/mode/markdown');

	var text_field = jQuery('#Post_text');
	editor.getSession().on('change', function(event) {
		text_field.text(editor.getValue());
	});
});
