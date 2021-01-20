<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Buy One Get One Free By Oscar Gare
 * Plugin URI: https://woocommerce.com/products/buy-one-get-one-free/
 */
class WFACP_Compatibility_With_WC_Buy_One_Get_One_Free {

	public function __construct() {
		add_action( 'wfacp_after_checkout_page_found', [ $this, 'remove_actions' ] );
		add_action( 'wfacp_checkout_page_found', [ $this, 'remove_actions' ] );
	}

	public function is_enabled() {
		if ( class_exists( 'WC_BOGOF_Cart' ) ) {
			return true;
		}

		return false;

	}

	public function remove_actions() {
		if ( ! $this->is_enabled() ) {
			return;
		}
		WFACP_Common::remove_actions( 'template_redirect', 'WC_BOGOF_Cart', 'refresh_cart_rules_items' );
	}
}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_With_WC_Buy_One_Get_One_Free(), 'wfacp-bogof' );
