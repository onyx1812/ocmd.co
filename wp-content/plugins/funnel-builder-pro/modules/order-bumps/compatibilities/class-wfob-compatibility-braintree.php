<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WFOB_Compatibility_With_braintree {
	public function __construct() {
		add_filter( 'wp_footer', [ $this, 'trigger_hidden_field_order_total' ] );
	}

	public function trigger_hidden_field_order_total() {
		?>

        <script>
            window.addEventListener('load', function () {
                (function ($) {
                    $(document.body).on('wfob_bump_trigger', function (e, rsp) {
                        if ($("input[name='wc-braintree-credit-card-3d-secure-order-total']").length === 0) {
                            return;
                        }

                        if (rsp && rsp.hasOwnProperty('cart_total')) {
                            $("input[name='wc-braintree-credit-card-3d-secure-order-total']").val(rsp.cart_total);
                        }
                    });

                })(jQuery);
            });
        </script>
		<?php

	}
}

WFOB_Plugin_Compatibilities::register( new WFOB_Compatibility_With_braintree(), 'braintree' );
