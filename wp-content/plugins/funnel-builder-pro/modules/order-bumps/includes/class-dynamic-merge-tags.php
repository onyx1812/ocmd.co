<?php

defined( 'ABSPATH' ) || exit;

class WFOB_Product_Switcher_Merge_Tags {

	public static $threshold_to_date = 30;
	/**
	 * @var $pro WC_Product || WC_Product_subscription || WC_Product_Subscription_Variation;
	 */
	protected static $pro;
	protected static $price_data;
	protected static $_data_shortcode = array();
	protected static $cart_item = [];
	protected static $product_data = [];
	protected static $cart_item_key = '';
	protected static $content = '';
	protected static $design_data = '';
	protected static $product_key = '';

	/**
	 * Maybe try and parse content to found the wfacp merge tags
	 * And converts them to the standard wp shortcode way
	 * So that it can be used as do_shortcode in future
	 *
	 * @param string $content
	 *
	 * @return mixed|string
	 */
	public static function maybe_parse_merge_tags( $content, $price_data, $pro = false, $product_data = [], $cart_item = [], $cart_item_key = '', $product_key = '', $design_data = [] ) {

		if ( $pro instanceof WC_Product ) {
			self::$pro = $pro;
		}

		self::$product_data  = $product_data;
		self::$cart_item     = $cart_item;
		self::$cart_item_key = $cart_item_key;
		self::$price_data    = $price_data;
		self::$content       = $content;
		self::$design_data   = $design_data;
		self::$product_key   = $product_key;


		$get_all      = self::get_all_tags();
		$get_all_tags = wp_list_pluck( $get_all, 'tag' );
		//iterating over all the merge tags
		if ( $get_all_tags && is_array( $get_all_tags ) && count( $get_all_tags ) > 0 ) {
			foreach ( $get_all_tags as $tag ) {

				$matches = array();
				$re      = sprintf( '/\{{%s(.*?)\}}/', $tag );
				$str     = $content;

				//trying to find match w.r.t current tag
				preg_match_all( $re, $str, $matches );

				//if match found
				if ( $matches && is_array( $matches ) && count( $matches ) > 0 ) {

					foreach ( $matches[0] as $exact_match ) {

						//preserve old match
						$old_match = $exact_match;

						$single = str_replace( '{{', '', $old_match );
						$single = str_replace( '}}', '', $single );

						if ( method_exists( __CLASS__, $single ) ) {
							$get_parsed_value = call_user_func( array( __CLASS__, $single ) );
							$content          = trim( str_replace( $old_match, $get_parsed_value, $content ) );
						}
					}
				}
			}
		}

		return $content;

	}

	private static function more() {
		$read_more = __( 'more', 'woofunnels-aero-checkout' );
		if ( isset( self::$design_data[ "product_" . self::$product_key . "_read_more" ] ) ) {
			$read_more = self::$design_data[ "product_" . self::$product_key . "_read_more" ];
		}

		return "<a href='#' class='wfob_read_more_link'>" . $read_more . '</a>';
	}

	public static function get_all_tags() {

		$tags = array(
			array(
				'name' => __( 'Subscription Summary', 'woofunnels-aero-checkout' ),
				'tag'  => 'subscription_summary',
			),
			array(
				'name' => __( 'Subscription Summary', 'woofunnels-aero-checkout' ),
				'tag'  => 'product_name',
			),
			array(
				'name' => __( 'You Save', 'woofunnels-aero-checkout' ),
				'tag'  => 'saving_value',
			),
			array(
				'name' => __( 'You Save', 'woofunnels-aero-checkout' ),
				'tag'  => 'saving_percentage',
			),
			array(
				'name' => __( 'Quantity', 'woocommerce' ),
				'tag'  => 'quantity',
			),
			array(
				'name' => __( 'Quantity Incrementer', 'woocommerce' ),
				'tag'  => 'quantity_incrementer',
			),
			array(
				'name' => __( 'Variation Attributes', 'woocommerce' ),
				'tag'  => 'variation_attribute_html',
			),
			array(
				'name' => __( 'Read More', 'woocommerce' ),
				'tag'  => 'more',
			),
			array(
				'name' => __( 'Price', 'woocommerce' ),
				'tag'  => 'price_range',
			),
			array(
				'name' => __( 'Final Price', 'woocommerce' ),
				'tag'  => 'price',
			),
		);

		return $tags;
	}

	public static function saving_value() {
		$difference = floatval( self::$price_data['regular_org'] ) - floatval( self::$price_data['price'] );

		if ( 0 < $difference ) {
			return wc_price( $difference );
		}

		return '';
	}

