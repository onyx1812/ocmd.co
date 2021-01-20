<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WFOB_Compatibility_With_AeroCheckout {
	public function __construct() {
		/* checkout page */
		add_action( 'wfacp_after_checkout_page_found', [ $this, 'alter_bump_position' ] );
		add_action( 'wfacp_before_process_checkout_template_loader', [ $this, 'alter_bump_position' ] );
	}

	public function alter_bump_position() {
		add_filter( 'wfob_bump_positions', [ $this, 'wfob_bump_positions' ] );
	}

	public function wfob_bump_positions( $position ) {

		$position['woocommerce_checkout_order_review_above_order_summary']['hook'] = 'wfacp_before_order_summary_field';
		$position['woocommerce_checkout_order_review_below_order_summary']['hook'] = 'wfacp_after_order_summary_field';
		return $position;
	}
}

WFOB_Plugin_Compatibilities::register( new WFOB_Compatibility_With_AeroCheckout(), 'aerocheckout' );
