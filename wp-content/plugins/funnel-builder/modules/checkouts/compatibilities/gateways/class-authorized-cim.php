<?php

class WFACP_Compatibility_Authorize {
	public function __construct() {
		add_action( 'wfacp_before_process_checkout_template_loader', [ $this, 'actions' ], 10, 2 );
	}

	public function actions( $wfacp_id, $instances ) {
		$payment_method = filter_input( INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING );
		if ( ! is_null( $payment_method ) && false !== strpos( $payment_method, 'authorize_net_cim' ) ) {
			remove_action( 'woocommerce_checkout_order_processed', [ $instances, 'woocommerce_checkout_update_order_meta' ] );
			add_action( 'woocommerce_checkout_update_order_meta', [ $instances, 'woocommerce_checkout_update_order_meta' ] );
		}
	}
}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_Authorize(), 'authorize' );