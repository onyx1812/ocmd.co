<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wfob_bump_r_outer_wrap" v-if="model.layout=='layout_1' || model.layout=='layout_2'">
    <div class="wfob_wrapper wfob_bump wfob_clear">
        <div class="wfob_outer">
            <div class="wfob_Box">
                <div class="wfob_bgBox_table">
                    <div class="wfob_bgBox_tablecell wfob_check_container">
								<span v-if="model.header_enable_pointing_arrow" class="wfob_checkbox_blick_image_container wfob_bk_blink_wrap">
									<span v-if="model.point_animation=='1'" class="wfob_checkbox_blick_image"><img src="<?php echo WFOB_PLUGIN_URL; ?>/assets/img/arrow-blink.gif"></span>
									<span v-if="model.point_animation=='0'" class="wfob_checkbox_blick_image"><img src="<?php echo WFOB_PLUGIN_URL; ?>/assets/img/arrow-no-blink.gif"></span>
								</span>
                        <input type="checkbox" id="" data-value="" class="wfob_checkbox wfob_bump_product">
                        <label for="" class="wfob_title" v-html="<?php echo 'model.product_' . $product_key . '_title'; ?>"></label>
                    </div>
                    <div class="wfob_bgBox_tablecell wfob_price_container" v-if="model.enable_price=='1'">
                        <div class="wfob_price">
							<?php
							if ( $price_data['price'] > 0 && ( absint( $price_data['price'] ) !== absint( $price_data['regular_org'] ) ) ) {
								echo wc_format_sale_price( $price_data['regular_org'], $price_data['price'] );
							} else {
								echo wc_price( $price_data['price'] );
							}
							?>
                        </div>
                    </div>
                </div>
                <div class="wfob_contentBox wfob_clear">
                    <div class="wfob_pro_img_wrap"
                         v-if="<?php echo 'model.product_' . $product_key . '_featured_image'; ?>">
						<?php
						$img_src = $wc_product->get_image();
						echo $img_src;
						?>
                    </div>
                    <div class="wfob_pro_txt_wrap">
                        <div class="wfob_l3_l_desc" v-html="<?php echo 'model.product_' . $product_key . '_description'; ?>"></div>
						<?php
						if ( isset( $data['variable'] ) ) {
							printf( "<a href='#' class='wfob_qv-button var_product' qv-id='%d' qv-var-id='%d'>%s</a>", 0, 0, __( 'Choose an option', 'woocommerce' ) );
						}
						?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
