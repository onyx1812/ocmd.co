<?php
$css            = [];

$label_color = isset( $customizations['input_label_color'] ) ? "color:" . $customizations['input_label_color'] . ";" : '';

$css[".bwfac_forms_outer .bwfac_form_sec label"] = $label_color;

$text_color = isset( $customizations['input_text_color'] ) ? "color:" . $customizations['input_text_color'] . ";" : "";

$bg_color = isset( $customizations['input_bg_color'] ) ? "background-color:" . $customizations['input_bg_color'] . ";" : "";

$font_size = isset( $customizations['input_font_size'] ) ? "font-size:" . $customizations['input_font_size'] . "px;line-height:" . ( $customizations['input_font_size'] + 10 ) . "px;" : "";

$label_font_size = isset( $customizations['label_font_size'] ) ? "font-size:" . $customizations['label_font_size'] . "px;line-height:" . ( $customizations['label_font_size'] + 10 ) . "px;" : "";

$font_weight = isset( $customizations['input_font_weight'] ) ? "font-weight:" . $customizations['input_font_weight'] . ";" : "normal";

$font_family = isset( $customizations['input_font_family'] ) ? "font-family:" . $customizations['input_font_family'] . ";" : "";

$btn_font_size = isset( $customizations['button_font_size'] ) ? "font-size:" . $customizations['button_font_size'] . "px;line-height:" . ( $customizations['button_font_size'] + 10 ) . "px;" : "";

$btn_font_family = isset( $customizations['button_font_family'] ) ? "font-family:" . $customizations['button_font_family'] . ";" : "";

$btn_font_weight = isset( $customizations['button_font_weight'] ) ? "font-weight:" . $customizations['button_font_weight'] . ";" : "normal";

$btn_text_color = isset( $customizations['button_text_color'] ) ? "color:" . $customizations['button_text_color'] . ";" : "";

$btn_text_color_hover = isset( $customizations['button_text_color_hover'] ) ? "color:" . $customizations['button_text_color_hover'] . ";" : "";

$btn_bg_color = isset( $customizations['button_bg_color'] ) ? "background-color:" . $customizations['button_bg_color'] . ";" : "";

$btn_bg_color_hover = isset( $customizations['button_bg_color_hover'] ) ? "background-color:" . $customizations['button_bg_color_hover'] . ";" : "";

$btn_border_color_hover = isset( $customizations['button_border_color_hover'] ) ? "border-color:" . $customizations['button_border_color_hover'] . ";" : "";

$css[".bwfac_forms_outer .bwfac_form_sec ::-webkit-input-placeholder"] = $text_color;
$css[".bwfac_forms_outer .bwfac_form_sec ::-moz-placeholder"]          = $text_color;
$css[".bwfac_forms_outer .bwfac_form_sec :-ms-input-placeholder"]      = $text_color;
$css[".bwfac_forms_outer .bwfac_form_sec :-moz-placeholder"]           = $text_color;

$css['.bwfac_forms_outer .bwfac_form_sec input[type="text"], .bwfac_forms_outer .bwfac_form_sec input[type="email"], .bwfac_forms_outer .bwfac_form_sec input[type="tel"], .bwfac_forms_outer .bwfac_form_sec select, .bwfac_forms_outer .bwfac_form_sec textarea'] = 'border:' . $customizations['input_border_size'] . 'px ' . $customizations['input_border_type'] . ' ' . $customizations['input_border_color'] . ';' . $text_color . $bg_color;

$css['.bwfac_forms_outer .bwfac_form_sec input, .bwfac_forms_outer .bwfac_form_sec textarea, .bwfac_forms_outer .bwfac_form_sec select'] = $font_size . $font_weight . $font_family;

$css['.bwfac_forms_outer .bwfac_form_sec, .bwfac_forms_outer .bwfac_form_sec label'] = $label_font_size . $font_weight . $font_family;

$css['.bwfac_forms_outer .bwfac_form_sec .wfop_submit_btn'] = 'cursor:pointer;border: ' . $customizations['button_border_size'] . 'px ' . $customizations['button_border_type'] . ' ' . $customizations['button_border_color'] . ';' . $btn_font_size . $btn_font_family . $btn_font_weight . $btn_text_color . $btn_bg_color;

$css['.bwfac_forms_outer .bwfac_form_sec .wfop_submit_btn:hover'] = $btn_text_color_hover . $btn_bg_color_hover . $btn_border_color_hover;

$heading_color = isset( $customizations['popup_heading_color'] ) ? "color:" . $customizations['popup_heading_color'] . ";" : "";

