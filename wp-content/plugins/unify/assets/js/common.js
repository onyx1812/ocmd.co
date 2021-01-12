jQuery(document).ready(function ($) {	
	
	// *********** Hide connection message
	$('.cross-position').click(function () {
		$(this).parents('div .container-fluid').fadeOut();
	});

});