var $ = jQuery;

jQuery(document).ready(function ($) {
	//// ############## CUSTOM DROPDOWN UI JS STARTS ################### //
	$(".custom-select").each(function () {
		var classes = $(this).attr("class"),
			id = $(this).attr("id"),
			name = $(this).attr("name");
		var template = '<div class="' + classes + '">';
		template += '<span class="custom-select-trigger">' + $(this).attr("placeholder") + '</span>';
		template += '<div class="custom-options">';
		$(this).find("option").each(function () {
			template += '<span class="custom-option ' + $(this).attr("class") + '" data-value="' + $(this).attr("value") + '">' + $(this).html() + '</span>';
		});
		template += '</div></div>';

		$(this).wrap('<div class="custom-select-wrapper"></div>');
		$(this).hide();
		$(this).after(template);
	});
	
	$(".custom-option:first-of-type").hover(function () {
		$(this).parents(".custom-options").addClass("option-hover");
	}, function () {
		$(this).parents(".custom-options").removeClass("option-hover");
	});
	
	$(".custom-select-trigger").on("click", function () {
//		$('html').one('click', function () {
//			$(".custom-select").removeClass("opened");
//		});
		$(this).parents(".custom-select").toggleClass("opened");
		event.stopPropagation();
	});
	
	$(".custom-option").on("click", function () {
		var id = $(this).parents(".custom-select-wrapper").find("select").attr("id");
		$(this).parents(".custom-select-wrapper").find("select").val($(this).data("value"));
		$(this).parents(".custom-options").find(".custom-option").removeClass("selection");
		$(this).addClass("selection");
		$(this).parents(".custom-select").removeClass("opened");
		$(this).parents(".custom-select").find(".custom-select-trigger").text($(this).text());

		$('#' + id).trigger("click");
	});
	//// ############## CUSTOM DROPDOWN UI JS ENDS ################### //

	// Custom Js

	// $('select[name=unify_connection_crm_select]').on('click', function () {
	// 	$('#unify_connection_crm').val($(this).find(":selected").val());
	// 	$('#unify_connection_crm').trigger('blur');
	// 	if ($(this).find(":selected").val() == 'limelight') {
	// 		$('#offer_model_ent_div').show();
	// 		$('#unify_connection_endpoint_div').show();
	// 	} else {
	// 		$('#offer_model_ent_div').hide();
	// 		$('#unify_connection_endpoint_div').hide();
	// 	}
	// });


	$('select[name=unify_connection_crm_select]').on('click', function () {
		$('#unify_connection_crm').val($(this).find(":selected").val());
		$('#unify_connection_crm').trigger('blur');

		/**
		 * Dynamically calling UI methods
		 */
		if ($(this).find(":selected").val() != '') {
			eval($(this).find(":selected").val() + 'UiConfig()')
		}
	});




	if ($('#unify_connection_crm').val() != '') {
		$('select[name=unify_connection_crm_select]').next(".custom-select").find(".custom-select-trigger").text($('#unify_connection_crm').attr('data-txt'));
		

		$(".custom-option").each(function() {
		var atrFil = $(this).attr("data-value");
		if (atrFil == $('#unify_connection_crm').val()) {
		$(this).trigger('click');
		}
		});
	}
	
	$('select[name=unify_connection_offer_model_select]').on('click', function () {
		$('#unify_connection_offer_model').val($(this).find(":selected").val());
	});

	$('select[name=unify_order_note_enable]').on('click', function () {
		$('#unify_order_note').val($(this).find(":selected").val());
	});

	//// ############## On Load setting dropdwon val STARTS ################### //
	if ($('#unify_connection_crm').val() == 'limelight') {
		$('#offer_model_ent_div').show();
		$('#unify_connection_endpoint_div').show();
	}

	$("#post-stat-action").hide();
	$("#post-stat").click(function () {
		$(this).next('#post-stat-action').toggle();
	});

	$('.selct-stat-val').click(function () {
		var stat = $(this).attr('val');
		$('#post_status').val(stat);
		$('#post-stat').html(stat.charAt(0).toUpperCase() + stat.slice(1));
	});

//	// *********** Hide connection message
//	$('.cross-position').click(function () {
//		$(this).parents('div .container-fluid').fadeOut();
//	});
	
	
	// ######### ON SUBMIT OF FORM JS VALIDATION STARTS #########//
	
	$('#submit_connection').click(function(){
		
		$("#unify_connections_post").validate({ // initialize the plugin
			ignore: [],
			rules: {
				post_title: {
					required: true
				},
				unify_connection_campaign_id: {
					required: true
				},
				unify_connection_shipping_id: {
					required: function (element) {
						if($('#unify_connection_crm_select').val() == 'limelight'){
							return true;
						}
						return false;
					}
				},
				unify_connection_crm: {
					required: true
				},
				unify_connection_endpoint: {
					required: function (element) {
						if($('#unify_connection_crm_select').val() == 'limelight'){
							return true;
						}
						return false;
					}
				},
				unify_connection_api_username: {
					required: true
				},
				unify_connection_api_password: {
					required: function (element) {
						if($('#unify_connection_crm_select').val() == 'response'){
							return false;
						}
						return true;
					}
				},
			},
			messages :{
				post_title: {
					required: 'Post Title is a required field.'
				},
				unify_connection_campaign_id: {
                                        required:function(element) {
                                         if($('#unify_connection_crm_select').val() == 'response'){
											return 'Site ID is a required field.';
									}
                                            return 'Campaign ID is a required field.';
                                        } 
				},
				unify_connection_shipping_id: {
                                        required:function(element) {
                                         if($('#unify_connection_crm_select').val() == 'limelight')
                                         {
											return 'Shipping ID is a required field.';
										}     
                                    } 
				},
				unify_connection_crm: {
					required: 'CRM is a required field.'
				},
				unify_connection_endpoint: {
					required: 'Endpoint is a required field.'
				},
				unify_connection_api_username: {
					required:function(element) {
                                         if($('#unify_connection_crm_select').val() == 'response'){
							return 'Secret is a required field.';
						}
                                            return 'Username is a required field.';
                                        } 
				},
				unify_connection_api_password: {
					required: 'Password is a required field.'
				}
			},
			errorClass:'text-danger',
		});
		
		if($("#unify_connections_post").valid()){
			$('#unify_connections_post').submit();
			return true;
		}
		return false;
	});

	// ######### ON SUBMIT OF FORM JS VALIDATION ENDS #########//

	var enable_unify_order_note_value = $('#unify_order_note').val();

	if(enable_unify_order_note_value == 1){
		$('#unify_order_note_enable').attr("checked", "checked");
	}
	$('#unify_order_note_enable').change(function() {
        if(this.checked) {
            $("#unify_order_note").val("1");
            $(this).attr("checked", "checked");
        }else{
        	$("#unify_order_note").val("0");
        	$(this).removeAttr("checked");
        }
              
    });


    var enable_unify_billing_model_value = $('#unify_connection_offer_model').val();

	if(enable_unify_billing_model_value == 1){
		$('#unify_connection_offer_model_select').attr("checked", "checked");
		$("#unify_order_note").val("0");
        $('.unify_order_note_enable_div').css("display","none");
	}
	$('#unify_connection_offer_model_select').change(function() {
        if(this.checked) {
            $("#unify_connection_offer_model").val("1");
            $(this).attr("checked", "checked");
            $("#unify_order_note").val("0");
            $('.unify_order_note_enable_div').css("display","none");
        }else{
        	$("#unify_connection_offer_model").val("0");
        	$(this).removeAttr("checked");
        	$('.unify_order_note_enable_div').css("display","block");
        }
              
    });


    var enable_unify_response_crm_type = $('#unify_response_crm_type_enable').val();

	if(enable_unify_response_crm_type == 1){
		$('#unify_connection_response_crm_type_enable').attr("checked", "checked");
	}
	$('#unify_connection_response_crm_type_enable').change(function() {
        if(this.checked) {
            $("#unify_response_crm_type_enable").val("1");
            $(this).attr("checked", "checked");
        }else{
        	$("#unify_response_crm_type_enable").val("0");
        	$(this).removeAttr("checked");
        }
              
    });

});

