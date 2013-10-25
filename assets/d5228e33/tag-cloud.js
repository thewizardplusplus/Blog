jQuery(document).ready(function() {
	var result = $('#tag-cloud-canvas').tagcanvas({
		textColour : '#0000ff',
		weight: true
	}, 'tag-cloud-list');
	if (!result) {
		jQuery('#tag-cloud-canvas-container').hide();
	}
});
