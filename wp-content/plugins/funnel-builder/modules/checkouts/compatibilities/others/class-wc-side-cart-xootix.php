<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Woocommerce Side Cart Premium
 * http://xootix.com/side-cart-woocommerce
 * Class WFACP_Compatibility_Xoo_Wsc
 */
class WFACP_Compatibility_Xoo_Wsc {
	public function __construct() {
		add_action( 'wfacp_before_process_checkout_template_loader', [ $this, 'remove_action' ] );
	}

	public function remove_action() {
		if ( class_exists( 'Xoo_Wsc_Cart' ) ) {
			WFACP_Common::remove_actions( 'woocommerce_update_order_review_fragments', 'Xoo_Wsc_Cart', 'set_ajax_fragments' );
		}
	}
}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_Xoo_Wsc(), 'Xoo_Wsc' );