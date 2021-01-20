<?php

/**
 * WooCommerce wFirma By WP Desk
 * Plugin URI: https://www.wpdesk.pl/sklep/wfirma-woocommerce/
 * Version: 2.2.6
 */
class WFACP_Compatibility_wfirma_wc {
	public function __construct() {
		/* Register Add field */
		add_action( 'after_setup_theme', [ $this, 'setup_fields_billing' ] );
		
	}

	public function is_enabled() {
		return class_exists( 'WPDesk\WooCommerceWFirma\WoocommerceIntegration' );
	}

	public function setup_fields_billing() {
		if ( false === $this->is_enabled() ) {
			return;
		}

		new WFACP_Add_Address_Field( 'nip', array(
			'type'         => 'text',
			'label'        => __( 'NIP', 'woocommerce-wfirma' ),
			'palaceholder' => __( 'NIP', 'woocommerce-wfirma' ),
			'cssready'     => [ 'wfacp-col-full' ],
			'class'        => array( 'form-row-third first', 'wfacp-col-full' ),
			'required'     => false,
			'priority'     => 60,
		) );
	}
}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_wfirma_wc(), 'woocommerce-wfirma' );
