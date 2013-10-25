jQuery(document).ready(function() {
	var result = $('#tag-cloud-canvas').tagcanvas({
		textColour : null
	}, 'tag-cloud-list');
	alert(result);
	if (!result) {
		jQuery('#tag-cloud-canvas-container').hide();
	}
});
