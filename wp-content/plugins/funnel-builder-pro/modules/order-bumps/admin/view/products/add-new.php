<?php
defined( 'ABSPATH' ) || exit;
?>
<div class="wfob_welcome_wrap" v-if="isEmpty()">
    <div class="wfob_welcome_wrap_in">
        <div class="wfob_first_product">
            <div class="wfob_welc_head">
                <div class="wfob_welc_icon"><img src="<?php echo WFOB_PLUGIN_URL; ?>/admin/assets/img/clap.png" alt="" title=""/></div>
                <div class="wfob_welc_title"> <?php _e( 'Add a product for OrderBump', 'woofunnels-order-bump' ); ?>
                </div>
            </div>

            <div class="wfob_welc_text">
                <p><?php _e( ' Add a product to be associated as a order bump on the checkout page. You can<br>add multiple products as well.', 'woofunnels-order-bump' ); ?></p>
            </div>
            <button type="button" class="wfob_step wfob_button_add wfob_button_inline wfob_modal_open wfob_welc_btn" data-izimodal-open="#modal-add-product" data-iziModal-title="Create New Funnel Step" data-izimodal-transitionin="fadeInDown">
				<?php _e( 'Add Product', 'woofunnels-order-bump' ); ?>
            </button>
        </div>
    </div>
</div>