jQuery(document).on("click", function (event) {

	if (event.target.id != 'post-stat') {
		jQuery("#post-stat-action").hide();
	}
	
	if (jQuery(event.target).is(".custom-select") == false) {
		jQuery(".custom-select").removeClass("opened");
	}

});



/**
 * Configure Connection's UI for Limelight
 */
function limelightUiConfig() {
	$('#offer_model_ent_div').show();
	$('#unify_connection_campaign_details').show();
	$('#unify_connection_api_username_details').show();
	$('#unify_connection_endpoint_div').show();
	$('#unify_connection_site_id').hide();
	$('#unify_connection_campaign_id').html('Campaign ID');
	$('#unify_connection_shipping_id').show();
	$('#unify_campaign_details').show();
	$('#unify_connection_username_label').html('Username');
	$('#unify_connection_password_label').show();
}

/**
 * Configure Connection's UI for Konnektive
 */
function konnektiveUiConfig() {
	$('#unify_connection_campaign_details').show();
	$('#unify_connection_api_credential').hide();
	$('#offer_model_ent_div').hide();
	$('#unify_connection_api_username_details').show();
	$('#unify_connection_endpoint_div').hide();
	//$('#unify_connection_shipping_id').hide();
	$('#unify_connection_campaign_id').html('Campaign ID');
        $('#unify_connection_username_label').html('Username');
        $('#unify_connection_password_label').show();
}

/**
 * Configure Connection's UI for Konnektive
 */
function responseUiConfig() {
	$('#unify_connection_campaign_details').show();
	$('#unify_connection_shipping_id').hide();
	$('#unify_connection_api_username_details').show();
	$('#unify_connection_campaign_id').html('Site ID');
	$('#unify_connection_username_label').html('Secret');  
	$('#offer_model_ent_div').hide();
    $('#unify_connection_endpoint_div').hide();
	$('#unify_connection_password_label').hide();
	$('#response_crm_ent_div').show();
}





