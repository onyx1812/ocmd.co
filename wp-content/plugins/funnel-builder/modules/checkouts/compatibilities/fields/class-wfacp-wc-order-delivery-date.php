<?php

/**
 * WooCommerce Order Delivery by Themesquad
 * Plugin URL: https://woocommerce.com/products/woocommerce-order-delivery/
 * Class WFACP_Compatibility_WC_delivery_date
 */
class WFACP_Compatibility_WC_delivery_date {

	private $wc_delivery_date_obj = null;

	public function __construct() {

		/* Register Add field */
		add_action( 'wfacp_template_load', [ $this, 'remove_wc_delivery_date_hook' ] );
		add_filter( 'wfacp_advanced_fields', [ $this, 'add_field' ], 20 );

		add_filter( 'wfacp_html_fields_wfacp_delivery_date', '__return_false' );

		add_action( 'process_wfacp_html', [ $this, 'call_wc_delivery_date_hook' ], 10, 3 );

		add_action( 'wfacp_internal_css', [ $this, 'internal_css' ] );
	}

	public function remove_wc_delivery_date_hook() {
		if ( ! $this->is_enable() ) {
			return;
		}
		$obj = WFACP_Common::remove_actions( 'woocommerce_checkout_shipping', 'WC_OD_Checkout', 'checkout_content' );
		if ( ! is_null( $obj ) ) {
			$this->wc_delivery_date_obj = $obj;

			return;
		}
	}

	public function add_field( $fields ) {
		if ( ! $this->is_enable() ) {
			return $fields;
		}
		$fields['wfacp_delivery_date'] = [
			'type'       => 'wfacp_html',
			'class'      => [ 'wfacp-col-full', 'wfacp-form-control-wrapper', 'wfacp_anim_wrap' ],
			'id'         => 'wfacp_delivery_date',
			'field_type' => 'advanced',
			'label'      => __( 'WC Order Delivery', 'woocommerce-order-delivery' ),
		];

		return $fields;
	}

	public function call_wc_delivery_date_hook( $field, $key, $args ) {
		if ( ! empty( $key ) && $key == 'wfacp_delivery_date' && $this->is_enable() && ! is_null( $this->wc_delivery_date_obj ) ) {
			echo "<div class='wfacp_delivery_date_wrap wfacp_clear'>";
			$this->wc_delivery_date_obj->checkout_content();
			echo "</div>";
		}
	}

	public function is_enable() {
		if ( class_exists( 'WC_Order_Delivery' ) ) {
			return true;
		}

		return false;
	}

	public function internal_css() {
		if ( ! $this->is_enable() ) {
			return '';
		}
		if ( ! function_exists( 'wfacp_template' ) ) {
			return;
		}
		$instance = wfacp_template();
		if ( ! $instance instanceof WFACP_Template_Common ) {
			return;
		}
		$px = $instance->get_template_type_px();
		if ( false !== strpos( $instance->get_template_type(), 'elementor' ) ) {
			$px = "7";
		}
		echo '<style>';
		if ( $px != '' ) {
			echo "body .wfacp_main_form.woocommerce .wfacp_delivery_date_wrap{clear: both;padding:0 $px" . 'px !important' . "}";
			echo "body .wfacp_main_form.woocommerce .wfacp_delivery_date_wrap p{margin-bottom:8px !important;}";
			echo "body .wfacp_main_form.woocommerce .wfacp_delivery_date_wrap h3{margin-top:0px !important;}";
			echo "body .wfacp_main_form.woocommerce .wfacp_delivery_date_wrap #wc-od #delivery_date{padding: 10px 12px 10px;}";
		}
		echo '</style>';
	}
}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_WC_delivery_date(), 'wfacp-wc-delivery-date' );
