$(document).ready(
	function() {
		var PASSWORD_CLEANING_DELAY_IN_S = 500;

		setTimeout(
			function() {
				$('#ParametersForm_password').val('');
				$('#ParametersForm_password_copy').val('');
			},
			PASSWORD_CLEANING_DELAY_IN_S
		);

		$("#ParametersForm_posts_on_page").spinner();
		$("#ParametersForm_maximal_width_of_images").spinner();
	}
);
