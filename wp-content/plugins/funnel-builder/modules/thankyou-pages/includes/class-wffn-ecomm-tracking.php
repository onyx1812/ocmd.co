<?php

/**
 * This class take care of ecommerce tracking setup
 * It renders necessary javascript code to fire events as well as creates dynamic data for the tracking
 * @author woofunnels.
 */
class WFFN_Ecomm_Tracking {
	private static $ins = null;
	private $data = [];
	private $general_data = [];
	private $admin_general_settings;

	public function __construct() {
		add_action( 'wp_head', array( $this, 'render' ), 90 );
		add_action( 'wp_head', array( $this, 'render_js_to_track_referer' ), 10 );
		add_action( 'wp_footer', array( $this, 'maybe_clear_local_storage_for_tracking_log' ) );
		$this->admin_general_settings = BWF_Admin_General_Settings::get_instance();
	}

	public static function get_instance() {
		if ( self::$ins === null ) {
			self::$ins = new self();
		}

		return self::$ins;
	}

	public function render() {
		if ( $this->should_render() && false === $this->is_purchase_event_pushed() ) {
			$this->maybe_save_order_data( WFFN_Core()->thank_you_pages->data->get_order() );
			$this->render_fb();
			$this->render_ga();
			$this->render_gad();
			$this->render_general_data();
			$this->update_order_event_pushed();
		}
	}

	public function is_purchase_event_pushed() {
		$get_order = WFFN_Core()->thank_you_pages->data->get_order();
		if ( empty( $get_order ) ) {
			return false;
		}

		return ( 'yes' === $get_order->get_meta( '_wffn_ecom_purchase', true ) );
	}

	public function update_order_event_pushed() {
		$get_order = WFFN_Core()->thank_you_pages->data->get_order();
		if ( empty( $get_order ) ) {
			return false;
		}
		$get_order->update_meta_data( '_wffn_ecom_purchase', 'yes' );
		$get_order->save_meta_data();
	}

