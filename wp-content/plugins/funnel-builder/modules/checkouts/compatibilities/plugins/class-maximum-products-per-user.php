<?php

/**
 * By Algoritmika
 * Class WFACP_Maximum_Products_Per_User
 */
class WFACP_Maximum_Products_Per_User {
	public function __construct() {
		add_action( 'wfacp_after_template_found', [ $this, 'action' ], 1 );
	}

	public function action() {
		if ( class_exists( 'Alg_WC_MPPU_Core' ) ) {
			WFACP_Common::remove_actions( 'wp', 'Alg_WC_MPPU_Core', 'block_checkout' );
		}
	}
}

WFACP_Plugin_Compatibilities::register( new WFACP_Maximum_Products_Per_User(), 'mppu' );