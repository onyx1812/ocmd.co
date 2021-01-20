<?php

if ( ! defined( 'WFACP_TEMPLATE_DIR' ) ) {
	return '';
}

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
	return;
}
$settings          = WFACP_Common::get_page_settings( WFACP_Common::get_id() );
$is_disable_coupon = ( isset( $settings['disable_coupon'] ) && 'true' == $settings['disable_coupon'] );
if ( $is_disable_coupon ) {
	return;
}


$coupon_cls = 'wfacp-col-left-half';
if ( $this->template_type == 'embed_form' ) {
	$coupon_cls = 'wfacp-col-full';
}

$is_disable_coupon_sidebar = apply_filters( 'wfacp_mini_cart_hide_coupon', true );


$wfacp_sidebar_coupon_text = apply_filters( 'wfacp_mini_cart_coupon_text', __( 'Coupon', 'woocommerce' ) );


if ( $is_disable_coupon_sidebar ) {

	if ( apply_filters( 'wfacp_form_coupon_widgets_enable', false, $this ) ) {

		$coupon_sidebar                     = wc_string_to_bool( $this->enable_collapsed_coupon_field() );
		$collapse_enable_coupon_collapsible = wc_string_to_bool( $this->collapse_enable_coupon_collapsible() );


		if ( $collapse_enable_coupon_collapsible == true ) {

			$classBlock = 'wfacp_display_none';

		} else {
			$classBlock     = 'wfacp_display_block';
			$coupon_sidebar = false;
		}


	} else {
		$coupon_sidebar = $this->collapse_enable_coupon_collapsible();
		$classBlock     = 'wfacp_display_block';
	}


	?>
    <div class="wfacp_woocommerce_form_coupon wfacp_template_9_coupon">
        <div class="wfacp-coupon-section wfacp_custom_row_wrap clearfix">
            <div class="wfacp-coupon-page">
				<?php
				if ( true === $coupon_sidebar ) {
					wc_print_notice( apply_filters( 'woocommerce_checkout_coupon_message', '<span class="wfacp_main_showcoupon">' . __( 'Have a coupon?', 'woocommerce' ) . ' ' . __( 'Click here to enter your code', 'woocommerce' ) . '</span>' ), 'notice' );
					if ( count( WC()->cart->applied_coupons ) >= 0 ) {
						$classBlock = 'wfacp_display_none';
					}
				}
				?>
                <div class='wfacp_mini_cart_classes '>
                    <form class="wfacp_layout_shopcheckout checkout_coupon woocommerce-form-coupon <?php echo $classBlock; ?>" method="post">
                        <div class="wfacp-row wfacp_coupon_row">
                            <p class="form-row form-row-first wfacp-form-control-wrapper <?php echo $coupon_cls; ?> wfacp-input-form">

                                <label for="coupon_code" class="wfacp-form-control-label"><?php echo $wfacp_sidebar_coupon_text; ?></label>
                                <input type="text" name="coupon_code" class="input-text wfacp-form-control" placeholder="" id="coupon_code" value=""/>
                            </p>
                            <p class="form-row form-row-last <?php echo $coupon_cls; ?>">
                                <button type="submit" class="button wfacp-coupon-btn" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
                            </p>
                            <div class="clear"></div>
                        </div>
                        <div class="wfacp-row ">
                            <div class="wfacp_coupon_msg"></div>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
	<?php
}


?>
