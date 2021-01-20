<?php
?>
<div class="wfob_bump_r_outer_wrap wfob_layout_3" v-if="model.layout=='layout_3' || model.layout=='layout_4'" data-product-key="<?php echo $product_key ?>">
    <div class="wfob_l3_wrap">
        <div v-bind:class="get_the_image_cls('<?php echo $product_key ?>')">
            <div class="wfob_l3_s_img wfacp_product_image" v-if="<?php echo 'model.product_' . $product_key . '_featured_image'; ?>">
				<?php
				$img_src = $wc_product->get_image();
				echo $img_src;
				?>
            </div>
            <div class="wfob_l3_s_c">
                <div class="wfob_l3_s_data">
                    <div class="wfob_l3_c_head" v-html="get_data('title','<?php echo $product_key ?>')"></div>
                    <div class="wfob_l3_c_sub_head" v-if="<?php echo 'model.product_' . $product_key . '_sub_title'; ?>!=''" v-html="get_data('sub_title','<?php echo $product_key ?>')"></div>
                    <div class="wfob_l3_c_sub_desc show-read-more" v-if="<?php echo 'model.product_' . $product_key . '_small_description'; ?>!=''" v-html="get_data('small_description','<?php echo $product_key ?>')"></div>
                    <div class="wfob_l3_c_sub_desc_choose_option">
						<?php
						if ( isset( $data['variable'] ) ) {
							printf( "<a href='#' class='wfob_qv-button var_product' qv-id='%d' qv-var-id='%d'>%s</a>", 0, 0, __( 'Choose an option', 'woocommerce' ) );
						}
						?>
                    </div>
                </div>
                <div class="wfob_l3_s_btn">
                    <div class="wfob_price" v-if="1==model.enable_price">
						<?php
						if ( $price_data['price'] > 0 && ( absint( $price_data['price'] ) !== absint( $price_data['regular_org'] ) ) ) {
							echo wc_format_sale_price( $price_data['regular_org'], $price_data['price'] );
						} else {
							echo wc_price( $price_data['price'] );
						}
						?>
                    </div>
                    <a href="#" class="wfob_l3_f_btn wfob_btn_add" data-key="<?php echo $product_key ?>" v-html="add_button_text('<?php echo $product_key; ?>')"></a>
                    <a href="#" class="wfob_l3_f_btn wfob_btn_remove" data-key="<?php echo $product_key ?>">
                        <span class="wfob_btn_text_added" v-html="added_button_text('<?php echo $product_key; ?>')"></span>
                        <span class="wfob_btn_text_remove" v-html="remove_button_text('<?php echo $product_key; ?>')"></span>
                    </a>
                </div>
                <div class="wfob_clearfix"></div>
            </div>
            <div class="wfob_clearfix"></div>
        </div>
        <div class="wfob_l3_s_desc" v-if="<?php echo 'model.product_' . $product_key . '_description'; ?>!=''">
            <div class="wfob_l3_l_desc" v-html="get_data('description','<?php echo $product_key ?>')"></div>
        </div>
    </div>
</div>
