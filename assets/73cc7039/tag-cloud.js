jQuery(document).ready(function() {
	var result = $('#tag-cloud-canvas').tagcanvas({
		textColour : '#ffffff',
		outlineThickness : 1,
		maxSpeed : 0.03,
		depth : 0.75
	}, 'tag-cloud-list');
	alert(result);
	if (!result) {
		jQuery('#tag-cloud-canvas-container').hide();
	}
});
