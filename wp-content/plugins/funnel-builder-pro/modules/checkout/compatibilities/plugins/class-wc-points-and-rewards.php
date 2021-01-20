<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Points and Rewards By WooCommerce
 * https://woocommerce.com/products/woocommerce-points-and-rewards/
 */
class WFACP_Compatibility_With_WC_Points_and_Reward {
	public function __construct() {

		/* Unhook rewards and points  */
		add_action( 'wfacp_checkout_page_found', [ $this, 'actions' ] );
		add_action( 'wfacp_internal_css', [ $this, 'add_js' ] );

	}

	public function actions() {
		if ( class_exists( 'WC_Points_Rewards_Cart_Checkout' ) ) {
			WFACP_Common::remove_actions( 'woocommerce_applied_coupon', 'WC_Points_Rewards_Cart_Checkout', 'discount_updated' );
		}
	}

	public function add_js() {
		if ( ! class_exists( 'WC_Points_Rewards_Cart_Checkout' ) ) {
			return;
		}

		?>
        <style>
            .wfacp-hide-sec {
                display: none !important;
            }
        </style>
        <script>

            window.addEventListener('load', function () {
                (function ($) {

                    $(document.body).on('wfacp_coupon_form_removed', function () {
                        setTimeout(function () {
                            remove_reward_notices();
                        }, 500)
                    });


                    function remove_reward_notices() {
                        if ($('.woocommerce-info.wc_points_rewards_earn_points').length > 0) {
                            $('.woocommerce-info.wc_points_rewards_earn_points').addClass("wfacp-hide-sec");
                            $('.woocommerce-info.wc_points_redeem_earn_points').addClass("wfacp-hide-sec");

                            $('.wfacp_layout_9_coupon_error_msg .woocommerce-info.wc_points_rewards_earn_points').removeClass("wfacp-hide-sec");
                            $('.wfacp_layout_9_coupon_error_msg .woocommerce-info.wc_points_redeem_earn_points').removeClass("wfacp-hide-sec");
                        }


                    }


                })(jQuery);
            });
        </script>
		<?php


	}


}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_With_WC_Points_and_Reward(), 'wfacp-wc-pints-rewards' );
