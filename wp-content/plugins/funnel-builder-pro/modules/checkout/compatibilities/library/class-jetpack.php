<?php
/**
 *
 * Remove JetPack Notes JS
 * Class WFACP_Compatibility_JetPack
 */

class WFACP_Compatibility_JetPack {
	public function __construct() {
		add_action( 'wp_loaded', [ $this, 'remove_action' ] );
	}

	public function remove_action() {
		if ( WFACP_Common::is_builder() ) {
			WFACP_Common::remove_actions( 'admin_head', 'Jetpack_Notifications', 'styles_and_scripts' );
		}
	}
}

new WFACP_Compatibility_JetPack();