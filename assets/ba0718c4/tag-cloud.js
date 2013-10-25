jQuery(document).ready(function() {
	alert('OK');
	var result = $('#tag-cloud-canvas').tagcanvas({
		textColour : '#ffffff',
		outlineThickness : 1,
		maxSpeed : 0.03,
		depth : 0.75
	});
	if (!result) {
		jQuery('#tag-cloud-canvas-container').hide();
	}
});
