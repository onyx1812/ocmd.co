<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * this official WooCommerce MyParcel plugin by MyParcel
 * URL: https://www.myparcel.nl
 */
class WFACP_Compatibility_With_WC_MyParcel_2_1_4 {
	private static $instance = null;
	private $parcel_keys = [
		'shipping_street_name',
		'shipping_house_number',
		'shipping_house_number_suffix',
		'billing_street_name',
		'billing_house_number',
		'billing_house_number_suffix',
	];

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'after_setup_theme', [ $this, 'setup_fields_billing' ] );
		add_action( 'after_setup_theme', [ $this, 'setup_fields_shipping' ] );


	}


	private function is_enabled() {
		$page_version = WFACP_Common::get_checkout_page_version();


		if ( class_exists( 'WCMYPA' ) && version_compare( $page_version, '2.1.3', '>' ) ) {
			return true;
		}


		return false;

	}

	public function setup_fields_billing() {
		if ( false == $this->is_enabled() ) {
			return;
		}



		new WFACP_Add_Address_Field( 'street_name', array(
			'label'       => __( 'Street name', 'woocommerce-myparcel' ),
			'placeholder' => __( 'Street name', 'woocommerce-myparcel' ),
			'class'       => [ 'form-row-first', 'address-field', 'wfacp_street_name', 'wfacp-draggable' ],
			'cssready'    => [ 'wfacp-col-full' ],
			'clear'       => false,
			'required'    => false,
			'priority'    => 90,
		) );

		new WFACP_Add_Address_Field( 'house_number', array(
			'label'       => __( 'No.', 'woocommerce-myparcel' ),
			'placeholder' => __( 'No.', 'woocommerce-myparcel' ),
			'class'       => [ 'form-row-first', 'address-field', 'wfacp_house_number', 'wfacp-draggable' ],
			'cssready'    => [ 'wfacp-col-full' ],
			'clear'       => false,
			'required'    => false,
			'priority'    => 90,
		) );

		new WFACP_Add_Address_Field( 'house_number_suffix', array(
			'label'       => __( 'Suffix', 'woocommerce-myparcel' ),
			'placeholder' => __( 'Suffix', 'woocommerce-myparcel' ),
			'class'       => [ 'form-row-first', 'address-field', 'wfacp_house_number_suffix', 'wfacp-draggable' ],
			'cssready'    => [ 'wfacp-col-full' ],
			'clear'       => false,
			'required'    => false,
			'priority'    => 90,
		) );


	}

	public function setup_fields_shipping() {
		if ( false == $this->is_enabled() ) {
			return;
		}

		new WFACP_Add_Address_Field( 'street_name', array(
			'label'       => __( 'Street name', 'woocommerce-myparcel' ),
			'placeholder' => __( 'Street name', 'woocommerce-myparcel' ),
			'class'       => [ 'form-row-first', 'address-field', 'wfacp_street_name', 'wfacp-draggable' ],
			'cssready'    => [ 'wfacp-col-full' ],
			'clear'       => false,
			'required'    => false,
			'priority'    => 60,
		), 'shipping' );

		new WFACP_Add_Address_Field( 'house_number', array(
			'label'       => __( 'No.', 'woocommerce-myparcel' ),
			'placeholder' => __( 'No.', 'woocommerce-myparcel' ),
			'class'       => [ 'form-row-first', 'address-field', 'wfacp_house_number', 'wfacp-draggable' ],
			'cssready'    => [ 'wfacp-col-full' ],
			'clear'       => false,
			'required'    => false,
			'priority'    => 60,
		), 'shipping' );

		new WFACP_Add_Address_Field( 'house_number_suffix', array(
			'label'       => __( 'Suffix', 'woocommerce-myparcel' ),
			'placeholder' => __( 'Suffix', 'woocommerce-myparcel' ),
			'class'       => [ 'form-row-first', 'address-field', 'wfacp_house_number_suffix', 'wfacp-draggable' ],
			'cssready'    => [ 'wfacp-col-full' ],
			'clear'       => false,
			'required'    => false,
			'priority'    => 60,
		), 'shipping' );


	}

}

WFACP_Compatibility_With_WC_MyParcel_2_1_4::get_instance();

