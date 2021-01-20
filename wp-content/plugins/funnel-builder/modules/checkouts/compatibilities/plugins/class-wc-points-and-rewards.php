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

	}

	public function actions() {
		if ( class_exists( 'WC_Points_Rewards_Cart_Checkout' ) ) {
			WFACP_Common::remove_actions( 'woocommerce_applied_coupon', 'WC_Points_Rewards_Cart_Checkout', 'discount_updated' );
		}
	}
}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_With_WC_Points_and_Reward(), 'wfacp-wc-pints-rewards' );
