<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( apply_filters( 'wfacp_disabled_order_bump_css_printing', false, $this ) ) {
	return;
}
$design_data = $this->get_design_data();

//for default css
include __DIR__ . '/default-css.php';
if ( $design_data['layout'] == 'layout_3' || $design_data['layout'] == 'layout_4' ) {
	//for layout 3,4
	include __DIR__ . '/skin-3-css.php';
} else {
	// for layout 1,2
	include __DIR__ . '/skin-1-css.php';
}

$globalSetting = WFOB_Common::get_global_setting();
if ( isset( $globalSetting['css'] ) && $globalSetting['css'] != '' ) {
	echo "<style>" . $globalSetting['css'] . "</style>";
}

do_action( 'wfob_layout_style' );
