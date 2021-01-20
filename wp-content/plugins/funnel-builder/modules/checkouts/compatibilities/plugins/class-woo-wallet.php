<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Plugin Name: TeraWallet by WCBeginner
 * Plugin URI: https://wordpress.org/plugins/woo-wallet/
 */
class WFACP_Compatibility_With_Woo_Wallet {

	public function __construct() {
		add_filter( 'wfacp_css_js_deque', [ $this, 'css_enqueue' ], 10, 4 );
	}

	public function css_enqueue( $bool, $path, $url, $current ) {
		if ( ! class_exists( 'WooWallet' ) ) {
			return $bool;
		}
		if ( false !== strpos( $url, '/smoothness/jquery-ui.css' ) ) {
			return false;
		}

		return $bool;
	}
}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_With_Woo_Wallet(), 'woo-wallet' );
