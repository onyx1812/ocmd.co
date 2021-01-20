<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WFACP_Compatibility_With_WC_Select_City {


	public function __construct() {

		add_action( 'wfacp_internal_css', [ $this, 'execute_style_script' ] );

	}


	public function execute_style_script() {

		if ( ! class_exists( 'WC_City_Select' ) ) {
			return '';
		}


		?>

        <style>
            body .wfacp-form .wfacp_main_form.woocommerce form .form-row .select2-container {
                width: 100% !important;
            }

        </style>
        <script>

            window.addEventListener('load', function () {
                (function ($) {

                    if (jQuery('#billing_state').length > 0) {
                        jQuery('#billing_state').trigger('change')
                    }
                    if (jQuery('#shipping_state').length > 0) {
                        jQuery('#shipping_state').trigger('change')


                    }
                    $(document.body).on('updated_checkout', function () {
                        setTimeout(function () {
                            run_fields();
                        }, 500);
                    });

                    if ( typeof wc_country_select_params !== 'undefined' && typeof wc_city_select_params !== 'undefined' ) {
                        trigger_country();
                    }


                    function trigger_country() {
                        if ($('#billing_country').length > 0) {
                            $('#billing_country').trigger('change')
                        }
                        if ($('#shipping_country').length > 0) {
                            $('#shipping_country').trigger('change')
                        }
                    }


                    function run_fields() {
                        if ($('#shipping_city').length > 0) {
                            run_select2($('#shipping_city'));
                        }
                        if ($('#billing_city').length > 0) {
                            run_select2($('#billing_city'));
                        }
                    }


                    function run_select2(el) {
                        if (el[0].tagName === 'select' || el[0].tagName === 'SELECT') {
                            if (!el.parents('.form-row').hasClass('wfacp-anim-wrap')) {
                                el.parents('.form-row').addClass('wfacp-anim-wrap');
                            }
                            el.select2();
                        }


                    }


                })(jQuery);
            });
        </script>
		<?php

	}
}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_With_WC_Select_City(), 'wsc' );
