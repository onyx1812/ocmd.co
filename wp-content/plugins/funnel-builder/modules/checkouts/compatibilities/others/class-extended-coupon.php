<?php

/**
 * WooCommerce Extended Coupon Features PRO
 * By Soft79
 * Class WFACP_Compatibility_Extended_Coupon_Pro
 */
class WFACP_Compatibility_Extended_Coupon_Pro {
	public function __construct() {
		add_action( 'wfacp_before_coupon_apply', [ $this, 'action' ] );
	}

	public function action() {
		if ( class_exists( 'WJECF_Bootstrap' ) && class_exists( 'WC_Subscriptions_Coupon' ) ) {
			remove_action( 'woocommerce_before_calculate_totals', 'WC_Subscriptions_Coupon::remove_coupons' );
		}
	}
}

new WFACP_Compatibility_Extended_Coupon_Pro();
