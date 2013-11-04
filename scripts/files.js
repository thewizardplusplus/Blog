function fileView(link) {
	$('#file-path').val('/files/' + $(link).attr('href'));
	$('#file-path-dialog').modal();

	return false;
}

function fileRename(link) {
	var url = $(link).attr('href');
	var old_filename = '';
	var index = url.lastIndexOf('=');
	if (index != -1) {
		old_filename = url.substring(index + 1);
	}

	var new_filename = prompt('Новое имя:', old_filename);
	if (new_filename) {
		$(link).attr('href', url + '&new_filename=' + new_filename);
		return true;
	}

	return false;
}

function fileRemove(link) {
	return confirm('Удалить файл?');
}