	/**
	 * render script to load facebook pixel core js
	 */
	public function render_fb() {
		if ( false !== $this->is_fb_pixel() ) {
			$fb_advanced_pixel_data = $this->get_advanced_pixel_data(); ?>
            <!-- Facebook Analytics Script Added By WooFunnels -->
            <script>
                !function (f, b, e, v, n, t, s) {
                    if (f.fbq) return;
                    n = f.fbq = function () {
                        n.callMethod ?
                            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                    };
                    if (!f._fbq) f._fbq = n;
                    n.push = n;
                    n.loaded = !0;
                    n.version = '2.0';
                    n.queue = [];
                    t = b.createElement(e);
                    t.async = !0;
                    t.src = v;
                    s = b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t, s)
                }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
				<?php

				$get_all_fb_pixel = $this->is_fb_pixel();
				$get_each_pixel_id = explode( ',', $get_all_fb_pixel );
				if ( is_array( $get_each_pixel_id ) && count( $get_each_pixel_id ) > 0 ) {
				foreach ( $get_each_pixel_id as $pixel_id ) {
				?>
				<?php if ( true === $this->is_fb_advanced_tracking_on() && count( $fb_advanced_pixel_data ) > 0 ) { ?>
                fbq('init', '<?php echo esc_js( trim( $pixel_id ) ); ?>', <?php echo wp_json_encode( $fb_advanced_pixel_data ); ?>);
				<?php } else { ?>
                fbq('init', '<?php echo esc_js( trim( $pixel_id ) ); ?>');
				<?php } ?>
				<?php
				}
				?>

				<?php esc_js( $this->render_fb_view() ); ?>
				<?php esc_js( $this->maybe_print_fb_script() ); ?>
				<?php
				}
				?>
            </script>
			<?php
		}
	}

	/**
	 * render script to print general data.
	 */
	public function render_general_data() {
		if ( $this->should_render() ) {
			$general_data = $this->general_data;
			if ( is_array( $general_data ) && count( $general_data ) > 0 ) { ?>
                <script type="text/javascript">
                    let wffn_tracking_data = JSON.parse('<?php echo wp_json_encode( $general_data ); ?>');
                </script>
				<?php
				do_action( 'wffn_custom_purchase_tracking', $general_data );
			}
		}
	}


	public function is_fb_pixel() {
		$get_pixel_key = apply_filters( 'bwf_fb_pixel_ids', $this->admin_general_settings->get_option( 'fb_pixel_key' ) );

		return empty( $get_pixel_key ) ? false : $get_pixel_key;
	}

	/**
	 * Decide whether script should render or not
	 * Bases on condition given and based on the action we are in there exists some boolean checks
	 *
	 * @param bool $allow_thank_you whether consider thank you page
	 * @param bool $without_offer render without an valid offer (valid funnel)
	 *
	 * @return bool
	 */
	public function should_render() {

		if ( true === apply_filters( 'wffn_do_not_run_ecomm_purchase_on_thankyou', defined( 'WFOCU_VERSION' ) ) ) {
			return false;
		}
		if ( WFFN_Core()->thank_you_pages->is_wfty_page() ) {
			return true;
		}

		return false;
	}

	public function get_advanced_pixel_data() {
		$data = $this->data;

		if ( ! is_array( $data ) ) {
			return array();
		}

		if ( ! isset( $data['fb'] ) ) {
			return array();
		}

		if ( ! isset( $data['fb']['advanced'] ) ) {
			return array();
		}

		return $data['fb']['advanced'];
	}

	public function is_fb_advanced_tracking_on() {
		$is_fb_advanced_tracking_on = $this->admin_general_settings->get_option( 'is_fb_advanced_event' );
		if ( is_array( $is_fb_advanced_tracking_on ) && count( $is_fb_advanced_tracking_on ) > 0 && 'yes' === $is_fb_advanced_tracking_on[0] ) {
			return true;
		}
	}

	/**
	 * maybe render script to fire fb pixel view event
	 */
	public function render_fb_view() {
		if ( $this->do_track_fb_view() && WFFN_Core()->thank_you_pages->is_wfty_page() ) {
			?>
            fbq('track', 'PageView');
			<?php
		}
	}

	public function do_track_fb_view() {

		return true;
	}

	/**
	 * Maybe print facebook pixel javascript
	 * @see WFFN_Ecomm_Tracking::render_fb();
	 */
	public function maybe_print_fb_script() {
		$data = $this->data; //phpcs:ignore

		include_once WFFN_Core()->thank_you_pages->get_module_path() . 'js-blocks/analytics-fb.phtml'; //phpcs:ignore

		if ( $this->do_track_fb_general_event() ) {
			global $post;

			$thank_you_id           = $post->ID;
			$getEventName           = $this->admin_general_settings->get_option( 'general_event_name' );
			$params                 = array();
			$params['post_type']    = $post->post_type;
			$params['content_name'] = get_the_title( $thank_you_id );
			$params['post_id']      = $thank_you_id;
			?>
            var wffnGeneralData = <?php echo wp_json_encode( $params ); ?>;
            wffnGeneralData = wffnAddTrafficParamsToEvent(wffnGeneralData);
            fbq('trackCustom', '<?php echo esc_js( $getEventName ); ?>', wffnGeneralData);
			<?php
		}
	}

	public function do_track_fb_synced_purchase() {
		$do_track_fb_synced_purchase = $this->admin_general_settings->get_option( 'is_fb_synced_event' );
		if ( is_array( $do_track_fb_synced_purchase ) && count( $do_track_fb_synced_purchase ) > 0 && 'yes' === $do_track_fb_synced_purchase[0] ) {
			return true;
		}

		return false;
	}

	public function do_track_fb_purchase_event() {
		$do_track_fb_purchase_event = $this->admin_general_settings->get_option( 'is_fb_purchase_event' );
		if ( is_array( $do_track_fb_purchase_event ) && count( $do_track_fb_purchase_event ) > 0 && 'yes' === $do_track_fb_purchase_event[0] ) {
			return true;
		}

		return false;
	}

	public function do_track_fb_general_event() {
		$enable_general_event = $this->admin_general_settings->get_option( 'enable_general_event' );
		if ( is_array( $enable_general_event ) && count( $enable_general_event ) > 0 && 'yes' === $enable_general_event[0] ) {
			return true;
		}

		return false;
	}

	/**
	 * render google analytics core script to load framework
	 */
	public function render_ga() {
		$get_tracking_code = $this->ga_code();
		if ( false === $get_tracking_code ) {
			return;
		}

		$get_tracking_code = explode( ",", $get_tracking_code );
		if ( ( $this->do_track_ga_purchase() || $this->do_track_ga_view() ) && false !== $get_tracking_code && $this->should_render() ) {
			?>
            <!-- Google Analytics Script Added By WooFunnels -->
            <script>
                (function (i, s, o, g, r, a, m) {
                    i['GoogleAnalyticsObject'] = r;
                    i[r] = i[r] || function () {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
                    a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a, m)
                })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
				<?php
				$count = false;
				foreach ( $get_tracking_code as $k => $ga_code ) {
					$tracker = ( true === $count ) ? ", 'tracker" . $k . "'" : "";
					echo "ga( 'create', '" . esc_js( trim( $ga_code ) ) . "', 'auto' " . $tracker . " );"; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$count = true;
				}
				?>

				<?php esc_js( $this->maybe_print_ga_script() ); ?>

            </script>

			<?php
		}
	}

	public function ga_code() {
		$get_ga_key = apply_filters( 'bwf_get_ga_key', $this->admin_general_settings->get_option( 'ga_key' ) );

		return empty( $get_ga_key ) ? false : $get_ga_key;
	}

	/**
	 * Maybe print google analytics javascript
	 * @see WFFN_Ecomm_Tracking::render_ga();
	 */
	public function maybe_print_ga_script() {
		$data = $this->data;
		if ( $this->do_track_ga_purchase() && is_array( $data ) && isset( $data['ga'] ) ) {
			include_once WFFN_Core()->thank_you_pages->get_module_path() . 'js-blocks/analytics-ga.phtml'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingNonPHPFile.IncludingNonPHPFile, WordPressVIPMinimum.Files.IncludingFile.UsingCustomFunction
		}
	}

	public function do_track_ga_purchase() {
		$do_track_ga_purchase = $this->admin_general_settings->get_option( 'is_ga_purchase_event' );
		if ( is_array( $do_track_ga_purchase ) && count( $do_track_ga_purchase ) > 0 && 'yes' === $do_track_ga_purchase[0] ) {
			return true;
		}

		return false;
	}


	public function do_track_ga_view() {
		return true;
	}

	/**
	 * render google analytics core script to load framework
	 */
	public function render_gad() {
		$get_tracking_code = $this->gad_code();
		if ( false === $get_tracking_code ) {
			return;
		}

		$get_tracking_code = explode( ",", $get_tracking_code );
		if ( $this->do_track_gad_purchase() && ( false !== $get_tracking_code ) && $this->should_render() ) {
			?>
            <!-- Google Ads Script Added By WooFunnels -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_js( $this->gad_code() ); ?>"></script>   <?php //phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript ?>
            <script>
                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push(arguments);
                }

                gtag('js', new Date());
				<?php
				foreach ( $get_tracking_code as $k => $gad_code ) {
					echo "gtag('config', '" . esc_js( trim( $gad_code ) ) . "');";
					$gad_label = false;
					if ( false !== $this->gad_purchase_label() ) {
						$gad_labels = explode( ",", $this->gad_purchase_label() );
						$gad_label  = isset( $gad_labels[ $k ] ) ? $gad_labels[ $k ] : $gad_labels[0];
					}
					echo esc_js( $this->maybe_print_gad_script( $k, $gad_code, $gad_label ) );
				}

				?>

            </script>
			<?php
		}
	}

	public function gad_code() {
		$get_gad_key = apply_filters( 'bwf_get_gad_key', $this->admin_general_settings->get_option( 'gad_key' ) );

		return empty( $get_gad_key ) ? false : $get_gad_key;
	}


	public function gad_purchase_label() {
		$get_gad_conversion_label = apply_filters( 'bwf_get_conversion_label', $this->admin_general_settings->get_option( 'gad_conversion_label' ) );

		return empty( $get_gad_conversion_label ) ? false : $get_gad_conversion_label;
	}

	/**
	 * Maybe print google analytics javascript
	 * @see WFFN_Ecomm_Tracking::render_ga();
	 */
	public function maybe_print_gad_script( $k, $gad_code, $gad_label ) { //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedParameter
		$data = $this->data;
		if ( $this->do_track_gad_purchase() && is_array( $data ) && isset( $data['gad'] ) ) {
			include_once WFFN_Core()->thank_you_pages->get_module_path() . 'js-blocks/analytics-gad.phtml'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingNonPHPFile.IncludingNonPHPFile,WordPressVIPMinimum.Files.IncludingFile.UsingCustomFunction
		}
	}

	public function do_track_gad_purchase() {
		$do_track_gad_purchase = $this->admin_general_settings->get_option( 'is_gad_purchase_event' );
		if ( is_array( $do_track_gad_purchase ) && count( $do_track_gad_purchase ) > 0 && 'yes' === $do_track_gad_purchase[0] ) {
			return true;
		}

		return false;
	}

	/**
	 * * @hooked over `wp_head`
	 *
	 * That will be further used by WFFN_Ecomm_Tracking::render_fb() && WFFN_Ecomm_Tracking::render_ga()
	 *
	 * @param WC_Order $order
	 *
	 * @return false
	 */
	public function maybe_save_order_data( $order ) {
		if ( $order instanceof WC_Order ) {
			$order = wc_get_order( $order );
		}

		if ( ! $order instanceof WC_Order ) {
			return false;
		}
		$order_id = BWF_WC_Compatibility::get_order_id( $order );
		$this->maybe_save_order_data_general( $order );
		if ( empty( $this->data ) ) {
			$items               = $order->get_items( 'line_item' );
			$content_ids         = [];
			$content_name        = [];
			$category_names      = [];
			$num_qty             = 0;
			$products            = [];
			$google_products     = [];
			$google_ads_products = [];
			$pint_products       = [];
			$billing_email       = BWF_WC_Compatibility::get_order_data( $order, 'billing_email' );
			foreach ( $items as $item ) {
				$pid     = $item->get_product_id();
				$product = wc_get_product( $pid );
				if ( $product instanceof WC_product ) {

					$category       = $product->get_category_ids();
					$content_name[] = $product->get_title();
					$variation_id   = $item->get_variation_id();
					$get_content_id = 0;
					if ( empty( $variation_id ) || ( ! empty( $variation_id ) && true === $this->do_treat_variable_as_simple() ) ) {
						$get_content_id = $content_ids[] = $this->get_woo_product_content_id( $item->get_product_id() );

					} elseif ( false === $this->do_treat_variable_as_simple() ) {

						$get_content_id = $content_ids[] = $this->get_woo_product_content_id( $item->get_variation_id() );

					}
					$category_name = '';

					if ( is_array( $category ) && count( $category ) > 0 ) {
						$category_id = $category[0];
						if ( is_numeric( $category_id ) && $category_id > 0 ) {
							$cat_term = get_term_by( 'id', $category_id, 'product_cat' );
							if ( $cat_term ) {
								$category_name    = $cat_term->name;
								$category_names[] = $category_name;
							}
						}
					}
					$num_qty           += $item->get_quantity();
					$products[]        = array_map( 'html_entity_decode', array(
						'name'       => $product->get_title(),
						'category'   => ( $category_name ),
						'id'         => $get_content_id,
						'quantity'   => $item->get_quantity(),
						'item_price' => $order->get_line_subtotal( $item ),
					) );
					$pint_products[]   = array_map( 'html_entity_decode', array(
						'product_name'     => $product->get_title(),
						'product_category' => ( $category_name ),
						'product_id'       => $get_content_id,
						'product_quantity' => $item->get_quantity(),
						'product_price'    => $order->get_line_subtotal( $item ),
					) );
					$google_products[] = array_map( 'html_entity_decode', array(
						'id'       => $pid,
						'sku'      => empty( $product->get_sku() ) ? $product->get_id() : $product->get_sku(),
						'category' => $category_name,
						'name'     => $product->get_title(),
						'quantity' => $item->get_quantity(),
						'price'    => $order->get_line_subtotal( $item ),
					) );

					$google_ads_products[] = array_map( 'html_entity_decode', array(
						'id'       => $this->gad_product_id( $pid ),
						'sku'      => $product->get_sku(),
						'category' => $category_name,
						'name'     => $product->get_title(),
						'quantity' => $item->get_quantity(),
						'price'    => $order->get_line_subtotal( $item ),
					) );
				}
			}

			$advanced = array();
			/**
			 * Facebook advanced matching
			 */
			if ( $this->is_fb_advanced_tracking_on() ) {

				if ( ! empty( $billing_email ) ) {
					$advanced['em'] = $billing_email;
				}

				$billing_phone = BWF_WC_Compatibility::get_order_data( $order, 'billing_phone' );
				if ( ! empty( $billing_phone ) ) {
					$advanced['ph'] = $billing_phone;
				}

				$shipping_first_name = BWF_WC_Compatibility::get_order_data( $order, 'shipping_first_name' );
				if ( ! empty( $shipping_first_name ) ) {
					$advanced['fn'] = $shipping_first_name;
				}

				$shipping_last_name = BWF_WC_Compatibility::get_order_data( $order, 'shipping_last_name' );
				if ( ! empty( $shipping_last_name ) ) {
					$advanced['ln'] = $shipping_last_name;
				}

				$shipping_city = BWF_WC_Compatibility::get_order_data( $order, 'shipping_city' );
				if ( ! empty( $shipping_city ) ) {
					$advanced['ct'] = $shipping_city;
				}

				$shipping_state = BWF_WC_Compatibility::get_order_data( $order, 'shipping_state' );
				if ( ! empty( $shipping_state ) ) {
					$advanced['st'] = $shipping_state;
				}

				$shipping_postcode = BWF_WC_Compatibility::get_order_data( $order, 'shipping_postcode' );
				if ( ! empty( $shipping_postcode ) ) {
					$advanced['zp'] = $shipping_postcode;
				}
			}
			$this->data = array(
				'fb'   => array(
					'products'       => $products,
					'total'          => $this->get_total_order_value( $order, 'order' ),
					'currency'       => BWF_WC_Compatibility::get_order_currency( $order ),
					'advanced'       => $advanced,
					'content_ids'    => $content_ids,
					'content_name'   => $content_name,
					'category_name'  => array_map( 'html_entity_decode', $category_names ),
					'num_qty'        => $num_qty,
					'additional'     => $this->purchase_custom_aud_params( $order ),
					'transaction_id' => $order_id,

				),
				'pint' => array(
					'order_id' => $order_id,
					'products' => $pint_products,
					'total'    => $this->get_total_order_value( $order, 'order', 'pint' ),
					'currency' => BWF_WC_Compatibility::get_order_currency( $order ),
					'email'    => $billing_email,
				),
				'ga'   => array(
					'products'    => $google_products,
					'transaction' => array(
						'id'          => $order_id,
						'affiliation' => esc_attr( get_bloginfo( 'name' ) ),
						'currency'    => BWF_WC_Compatibility::get_order_currency( $order ),
						'revenue'     => $order->get_total(),
						'shipping'    => BWF_WC_Compatibility::get_order_shipping_total( $order ),
						'tax'         => $order->get_total_tax(),
					),
				),
				'gad'  => array(
					'event_category'   => 'ecommerce',
					'transaction_id'   => (string) $order_id,
					'value'            => $this->get_total_order_value( $order, 'order', 'google' ),
					'currency'         => BWF_WC_Compatibility::get_order_currency( $order ),
					'items'            => $google_ads_products,
					'tax'              => $order->get_total_tax(),
					'shipping'         => BWF_WC_Compatibility::get_order_shipping_total( $order ),
					'ecomm_prodid'     => array_map( array( $this, 'gad_product_id' ), wp_list_pluck( $google_ads_products, 'id' ) ),
					'ecomm_pagetype'   => 'purchase',
					'ecomm_totalvalue' => array_sum( wp_list_pluck( $google_ads_products, 'price' ) ),
				),
			);
		}
		WFFN_Core()->logger->log( "Data tracking successfully saved for order id: $order_id." );
	}

	public function do_treat_variable_as_simple() {
		$do_treat_variable_as_simple = $this->admin_general_settings->get_option( 'content_id_variable' );
		if ( is_array( $do_treat_variable_as_simple ) && count( $do_treat_variable_as_simple ) > 0 && 'yes' === $do_treat_variable_as_simple[0] ) {
			return true;
		}

		return false;
	}

	public function get_woo_product_content_id( $product_id ) {
		$content_id_format = $this->admin_general_settings->get_option( 'content_id_value' );

		if ( $content_id_format === 'product_sku' ) {
			$content_id = get_post_meta( $product_id, '_sku', true );
		} else {
			$content_id = $product_id;
		}

		$prefix = $this->admin_general_settings->get_option( 'content_id_prefix' );
		$suffix = $this->admin_general_settings->get_option( 'content_id_suffix' );

		$value = $prefix . $content_id . $suffix;

		return ( $value );

	}

	public function gad_product_id( $product_id ) {
		$prefix = $this->admin_general_settings->get_option( 'id_prefix_gad' );
		$suffix = $this->admin_general_settings->get_option( 'id_suffix_gad' );

		return $prefix . $product_id . $suffix;
	}

	/**
	 * Get the value of purchase event for the different cases of calculations.
	 *
	 * @param WC_Order/offer_Data $data
	 * @param string $type type for which this function getting called, order|offer
	 *
	 * @return string the modified order value
	 */
	public function get_total_order_value( $data, $type = 'order', $party = 'fb' ) {

		$disable_shipping = $this->is_disable_shipping( $party );
		$disable_taxes    = $this->is_disable_taxes( $party );
		if ( 'order' === $type ) {
			//process order
			if ( ! $disable_taxes && ! $disable_shipping ) {
				//send default total
				$total = $data->get_total();
			} elseif ( ! $disable_taxes && $disable_shipping ) {

				$cart_total     = floatval( $data->get_total( 'edit' ) );
				$shipping_total = floatval( $data->get_shipping_total( 'edit' ) );
				$shipping_tax   = floatval( $data->get_shipping_tax( 'edit' ) );

				$total = $cart_total - $shipping_total - $shipping_tax;
			} elseif ( $disable_taxes && ! $disable_shipping ) {

				$cart_subtotal = $data->get_subtotal();

				$discount_total = floatval( $data->get_discount_total( 'edit' ) );
				$shipping_total = floatval( $data->get_shipping_total( 'edit' ) );

				$total = $cart_subtotal - $discount_total + $shipping_total;
			} else {
				$cart_subtotal = $data->get_subtotal();

				$discount_total = floatval( $data->get_discount_total( 'edit' ) );

				$total = $cart_subtotal - $discount_total;
			}
		} else {
			//process offer
			if ( ! $disable_taxes && ! $disable_shipping ) {

				//send default total
				$total = $data['total'];

			} elseif ( ! $disable_taxes && $disable_shipping ) {
				//total - shipping cost - shipping tax
				$total = $data['total'] - ( isset( $data['shipping']['diff'] ) && isset( $data['shipping']['diff']['cost'] ) ? $data['shipping']['diff']['cost'] : 0 ) - ( isset( $data['shipping']['diff'] ) && isset( $data['shipping']['diff']['tax'] ) ? $data['shipping']['diff']['tax'] : 0 );

			} elseif ( $disable_taxes && ! $disable_shipping ) {
				//total - taxes
				$total = $data['total'] - ( isset( $data['taxes'] ) ? $data['taxes'] : 0 );

			} else {

				//total - taxes - shipping cost
				$total = $data['total'] - ( isset( $data['taxes'] ) ? $data['taxes'] : 0 ) - ( isset( $data['shipping']['diff'] ) && isset( $data['shipping']['diff']['cost'] ) ? $data['shipping']['diff']['cost'] : 0 );

			}
		}
		$total = apply_filters( 'wffn_purchase_ecommerce_pixel_tracking_value', $total, $data );

		return number_format( $total, wc_get_price_decimals(), '.', '' );
	}

	public function is_disable_shipping( $party = 'fb' ) {
		if ( $party === 'fb' ) {
			$exclude_from_total = $this->admin_general_settings->get_option( 'exclude_from_total' );
		} else {
			$exclude_from_total = $this->admin_general_settings->get_option( 'gad_exclude_from_total' );
		}

		if ( is_array( $exclude_from_total ) && count( $exclude_from_total ) > 0 && in_array( 'is_disable_shipping', $exclude_from_total, true ) ) {
			return true;
		}

		return false;

	}

	public function is_disable_taxes( $party = 'fb' ) {
		if ( $party === 'fb' ) {
			$exclude_from_total = $this->admin_general_settings->get_option( 'exclude_from_total' );
		} else {
			$exclude_from_total = $this->admin_general_settings->get_option( 'gad_exclude_from_total' );
		}

		if ( is_array( $exclude_from_total ) && count( $exclude_from_total ) > 0 && in_array( 'is_disable_taxes', $exclude_from_total, true ) ) {
			return true;
		}

		return false;

	}

	/**
	 * @param WC_Order $order
	 *
	 * @return array
	 */
	public function purchase_custom_aud_params( $order ) {
		$params                = array();
		$get_custom_aud_config = $this->admin_general_settings->get_option( 'custom_aud_opt_conf' );
		$add_address           = in_array( 'add_town_s_c', $get_custom_aud_config, true );
		$add_payment_method    = in_array( 'add_payment_method', $get_custom_aud_config, true );
		$add_shipping_method   = in_array( 'add_shipping_method', $get_custom_aud_config, true );
		$add_coupons           = in_array( 'add_coupon', $get_custom_aud_config, true );

		if ( BWF_WC_Compatibility::is_wc_version_gte_3_0() ) {
			// town, state, country
			if ( $add_address ) {
				$params['town']    = $order->get_billing_city();
				$params['state']   = $order->get_billing_state();
				$params['country'] = $order->get_billing_country();
			}
			// payment method
			if ( $add_payment_method ) {
				$params['payment'] = $order->get_payment_method_title();
			}
		} else {
			// town, state, country
			if ( $add_address ) {
				$params['town']    = $order->billing_city;
				$params['state']   = $order->billing_state;
				$params['country'] = $order->billing_country;
			}
			// payment method
			if ( $add_payment_method ) {
				$params['payment'] = $order->payment_method_title;
			}
		}

		// shipping method
		$shipping_methods = $order->get_items( 'shipping' );
		if ( $add_shipping_method && $shipping_methods ) {
			$labels = array();
			foreach ( $shipping_methods as $shipping ) {
				$labels[] = $shipping['name'] ? $shipping['name'] : null;
			}
			$params['shipping'] = implode( ', ', $labels );
		}

		// coupons
		$coupons = $order->get_items( 'coupon' );
		if ( $add_coupons && $coupons ) {

			$labels = array();
			foreach ( $coupons as $coupon ) {
				$labels[] = $coupon['name'] ? $coupon['name'] : null;
			}

			$params['coupon_used'] = 'yes';
			$params['coupon_name'] = implode( ', ', $labels );

		} elseif ( $add_coupons ) {
			$params['coupon_used'] = 'no';
		}

		return $params;

	}

	public function render_js_to_track_referer() {
		?>
        <script>
            var wffnPixelOptions = {};
            wffnPixelOptions.site_url = '<?php echo esc_url( site_url() ); ?>';
            wffnPixelOptions.DotrackTrafficSource = {'fb': false, 'ga': false};
			<?php if('1' === esc_js( wc_string_to_bool( count( $this->admin_general_settings->get_option( 'track_traffic_source' ) ) ) ) ) {
			?>
            wffnPixelOptions.DotrackTrafficSource.fb = true;
			<?php
			}?>

			<?php if('1' === esc_js( wc_string_to_bool( count( $this->admin_general_settings->get_option( 'ga_track_traffic_source' ) ) ) ) ) {
			?>
            wffnPixelOptions.DotrackTrafficSource.ga = true;
			<?php
			}?>

            var wffnUtm_terms = ['utm_source', 'utm_media', 'utm_campaign', 'utm_term', 'utm_content'];
            var wffnCookieManage = {
                'setCookie': function (cname, cvalue, exdays) {
                    var d = new Date();
                    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                    var expires = "expires=" + d.toUTCString();
                    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
                },
                'getCookie': function (cname) {
                    var name = cname + "=";
                    var ca = document.cookie.split(';');
                    for (var i = 0; i < ca.length; i++) {
                        var c = ca[i];
                        while (c.charAt(0) == ' ') {
                            c = c.substring(1);
                        }
                        if (c.indexOf(name) == 0) {
                            return c.substring(name.length, c.length);
                        }
                    }
                    return "";
                },
                'remove': function (cname) {
                    var d = new Date();
                    d.setTime(d.getTime() - (24 * 60 * 60 * 1000));
                    var expires = "expires=" + d.toUTCString();
                    document.cookie = cname + "=" + '' + ";" + expires + ";path=/";

                },

                'commons': {
                    'inArray': function (value, ar) {

                        if (ar.indexOf(value) !== -1) {
                            return false;
                        }
                        return true;
                    }
                }
            };

            /**
             * Return query variables object with where property name is query variable and property value is query variable value.
             */
            function wffnGetQueryVars() {
                try {
                    var result = {}, tmp = [];
                    window.location.search
                        .substr(1)
                        .split("&")
                        .forEach(function (item) {

                            tmp = item.split('=');

                            if (tmp.length > 1) {
                                result[tmp[0]] = tmp[1];
                            }
                        });
                    return result;

                } catch (e) {
                    console.log(e);
                    return {};
                }
            }

            function wffnGetTrafficSource() {
                try {
                    var referrer = document.referrer.toString();
                    var direct = referrer.length === 0;
                    //noinspection JSUnresolvedVariable
                    var internal = direct ? false : referrer.indexOf(wffnPixelOptions.site_url) === 0;
                    var external = !(direct || internal);
                    var cookie = wffnCookieManage.getCookie('wffn_fb_pixel_traffic_source') === '' ? false : wffnCookieManage.getCookie('wffn_fb_pixel_traffic_source');

                    if (external === false) {
                        return cookie ? cookie : 'direct';
                    } else {
                        return cookie && cookie === referrer ? cookie : referrer;
                    }

                } catch (e) {
                    console.log(e);
                    return '';
                }
            }

            function wffnManageCookies() {
                try {
                    var source = wffnGetTrafficSource();
                    if (source !== 'direct') {
                        wffnCookieManage.setCookie('wffn_fb_pixel_traffic_source', source, 2);
                    } else {
                        wffnCookieManage.remove('wffn_fb_pixel_traffic_source');
                    }

                    var queryVars = wffnGetQueryVars();
                    for (var k in wffnUtm_terms) {
                        if (wffnCookieManage.getCookie('wffn_fb_pixel_' + wffnUtm_terms[k]) === '' && queryVars.hasOwnProperty(wffnUtm_terms[k])) {

                            wffnCookieManage.setCookie('wffn_fb_pixel_' + wffnUtm_terms[k], queryVars[wffnUtm_terms[k]], 2);
                        }
                    }
                } catch (e) {
                    console.log(e);
                }
            }

            /**
             * Return UTM terms from request query variables or from cookies.
             */
            function wffnGetUTMs() {
                try {
                    var terms = {};
                    var queryVars = wffnGetQueryVars();
                    for (var k in wffnUtm_terms) {
                        if (wffnCookieManage.getCookie('wffn_fb_pixel_' + wffnUtm_terms[k])) {
                            terms[wffnUtm_terms[k]] = wffnCookieManage.getCookie('wffn_fb_pixel_' + wffnUtm_terms[k]);
                        } else if (queryVars.hasOwnProperty(wffnUtm_terms[k])) {
                            terms[wffnUtm_terms[k]] = queryVars[wffnUtm_terms[k]];
                        }
                    }

                    return terms;

                } catch (e) {
                    console.log(e);
                    return {};
                }
            }

            function wffnAddTrafficParamsToEvent(params, mode) {
                if (typeof mode === "undefined") {
                    mode = 'fb';
                }
                try {
                    var get_generic_params = '<?php echo wp_json_encode( $this->get_generic_event_params() ); ?>';
                    var json_get_generic_params = JSON.parse(get_generic_params);

                    for (var k in json_get_generic_params) {
                        params[k] = json_get_generic_params[k];
                    }

                    /**
                     * getting current day and time to send with this event
                     */
                    var e = new Date;
                    var a = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"][e.getDay()],
                        b = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"][e.getMonth()],
                        c = ["00-01", "01-02", "02-03", "03-04", "04-05", "05-06", "06-07", "07-08", "08-09", "09-10", "10-11", "11-12", "12-13", "13-14", "14-15", "15-16", "16-17", "17-18", "18-19", "19-20", "20-21", "21-22", "22-23", "23-24"][e.getHours()];

                    params.event_month = b;
                    params.event_day = a;
                    params.event_hour = c;

                    //noinspection JSUnresolvedVariable
                    if (false === wffnPixelOptions.DotrackTrafficSource[mode]) {
                        return params;
                    }

                    params.traffic_source = wffnGetTrafficSource();


                    var getUTMs = wffnGetUTMs();

                    for (var k in getUTMs) {
                        if (wffnCookieManage.commons.inArray(getUTMs[k], wffnUtm_terms) >= 0) {
                            params[getUTMs[k]] = getUTMs[k];
                        }
                    }
                    return params;
                } catch (e) {
                    return params;
                }
            }

            wffnManageCookies();
        </script>
		<?php
	}

	/**
	 * Add Generic event params to the data in events
	 * @return array
	 */
	public function get_generic_event_params() {
		$user = wp_get_current_user();
		if ( $user->ID !== 0 ) {
			$user_roles = implode( ',', $user->roles );
		} else {
			$user_roles = 'guest';
		}

		return array(
			'domain'     => substr( get_home_url( null, '', 'http' ), 7 ),
			'user_roles' => $user_roles,
			'plugin'     => 'Funnel Builder',
		);

	}

	/**
	 * @param string $taxonomy Taxonomy name
	 * @param int $post_id (optional) Post ID. Current will be used of not set
	 *
	 * @return string|array List of object terms
	 */
	public function get_object_terms( $taxonomy, $post_id = null, $implode = true ) {

		$post_id = isset( $post_id ) ? $post_id : get_the_ID();
		$terms   = get_the_terms( $post_id, $taxonomy );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return $implode ? '' : array();
		}

		$results = array();

		foreach ( $terms as $term ) {
			$results[] = html_entity_decode( $term->name );
		}

		if ( $implode ) {
			return implode( ', ', $results );
		} else {
			return $results;
		}
	}

	public function get_localstorage_hash() {
		$data = $this->data;
		if ( is_array( $data ) && count( $data ) > 0 ) {
			return md5( wp_json_encode( array( 'key' => WFFN_Core()->data->get_transient_key(), 'data' => $data ) ) );
		}

		return 0;
	}

	/**
	 * We track in localstorage if we pushed ecommerce event for certain data or not
	 * Unfortunately we cannot remove the storage on thank you as user still can press the back button and events will fire again
	 * So the next most logical way to remove the storage is during the next updated checkout action.
	 */
	public function maybe_clear_local_storage_for_tracking_log() {
		if ( is_checkout() ) {
			?>
            <script type="text/javascript">
                if (window.jQuery) {
                    (function ($) {
                        if (!String.prototype.startsWith) {
                            String.prototype.startsWith = function (searchString, position) {
                                position = position || 0;
                                return this.indexOf(searchString, position) === position;
                            };
                        }
                        $(document.body).on('updated_checkout', function () {
                            if (localStorage.length > 0) {
                                var len = localStorage.length;
                                var wffnRemoveLS = [];
                                for (var i = 0; i < len; ++i) {
                                    var storage_key = localStorage.key(i);
                                    if (storage_key.startsWith("wffnH_") === true) {
                                        wffnRemoveLS.push(storage_key);
                                    }
                                }
                                for (var eachLS in wffnRemoveLS) {
                                    localStorage.removeItem(wffnRemoveLS[eachLS]);
                                }
                            }
                        });
                    })(jQuery);
                }
            </script>
			<?php
		}
	}

	/**
	 * * @hooked over `wp_head`
	 *
	 * That will be further used by general rendering
	 *
	 * @param WC_Order $order
	 *
	 * @return false
	 */
	public function maybe_save_order_data_general( $order ) {
		if ( empty( $this->general_data ) ) {
			$items          = $order->get_items( 'line_item' );
			$content_ids    = [];
			$content_name   = [];
			$category_names = [];
			$num_qty        = 0;
			$products       = [];
			$billing_email  = BWF_WC_Compatibility::get_order_data( $order, 'billing_email' );
			$order_id       = BWF_WC_Compatibility::get_order_id( $order );
			foreach ( $items as $item ) {
				$pid     = $item->get_product_id();
				$product = wc_get_product( $pid );
				if ( $product instanceof WC_product ) {

					$category       = $product->get_category_ids();
					$content_name[] = $product->get_title();
					$variation_id   = $item->get_variation_id();
					$get_content_id = 0;
					if ( empty( $variation_id ) || ( ! empty( $variation_id ) && true === $this->do_treat_variable_as_simple() ) ) {
						$get_content_id = $content_ids[] = $this->get_woo_product_content_id( $item->get_product_id() );
					} elseif ( false === $this->do_treat_variable_as_simple() ) {
						$get_content_id = $content_ids[] = $this->get_woo_product_content_id( $item->get_variation_id() );
					}
					$category_name = '';

					if ( is_array( $category ) && count( $category ) > 0 ) {
						$category_id = $category[0];
						if ( is_numeric( $category_id ) && $category_id > 0 ) {
							$cat_term = get_term_by( 'id', $category_id, 'product_cat' );
							if ( $cat_term ) {
								$category_name    = $cat_term->name;
								$category_names[] = $category_name;
							}
						}
					}
					$num_qty    += $item->get_quantity();
					$products[] = array_map( 'html_entity_decode', array(
						'name'       => $product->get_title(),
						'category'   => ( $category_name ),
						'id'         => $get_content_id,
						'pid'        => $pid,
						'sku'        => empty( $product->get_sku() ) ? $pid : $product->get_sku(),
						'quantity'   => $item->get_quantity(),
						'item_price' => $order->get_line_subtotal( $item ),
					) );
				}
			}

			$advanced = array();

			if ( ! empty( $billing_email ) ) {
				$advanced['em'] = $billing_email;
			}

			$billing_phone = BWF_WC_Compatibility::get_order_data( $order, 'billing_phone' );
			if ( ! empty( $billing_phone ) ) {
				$advanced['ph'] = $billing_phone;
			}

			$shipping_first_name = BWF_WC_Compatibility::get_order_data( $order, 'shipping_first_name' );
			if ( ! empty( $shipping_first_name ) ) {
				$advanced['fn'] = $shipping_first_name;
			}

			$shipping_last_name = BWF_WC_Compatibility::get_order_data( $order, 'shipping_last_name' );
			if ( ! empty( $shipping_last_name ) ) {
				$advanced['ln'] = $shipping_last_name;
			}

			$shipping_city = BWF_WC_Compatibility::get_order_data( $order, 'shipping_city' );
			if ( ! empty( $shipping_city ) ) {
				$advanced['ct'] = $shipping_city;
			}

			$shipping_state = BWF_WC_Compatibility::get_order_data( $order, 'shipping_state' );
			if ( ! empty( $shipping_state ) ) {
				$advanced['st'] = $shipping_state;
			}

			$shipping_postcode = BWF_WC_Compatibility::get_order_data( $order, 'shipping_postcode' );
			if ( ! empty( $shipping_postcode ) ) {
				$advanced['zp'] = $shipping_postcode;
			}

			$this->general_data = array(
				'products'         => $products,
				'total'            => $this->get_total_order_value( $order, 'order' ),
				'currency'         => BWF_WC_Compatibility::get_order_currency( $order ),
				'advanced'         => $advanced,
				'content_ids'      => $content_ids,
				'content_name'     => $content_name,
				'category_name'    => array_map( 'html_entity_decode', $category_names ),
				'num_qty'          => $num_qty,
				'additional'       => $this->purchase_custom_aud_params( $order ),
				'transaction_id'   => $order_id,
				'order_id'         => $order_id,
				'email'            => $billing_email,
				'shipping'         => BWF_WC_Compatibility::get_order_shipping_total( $order ),
				'affiliation'      => esc_attr( get_bloginfo( 'name' ) ),
				'ecomm_prodid'     => array_map( array( $this, 'gad_product_id' ), wp_list_pluck( $products, 'id' ) ),
				'ecomm_pagetype'   => 'purchase',
				'ecomm_totalvalue' => array_sum( wp_list_pluck( $products, 'item_price' ) )
			);
		}
		WFFN_Core()->logger->log( "General data tracking successfully saved for order id: $order_id" );
	}
}