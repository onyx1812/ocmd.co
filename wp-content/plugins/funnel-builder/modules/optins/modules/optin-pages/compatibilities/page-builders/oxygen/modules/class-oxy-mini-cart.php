<?php

class WFACP_OXY_Summary extends WFACP_OXY_HTML_BLOCK {


	public $slug = 'wfacp_checkout_form_summary';
	protected $id = 'wfacp_order_summary_widget';
	protected $get_local_slug = 'order_summary';

	public function __construct() {
		$this->name = __( 'Mini Cart', 'woofunnels-aero-checkout' );
		parent::__construct();

	}

	function name() {
		return $this->name;
	}


	/**
	 * @param $template WFACP_Template_Common;
	 */
	public function setup_data( $template ) {
		$this->mini_cart();
	}


	protected function mini_cart() {
		$tab_id = $this->add_tab( __( 'Heading', 'woofunnels-aero-checkout' ), 5 );
		$this->add_text( $tab_id, 'mini_cart_heading', __( 'Title', 'woofunnels-aero-checkout' ), __( 'Order Summary', 'woofunnels-aero-checkout' ) );
		$this->add_typography( $tab_id, 'mini_cart_section_typo', '.wfacp_mini_cart_start_h .wfacp-order-summary-label', __( 'Heading Typography', 'woofunnels-aero-checkout' ) );


		$cart_id = $this->add_tab( __( 'Cart', 'woocommerce' ), 2 );
		$this->add_switcher( $cart_id, 'enable_product_image', __( 'Image', 'woofunnels-aero-checkout' ), 'on' );
		$this->add_switcher( $cart_id, 'enable_quantity_box', __( 'Quantity Switcher', 'woofunnels-aero-checkout' ), 'off' );
		$this->add_switcher( $cart_id, 'enable_delete_item', __( 'Allow Deletion', 'woofunnels-aero-checkout' ), 'off' );

		$mini_cart_product_typo = [
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_items',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_items .product-total',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_items .product-total bdi',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_items .product-total span:not(.wfacp_cart_product_name_h):not(.wfacp_delete_item_wrap)',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_items .product-total small',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_items dl',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_items dt',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_items dd',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_items dd p',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container tr.cart_item td .product-name',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container tr.cart_item td',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container tr.cart_item td small',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container span.subscription-details',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container tr.cart_item td p',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container tr.cart_item td .product-name span:not(.subscription-details)',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container tr.cart_item td .product-name',

		];
		$this->add_typography( $cart_id, 'mini_cart_product_typo', implode( ',', $mini_cart_product_typo ), __( 'Product Typography', 'wooofunels-aero-checkout' ) );
		$this->add_border_color( $cart_id, 'mini_cart_product_image_border_color', '.wfacp_mini_cart_start_h .wfacp_order_sum .product-image', '', __( 'Image Border Color', 'woofunnel-aero-checkout' ) );


		$mini_cart_product_meta_typo = [
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_reviews',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_reviews tr:not(.order-total)',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_reviews tr:not(.order-total) td',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_reviews tr:not(.order-total) th',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_reviews tr:not(.order-total) td span',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_reviews tr:not(.order-total) td span bdi',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_reviews tr:not(.order-total) td small',
			'.wfacp_mini_cart_start_h span.wfacp_coupon_code',
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_reviews tr:not(.order-total) td span.amount',
		];
		$mini_cart_product_meta_typo = implode( ',', $mini_cart_product_meta_typo );
		$this->add_typography( $cart_id, 'mini_cart_product_meta_typo', $mini_cart_product_meta_typo, __( 'Subtotal Typography', 'woofunnels-aero-checkout' ) );

		$mini_cart_total_typo = [
			'.wfacp_mini_cart_start_h .wfacp_order_summary_container table.wfacp_mini_cart_reviews tr.order-total td span.woocommerce-Price-amount.amount',
			'.wfacp_mini_cart_start_h table.shop_table tr.order-total td',
			'.wfacp_mini_cart_start_h table.shop_table tr.order-total th',
			'.wfacp_divi_forms .wfacp_wrapper_start.wfacp_mini_cart_start_h table.shop_table tr.order-total th',
			'.wfacp_mini_cart_start_h table.shop_table tr.order-total td span',
			'.wfacp_mini_cart_start_h table.shop_table tr.order-total td span bdi',
			'.wfacp_mini_cart_start_h table.shop_table tr.order-total td small'
		];

		$mini_cart_total_typo = implode( ', ', $mini_cart_total_typo );
		$this->add_typography( $cart_id, 'mini_cart_total_typo', $mini_cart_total_typo, __( 'Cart Total Typography', 'woofunnels-aero-checkout' ) );

		$this->add_heading( $cart_id, __( 'Divider', 'woocommerce' ) );

		$border_color = [
			'.wfacp_mini_cart_start_h .wfacp_mini_cart_divi .cart_item',
			'.wfacp_mini_cart_start_h table.shop_table tr.cart-subtotal',
			'.wfacp_mini_cart_start_h table.shop_table tr.order-total',
			'.wfacp_mini_cart_start_h table.shop_table tr.wfacp_ps_error_state td',
			'.wfacp_wrapper_start.wfacp_mini_cart_start_h .wfacp-coupon-section .wfacp-coupon-page',
			'.wfacp_wrapper_start.wfacp_mini_cart_start_h .wfacp_mini_cart_elementor .cart_item',
			'.wfacp_mini_cart_start_h .wfacp-coupon-section .wfacp-coupon-page',
		];
		$this->add_border_color( $cart_id, 'mini_cart_divider_color', implode( ',', $border_color ), '', __( 'Color', 'woofunnel-aero-checkout' ) );


		$enable_coupon             = [
			'enable_coupon' => 'on'
		];
		$enable_coupon_collapsible = [
			'enable_coupon_collapsible' => 'on',
			'enable_coupon'             => 'on'
		];
		$coupon_tab_id             = $this->add_tab( __( 'Coupon', 'woofunnels-aero-checkout' ), 5 );
		$this->add_switcher( $coupon_tab_id, 'enable_coupon', __( 'Enable Coupon', 'woofunnels-aero-checkout' ), 'off' );
		$this->add_switcher( $coupon_tab_id, 'enable_coupon_collapsible', __( 'Collapsible Coupon', 'woofunnels-aero-checkout' ), 'off', [ 'enable_coupon' => 'on' ] );


		$this->add_typography( $coupon_tab_id, 'mini_cart_coupon_heading_typo', '.wfacp_mini_cart_start_h .wfacp-coupon-section .wfacp-coupon-page .wfacp_main_showcoupon', __( 'Link Typography' ), '', $enable_coupon_collapsible );


		$this->add_typography( $coupon_tab_id, 'wfacp_form_mini_cart_coupon_label_typo', '.wfacp_mini_cart_start_h form.checkout_coupon.woocommerce-form-coupon p:not(.wfacp-anim-wrap) .wfacp-form-control-label', __( 'Label Typography', 'woofunnels-aero-checkout' ) );
		$this->add_typography( $coupon_tab_id, 'wfacp_form_mini_cart_coupon_input_typo', '.wfacp_mini_cart_start_h form.checkout_coupon.woocommerce-form-coupon .wfacp-form-control', __( 'Coupon Field Typography' ) );
		$this->add_border_color( $coupon_tab_id, 'wfacp_form_mini_cart_coupon_focus_color', '.wfacp_mini_cart_start_h form.checkout_coupon.woocommerce-form-coupon .wfacp-form-control:focus', '#61bdf7', __( 'Focus Color', 'woofunnel-aero-checkout' ), false, $enable_coupon );
		$this->add_border( $coupon_tab_id, 'wfacp_form_mini_cart_coupon_border', '.wfacp_mini_cart_start_h form.checkout_coupon.woocommerce-form-coupon .wfacp-form-control', __( 'Coupon Field Border' ), $enable_coupon );


		/* Button color setting */

		$this->add_background_color( $coupon_tab_id, 'mini_cart_coupon_btn_color', '.wfacp_mini_cart_start_h button.wfacp-coupon-btn', '#000000', __( 'Button Background', 'woofunnels-aero-checkout' ) );
		$this->add_color( $coupon_tab_id, 'mini_cart_coupon_btn_lable_color', '.wfacp_mini_cart_start_h button.wfacp-coupon-btn', __( 'Button Label Color', 'woofunnels-aero-checkout' ) );

		$this->add_background_color( $coupon_tab_id, 'mini_cart_coupon_btn_lable_hover_color', '.wfacp_mini_cart_start_h button.wfacp-coupon-btn:hover', __( 'Button Hover Background', 'woofunnels-aero-checkout' ) );
		$this->add_color( $coupon_tab_id, 'mini_cart_coupon_btn_hover_label_color', '.wfacp_mini_cart_start_h button.wfacp-coupon-btn:hover', __( 'Button Label Hover Color', 'woofunnels-aero-checkout' ) );


		$this->add_typography( $coupon_tab_id, 'wfacp_form_mini_cart_coupon_button_typo', '.wfacp_mini_cart_start_h button.wfacp-coupon-btn', __( 'Button Typography' ) );


		$this->add_color( $coupon_tab_id, 'default_mini_cart_link_color', '.wfacp_mini_cart_start_h a', __( 'Form Link Color', 'woofunnels-aero-checkout' ), '#dd7575' );
		$this->add_color( $coupon_tab_id, 'default_mini_cart_link_hover_color', '.wfacp_mini_cart_start_h a:hover', __( 'Form Link HOver Color', 'woofunnels-aero-checkout' ) );
	}


	public function html( $setting, $defaults, $content ) {

		$template = wfacp_template();
		if ( is_null( $template ) ) {
			return '';
		}
		$key     = 'wfacp_mini_cart_widgets';
		$widgets = WFACP_Common::get_session( $key );
		if ( ! in_array( $key, $widgets ) ) {
			$widgets[] = $this->get_id();
		}
		WFACP_Common::set_session( $key, $widgets );
		$template->get_mini_cart_widget( $this->get_id() );
	}


}

new WFACP_OXY_Summary;