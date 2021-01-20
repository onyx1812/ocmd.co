<?php
/**
 * Without template
 */
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/** Registering Settings in top bar */
if ( class_exists( 'BWF_Admin_Breadcrumbs' ) ) {
	BWF_Admin_Breadcrumbs::register_node( [ 'text' => esc_html__( 'Settings', 'funnel-builder' ) ] );
}
 BWF_Admin_Breadcrumbs::render_sticky_bar();
?>
<div class="wrap wffn-funnel-common">
    <div class="wffn_clear_10"></div>
	<?php WFFN_Core()->landing_pages->admin->render_primary_nav(); ?>
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Settings', 'funnel-builder' ); ?></h1>

	<?php
	$admin_settings = BWF_Admin_Settings::get_instance();
	$admin_settings->render_tab_html( 'lp-settings' );
	?>

    <div id="wffn_lp_settings_vue_wrap" class="wffn-tabs-view-vertical wffn-widget-tabs">

        <div class="wffn-tabs-wrapper wffn-tab-center">
            <div class="wffn-tab-title wffn-tab-desktop-title additional_information_tab wffn-active" id="tab-title-additional_information" data-tab="1" role="tab" aria-controls="wffn-tab-content-additional_information">
				<?php esc_html_e( 'Custom CSS', 'funnel-builder' ); ?>
            </div>
            <div class="wffn-tab-title wffn-tab-desktop-title additional_information_tab wffn-active" id="tab-title-additional_information" data-tab="2" role="tab" aria-controls="wffn-tab-content-additional_information">
				<?php esc_html_e( 'External Scripts', 'funnel-builder' ); ?>
            </div>
        </div>

        <div class="wffn-tabs-content-wrapper">
            <div class="wffn_global_setting_inner" id="wffn_global_setting">
                <form class="wffn_forms_wrap wffn_forms_global_settings ">
                    <fieldset>
                        <vue-form-generator :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
                    </fieldset>
                    <div style="display: none" id="modal-global-settings_success" data-iziModal-icon="icon-home">
                    </div>
                </form>
                <div class="bwf_form_button">
                    <span class="wffn_loader_global_save spinner" style="float: left;"></span>
                    <button v-on:click.self="onSubmit" class="wffn_save_btn_style"><?php esc_html_e( 'Save Changes', 'funnel-builder' ); ?></button>
                </div>
            </div>
        </div>

    </div>

</div>
