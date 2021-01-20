<?php
/**
 * Author PhpStorm.
 */

class WFOB_Shortcodes {
	private static $ins = null;

	public function __construct() {

		$shortcodes = $this->get_shortcodes();

		foreach ( $shortcodes as $shortcode ) {

			add_shortcode( $shortcode, array( $this, $shortcode . '_output' ) );

		}
	}

	public function get_shortcodes() {
		return apply_filters( 'wfob_shortcodes', array(
			'wfob_yes_link',
			'wfob_no_link',
			'wfob_variation_selector_form',
		) );
	}

	public static function get_instance() {
		if ( null == self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	public function wfob_yes_link_output( $atts, $html = '' ) {
		$atts = shortcode_atts( array(
			'key'   => '',
			'class' => '',
		), $atts );

		if ( '' === $atts['key'] ) {
			return __( 'Key is a required parameter in this shortcode', 'woofunnels-order-bump' );
		}

		return sprintf( '<a href="javascript:void(0);" class="%s" data-key="%s">%s</a>', 'wfob_upsell ' . $atts['class'], $atts['key'], do_shortcode( $html ) );
	}

	public function wfob_no_link_output( $atts, $html = '' ) {
		$atts = shortcode_atts( array(
			'key'   => '',
			'class' => '',
		), $atts );

		if ( '' === $atts['key'] ) {
			return __( 'Key is a required parameter in this shortcode', 'woofunnels-order-bump' );
		}

		return sprintf( '<a href="javascript:void(0);" class="%s" data-key="%s">%s</a>', 'wfob_skip_offer ' . $atts['class'], $atts['key'], do_shortcode( $html ) );
	}

	public function wfob_variation_selector_form_output( $atts ) {
		$atts = shortcode_atts( array(
			'key'   => '',
			'label' => __( 'No, thanks', 'woofunnels-order-bump' ),
		), $atts );

		if ( '' === $atts['key'] ) {
			return __( 'Key is a required parameter in this shortcode', 'woofunnels-order-bump' );
		}

		$data = WFOB_Core()->data->get( '_current_offer_data' );
		if ( false === $data ) {
			return '';
		}

		if ( ! isset( $data->products->{$atts['key']} ) ) {
			return '';
		}

		if ( ! isset( $data->products->{$atts['key']}->variations_data ) ) {
			return '';
		}
		$product_raw = array(
			'key'     => $atts['key'],
			'product' => $data->products->{$atts['key']},
		);
		ob_start();
		WFOB_Core()->template_loader->get_template_part( 'product/variation-form', $product_raw );

		return ob_get_clean();
	}


}

if ( class_exists( 'WFOB_Core' ) ) {
	WFOB_Core::register( 'shortcodes', 'WFOB_Shortcodes' );
}
