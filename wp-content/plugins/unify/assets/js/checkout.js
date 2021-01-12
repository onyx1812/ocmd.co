jQuery(function ($) {
    $(document).on('updated_checkout', function () {
        $('#billing_state_field > label > .optional').remove();
        $('#billing_state_field').addClass('validate-required');
        
        if($('#billing_state_field > label').html().search('required') < 1)
        {
            $('#billing_state_field > label').append('<abbr class="required" title="required">*</abbr>');
        }

        $('#shipping_state_field > label > .optional').remove();
        $('#shipping_state_field').addClass('validate-required');
        
        if($('#shipping_state_field > label').html() != undefined && $('#shipping_state_field > label').html().search('required') < 1)
        {
            $('#shipping_state_field > label').append('<abbr class="required" title="required">*</abbr>');
        }
    });

    jQuery( 'body' )
    .on( 'updated_checkout', function() {
          usingGateway();
            jQuery('input[name="payment_method"]').change(function(){
            $('.overlayDiv').show();
              usingGateway();
        });
    });

    $(".place_order_paypal").on('click', function() {
        var payment_method = jQuery('form.checkout').find('input[name^="payment_method"]:checked').val();
        if(payment_method === 'codeclouds_unify_paypal_payment'){   
            var v = valid_billing();
            if(v){
                gatewayFunction( this );
            }
        }
     } ); 
    function gatewayFunction( form ) {
        var origin = window.location.pathname;
          jQuery.ajax({
                url: origin+'?wc-ajax=checkout',
                type: "POST",
                data: jQuery(".woocommerce-checkout").serialize(),
                beforeSend: function () {
                    $('.overlayDiv').show();
                },
                success: function(response) {
                    var obj = typeof response === 'string' ? JSON.parse(response) : response;
                    var res = obj.result;
                    var reserror = obj.messages;
                    $('.overlayDiv').hide();
                    if(res!='failure'){
                        let left = (screen.width - 600) / 2;
                        let top = (screen.height - 600) / 4;
                        if(typeof res == 'string' && res.includes('<html>')) {
                            var w = window.open("","unifyWindow","width=600,height=600" + "top=" + top + ", left=" + left);
                                    w.document.open();
                                    w.document.write("<div class='spinner loading'></div>");
                                    w.document.write(res);
                                    w.document.close();
                                     w.focus();
                                    setInterval(function() {
                                        try {
                                            if (w.closed == true) {
                                            return;
                                        }
                                        w.focus();
                                        var url = w.location.href;
                                            var urlParams = new URLSearchParams(w.location.search);
                                            if(urlParams.has('responseCode')){
                                                w.close();
                                                window.location.replace(url);
                                            }
                                        } catch (e) {
                                        if (w.closed == true) {
                                        }
                                    }
                                        }.bind(this), 100);
                        } else {
                            var w = window.open(res,"unifyWindow","width=600,height=600" + "top=" + top + ", left=" + left);
                                     w.focus();
                                    setInterval(function() {
                                        try {
                                            if (w.closed == true) {
                                            return;
                                        }
                                        w.focus();
                                        var url = w.location.href;
                                            var urlParams = new URLSearchParams(w.location.search);
                                            if(urlParams.has('responseCode')){
                                                w.close();
                                                window.location.replace(url);
                                            }
                                        } catch (e) {
                                        if (w.closed == true) {
                                        }
                                    }
                                        }.bind(this), 100);
                        }
                    }else{
                        $('.overlayDiv').hide();
                        $(reserror).insertBefore("#customer_details");
                        $('.woocommerce-error').delay(5000).fadeOut('slow');
                            $('html, body').animate({scrollTop:10}, 'slow');

                    }
                },
                error: function() {
                    console.log("error");
                }
            }); 
        return false;
    }

    function usingGateway(){
        var payment_method = jQuery('form.checkout').find('input[name^="payment_method"]:checked').val();
        if(payment_method == 'codeclouds_unify_paypal_payment'){
            $('#place_order').css("display","none");
            $('#place_order_paypal').css("display","block");
            $('.overlayDiv').hide();
        }else{
            $('#place_order_paypal').css("display","none");
            $('#place_order').css("display","block");
            $('.overlayDiv').hide();
        }
    }

    function valid_billing() {
            var valid = true;
            var message = '';
            var billing_first_name = $("#billing_first_name").val();
            var billing_last_name = $("#billing_last_name").val();
            var billing_address = $("#billing_address_1").val();
            var billing_city = $("#billing_city").val();
            var billing_zip = $("#billing_postcode").val();
            var billing_email = $("#billing_phone").val();
            var billing_phone = $("#billing_email").val();
            if (billing_first_name === '') {
                valid = false;
                var wrapper = $("#billing_first_name").closest('.form-row');
                wrapper.addClass('woocommerce-invalid'); 
            }
            if (billing_last_name === '') {
                valid = false;
                var wrapper = $("#billing_last_name").closest('.form-row');
                wrapper.addClass('woocommerce-invalid'); 
            }
            if (billing_address === '') {
                valid = false;
                var wrapper = $("#billing_address_1").closest('.form-row');
                wrapper.addClass('woocommerce-invalid'); 
            }
            if (billing_city === '') {
                valid = false;
                var wrapper = $("#billing_city").closest('.form-row');
                wrapper.addClass('woocommerce-invalid'); 
            }
            if (billing_zip === '') {
                valid = false;
                var wrapper = $("#billing_postcode").closest('.form-row');
                wrapper.addClass('woocommerce-invalid'); 
            }
            if (billing_email === '') {
                valid = false;
                var wrapper = $("#billing_email").closest('.form-row');
                wrapper.addClass('woocommerce-invalid'); 
            }
            if (billing_phone === '') {
                valid = false;
                var wrapper = $("#billing_phone").closest('.form-row');
                wrapper.addClass('woocommerce-invalid'); 
            }
            

            if (valid) {
                return true;


            } else {
                return false;
            }


    }

 
});