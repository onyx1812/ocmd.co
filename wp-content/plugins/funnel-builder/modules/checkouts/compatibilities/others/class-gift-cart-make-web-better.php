<?php

/**
 * https://makewebbetter.com/
 * Author   MakeWebBetter
 * Class WFACP_Compatibility_Gift_Card_MakeWebBetter
 */
class WFACP_Compatibility_Gift_Card_MakeWebBetter {
	public function __construct() {
		add_action( 'wfacp_checkout_page_found', [ $this, 'remove_action' ] );
	}

	public function remove_action() {
		if ( class_exists( 'Woocommerce_Gift_Cards_Lite_Public' ) ) {
			WFACP_Common::remove_actions( 'woocommerce_add_cart_item_data', 'Woocommerce_Gift_Cards_Lite_Public', 'mwb_wgm_woocommerce_add_cart_item_data' );
		}
	}
}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_Gift_Card_MakeWebBetter(), 'gift_make_web_better' );
