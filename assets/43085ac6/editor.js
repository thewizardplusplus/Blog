jQuery(document).ready(function() {
	var editor = ace.edit('editor');
	editor.setTheme('ace/theme/chaos');
	editor.getSession().setMode('ace/mode/markdown');
});
