<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * @var $this WFOB_Bump;
 * @var $wc_product WC_Product;
 */

$cart_item_key       = '';
$cart_item           = [];
$result              = WFOB_Common::get_cart_item_key( $product_key );
$is_variable_product = false;

$parent_id = absint( $data['id'] );
if ( $data['parent_product_id'] && $data['parent_product_id'] > 0 ) {
	$parent_id = absint( $data['parent_product_id'] );

}


if ( ! is_null( $result ) ) {
	$cart_item_key = $result[0];
	$cart_item     = $result[1];
}
$qty               = absint( $data['quantity'] );
$cart_variation_id = 0;
if ( ! empty( $cart_item ) && ! is_null( $cart_item ) ) {
	$qty        = $cart_item['quantity'];
	$wc_product = $cart_item['data'];
	if ( isset( $cart_item['variation_id'] ) ) {
		$cart_variation_id = $cart_item['variation_id'];
	}
} else {
	if ( isset( $data['variable'] ) ) {
		$is_variable_product = true;
		$variation_id        = absint( $data['default_variation'] );
		$wc_product          = WFOB_Common::wc_get_product( $variation_id );
	} else {
		$wc_product = WFOB_Common::wc_get_product( $data['id'] );
	}
}

if ( ! $wc_product instanceof WC_Product ) {
	return;
}

if ( ! $wc_product->is_purchasable() && '' == $cart_item_key ) {
	return '';
}


$wc_product     = WFOB_Common::set_product_price( $wc_product, $data, $cart_item_key );
$design_data    = $this->get_design_data();
$parent_product = WFOB_Common::wc_get_product( $parent_id );
$product_title  = '';
if ( ! isset( $design_data["product_{$product_key}_title"] ) || '' == $design_data["product_{$product_key}_title"] ) {
	$product_title = $wc_product->get_title();
	if ( in_array( $wc_product->get_type(), WFOB_Common::get_variation_product_type() ) ) {
		if ( absint( $data['parent_product_id'] ) > 0 || '' !== $cart_item_key ) {
			$product_title = $wc_product->get_name();
		}
	}
	$product_title = __( "<span style='color:#ff0000'>Yes!</span> Add ", 'woofunnels-order-bump' ) . '{{product_name}}' . __( ' to my order', 'woofunnels-order-bump' );
} else {

	$product_title = $design_data["product_{$product_key}_title"];

}

$default_data = WFOB_Common::get_default_model_data();

$default_data      = $default_data[ $design_data['layout'] ];
$small_description = isset( $design_data["product_{$product_key}_small_description"] ) ? $design_data["product_{$product_key}_small_description"] : $default_data['product_small_description'];
$sub_title         = isset( $design_data["product_{$product_key}_sub_title"] ) ? $design_data["product_{$product_key}_sub_title"] : $default_data['product_small_title'];
$add_btn_text      = isset( $design_data["product_{$product_key}_add_btn_text"] ) ? $design_data["product_{$product_key}_add_btn_text"] : $default_data['product_add_button_text'];
$added_btn_text    = isset( $design_data["product_{$product_key}_added_btn_text"] ) ? $design_data["product_{$product_key}_added_btn_text"] : $default_data['product_added_button_text'];
$remove_btn_text   = isset( $design_data["product_{$product_key}_remove_btn_text"] ) ? $design_data["product_{$product_key}_remove_btn_text"] : $default_data['product_remove_button_text'];
$description       = isset( $design_data["product_{$product_key}_description"] ) ? $design_data["product_{$product_key}_description"] : $default_data['product_description'];


$featured_image = true;
if ( ! isset( $design_data["product_{$product_key}_featured_image"] ) || '' == $design_data["product_{$product_key}_featured_image"] ) {
	$featured_image = true;
} else {
	$featured_image = $design_data["product_{$product_key}_featured_image"];
}


$price_data = apply_filters( 'wfob_product_switcher_price_data', [], $wc_product );
if ( empty( $price_data ) ) {
	$price_data['regular_org'] = $wc_product->get_regular_price( 'edit' );
	$price_data['price']       = $wc_product->get_price( 'edit' );
}

$price_data['regular_org'] *= $qty;
$price_data['price']       *= $qty;
$price_data['quantity']    = $qty;

$enable_price = true;
if ( isset( $design_data['enable_price'] ) ) {
	if ( '0' === $design_data['enable_price'] ) {
		$enable_price = false;
	}
}


$variation_attributes = [];
$product_attributes   = [];
if ( ! is_null( $cart_item ) && isset( $cart_item['variation_id'] ) ) {
	if ( is_array( $cart_item['variation'] ) && count( $cart_item['variation'] ) ) {
		$product_attributes = $cart_item['variation'];
	} elseif ( 'variation' == $cart_item['data']->get_type() ) {
		$product_attributes = $cart_item['data']->get_attributes();
	}
} elseif ( 'variation' == $wc_product->get_type() ) {
	$product_attributes = $wc_product->get_attributes();
}

$variable_checkbox = '';
if ( isset( $data['variable'] ) && $cart_variation_id == 0 ) {
	$variable_checkbox = 'wfob_choose_variation';
}

$enable_pointer = '';


if ( '' !== $cart_item_key ) {
	$price_data = WFOB_Common::get_cart_product_price_data( $wc_product, $cart_item, $cart_item['quantity'] );
} else {
	$price_data             = WFOB_Common::get_product_price_data( $wc_product, $price_data );
	$price_data['quantity'] = $qty;
}

$printed_price = '';

