<?php

/**
 * https://wordpress.org/plugins/gumlet/
 * Class WFACP_Compatibility_GumLet
 */
class WFACP_Compatibility_GumLet {
	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'remove_action' ] );
	}

	public function remove_action() {
		if ( isset( $_REQUEST['wc-ajax'] ) && class_exists( 'WFACP_Common' ) && class_exists( 'Gumlet' ) ) {
			WFACP_Common::remove_actions( 'init', 'Gumlet', 'init_ob' );
		}
	}
}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_GumLet(), 'gumLet' );