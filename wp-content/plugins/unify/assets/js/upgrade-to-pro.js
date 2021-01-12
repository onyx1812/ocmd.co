jQuery(document).ready(function ($) {	
	
	// ######### ON SUBMIT OF FORM JS VALIDATION STARTS #########//
	
	$('#submit_unify_pro').click(function(){
		
		$("#request_unify_pro_form").validate({ // initialize the plugin
			rules: {
				full_name: {
					required: true
				},
				company_name: {
					required: true
				},
				email_address: {
					required: true,
					email: true,
				},
				phone_number: {
					required : true,
					number: true,
					minlength: 10,
					maxlength: 15,
				},
				comment: {
					required: true
				},
			},
			messages :{
				full_name: {
					required: 'Full Name is a required field.'
				},
				company_name: {
					required: 'Company Name is a required field.'
				},
				email_address: {
					required: 'Email Address is a required field.',
					email: 'Please provide a valid email address.',
				},
				phone_number: {
					required : 'Phone Number is a required field.',
					digits: 'Please provide a valid phone number.'
				},
				comment: {
					required: 'Comment is a required field.'
				},
			},
			errorClass:'error',
			errorPlacement: function (error, element) {
				$(this).addClass('error');
			}
		});
		
		if($("#request_unify_pro_form").valid()){
			$('#request_unify_pro_form').submit();
			return true;
		}
		return false;
	});

	// ######### ON SUBMIT OF FORM JS VALIDATION ENDS #########//

});