if ( in_array( $wc_product->get_type(), WFOB_Common::get_subscription_product_type() ) ) {
	$printed_price = wc_price( WFOB_Common::get_subscription_price( $wc_product, $price_data, $cart_item_key ) );
} else {
	if ( $price_data['price'] > 0 && ( absint( $price_data['price'] ) !== absint( $price_data['regular_org'] ) ) ) {
		$printed_price = wc_format_sale_price( $price_data['regular_org'], $price_data['price'] );
	} else {
		$printed_price = wc_price( $price_data['price'] );
	}
}

$description_display_none = '';
if ( false !== strpos( $product_title, '{{more}}' ) || false !== strpos( $sub_title, '{{more}}' ) || false !== strpos( $small_description, '{{more}}' ) ) {
	$description_display_none = 'display:none';
}

$product_title     = WFOB_Common::decode_merge_tags( $product_title, $price_data, $wc_product, $data, $cart_item, $cart_item_key, $product_key, $design_data );
$sub_title         = WFOB_Common::decode_merge_tags( $sub_title, $price_data, $wc_product, $data, $cart_item, $cart_item_key, $product_key, $design_data );
$small_description = WFOB_Common::decode_merge_tags( $small_description, $price_data, $wc_product, $data, $cart_item, $cart_item_key, $product_key, $design_data );
$description       = WFOB_Common::decode_merge_tags( $description, $price_data, $wc_product, $data, $cart_item, $cart_item_key, $product_key, $design_data );
$featured_image    = wc_string_to_bool( $featured_image )
?>
<div id="wfob_wrap" class="wfob_wrap_start">
	<?php do_action( 'wfob_before_wrapper', $this->get_id(), $wc_product, $product_key, $cart_item_key ); ?>
    <div id="wfob_main_wrapper_start" class="wfob_wrapper" data-wfob-id="<?php echo $this->get_id(); ?>">
        <div class="wfob_bump wfob_bump_r_outer_wrap wfob_layout_3" data-product-key="<?php echo $product_key; ?>" data-wfob-id="<?php echo $this->get_id(); ?>" cart_key="<?php echo $cart_item_key; ?>">
            <div class="wfob_l3_wrap">
                <div class="wfob_l3_s <?php echo ! $featured_image ? 'wfob_l3_no_img' : '' ?>">
					<?php
					if ( true == $featured_image ) {
						?>
                        <div class="wfob_l3_s_img wfob_product_image">
							<?php
							echo $wc_product->get_image();
							?>
                        </div>
						<?php
					}
					?>
                    <div class="wfob_l3_s_c">
                        <div class="wfob_l3_s_data">
							<?php
							if ( '' != $product_title ) {
								?>
                                <div class="wfob_l3_c_head"><?php echo $product_title; ?></div>
								<?php
							}
							if ( '' != $sub_title ) {
								?>
                                <div class="wfob_l3_c_sub_head"><?php echo $sub_title ?></div>
								<?php
							}
							if ( '' != $small_description ) {
								?>
                                <div class="wfob_l3_c_sub_desc show-read-more"><?php echo $small_description ?></div>
								<?php
							}
							if ( isset( $data['variable'] ) && false == WFOB_Common::display_not_selected_attribute( $data, $wc_product ) ) {
								echo sprintf( "<div class='wfob_l3_c_sub_desc_choose_option'><a href='#' class='wfob_qv-button var_product' qv-id='%d' qv-var-id='%d'>%s</a></div>", $data['id'], $cart_variation_id, apply_filters( 'wfob_choose_option_text', __( 'Choose an option', 'woocommerce' ) ) );
							}
							?>
                        </div>
                        <div class="wfob_l3_s_btn">
							<?php
							if ( $enable_price ) {
								?>
                                <div class="wfob_price">
									<?php echo $printed_price ?>
                                </div>
								<?php
							}
							?>
                            <a href="#" class="wfob_l3_f_btn wfob_btn_add <?php echo '' != $variable_checkbox ? $variable_checkbox : ''; ?>" style="<?php echo '' !== $cart_item_key ? 'display:none' : '' ?>"><?php echo $add_btn_text ?></a>
                            <a href="#" class="wfob_l3_f_btn wfob_btn_add wfob_btn_remove <?php echo '' !== $cart_item_key ? 'wfob_item_present' : '' ?>">
                                <span class="wfob_btn_text_added"><?php echo $added_btn_text ?></span>
                                <span class="wfob_btn_text_remove"><?php echo $remove_btn_text ?></span>
                            </a>
                        </div>
                        <div class="wfob_clearfix"></div>
                    </div>
                    <div class="wfob_clearfix"></div>
                </div>
				<?php
				if ( '' !== $description ) {
					if ( isset( $data['variable'] ) && false == WFOB_Common::display_not_selected_attribute( $data, $wc_product ) ) {
						$description .= sprintf( "<a href='#' class='wfob_qv-button var_product' qv-id='%d' qv-var-id='%d'>%s</a>", $data['id'], $cart_variation_id, apply_filters( 'wfob_choose_option_text', __( 'Choose an option', 'woocommerce' ) ) );
					}
					?>
                    <div class="wfob_l3_s_desc" style="<?php echo $description_display_none; ?>">
                        <div class="wfob_l3_l_desc"><?php echo $description ?></div>
                    </div>
					<?php
				}
				?>
            </div>
        </div>
    </div>
	<?php do_action( 'wfob_after_wrapper', $this->get_id(), $wc_product, $product_key, $cart_item_key ); ?>
</div>