	public static function saving_percentage() {
		$regular_org = floatval( self::$price_data['regular_org'] );
		$price       = floatval( self::$price_data['price'] );
		$percentage  = 0;

		if ( $regular_org == 0 ) {
			return '';
		}
		// get price of product is zero means 100% off
		if ( $price == 0 ) {
			return 100 . '%';
		}
		if ( $regular_org == $price ) {
			return '';
		}
		if ( $price > $regular_org ) {
			return '';
		}

		$temp_percentage = ( ( ( $price / $regular_org ) * 100 ) );
		if ( $temp_percentage > 0 ) {
			$percentage = 100 - ( ( $price / $regular_org ) * 100 );
		} else {
			return '';
		}

		$t = absint( $percentage );

		if ( ( $percentage / $t ) > 0 ) {
			$percentage = number_format( $percentage, 2 );
		}
		unset( $t );

		return $percentage . '%';
	}

	public static function quantity() {
		return self::$price_data['quantity'];
	}


	public static function subscription_summary() {
		if ( self::$pro instanceof WC_Product_Subscription || self::$pro instanceof WC_Product_Subscription_Variation ) {
			return WFOB_Common::subscription_product_string( self::$pro, self::$price_data, self::$cart_item, self::$cart_item_key );
		} else {
			return '';
		}
	}


	public static function product_name() {
		$product_name = '';
		if ( self::$pro instanceof WC_Product ) {
			if ( '' !== self::$cart_item_key ) {
				$item = WC()->cart->get_cart_item( self::$cart_item_key );
				if ( isset( $item['data'] ) ) {
					$product = $item['data'];
					if ( $product instanceof WC_Product ) {
						$product_name = $product->get_name();
					}
				}
			} else {
				$product_name = self::$pro->get_name();
			}

			return apply_filters( 'wfob_product_name_merge_tags', $product_name, self::$pro, self::$cart_item_key );
		}

		return '';
	}

	public static function quantity_incrementer() {
		if ( self::$pro instanceof WC_Product ) {

			if ( self::$pro->is_sold_individually() ) {
				return '';
			}
			$actual_quantity = 1;
			if ( ! empty( self::$cart_item_key ) ) {
				$actual_quantity = ( self::$cart_item['quantity'] / self::$product_data['quantity'] );
			}

			ob_start();
			?>
            <div class="wfob_quantity q_h">
                <div class="wfob_qty_wrap">
                    <div class="value-button wfob_decrease_item" onclick="numdecreaseItmQty(this,'')" value="Decrease Value"></div>
                    <input type="number" class="wfob_quantity_increment" min="0" value="<?php echo $actual_quantity; ?>">
                    <div class="value-button wfob_increase_item" onclick="numincreaseItmQty(this,'')" value="Increase Value"></div>
                </div>
            </div>
			<?php

			$qty_html = ob_get_clean();

			return $qty_html;

			//return sprintf( '<span class="wfob_bump_qty_wrap"><input type="number" class="wfob_quantity_increment" min="0" value="%d"></span>', $actual_quantity );

		}

		return '';
	}


