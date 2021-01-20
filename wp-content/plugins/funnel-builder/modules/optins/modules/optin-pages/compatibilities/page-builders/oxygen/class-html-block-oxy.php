<?php

abstract class WFFN_Optin_OXY_HTML_BLOCK extends WFFN_Optin_OXY_Field {


	public function __construct() {
		parent::__construct();

		//WFACP_OXY::set_locals( $this->get_local_slug(), $this->get_id() );
	}

	public function options() {
		return [ 'rebuild_on_dom_change' => true ];
	}

	final public function render( $setting, $defaults, $content ) {


		if ( apply_filters( 'wffn_optin_print_oxy_widget', true, $this->get_id(), $this ) ) {
			$this->settings = $setting;
			$id             = $this->get_id();
			//WFACP_Common::set_session( $id, $this->settings );
			$this->html( $setting, $defaults, $content );
			if ( isset( $_REQUEST['action'] ) && false !== strpos( $_REQUEST['action'], 'oxy_render' ) ) {
				exit;
			}
		}

	}

	protected function html( $setting, $defaults, $content ) {

	}


	protected function available_html_block() {
		$block = [ 'product_switching', 'order_total' ];

		return apply_filters( 'wffn_optin_html_block_elements', $block );
	}

	public function get_title() {
		return __( 'Checkout Form', 'woofunnels-aero-checkout' );
	}

