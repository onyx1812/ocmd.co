<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WFOB_Public class
 */
class WFOB_Public {

	private static $ins = null;
	public $posted_data = [];
	private $is_footer_loaded = false;

	protected function __construct() {
		add_action( 'wp_loaded', [ $this, 'make_cart_empty' ], 99 );
		add_action( 'wp', [ $this, 'attach_hooks' ] );
		add_action( 'woocommerce_before_calculate_totals', [ $this, 'calculate_totals' ] );
		add_action( 'woocommerce_cart_loaded_from_session', [ $this, 'calculate_totals' ], 2 );
		add_action( 'wp', [ $this, 'reset_wc_session' ] );
		add_action( 'wfob_order_bump_fragments', [ $this, 'setup_data_for_ajx' ] );
		add_action( 'wfacp_get_fragments', [ $this, 'setup_for_wfacp_action' ], 10, 2 );
		add_action( 'wfob_after_add_to_cart', [ $this, 'setup_order_bumps' ] );
		add_action( 'wfob_before_add_to_cart', [ $this, 'setup_order_bumps' ] );
		// for first we display bump in payment.php and leve div container for further fragment of orderbump
		add_action( 'woocommerce_before_template_part', [ $this, 'add_order_bump' ], 10, 2 );
		add_filter( 'woocommerce_update_order_review_fragments', [ $this, 'send_cart_total_fragment' ], 12 );
		add_filter( 'wfacp_skip_global_switcher_item', [ $this, 'wfacp_skip_global_switcher_item' ], 10, 2 );
		add_filter( 'woocommerce_cart_item_quantity', [ $this, 'remove_quantity_selector_from_cart' ], 10, 3 );
		add_action( 'wp_head', [ $this, 'execute_bump_action' ], 20 );
		add_action( 'woocommerce_add_to_cart_sold_individually_found_in_cart', [ $this, 'restrict_sold_individual' ], 10, 2 );
		add_action( 'woocommerce_before_checkout_process', [ $this, 'capture_posted_data' ] );
		add_filter( 'wfacp_display_quantity_increment', [ $this, 'do_not_display_order_bump_quantity' ], 10, 2 );
		add_action( 'woocommerce_checkout_before_customer_details', [ $this, 'add_input_hidden' ] );

		$this->execute_bump_fragments();
	}

	public function add_input_hidden() {
		?>
        <input type="hidden" name="wfob_input_hidden_data" id="wfob_input_hidden_data">
		<?php
	}