	/**
	 * @param $cart_item
	 * @param $cart_item_key
	 * @param $pro WC_Product
	 * @param $switcher_settings
	 * @param $product_data
	 *
	 * @return mixed|void
	 */
	public final static function variation_attribute_html() {

		if ( ! self::$pro instanceof WC_Product ) {

			return '';
		}


		$product_data = self::$product_data;

		$pro = self::$pro;
		if ( ! in_array( $pro->get_type(), WFOB_Common::get_variation_product_type() ) ) {
			return;
		}

		$cart_item              = self::$cart_item;
		$is_product_is_variable = ( isset( $product_data['variable'] ) && isset( $product_data['variable'] ) );
		$parent_id              = $pro->get_parent_id();
		$product_obj            = WFOB_Common::wc_get_product( $parent_id, $product_data['item_key'] );
		$variation_attributes   = array_filter( $product_obj->get_attributes(), 'WFOB_Common::filter_variation_attributes' );
		if ( empty( $variation_attributes ) ) {
			return '';
		}


		$item_in_cart            = false;
		$cart_product_attributes = [];
		if ( ! empty( $cart_item ) && isset( $cart_item['data'] ) ) {
			/**
			 * @var $cart_product_object
			 */
			$item_in_cart            = true;
			$cart_product_attributes = $cart_item['variation'];
		}

		$attribute_string  = '';
		$only_attribute    = [];
		$cart_variation_id = 0;
		if ( ! is_null( $cart_item ) ) {
			if ( isset( $cart_item['variation_id'] ) ) {
				$cart_variation_id = $cart_item['variation_id'];
			}
		}

		/**
		 * @var $attribute WC_Product_Attribute
		 */

		foreach ( $variation_attributes as $slug => $attribute ) {

			$only_attribute[] = wc_attribute_label( $attribute->get_name() );
			if ( false == $item_in_cart && true == $is_product_is_variable ) {

				continue;
			}

			$temp_terms = [];
			$terms      = $attribute->get_terms();
			if ( ! is_null( $terms ) ) {
				foreach ( $terms as $term ) {
					$temp_terms[ $term->slug ] = $term->name;
				}
			}
			$attr_value          = ( $is_product_is_variable ) ? __( 'Select', 'woofunnels-aero-checkout' ) : '';
			$temp_slug           = 'attribute_' . $slug;
			$value_not_available = '';
			if ( ! empty( $cart_product_attributes ) && isset( $cart_product_attributes[ $temp_slug ] ) && '' !== $cart_product_attributes[ $temp_slug ] ) {
				$attr_value = $cart_product_attributes[ $temp_slug ];
				if ( isset( $temp_terms[ $attr_value ] ) ) {
					$attr_value = $temp_terms[ $attr_value ];
				}
			} else {
				if ( $is_product_is_variable ) {
					$value_not_available = 'wfob_attr_value_not_available';
				}
			}
			if ( '' !== $attr_value ) {
				$attribute_string .= sprintf( '<div class="wfob_pro_attr_single"><span class="wfob_attribute_id">%s</span><span class="wfob_attributes_sep">: </span><span class="wfob_attribute_value %s">%s</span><span></span></div>', wc_attribute_label( $attribute->get_name() ), $value_not_available, $attr_value );
			}
		}


		if ( '' != $attribute_string && true == $item_in_cart ) {
			return sprintf( '<div class="wfob_selected_attributes">%s</div>', $attribute_string );
		}


		if ( true == $is_product_is_variable && ! empty( $only_attribute ) && WFOB_Common::display_not_selected_attribute( $product_data, $pro ) ) {
			$not_selected = __( 'Select', 'woofunnels-aero-checkout' );
			if ( count( $only_attribute ) > 1 ) {
				$last = end( $only_attribute );
				$size = count( $only_attribute );
				unset( $only_attribute[ $size - 1 ] );
				$not_selected .= ' ' . implode( ', ', $only_attribute ) . ' &amp; ' . $last;
			} else {
				$not_selected .= ' ' . $only_attribute[0];
			}

			$choose_label = sprintf( "<a href='#' class='wfob_qv-button var_product' qv-id='%d' qv-var-id='%d'>%s</a>", $product_data['id'], $cart_variation_id, apply_filters( 'wfob_choose_option_text', $not_selected ) );

			return sprintf( '<div class="wfob_not_selected_attributes">%s</div>', $choose_label );
		} else {
			ob_start();
			do_action( 'wfob_display_not_selected_attribute_placeholder', $only_attribute, $product_data, $pro );

			return ob_get_clean();
		}

	}

	public static function price() {
		$printed_price = '';

		if ( ! self::$pro instanceof WC_Product || empty( self::$price_data ) ) {
			return $printed_price;
		}
		$wc_product = self::$pro;
		$price_data = self::$price_data;
		if ( in_array( $wc_product->get_type(), WFOB_Common::get_subscription_product_type() ) ) {
			$printed_price = wc_price( WFOB_Common::get_subscription_price( $wc_product, $price_data, self::$cart_item_key ) );
		} else {
			if ( $price_data['price'] > 0 && ( absint( $price_data['price'] ) !== absint( $price_data['regular_org'] ) ) ) {
				$printed_price = wc_format_sale_price( $price_data['regular_org'], $price_data['price'] );
			} else {
				$printed_price = wc_price( $price_data['price'] );
			}
		}

		return $printed_price;
	}

	public static function price_range() {
		$printed_price = '';

		if ( ! self::$pro instanceof WC_Product || empty( self::$price_data ) ) {
			return $printed_price;
		}
		$wc_product = self::$pro;
		$price_data = self::$price_data;
		if ( in_array( $wc_product->get_type(), WFOB_Common::get_subscription_product_type() ) ) {
			$printed_price = wc_price( WFOB_Common::get_subscription_price( $wc_product, $price_data, self::$cart_item_key ) );
		} else {
			$printed_price = wc_price( $price_data['price'] );
		}

		return $printed_price;
	}

}

