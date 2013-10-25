jQuery(document).ready(function() {
	var result = $('#tag-cloud-canvas').tagcanvas({
		textColour : null,
		depth: 0.75
	}, 'tag-cloud-list');
	if (!result) {
		jQuery('#tag-cloud-canvas-container').hide();
	}
});
