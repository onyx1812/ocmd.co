<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WFOB_WC_Germinized {
	public function __construct() {
		/* checkout page */
		add_action( 'wp', [ $this, 'actions' ] );
	}

	public function actions() {

		if ( class_exists( 'WooCommerce_Germanized' ) && function_exists( 'is_checkout' ) && is_checkout() ) {

			if ( ! is_null( WFOB_Core()->public ) ) {

				$this->remove_hook();
			}
		}

	}

	public function remove_hook() {
		remove_action( 'woocommerce_before_template_part', [ WFOB_Core()->public, 'add_order_bump' ], 10 );
		add_action( 'woocommerce_before_template_part', [ $this, 'add_order_bump' ], 10, 2 );
	}

	public function add_order_bump( $template_name, $template_path ) {
		if ( 'checkout/terms.php' === $template_name ) {
			do_action( 'wfob_below_payment_gateway' );
		}
	}
}

WFOB_Plugin_Compatibilities::register( new WFOB_WC_Germinized(), 'germinized' );
