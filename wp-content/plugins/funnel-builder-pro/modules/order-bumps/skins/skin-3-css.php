<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<style>
    body #wfob_wrap .wfob_wrapper[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_bump {
        background: <?php echo $design_data['box_background']; ?>;
    }

    body #wfob_wrap .wfob_wrapper[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_bump:hover {
        background: <?php echo $design_data['box_background_hover']; ?>;
    }

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_c_head {
        color: <?php echo $design_data['heading_color']; ?>;
        font-size: <?php echo $design_data['heading_font_size']; ?>px;

    }

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>']:hover .wfob_l3_c_head {
        color: <?php echo $design_data['heading_hover_color']; ?>;
    }

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_c_sub_head,
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_c_sub_head span,
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_c_sub_head span bdi {
        color: <?php echo $design_data['sub_heading_color']; ?>;
        font-size: <?php echo $design_data['sub_heading_font_size']; ?>px;

    }

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_c_sub_head span.woocommerce-Price-amount.amount * {
        color: <?php echo $design_data['sub_heading_color']; ?>;
    }

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_c_sub_head span.woocommerce-Price-amount.amount bdi {
        color: <?php echo $design_data['sub_heading_color']; ?>;
    }

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>']:hover .wfob_l3_c_sub_head {
        color: <?php echo $design_data['sub_heading_hover_color']; ?>;
    }

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>']:hover .wfob_l3_c_sub_head span.woocommerce-Price-amount.amount * {
        color: <?php echo $design_data['sub_heading_hover_color']; ?>;
    }

    /**
	SUb content
	*/
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_c_sub_desc,
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_c_sub_desc p {
        color: <?php echo $design_data['sub_content_color']; ?>;
        font-size: <?php echo $design_data['sub_content_font_size']; ?>px;

    }

    /**
	Content
	*/
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_l_desc,
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_l_desc p,
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_l_desc span,
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_l_desc span bdi {
        color: <?php echo $design_data['content_color']; ?>;
        padding: <?php echo $design_data['content_box_padding']; ?>px;
        font-size: <?php echo $design_data['content_font_size']; ?>px;

    }

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_l_desc p {
        color: <?php echo $design_data['content_color']; ?>;
        font-size: <?php echo $design_data['content_font_size']; ?>px;
    }

    /**
	Add Buttons
	*/


    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] a.wfob_l3_f_btn.wfob_btn_add {
        color: <?php echo $design_data['add_button_color']; ?>;
        padding: 5px <?php echo $design_data['add_button_padding']; ?>px;
        font-size: <?php echo $design_data['add_button_font_size']; ?>px;
        width: <?php echo $design_data['add_button_width']; ?>px;
        background-color: <?php echo $design_data['add_button_bg_color']; ?>;
    <?php if ( $design_data['add_button_enable_box_border'] == 'true' ) {		?> border: <?php echo sprintf( '%dpx %s %s', $design_data['add_button_border_width'], $design_data['add_button_border_style'], $design_data['add_button_border_color'] ); ?>;
    <?php	}?>
    }

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_s_btn a.wfob_l3_f_btn.wfob_btn_add:hover {
        background-color: <?php echo $design_data['add_button_hover_bg_color']; ?>;
        color: <?php echo $design_data['add_button_hover_color']; ?>;

    <?php if ( $design_data['add_button_enable_box_border'] == 'true' ) {?> border-color: <?php echo $design_data['add_button_hover_bg_color']; ?>;
    <?php	}?>
    }

    /**
	Remove Buttons
	*/


    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] a.wfob_l3_f_btn.wfob_btn_remove.wfob_item_present:hover > .wfob_btn_text_remove,
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] a.wfob_l3_f_btn.wfob_btn_remove.wfob_item_present,
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] a.wfob_l3_f_btn.wfob_btn_remove.wfob_item_present .wfob_btn_text_added {
        display: block;
    }


    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] a.wfob_l3_f_btn.wfob_btn_remove.wfob_item_present:hover > .wfob_btn_text_added,
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] a.wfob_l3_f_btn.wfob_btn_remove.wfob_item_present .wfob_btn_text_remove {
        display: none;
    }


    /* added */
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] a.wfob_l3_f_btn.wfob_btn_remove.wfob_item_present {
        color: <?php echo $design_data['added_button_color']; ?>;
        background-color: <?php echo $design_data['added_button_bg_color']; ?>;

    <?php if ( $design_data['add_button_enable_box_border'] == 'true' ) {		?> border: <?php echo sprintf( '%dpx %s %s', $design_data['add_button_border_width'], $design_data['add_button_border_style'], $design_data['add_button_border_color'] ); ?>;
    <?php
    }
    ?> border-color: <?php echo $design_data['added_button_bg_color']; ?>;
    }


    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_s_btn a.wfob_l3_f_btn.wfob_btn_remove.wfob_item_present:hover {
        background-color: <?php echo $design_data['remove_button_bg_color']; ?>;
        color: <?php echo $design_data['remove_button_color']; ?>;

    }

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_s_btn {
        min-width: <?php echo ($design_data['add_button_width']+15)?>px;
    }

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_s_data {
        flex: 0 0 calc(100% - <?php echo ($design_data['add_button_width']+15)?>px);
        -webkit-flex: 0 0 calc(100% - <?php echo ($design_data['add_button_width']+15)?>px);
    }

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_qv-button {
        color: <?php echo $design_data['content_variation_link_color']?>;
    }

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_qv-button:hover {
        color: <?php echo $design_data['content_variation_link_hover_color']?>;
    }

    <?php
	if(isset($design_data['price_color'])){
		?>

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] span.woocommerce-Price-amount.amount,
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] span.woocommerce-Price-currencySymbol{
        color: <?php echo $design_data['price_color']?>;
        font-size: <?php echo $design_data['price_font_size']?>px;
    }
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] span.woocommerce-Price-amount.amount bdi{
        color: <?php echo $design_data['price_color']?>;
        font-size: <?php echo $design_data['price_font_size']?>px;
    }

    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] del span.woocommerce-Price-amount.amount,
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] del span *,
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] del span.woocommerce-Price-amount.amount bdi{
        color: <?php echo $design_data['price_color']?>;
    }



    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] ins span.woocommerce-Price-amount.amount bdi,
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] ins span.woocommerce-Price-currencySymbol ,
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] ins span.woocommerce-Price-amount.amount{
        color: <?php echo $design_data['price_sale_color']?>;
    }

    <?php
}



if ( $design_data['enable_featured_image_border'] == 'true' ) { ?>
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_l3_s_img {
        border: <?php echo sprintf( '%dpx %s %s', $design_data['featured_image_border_width'], $design_data['featured_image_border_style'], $design_data['featured_image_border_color'] ); ?>;
    }

    <?php
}
if ( $design_data['bump_max_width'] > 0) {
	?>
    body #wfob_wrap .wfob_bump_r_outer_wrap.wfob_layout_3[data-wfob-id='<?php echo $this->get_id(); ?>'] {
        width: <?php echo $design_data['bump_max_width']?>px;
    }

    <?php
}
?>
</style>
