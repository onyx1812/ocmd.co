<?php

abstract class WFACP_Elementor_HTML_BLOCK extends WFACP_EL_Fields {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
	}


	final protected function render() {
		WFACP_Elementor::set_locals( $this->get_name(), $this->get_id() );
		if ( ! wp_doing_ajax() && is_admin() ) {
			return;
		}
		if ( apply_filters( 'wfacp_print_elementor_widget', true, $this->get_id(), $this ) ) {
			$setting = $this->get_settings();
			$id      = $this->get_id();
			WFACP_Common::set_session( $id, $setting );
			$this->html();
		}
	}

	protected function html() {

	}


	protected function order_summary( $field_key ) {


		$this->add_tab( __( 'Order Summary', 'woofunnel-aero-checkout' ), 2 );
		$this->add_heading( 'Product' );

		$this->add_switcher( 'order_summary_enable_product_image', __( 'Enable Image', 'woofunnels-aero-checkout' ), '', '', "yes", 'yes', [], '', '', 'wfacp_elementor_device_hide' );

		$cart_item_color = [
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody .wfacp_order_summary_item_name',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody .product-name .product-quantity',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody td.product-total',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody .cart_item .product-total span',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody .cart_item .product-total span.amount',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody .cart_item .product-total small',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody .wfacp_order_summary_container dl',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody .wfacp_order_summary_container dd',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody .wfacp_order_summary_container dt',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody .wfacp_order_summary_container p',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody tr span.amount',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody dl',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody dd',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody dt',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody p',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody tr td span:not(.wfacp-pro-count)',
		];

		$this->add_typography( $field_key . '_cart_item_typo', implode( ',', $cart_item_color ) );

		$this->add_color( $field_key . '_cart_item_color', $cart_item_color, '#666666' );


		$this->add_border_color( 'mini_product_image_border_color', [ '{{WRAPPER}} #wfacp-e-form table.shop_table.woocommerce-checkout-review-order-table tr.cart_item .product-image img' ], '', __( 'Image Border Color', 'woofunnel-aero-checkout' ), false, [ 'order_summary_enable_product_image' => 'yes' ] );

		$this->add_heading( __( 'Subtotal', 'woocommerce' ) );


		$cart_subtotal_color_option = [
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-subtotal th',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot .shipping_total_fee td',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-subtotal td',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-subtotal td span.woocommerce-Price-amount.amount',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-subtotal td p',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-subtotal td span',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.shipping_total_fee td span.amount',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.shipping_total_fee td span',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-discount td',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-discount th',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-discount td span',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-discount td span.amount',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-discount td p',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total)',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) td',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) td span',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) td small',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) td a',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) td p',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) th',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) th span',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) th small',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) th a',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) ul',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) ul li',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) ul li label',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) td span.woocommerce-Price-amount.amount',
		];


		$fields_options = [
			'font_weight' => [
				'default' => '400',
			],
		];

		$this->add_typography( 'order_summary_product_meta_typo', implode( ',', $cart_subtotal_color_option ) );
		$this->add_color( 'order_summary_product_meta_color', $cart_subtotal_color_option );

		$cart_total_color_option = [
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total th',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td span.woocommerce-Price-amount.amount',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td p',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td span',

			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td span',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td small',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td a',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td p',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total th',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total th span',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total th small',
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total th a',


		];

		$this->add_heading( 'Total' );
		$this->add_typography( $field_key . '_cart_subtotal_heading_typo', implode( ',', $cart_total_color_option ), $fields_options );
		$this->add_color( $field_key . '_cart_subtotal_heading_color', $cart_total_color_option, '' );

		$this->add_heading( __( 'Divider', 'woocommerce' ) );
		$divider_line_color = [
			'{{WRAPPER}} #wfacp-e-form .wfacp_main_form.woocommerce table.shop_table tbody .wfacp_order_summary_item_name',
			'{{WRAPPER}} #wfacp-e-form table.shop_table.woocommerce-checkout-review-order-table tr.cart_item',
			'{{WRAPPER}} #wfacp-e-form table.shop_table.woocommerce-checkout-review-order-table tr.cart-subtotal',
			'{{WRAPPER}} #wfacp-e-form table.shop_table.woocommerce-checkout-review-order-table tr.order-total',
		];


		$this->add_border_color( $field_key . '_divider_line_color', $divider_line_color, '' );
		$this->end_tab();

	}


	/**
	 * @param $field STring
	 * @param $this \Elementor\Widget_Base
	 */
	protected function generate_html_block( $field_key ) {
		if ( method_exists( $this, $field_key ) ) {
			$this->{$field_key}( $field_key );
		}
	}

}