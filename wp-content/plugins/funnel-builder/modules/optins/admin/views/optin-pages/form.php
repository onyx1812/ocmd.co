<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly
 BWF_Admin_Breadcrumbs::render_sticky_bar();
?>
<div class="wrap" id="wffn_op_edit_vue_wrap">
    <div class="bwf_breadcrumb">
        <div class="bwf_before_bre"></div>
        <div class="wf_funnel_card_switch">
            <label class="wf_funnel_switch">
                <input type="checkbox" <?php checked( 'publish', WFOPP_Core()->optin_pages->get_status() ); ?>>
                <div class="wf_funnel_slider"></div>
            </label>
        </div>
		<?php BWF_Admin_Breadcrumbs::render(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="bwf_after_bre">
            <a v-on:click="updateOptin()" href="javascript:void(0);" class="bwf_edt fff">
                <i class="dashicons dashicons-edit"></i> <?php esc_html_e( 'Edit', 'funnel-builder' ); ?>
            </a>
            <a v-bind:href="view_url" target="_blank" class="wffn-step-preview wffn-step-preview-admin">
                <i class="dashicons dashicons-visibility wffn-dash-eye"></i>
                <span class="preview_text"><?php esc_html_e( 'View', 'funnel-builder' ); ?></span>
            </a>
        </div>
    </div>
	<?php WFOPP_Core()->optin_pages->admin->get_tabs_html( WFOPP_Core()->optin_pages->get_edit_id() ); ?>
</div>
<?php

include __DIR__ . '/fields.php'; ?>