	protected function order_summary( $field_key ) {


		$tab_id = $this->add_tab( __( 'Order Summary', 'woofunnel-aero-checkout' ), 2 );
		$this->add_heading( $tab_id, 'Product' );

		$this->add_switcher( $tab_id, 'order_summary_enable_product_image', __( 'Enable Image', 'woofunnels-aero-checkout' ), 'on' );

		$cart_item_color = [
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody .wffn_optin_order_summary_item_name',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody .product-name .product-quantity',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody td.product-total',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody .cart_item .product-total span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody .cart_item .product-total span bdi',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody .cart_item .product-total span.amount',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody .cart_item .product-total small',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody .wffn_optin_order_summary_container dl',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody .wffn_optin_order_summary_container dd',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody .wffn_optin_order_summary_container dt',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody .wffn_optin_order_summary_container p',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody tr span.amount',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody dl',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody dd',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody dt',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody p',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody tr td span:not(.wfacp-pro-count)',
		];
		$cart_item_color = implode( ',', $cart_item_color );
		$this->add_typography( $tab_id, $field_key . '_cart_item_typo', $cart_item_color, 'Product Typography' );
		$this->add_border_color( $tab_id, 'mini_product_image_border_color', '#wfacp-e-form table.shop_table.woocommerce-checkout-review-order-table tr.cart_item .product-image img', '', __( 'Image Border Color', 'woofunnel-aero-checkout' ), false, [ 'order_summary_enable_product_image' => 'on' ] );


		$cart_subtotal_color_option = [
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-subtotal th',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot .shipping_total_fee td',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-subtotal td',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-subtotal td span.woocommerce-Price-amount.amount',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-subtotal td p',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-subtotal td span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.shipping_total_fee td span.amount',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.shipping_total_fee td span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.shipping_total_fee td span bdi',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-discount td',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-discount th',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-discount td span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-discount td span bdi',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-discount td span.amount',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.cart-discount td p',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total)',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) td',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) td span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) td span bdi',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) td small',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) td p',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) th',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) th span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) th small',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) ul',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) ul li',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) ul li label',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr:not(.order-total) td span.woocommerce-Price-amount.amount',
		];


		$cart_subtotal_color_option = implode( ',', $cart_subtotal_color_option );
		$font_side_default          = [ 'default' => '14px', 'unit' => 'px' ];
		$this->add_typography( $tab_id, 'order_summary_product_meta_typo', $cart_subtotal_color_option, 'Subtotal Typography', '', [], $font_side_default );

		$cart_total_color_option = [
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total th',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td span.woocommerce-Price-amount.amount',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td p',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td span bdi',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td small',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total td p',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total th',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total th span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table.woocommerce-checkout-review-order-table tfoot tr.order-total th small',
		];


		$cart_total_color_option = implode( ',', $cart_total_color_option );
		$this->add_typography( $tab_id, $field_key . '_cart_subtotal_heading_typo', $cart_total_color_option, 'Total Typography' );

		$this->add_heading( $tab_id, __( 'Divider', 'woocommerce' ) );
		$divider_line_color = [
			'#wfacp-e-form .wffn_optin_main_form.woocommerce table.shop_table tbody .wffn_optin_order_summary_item_name',
			'#wfacp-e-form table.shop_table.woocommerce-checkout-review-order-table tr.cart_item',
			'#wfacp-e-form table.shop_table.woocommerce-checkout-review-order-table tr.cart-subtotal',
			'#wfacp-e-form table.shop_table.woocommerce-checkout-review-order-table tr.order-total',
		];
		$this->add_border_color( $tab_id, $field_key . '_divider_line_color', implode( ',', $divider_line_color ), '' );

	}


	/**
	 * @param $field STring
	 * @param \Elementor\Widget_Base
	 */
	protected function product_switching( $field_key ) {

		$tab_id = $this->add_tab( __( 'Product Switcher', 'woofunnel-aero-checkout' ), 2 );

		/*  Selected Items Setting */

		$this->add_heading( $tab_id, __( 'Selected Items', 'woofunnels-aero-checkout' ) );

		/* Typography  */

		$product_switcher_typo_option = [
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset.wfacp-selected-product .wffn_optin_product_sec .wffn_optin_product_name_inner *',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset.wfacp-selected-product .wffn_optin_product_sec .wffn_optin_product_attributes .wffn_optin_selected_attributes  *',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset.wfacp-selected-product .wffn_optin_quantity_selector input',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset.wfacp-selected-product .wffn_optin_product_price_sec span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset.wfacp-selected-product .wffn_optin_product_price_sec span bdi',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset.wfacp-selected-product .wffn_optin_product_switcher_col_2 .wffn_optin_product_subs_details > span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset.wfacp-selected-product .wffn_optin_product_subs_details span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset.wfacp-selected-product .wffn_optin_product_subs_details *',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset.wfacp-selected-product .wffn_optin_product_sec .wffn_optin_product_select_options .wffn_optin_qv-button',
		];

		$product_switcher_typo_string = implode( ',', $product_switcher_typo_option );

		$this->add_typography( $tab_id, 'selected_item_typography', $product_switcher_typo_string, 'Selected Items Typography' );

		/* Items Color */
		$selector = [
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .woocommerce-cart-form__cart-item.cart_item.wfacp-selected-product .wffn_optin_row_wrap .wffn_optin_product_choosen_label .wffn_optin_product_switcher_item',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .woocommerce-cart-form__cart-item.cart_item.wfacp-selected-product .wffn_optin_row_wrap .wffn_optin_product_choosen_label .wffn_optin_product_row_quantity',
		];

		$this->add_color( $tab_id, $field_key . '_label_color', implode( ',', $selector ), 'Item Color', '' );

		/* Items Price Color */

		$itemPriceColorOpt = [
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .shop_table.wfacp-product-switch-panel .wfacp-selected-product .product-price',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .shop_table.wfacp-product-switch-panel .wfacp-selected-product .product-price span',
		];
		$this->add_color( $tab_id, $field_key . '_price_color', implode( ',', $itemPriceColorOpt ), 'Item Price Color ', "" );

		$variant_color = [
			'#wfacp-e-form .wffn_optin_main_form .wffn_optin_selected_attributes .wffn_optin_pro_attr_single span',
			'#wfacp-e-form .wffn_optin_main_form .wffn_optin_selected_attributes .wffn_optin_pro_attr_single span:last-child',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce #product_switching_field .wffn_optin_product_switcher_col_2 .wffn_optin_product_subs_details',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce #product_switching_field .wffn_optin_product_switcher_col_2 .wffn_optin_product_subs_details span',
		];
		$this->add_color( $tab_id, $field_key . '_variant_color', implode( ',', $variant_color ), 'Variant Color', '#666666' );


		/* Background Color */
		$itemBgColor = '#wfacp-e-form .wffn_optin_main_form.woocommerce .woocommerce-cart-form__cart-item.cart_item.wfacp-selected-product';
		$this->add_background_color( $tab_id, $field_key . '_item_background', $itemBgColor, "", 'Background Color' );


		/* Border selected */

		$fields_options = '#wfacp-e-form .wffn_optin_main_form.woocommerce .woocommerce-cart-form__cart-item.cart_item.wfacp-selected-product';

		$this->add_border( $tab_id, $field_key . '_border', $fields_options, 'Selected Items Border' );


		/* Optional Item Setting */
		$this->add_heading( $tab_id, __( 'Non-selected Items', 'woofunnels-aero-checkout' ) );
		$product_switcher_typo_optional = [
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset:not(.wfacp-selected-product) .wffn_optin_product_sec .wffn_optin_product_name_inner *',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset:not(.wfacp-selected-product) .wffn_optin_product_sec .wffn_optin_product_attributes .wffn_optin_selected_attributes  *',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset:not(.wfacp-selected-product) .wffn_optin_quantity_selector input',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset:not(.wfacp-selected-product) .wffn_optin_product_price_sec span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset:not(.wfacp-selected-product) .wffn_optin_product_price_sec span bdi',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset:not(.wfacp-selected-product) .wffn_optin_product_switcher_col_2 .wffn_optin_product_subs_details > span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset:not(.wfacp-selected-product) .wffn_optin_product_subs_details span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset:not(.wfacp-selected-product) .wffn_optin_product_subs_details *',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset:not(.wfacp-selected-product) .wffn_optin_product_sec .wffn_optin_product_select_options .wffn_optin_qv-button',
		];

		$this->add_typography( $tab_id, $field_key . '_optional_item_typography', implode( ',', $product_switcher_typo_optional ), 'Non-selected Items Typography' );


		/* Label Color Setting */
		$optionalLabelColorOpt = [
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .woocommerce-cart-form__cart-item.cart_item .wffn_optin_row_wrap .wffn_optin_product_choosen_label .wffn_optin_product_switcher_item',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .woocommerce-cart-form__cart-item.cart_item .wffn_optin_row_wrap .wffn_optin_product_choosen_label .wffn_optin_product_row_quantity'
		];

		$this->add_color( $tab_id, $field_key . '_optional_label_color', implode( ',', $optionalLabelColorOpt ), esc_attr__( 'Item Color', 'woofunnels-aero-checkout' ) );


		/* Items Price Color */

		$optional_price_color_option = [
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .shop_table.wfacp-product-switch-panel .product-price',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .shop_table.wfacp-product-switch-panel .wffn_optin_product_price_sec span',
		];
		$this->add_color( $tab_id, $field_key . '_optional_price_color', implode( ',', $optional_price_color_option ), 'Item Price Color' );
		/* Background Color */

		$this->add_background_color( $tab_id, $field_key . '_optional_background', '.woocommerce-cart-form__cart-item.cart_item:not(.wfacp-selected-product)', "#ffffff", 'Background Color' );
		$this->add_background_color( $tab_id, $field_key . '_optional_background_hover', '.wfacp-product-switch-panel .woocommerce-cart-form__cart-item.cart_item:not(.wfacp-selected-product):hover', "#fbfbfb", 'Background Hover Color' );


		/* Non Selected border*/
		$fields_options = '#wfacp-e-form .wffn_optin_main_form.woocommerce .woocommerce-cart-form__cart-item.cart_item:not(.wfacp-selected-product)';
		$this->add_border( $tab_id, $field_key . '_border_non_selected', $fields_options, 'Non-selected Items Border' );


		/* Non Selected border*/

		$typography = [
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-product-switch-panel fieldset .wffn_optin_you_save_text',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_row_wrap .wffn_optin_you_save_text span',
		];

		$this->add_typography( $tab_id, $field_key . '_you_save_typo', implode( ',', $typography ), 'Saving Text Typography' );


		//Best value Controls

		if ( true === WFACP_Common::is_best_value_available() ) {
			$selector = [
				'#wfacp-e-form .wffn_optin_main_form.woocommerce #product_switching_field fieldset .wffn_optin_best_value_container .wffn_optin_best_value',
				'#wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_best_value.wffn_optin_top_left_corner',
				'#wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_best_value.wffn_optin_top_right_corner',
			];
			$selector = implode( ',', $selector );
			/* Best Value: Color Setting */
			$this->add_typography( $tab_id, $field_key . '_best_value_typography', $selector, 'Best Value Typography' );
			$this->add_background_color( $tab_id, $field_key . '_best_value_bg_color', $selector, "#b22323", 'Best Value Background Color' );
			$this->add_border( $tab_id, $field_key . '_best_value_border', $selector, 'Best Value Border' );
		}


		if ( true === WFACP_Common::is_what_included_available() ) {


			$this->add_heading( $tab_id, __( "Custom Product Description", 'woofunnels-aero-checkout' ) );


			/* Section Heading Setting */
			$what_included_heading_opt = '#wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_whats_included h3';

			$this->add_typography( $tab_id, $field_key . '_what_included_heading', $what_included_heading_opt, 'Whats Included Heading typography' );

			/* Product Title Setting */

			$this->add_typography( $tab_id, $field_key . '_what_included_product_title', ' #wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_whats_included .wffn_optin_product_switcher_description h4', 'Whats Included Product title typography' );


			/* Product Description Setting */

			$description_typo = [
				' #wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_whats_included .wffn_optin_product_switcher_description .wffn_optin_description p',
				' #wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_whats_included .wffn_optin_product_switcher_description .wffn_optin_description',
			];
			$description_typo = implode( ',', $description_typo );
			$this->add_typography( $tab_id, $field_key . '_what_included_product_description', $description_typo, 'Whats Included Product Description Typography' );

			$advance_typo = '#wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_whats_included';
			$this->add_background_color( $tab_id, $field_key . '_what_included_bg', $advance_typo, "", 'Whats Included Background Color' );
			$this->add_border( $tab_id, $field_key . '_what_included_border', '#wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_whats_included', 'Whats Included Border' );
			$description = __( 'Note: Add this CSS class <strong>"wffn_optin_for_mb_style"</strong> here if your checkout page width is less than 375px on desktop browser', 'woofunnels-aero-checkout' );
			$this->add_text( $tab_id, 'product_switcher_mobile_style', __( 'Whats Included CSS Class', 'woofunnels-aero-checkout' ), '', [], '', $description );

		}
	}

	/**
	 * @param $field STring
	 * @param $this \Elementor\Widget_Base
	 */
	protected function order_total( $field_key ) {

		$tab_id = $this->add_tab( __( 'Order Total', 'woofunnel-aero-checkout' ), 2 );

		$order_total_text_color_opt = [
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_order_total_field table.wffn_optin_order_total_wrap tr td',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_order_total_field table.wffn_optin_order_total_wrap tr td strong > span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_order_total_field table.wffn_optin_order_total_wrap tr td strong > span bdi',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_order_total_field table.wffn_optin_order_total_wrap tr td strong > span span.woocommerce-Price-currencySymbol',
		];

		$order_total_text_color_opt = implode( ',', $order_total_text_color_opt );
		$this->add_typography( $tab_id, $field_key . '_typography', $order_total_text_color_opt );
		$order_total_bg_color_opt = '#wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_order_total .wffn_optin_order_total_wrap';
		$this->add_background_color( $tab_id, $field_key . '_order_total_bg_color', $order_total_bg_color_opt, "", 'Background Color' );
		$this->add_border( $tab_id, $field_key . '_order_total_border_sec', $order_total_bg_color_opt );

	}


	protected function order_coupon( $field_key ) {

		$tab_id = $this->add_tab( __( 'Coupon', 'woocommerce' ), 2 );
		$this->add_heading( $tab_id, __( 'Link', 'woofunnel-aero-checkout' ), '' );
		$coupon_typography_opt = [
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wfacp-coupon-section .wfacp-coupon-page .woocommerce-info > span',
			'#wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_woocommerce_form_coupon .wfacp-coupon-section .woocommerce-info .wffn_optin_showcoupon',
		];


		$this->add_typography( $tab_id, $field_key . '_coupon_typography', implode( ',', $coupon_typography_opt ), 'Link Typography' );
		$form_fields_label_typo = ' #wfacp-e-form .wffn_optin_main_form .wffn_optin_coupon_field_box p.wfacp-form-control-wrapper:not(.wfacp-anim-wrap) label.wfacp-form-control-label';
		$this->add_typography( $tab_id, $field_key . '_label_typo', $form_fields_label_typo, __( 'Label Typography', 'woofunnels-aero-checkout' ) );
		$fields_options = ' #wfacp-e-form .wffn_optin_main_form .wffn_optin_coupon_field_box p.wfacp-form-control-wrapper .wfacp-form-control';
		$this->add_typography( $tab_id, $field_key . '_input_typo', $fields_options, [], [], __( 'Coupon Field Typography' ) );
		$this->add_border_color( $tab_id, $field_key . '_focus_color', '#wfacp-e-form .wffn_optin_main_form.woocommerce .wffn_optin_coupon_field_box p.wfacp-form-control-wrapper .wfacp-form-control:focus', '#61bdf7', __( 'Focus Color', 'woofunnel-aero-checkout' ), true );
		$this->add_border( $tab_id, $field_key . '_coupon_border', $fields_options, __( 'Coupon Field Border' ) );
		$this->add_heading( $tab_id, __( 'Button Normal', 'woofunnel-aero-checkout' ) );
		/* Button color setting */
		$btnkey       = '#wfacp-e-form .wffn_optin_main_form .wffn_optin_woocommerce_form_coupon .wfacp-coupon-section .wffn_optin_coupon_field_box .wfacp-coupon-field-btn';
		$btnkey_hover = '#wfacp-e-form .wffn_optin_main_form .wffn_optin_woocommerce_form_coupon .wfacp-coupon-section .wffn_optin_coupon_field_box .wfacp-coupon-field-btn:hover';
		$this->add_background_color( $tab_id, $field_key . '_btn_bg_color', $btnkey, '', __( 'Background', 'woofunnels-aero-checkout' ) );
		$this->add_color( $tab_id, $field_key . '_btn_text_color', $btnkey, '', __( 'Label', 'woofunnels-aero-checkout' ) );
		$this->add_heading( $tab_id, __( 'Button Hover', 'woofunnel-aero-checkout' ) );
		$this->add_background_color( $tab_id, $field_key . '_btn_bg_hover_color', $btnkey_hover, __( 'Background', 'woofunnels-aero-checkout' ) );
		$this->add_color( $tab_id, $field_key . '_btn_bg_hover_text_color', $btnkey_hover, __( 'Label', 'woofunnels-aero-checkout' ) );


		$this->add_typography( $tab_id, $field_key . '_btn_typo', $btnkey, __( 'Button Typography' ) );

		/* Button color setting End*/
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

	protected function divider_field() {
		return [
			'wffn_optin_start_divider_billing',
			'wffn_optin_start_divider_shipping',
			'wffn_optin_end_divider_billing',
			'wffn_optin_end_divider_shipping'
		];
	}


}