	public static function get_instance() {
		if ( null == self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	public function attach_hooks() {
		if ( apply_filters( 'wfob_skip_order_bump', false, $this ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
		add_filter( 'wfob_add_quik_links', [ $this, 'add_bump_setting_link' ], 15, 2 );
		add_action( 'woocommerce_checkout_update_order_review', array( $this, 'get_cart_choosed_gateway' ), - 1 );
		add_action( 'woocommerce_after_calculate_totals', [ $this, 'setup_order_bumps' ], 999 );

		add_action( 'wfob_before_add_to_cart', [ $this, 'wfob_before_add_to_cart' ] );
		add_action( 'wfob_after_add_to_cart', [ $this, 'wfob_after_add_to_cart' ] );

		if ( class_exists( 'WFACP_Core' ) ) {

			add_action( 'wfacp_after_checkout_page_found', [ $this, 'wfacp_hooks' ] );

		}
		add_filter( 'wp_footer', [ $this, 'footer' ], 9, 2 );


	}

	/**
	 * @param $ins WC_Cart
	 */
	public function calculate_totals( $ins ) {

		if ( apply_filters( 'wfob_disabled_discounting', false, $this ) ) {
			return;
		}
		$cart_content = $ins->get_cart_contents();
		if ( count( $cart_content ) > 0 ) {
			foreach ( $cart_content as $key => $item ) {
				$item                       = $this->modify_calculate_price_per_session( $item, $key );
				$item                       = apply_filters( 'wfob_after_discount_added_to_item', $item, $key );
				$ins->cart_contents[ $key ] = $item;
			}
		}
	}


	/**
	 * Apply discount on basis of input for product raw prices
	 *
	 * @param $item WC_cart;
	 *
	 * @return mixed
	 */

	public function modify_calculate_price_per_session( $item, $key ) {

		if ( ! isset( $item['_wfob_product'] ) ) {
			return $item;
		}

		/**
		 * @var $product WC_product;
		 */
		$product  = $item['data'];
		$raw_data = $product->get_data();
		$raw_data = apply_filters( 'wfob_product_raw_data', $raw_data, $product, $key );

		$regular_price   = apply_filters( 'wfob_discount_regular_price_data', $raw_data['regular_price'], $key );
		$price           = apply_filters( 'wfob_discount_price_data', $raw_data['price'], $key );
		$discount_amount = apply_filters( 'wfob_discount_amount_data', $item['_wfob_options']['discount_amount'], $item['_wfob_options']['discount_type'], $key );
		$discount_data   = [
			'wfob_product_rp'      => $regular_price,
			'wfob_product_p'       => $price,
			'wfob_discount_amount' => $discount_amount,
			'wfob_discount_type'   => $item['_wfob_options']['discount_type'],
		];
		$new_price       = WFOB_Common::calculate_discount( $discount_data );
		if ( is_null( $new_price ) ) {
			return $item;
		} else {
			$item['data']->set_regular_price( $regular_price );
			$item['data']->set_price( $new_price );
			$item['data']->set_sale_price( $new_price );
		}

		return $item;
	}

	public function setup_data_for_ajx( $data ) {

		if ( ! is_array( $data ) || ! isset( $data['post_data'] ) ) {
			return;
		}
		$this->get_cart_choosed_gateway( $data['post_data'] );
		$this->setup_order_bumps( $data['post_data'] );
	}

	/**
	 * @param string $return
	 *
	 * @return bool|string
	 * Function to setup all the gifts available for the cart
	 */
	public function get_cart_choosed_gateway( $return = '' ) {
		global $woocommerce;

		$arr = array();

		wp_parse_str( $return, $arr );

		if ( isset( $arr['_wfacp_post_id'] ) && empty( $this->posted_data ) ) {
			$this->posted_data = $arr;
		}
		if ( is_array( $arr ) && isset( $arr['payment_method'] ) && ! empty( $arr['payment_method'] ) ) {
			WC()->session->set( 'wfob_payment_method', $arr['payment_method'] );
		}
		if ( is_array( $arr ) && isset( $arr['billing_country'] ) && ! empty( $arr['billing_country'] ) ) {
			WC()->session->set( 'wfob_billing_country', $arr['billing_country'] );
		}
		if ( is_array( $arr ) && isset( $arr['shipping_country'] ) && ! empty( $arr['shipping_country'] ) ) {
			WC()->session->set( 'wfob_shipping_country', $arr['shipping_country'] );
		}

		if ( is_array( $arr ) && isset( $arr['shipping_method'] ) && ! empty( $arr['shipping_method'] ) ) {
			WC()->session->set( 'wfob_shipping_method', $arr['shipping_method'] );
		}
	}

	public function make_cart_empty() {
		// make cart empty when bump product present in cart
		if ( $this->all_item_is_bump_in_cart() ) {
			$this->empty_cart();

			return;
		}

	}

	private function empty_cart() {
		if ( apply_filters( 'wfob_do_not_make_empty_cart', true, $this ) ) {
			WC()->cart->empty_cart();
		}
	}


	public function setup_order_bumps( $postdata ) {
		if ( ! wp_doing_ajax() && false == $this->show_on_load() ) {
			return;
		}
		if ( is_customize_preview() ) {
			return;
		}
		// make cart empty when bump product present in cart
		if ( $this->all_item_is_bump_in_cart() ) {
			$this->empty_cart();

			return;
		}

		if ( isset( $_POST['post_data'] ) && empty( $this->posted_data ) ) {
			$postdata  = $_POST['post_data'];
			$post_data = [];
			parse_str( $postdata, $post_data );
			if ( isset( $post_data['_wfacp_post_id'] ) ) {
				$this->posted_data = $post_data;
			}
		}

		$wfob_transient_obj = WooFunnels_Transient::get_instance();
		$wfob_cache_obj     = WooFunnels_Cache::get_instance();

		$key = 'wfob_instances';

		if ( defined( 'ICL_LANGUAGE_CODE' ) && ICL_LANGUAGE_CODE !== '' && function_exists( 'wpml_get_current_language' ) ) {
			$key .= '_' . wpml_get_current_language();

		}
		$bumps_from_base = apply_filters( 'wfob_bumps_from_external_base', false, $this->posted_data );
		if ( false === $bumps_from_base ) {
			$contents = array();
			do_action( 'wfob_before_query' );
			/**
			 * Setting xl cache and transient for NextMove page query
			 */
			$cache_data = $wfob_cache_obj->get_cache( $key, WFOB_SLUG );

			if ( false !== $cache_data ) {
				$contents = $cache_data;
			} else {
				$transient_data = $wfob_transient_obj->get_transient( $key, WFOB_SLUG );

				if ( false !== $transient_data ) {
					$contents = $transient_data;
				} else {
					$args = array(
						'post_type'        => WFOB_Common::get_bump_post_type_slug(),
						'post_status'      => 'publish',
						'nopaging'         => true,
						'order'            => 'ASC',
						'orderby'          => 'menu_order',
						'fields'           => 'ids',
						'suppress_filters' => false,
					);
					$args = apply_filters( 'wfob_add_control_meta_query', $args );

					$query_result = new WP_Query( $args );
					if ( $query_result instanceof WP_Query && $query_result->have_posts() ) {
						$contents = $query_result->posts;
						$wfob_transient_obj->set_transient( $key, $contents, 21600, WFOB_SLUG );
					}
				}
				$wfob_cache_obj->set_cache( $key, $contents, WFOB_SLUG );
			}
			do_action( 'wfob_after_query' );

		} else {
			$contents = $bumps_from_base;
		}

		$passed_rules = [];
		$failed_rules = [];
		if ( is_array( $contents ) && count( $contents ) > 0 ) {
			foreach ( $contents as $content_single ) {
				/**
				 * post instance extra checking added as some plugins may modify wp_query args on pre_get_posts filter hook
				 */
				$content_id = ( $content_single instanceof WP_Post && is_object( $content_single ) ) ? $content_single->ID : $content_single;
				if ( WFOB_Core()->rules->match_groups( $content_id ) ) {
					$passed_rules[] = $content_id;
				} else {
					$failed_rules[] = $content_id;
				}
			}
		}

		$add_to_cart_bump = WC()->session->get( 'wfob_added_bump_product', [] );
		if ( ! empty( $failed_rules ) ) {

			if ( ! did_action( 'woocommerce_before_checkout_process' ) ) {
				foreach ( $failed_rules as $failed_id ) {
					unset( $add_to_cart_bump[ $failed_id ] );
					$this->remove_items_by_bump_id( $failed_id );
				}
			}
		}

		$final_bumps = array();

		$max_bump         = WFOB_Bump_Fc::maximumn_bump_print();
		$added_final_bump = [];

		$already_added_bump = is_array( $add_to_cart_bump ) ? array_keys( $add_to_cart_bump ) : array();
		if ( ! empty( $already_added_bump ) ) {
			foreach ( $already_added_bump as $cart_bid ) {
				$added_final_bump[ $cart_bid ] = $cart_bid;
				$final_bumps[ $cart_bid ]      = $cart_bid;
			}
		}


		$passed_rules = is_array( $passed_rules ) ? $passed_rules : array();
		if ( ! empty( $passed_rules ) ) {
			foreach ( $passed_rules as $bump_id ) {
				$final_bumps[ $bump_id ] = $bump_id;
			}
		}
		/*if ( $max_bump > 0 && count( $final_bumps ) > 0 && $max_bump == count( $final_bumps ) ) {
			// Display only added bump
			$final_bumps = $added_final_bump;
		}*/
		$final_bumps = apply_filters( 'wfob_filter_final_bumps', $final_bumps, $this->posted_data );;

		foreach ( $final_bumps as $bump_id ) {
			WFOB_Bump_Fc::create( $bump_id );
		}

		return $this;
	}

	public function setup_for_wfacp_action( $wfacp_id, $data ) {

		if ( ! is_array( $data ) || ! isset( $data['post_data'] ) ) {
			return;
		}

		$this->get_cart_choosed_gateway( $data['post_data'] );
		$this->setup_order_bumps( $data['post_data'] );
	}

	public function enqueue() {
		if ( ! is_checkout() ) {
			return;
		}
		wp_enqueue_style( 'photoswipe' );
		wp_enqueue_style( 'photoswipe-default-skin' );
		wp_enqueue_script( 'wc-single-product' );
		wp_enqueue_script( 'zoom' );
		wp_enqueue_script( 'flexslider' );
		wp_enqueue_script( 'photoswipe' );
		wp_enqueue_script( 'photoswipe-ui-default' );
		wp_enqueue_script( 'wp-util' );
		wp_enqueue_script( 'wc-add-to-cart-variation' );


		wp_enqueue_style( 'wfob-style', WFOB_PLUGIN_URL . '/assets/css/public.min.css', false, WFOB_VERSION_DEV );
		wp_enqueue_script( 'wfob-bump', WFOB_PLUGIN_URL . '/assets/js/public.min.js', [ 'jquery' ], WFOB_VERSION_DEV, true );
		wp_localize_script( 'wfob-bump', 'wfob_frontend', [
			'admin_ajax'      => admin_url( 'admin-ajax.php' ),
			'wc_endpoints'    => WFOB_AJAX_Controller::get_public_endpoints(),
			'wfob_nonce'      => wp_create_nonce( 'wfob_secure_key' ),
			'cart_total'      => ! is_null( WC()->cart ) ? WC()->cart->get_total( 'edit' ) : 0,
			'cart_is_virtual' => ! is_null( WC()->cart ) ? WFOB_Common::is_cart_is_virtual() : false,
			'quick_popup'     => [
				'choose_an_option' => __( 'Choose an option', 'woocommerce' ),
				'add_to_cart_text' => __( 'Add to cart', 'woocommerce' ),
				'update'           => __( 'Update', 'woocommerce' ),
			],
		] );

	}


	public function add_bump_setting_link( $links_arr, $id ) {

		$order_bump_link = add_query_arg( [
			'wfob_id' => $id,
			'section' => 'order-bump',
		], admin_url( 'admin.php?page=wfob' ) );

		$links_arr['Order Bump'] = '<span><a href="' . $order_bump_link . '">' . __( 'Order Bump', 'woofunnels-order-bump' ) . '</a></span>';

		return $links_arr;

	}

	public function add_order_bump( $template_name, $template_path ) {
		if ( 'checkout/terms.php' === $template_name ) {
			do_action( 'wfob_below_payment_gateway' );
			$this->woocommerce_checkout_order_review_below_payment_gateway();
			remove_action( 'woocommerce_before_template_part', [ $this, 'add_order_bump' ], 10 );
		}
	}

	public function send_cart_total_fragment( $fragments ) {
		$fragments['.cart_total'] = WC()->cart->get_total( 'edit' );

		return $fragments;
	}

	public function wfob_before_add_to_cart() {
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'split_product_individual_cart_items' ), 10, 1 );
	}

	public function wfob_after_add_to_cart() {
		remove_filter( 'woocommerce_add_cart_item_data', array( $this, 'split_product_individual_cart_items' ), 10 );
	}

	function split_product_individual_cart_items( $cart_item_data ) {
		$cart_item_data['unique_key'] = uniqid();

		return $cart_item_data;
	}

	/**
	 *
	 * @param $item_data WC_Order_Item
	 *
	 * @return WC_Order_Item
	 */
	public function change_item_name( $item_name, $item_data ) {
		if ( ! isset( $item_data['_wfob_product'] ) ) {
			return $item_name;
		}
		$item_name = ( '' !== $item_data['_wfob_options']['title'] ) ? $item_data['_wfob_options']['title'] : $item_name;

		return $item_name;
	}

	public function footer() {
		if ( is_checkout() && false == $this->is_footer_loaded ) {
			$this->is_footer_loaded = true;
			woocommerce_photoswipe();
			include( __DIR__ . '/quick-view/quick-view-container.php' );
		}
	}


	public function woocommerce_template_single_add_to_cart() {
		global $product;
		do_action( 'wfob_woocommerce_' . $product->get_type() . '_add_to_cart' );
	}

	public function woocommerce_variable_add_to_cart() {
		global $product;

		// Enqueue variation scripts.

		// Get Available variations?
		$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

		$available_variations = $get_variations ? $product->get_available_variations() : false;
		$attributes           = $product->get_variation_attributes();
		$selected_attributes  = $product->get_default_attributes();

		include __DIR__ . '/quick-view/add-to-cart/variable.php';
	}


	public function woocommerce_variable_subscription_add_to_cart() {
		global $product;

		// Enqueue variation scripts.

		// Get Available variations?
		$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

		$available_variations = $get_variations ? $product->get_available_variations() : false;
		$attributes           = $product->get_variation_attributes();
		$selected_attributes  = $product->get_default_attributes();

		include __DIR__ . '/quick-view/add-to-cart/variable-subscription.php';
	}

	public function woocommerce_simple_add_to_cart() {
		include __DIR__ . '/quick-view/add-to-cart/simple.php';

	}

	public function woocommerce_subscription_add_to_cart() {
		include __DIR__ . '/quick-view/add-to-cart/subscription.php';
	}

	public function woocommerce_single_variation_add_to_cart_button() {
		include __DIR__ . '/quick-view/add-to-cart/variation-add-to-cart-button.php';
	}

	public function reset_wc_session() {
		if ( ! is_admin() && ! wp_doing_ajax() && ! is_null( WC()->session ) ) {
			WC()->session->set( 'wfob_added_bump_product', [] );
		}
	}

	public function wfacp_hooks() {
		add_action( 'wfacp_header_print_in_head', [ $this, 'footer' ] );
	}

	public function wfacp_skip_global_switcher_item( $status, $cart_item ) {

		if ( isset( $cart_item['_wfob_product'] ) ) {
			$status = true;
		}

		return $status;
	}

	public function remove_quantity_selector_from_cart( $product_quantity, $cart_item_key, $cart_item = [] ) {
		if ( empty( $cart_item ) ) {
			$cart_item = WC()->cart->get_cart_item( $cart_item_key );
		}

		if ( isset( $cart_item['_wfob_product'] ) ) {
			$product_quantity = sprintf( '<input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
		}

		return $product_quantity;
	}


	public function execute_bump_action() {

		$available_position = WFOB_Common::get_bump_position( true );
		if ( ! empty( $available_position ) ) {
			foreach ( $available_position as $pos_key => $position ) {
				$hook        = $position['hook'];
				$priority    = $position['priority'];
				$position_id = $position['id'];
				if ( method_exists( $this, $position_id ) ) {
					add_action( $hook, [ $this, $position_id ], $priority );
				} else {
					do_action( 'wfob_position_not_found', $position_id, $position );
				}
			}

		}
	}


	public function execute_bump_fragments() {


		$available_position = WFOB_Common::get_bump_position( true );
		if ( ! empty( $available_position ) ) {
			foreach ( $available_position as $pos_key => $position ) {
				$priority    = $position['priority'];
				$position_id = $position['id'];
				if ( method_exists( $this, $position_id . "_frg" ) ) {
					add_filter( 'woocommerce_update_order_review_fragments', [ $this, $position_id . "_frg" ], $priority );
				} else {
					do_action( 'wfob_position_fragment_not_found', $position_id, $position );
				}
			}

		}
	}

	private function print_position_bump( $position ) {
		if ( empty( $position ) ) {
			return '';
		}
		if ( apply_filters( 'wfob_do_not_print_bump_position', false, $this ) ) {
			return '';
		}

		ob_start();
		$bumps          = WFOB_Bump_Fc::get_bumps();
		$shown_bump_ids = [];
		WC()->session->set( 'wfob_no_of_bump_shown', [] );
		if ( count( $bumps ) > 0 ) {
			/**
			 * @var $bump WFOB_Bump
			 */
			foreach ( $bumps as $bump_id => $bump ) {
				$shown_bump_ids[] = $bump_id;
				if ( $bump->have_bumps() && $position == $bump->get_position() ) {

					$bump->print_bump();
				}
			}
		}
		WC()->session->set( 'wfob_no_of_bump_shown', $shown_bump_ids );

		return ob_get_clean();
	}

	public function get_bump_html( $fragments, $slug ) {

		if ( apply_filters( 'wfob_do_not_execute_bump_fragments', false, $this ) ) {
			return $fragments;
		}

		$uniqued               = ".wfob_bump_wrapper.{$slug}";
		$html                  = $this->print_position_bump( $slug );
		$html                  = sprintf( "<div class='wfob_bump_wrapper %s' data-time='%s'>%s</div>", $slug, time(), $html );
		$fragments[ $uniqued ] = $html;

		return $fragments;
	}

	public function show_on_load() {
		return apply_filters( 'wfob_show_on_load', false, $this );
	}

	public function woocommerce_before_checkout_form_above_the_form_frg( $fragments ) {
		$slug = 'woocommerce_before_checkout_form_above_the_form';

		return $this->get_bump_html( $fragments, $slug );

	}

	public function woocommerce_checkout_order_review_above_order_summary_frg( $fragments ) {
		$slug = 'woocommerce_checkout_order_review_above_order_summary';

		return $this->get_bump_html( $fragments, $slug );
	}

	public function woocommerce_checkout_order_review_below_order_summary_frg( $fragments ) {
		$slug = 'woocommerce_checkout_order_review_below_order_summary';

		return $this->get_bump_html( $fragments, $slug );
	}

	public function woocommerce_checkout_order_review_above_payment_gateway_frg( $fragments ) {
		$slug = 'woocommerce_checkout_order_review_above_payment_gateway';

		return $this->get_bump_html( $fragments, $slug );
	}

	public function woocommerce_checkout_order_review_below_payment_gateway_frg( $fragments ) {
		$slug = 'woocommerce_checkout_order_review_below_payment_gateway';

		return $this->get_bump_html( $fragments, $slug );
	}


	private function print_placeholder( $slug ) {
		$html = '';
		if ( $this->show_on_load() ) {
			$html = $this->print_position_bump( $slug );
		}
		if ( apply_filters( 'wfob_print_placeholder', true, $slug ) ) {
			printf( "<div class='wfob_bump_wrapper %s'>%s</div>", $slug, $html );

		}

	}


	public function woocommerce_before_checkout_form_above_the_form() {
		$this->print_placeholder( 'woocommerce_before_checkout_form_above_the_form' );
	}

	public function woocommerce_checkout_order_review_above_order_summary() {
		$this->print_placeholder( 'woocommerce_checkout_order_review_above_order_summary' );
	}

	public function woocommerce_checkout_order_review_below_order_summary() {
		$this->print_placeholder( 'woocommerce_checkout_order_review_below_order_summary' );

	}

	public function woocommerce_checkout_order_review_above_payment_gateway() {
		$this->print_placeholder( 'woocommerce_checkout_order_review_above_payment_gateway' );
	}

	public function woocommerce_checkout_order_review_below_payment_gateway() {
		$this->print_placeholder( 'woocommerce_checkout_order_review_below_payment_gateway' );
	}


	public function remove_items_by_bump_id( $bump_id ) {
		if ( apply_filters( 'wfob_remove_items_by_bump_id', true, $bump_id ) ) {
			$items = WC()->cart->get_cart();
			foreach ( $items as $index => $item ) {
				if ( isset( $item['_wfob_options'] ) && $item['_wfob_options']['_wfob_id'] == $bump_id ) {
					WC()->cart->remove_cart_item( $index );
				}
			}
		}
	}

	/**
	 * Make cart empty when all item is bump products
	 * We not proceed to checkout when only bump products present in cart
	 */
	public function all_item_is_bump_in_cart() {
		$status = false;
		if ( ! is_null( WC()->cart ) ) {
			$cart = WC()->cart->get_cart();
			if ( ! empty( $cart ) && count( $cart ) == 1 ) {
				foreach ( $cart as $item ) {
					if ( isset( $item['_wfob_options'] ) ) {
						$status = true;
					}
				}
			}
		}

		return apply_filters( 'wfob_allow_order_bump_item_as_last_item', $status, $this );
	}

	public function restrict_sold_individual( $status, $product_id ) {
		if ( class_exists( 'WFACP_Core' ) ) {
			return $status;
		}


		$cart_content = WC()->cart->get_cart_contents();
		if ( ! empty( $cart_content ) ) {
			foreach ( $cart_content as $item_key => $item_data ) {
				if ( $item_data['product_id'] == $product_id ) {
					$status = true;
					break;
				}
			}
		}

		return $status;
	}

	public function capture_posted_data() {
		if ( isset( $_REQUEST['_wfacp_post_id'] ) ) {
			$this->posted_data = $_REQUEST;
		}
	}

	public function do_not_display_order_bump_quantity( $status, $cart_item ) {
		if ( isset( $cart_item['_wfob_options'] ) ) {
			$status = false;
		}

		return $status;
	}

}

if ( class_exists( 'WFOB_Core' ) ) {
	WFOB_Core::register( 'public', 'WFOB_Public' );
}
