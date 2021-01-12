var $ = jQuery;

jQuery(document).ready(function ($) {

	$(".custom-select").each(function () {
		var classes = $(this).attr("class"),
			id = $(this).attr("id"),
			name = $(this).attr("name");
		var template = '<div class="' + classes + '">';
		template += '<span class="custom-select-trigger">' + $(this).attr("placeholder") + '</span>';
		template += '<div class="custom-options">';
		$(this).find("option").each(function () {
			template += '<span class="custom-option ' + $(this).attr("class") + '" data-value="' + $(this).attr("value") + '" data-crm="' + $(this).attr("data-crm") + '" data-billing-model="' + $(this).attr("data-billing-model") + '" >' + $(this).html() + '</span>';
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
	
	
	
	//// Form Validation 
	$('#submit_settings').click(function(){
		
		$("#unify_settings_form_post").validate({ // initialize the plugin
			ignore: [],
			rules: {
				title: {
					required: function (element) {
						if($('#enabled').is(':checked')){
							return true;
						}
						return false;
					}
				},
				description: {
					required: function (element) {
						if($('#enabled').is(':checked')){
							return true;
						}
						return false;
					}
				},
				connection: {
					required: function (element) {
						if($('#enabled').is(':checked')){
							return true;
						}
						return false;
					}
				},
				shipping_product_id: {
					required: function (element) {
						if($('#enabled').is(':checked') 
							&& $('select[name=connection]').find(":selected").attr('data-crm') == 'limelight' && $('#shipment_price_settings').val() == 1){
							return true;
						}
						return false;
					}
				},
				shipping_product_offer_id: {
					required: function (element) {
						if($('#enabled').is(':checked') && $('#shipment_price_settings').val() == 1 
							&& $('select[name=connection]').find(":selected").attr('data-crm') == 'limelight' && $('select[name=connection]').find(":selected").attr('data-billing-model') == 1){
							return true;
						}
						return false;
					}
				},
				shipping_product_billing_id: {
					required: function (element) {
						if($('#enabled').is(':checked') && $('select[name=connection]').find(":selected").attr('data-crm') == 'limelight' && $('#shipment_price_settings').val() == 1
							&& $('select[name=connection]').find(":selected").attr('data-billing-model') == 1){
							return true;
						}
						return false;
					}
				},
				paypal_payment_title: {
					required: function (element) {
						if($('#paypal_enabled').is(':checked') && $('select[name=connection]').find(":selected").attr('data-crm') == 'limelight'){
							return true;
						}
						return false;
					}
				},
				paypal_payment_description: {
					required: function (element) {
						if($('#paypal_enabled').is(':checked') && $('select[name=connection]').find(":selected").attr('data-crm') == 'limelight'){
							return true;
						}
						return false;
					}
				},
			},
			messages :{
				title: {
					required: 'Title is a required field.'
				},
				description: {
					required: 'Description is a required field.'
				},
				connection: {
					required: 'Connection is a required field.'
				},
				shipping_product_id: {
					required: 'CRM Product ID is a required field.'
				},
				shipping_product_offer_id: {
					required: 'CRM Offer ID is a required field.'
				},
				shipping_product_billing_id: {
					required: 'CRM Billing ID is a required field.'
				},
				paypal_payment_title: {
					required: 'Title is a required field.'
				},
				paypal_payment_description: {
					required: 'Description is a required field.'
				}
			},
			errorClass:'text-danger',
			errorPlacement: function(error, element) {
				//Custom position: connection
				if (element.attr("name") == "connection") 
				{
					$("#connection_error").append(error);
				} else
				{
					error.insertAfter(element);
				}
				
			},
		});
		
		if($("#unify_settings_form_post").valid()){
			$('#unify_settings_form_post').submit();
			return true;
		}
		return false;
	});
	
	
	// Custom Js
	onLoadFirst();
	activatePaymentMethod();

	// On Change connection
	$('select[name=connection]').on('click', function () {
		$('#connection_val').val($(this).find(":selected").val());			
		showAdditionalFeilds();
		onChangeConn();
		activatePaymentMethod();
	});
	
	
	// On change shipping
	$('select[name=shipment_price_settings]').on('click', function () {
		$('#shipment_price_settings_val').val($(this).find(":selected").val());	
		showShippingConfig();
	});

});

jQuery(document).on("click", function (e) {
	
	if (jQuery(e.target).is(".custom-select") == false) {
		jQuery(".custom-select").removeClass("opened");
	}

});

function isNumberKey(evt)
{
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;

	return true;
}

function onLoadFirst(){
	$('#shipment_price_settings_div').hide();
	$('#shipping_product_div').hide();
	
	if($('#connection_val').val() != ''){		
		$('select[name=connection]').val($('#connection_val').val());		
	}
	highlightDropDownConnection();
	showAdditionalFeilds();
	
	if($('#shipment_price_settings_val').val() != ''){		
		$('select[name=shipment_price_settings]').val($('#shipment_price_settings_val').val());			
	}
	highlightDropDownShipping();
	showShippingConfig();
}

function highlightDropDownConnection(){
	$('select[name=connection]').next().find(".custom-option").each(function () {
		var atrFil = $(this).attr("data-value");
		if (atrFil == $('#connection_val').val()) {
			$(this).trigger('click');
		}
	});
}

function highlightDropDownShipping(){
	$('select[name=shipment_price_settings]').next().find(".custom-option").each(function () {
		var atrFil = $(this).attr("data-value");
		if (atrFil == $('#shipment_price_settings_val').val()) {
			$(this).trigger('click');
		}
	});
}

function showAdditionalFeilds(){
	
	if ($('select[name=connection]').find(":selected").attr('data-crm') == 'limelight') {
		$('#shipment_price_settings_div').show();
	} else {
		$('#shipment_price_settings_div').hide();
	}	
}

function showShippingConfig(){
	if($('select[name=connection]').find(":selected").attr('data-crm') == 'limelight' 
		&& $('#shipment_price_settings_val').val() == 1){	
		$('#shipping_product_div').show();
		if($('select[name=connection]').find(":selected").attr('data-billing-model') == 1){
			$('#shipping_product_id_div').removeClass('col-sm-12').addClass('col-sm-4');
			$('.shipping_product_offer_div').show();			
		}else{
			$('.shipping_product_offer_div').hide();
			$('#shipping_product_id_div').addClass('col-sm-12').removeClass('col-sm-4');
		}
	}else{
		$('#shipping_product_div').hide();
	}
}

function onChangeConn(){	
	$('#shipment_price_settings_val').val('');
	$('#shipping_product_div').hide();
	$('.shipping_product_offer_div').hide();
	$('#shipping_product_id_div').addClass('col-sm-12').removeClass('col-sm-4');
	highlightDropDownShipping();
	$('#shipping_product_id').val('');
	$('#shipping_product_offer_id').val('');
	$('#shipping_product_billing_id').val('');
}

function activatePaymentMethod(){
	if ($('select[name=connection]').find(":selected").attr('data-crm') == 'limelight') {
		$('#additional_payment_method1').show();
		if ($("input[name=paypal_enabled]").is(':checked')){
			$('#additional_payment_method1_title').show();
	        $('#additional_payment_method1_desc').show(); 
		}
		$('input[name=paypal_enabled]').change(function() {
	        if(this.checked) {
	          $('#additional_payment_method1_title').show();
	          $('#additional_payment_method1_desc').show(); 
	        }else{
	          $('#additional_payment_method1_title').hide();
	          $('#additional_payment_method1_desc').hide();
	        }    
    	});
	} else {
		$('#additional_payment_method1').hide();
		$('#additional_payment_method1_title').hide();
        $('#additional_payment_method1_desc').hide(); 
	}
}


