<?php

/**
 * ========================
 * Quick View Template
 * ========================
 **/

//Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	return;
}
global $product;


add_action( 'wfob_qv_summary', 'woocommerce_template_single_title', 5 );
//add_action( 'wfob_qv_summary', 'woocommerce_template_single_rating', 10 );
add_action( 'wfob_qv_summary', 'woocommerce_template_single_price', 10 );
//add_action( 'wfacp_before_button', 'woocommerce_template_single_excerpt', 20 );
add_action( 'wfob_qv_summary', [ WFOB_Core()->public, 'woocommerce_template_single_add_to_cart' ], 25 );


remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
add_action( 'wfob_woocommerce_simple_add_to_cart', [ WFOB_Core()->public, 'woocommerce_simple_add_to_cart' ] );
add_action( 'woocommerce_single_variation', [ WFOB_Core()->public, 'woocommerce_single_variation_add_to_cart_button' ], 20 );

add_action( 'wfob_qv_images', function () {
	include_once __DIR__ . '/images/product-image.php';
}, 20 );


global $wfob_product;

if ( ! is_null( $wfob_product ) ) {
	add_action( 'wfob_woocommerce_variable_add_to_cart', [ WFOB_Core()->public, 'woocommerce_simple_add_to_cart' ] );
	add_action( 'wfob_woocommerce_variable-subscription_add_to_cart', [ WFOB_Core()->public, 'woocommerce_subscription_add_to_cart' ] );

} else {
	add_action( 'wfob_woocommerce_variable_add_to_cart', [ WFOB_Core()->public, 'woocommerce_variable_add_to_cart' ] );
	add_action( 'wfob_woocommerce_variable-subscription_add_to_cart', [ WFOB_Core()->public, 'woocommerce_variable_subscription_add_to_cart' ] );
}
add_filter( 'woocommerce_single_product_flexslider_enabled', function () {
	return true;
} );

add_action( 'woocommerce_before_variations_form', function () {
	global $wfob_item_data;
	echo "<input type='hidden' value='" . $wfob_item_data['item_key'] . "' name='wfob_item_key' class='wfob_quick_view_item_key'>";
} );
add_action( 'woocommerce_before_add_to_cart_button', function () {
	global $wfob_item_data;
	echo "<input type='hidden' value='" . $wfob_item_data['item_key'] . "' name='wfob_item_key'  class='wfob_quick_view_item_key'>";
} );
?>
<style>
    #wfob_qr_model_wrap .woocommerce-product-gallery__image a img {
        display: block;
        width: 100%;
        height: auto;
        box-shadow: none;
    }

    .single_variation_wrap .woocommerce-variation-price {
        display: none;
    }

    .wfob_option_btn {
        display: none;
    }

    @media (max-width: 767px) {
        .wfob_option_btn {
            display: block;
        }

        .wfob_option_btn, #wfob_qr_model_wrap .wfob_qr_wrap .button {
            background: #24ae4e;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            color: #fff;
            padding: 14px 10px;
            font-size: 17px;
            cursor: pointer;
        }

        #wfob_qr_model_wrap .wfob_qr_wrap .button {
            position: fixed;
            width: 100%;
        }

        .wfob_qv-main {
            padding: 10px 10px 35px;
        }
    }
</style>
<div id="wfob_qr_model_wrap" class=" wfob_qv-inner-modal" data-item-key="<?php echo $item_key; ?>" data-cart-key="<?php echo $cart_key; ?>">
    <div class="wfob_qv-container woocommerce single-product">
        <div class="wfob_qv-top-panel">
            <div class="wfob_qv-close wfob_qv xooqv-cross"></div>
            <div class="wfob_qv-preloader wfob_qv-mpl">
                <div class="wfob_qv-speeding-wheel"></div>
            </div>
        </div>
        <div class="wfob_qv-main">
            <div id="product-<?php echo $product->get_id(); ?>" <?php post_class( 'product' ); ?>>
                <div class="wfob_qr_wrap">
                    <div class="wfob_qv-images">
						<?php do_action( 'wfob_qv_images' ); ?>
                    </div>
                    <div class="wfob_qv-summary">
						<?php
						/**
						 * @todo
						 * Using our custom hook display only few content
						 * some themes like flatsome changes the normal behaviour of components
						 *
						 */
						?>
						<?php do_action( 'wfob_qv_summary' ); ?>
                    </div>

                    <div class="wfob_clear"></div>
                </div>
            </div>
        </div>
        <div class="wfob_option_btn"><?php _e( 'Choose an Option', 'woofunnels-order-bump' ) ?></div>
    </div>
</div>