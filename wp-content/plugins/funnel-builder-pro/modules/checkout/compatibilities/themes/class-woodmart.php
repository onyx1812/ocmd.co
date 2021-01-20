<?php

class WFACP_Compatibility_WoodMart_Theme {
	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'register_elementor_widget' ], 20 );
	}

	public function register_elementor_widget() {
		if ( defined( 'WOODMART_THEME_DIR' ) && class_exists( 'Elementor\Plugin' ) && class_exists( 'WFACP_Core' ) ) {
			if ( is_admin() ) {
				return;
			}
			if ( false == wfacp_elementor_edit_mode() ) {
				$r_instance = WFACP_Common::remove_actions( 'init', 'WOODMART_Theme', 'elementor_files_include' );
				if ( $r_instance instanceof WOODMART_Theme ) {
					add_action( 'wp', array( $r_instance, 'elementor_files_include' ), 100 );
				}
			}
		}
	}
}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_WoodMart_Theme(), 'woodmart' );