$heading_font_size = isset( $customizations['popup_heading_font_size'] ) ? "font-size:" . $customizations['popup_heading_font_size'] . "px;line-height:" . ( $customizations['popup_heading_font_size'] + 8 ) . "px;" : "";

$heading_font_family = isset( $customizations['popup_heading_font_family'] ) ? "font-family:" . $customizations['popup_heading_font_family'] . ";" : "";

$heading_font_weight = isset( $customizations['popup_heading_font_weight'] ) ? "font-weight:" . $customizations['popup_heading_font_weight'] . ";" : "normal";

$css['.bwf_pp_opt_head'] = $heading_color . $heading_font_size . $heading_font_family . $heading_font_weight;

$sub_heading_color = isset( $customizations['popup_sub_heading_color'] ) ? "color:" . $customizations['popup_sub_heading_color'] . ";" : "";

$sub_heading_font_size = isset( $customizations['popup_sub_heading_font_size'] ) ? "font-size:" . $customizations['popup_sub_heading_font_size'] . "px;line-height:" . ( $customizations['popup_sub_heading_font_size'] + 8 ) . "px;" : "";

$sub_heading_font_family = isset( $customizations['popup_sub_heading_font_family'] ) ? "font-family:" . $customizations['popup_sub_heading_font_family'] . ";" : "";

$sub_heading_font_weight = isset( $customizations['popup_sub_heading_font_weight'] ) ? "font-weight:" . $customizations['popup_sub_heading_font_weight'] . ";" : "normal";

$css['.bwf_pp_opt_sub_head'] = 'margin-bottom:20px;' . $sub_heading_color . $sub_heading_font_size . $sub_heading_font_family . $sub_heading_font_weight;

$heading_color = ( isset( $customizations['popup_bar_pp'] ) && $customizations['popup_bar_pp'] === 'disable' ) ? "color:" . $customizations['popup_heading_color'] . ";" : "";

$popup_bar             = ( $customizations['popup_bar_pp'] === 'disable' ) ? 'display: none;' : 'display: flex;';
$transition            = ( ! is_admin() ) ? 'transition: all 5s ease-in-out;' : '';
$popup_bar_width       = "width:" . $customizations['popup_bar_width'] . '%;';
$popup_bar_height      = "height:" . $customizations['popup_bar_height'] . 'px;';
$popup_bar_font_size   = "font-size:" . $customizations['popup_bar_font_size'] . "px;line-height:" . ( $customizations['popup_bar_font_size'] + 8 ) . "px;";
$popup_bar_font_family = "font-family:" . $customizations['popup_bar_font_family'] . ";";
$popup_bar_inner_gap   = "padding:" . $customizations['popup_bar_inner_gap'] . "px;";
$popup_bar_text_color  = "color:" . $customizations['popup_bar_text_color'] . ";";
$popup_bar_color       = "background-color:" . $customizations['popup_bar_color'] . ";";
$popup_bar_bg_color    = "background-color:" . $customizations['popup_bar_bg_color'] . ";";

$css['.bwf_pp_bar_wrap'] = $popup_bar . $popup_bar_bg_color . $popup_bar_height . $popup_bar_inner_gap;

$css['.bwf_pp_bar_wrap .bwf_pp_bar'] = $popup_bar_font_size . $popup_bar_font_family . $popup_bar_text_color . $popup_bar_width . $popup_bar_color;

$popup_footer_font_size   = "font-size:" . $customizations['popup_footer_font_size'] . "px;line-height:" . ( $customizations['popup_footer_font_size'] + 8 ) . "px;";
$popup_footer_font_family = "font-family:" . $customizations['popup_footer_font_family'] . ";";
$popup_footer_text_color  = "color:" . $customizations['popup_footer_text_color'] . ";";
$css['.bwf_pp_footer']    = $popup_footer_font_size . $popup_footer_font_family . $popup_footer_text_color;

$popup_width   = isset( $customizations['popup_width'] ) ? "max-width:" . $customizations['popup_width'] . "px;" : "";
$popup_padding = isset( $customizations['popup_padding'] ) ? "padding:" . $customizations['popup_padding'] . "px;" : "";

$css['.wfop_form_preview_wrap'] = $popup_width . $popup_padding;
$css['.bwf_pp_wrap']            = $popup_width;
$css['.bwf_pp_cont']            = $popup_padding;

$print_css = '';
foreach ( $css as $selector => $rules ) {
	$print_css .= $selector . '{' . $rules . '}';
}

if ( ! empty( $print_css ) ) {
	?>
    <style type="text/css" id="wfop_custom_css">
        <?php echo $print_css; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </style>
	<?php
}