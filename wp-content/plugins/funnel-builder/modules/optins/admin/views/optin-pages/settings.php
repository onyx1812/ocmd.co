<?php
/**
 * Without template
 */

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
			<?php echo BWF_Admin_Breadcrumbs::render(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <div class="bwf_after_bre">
                <a v-on:click="updateOptin()" href="javascript:void(0);" class="bwf_edt">
                    <i class="dashicons dashicons-edit"></i> <?php esc_html_e( 'Edit', 'funnel-builder' ); ?>
                </a>
                <a v-bind:href="view_url" target="_blank" class="wffn-step-preview wffn-step-preview-admin">
                    <i class="dashicons dashicons-visibility wffn-dash-eye"></i>
                    <span class="preview_text"><?php esc_html_e( 'View', 'funnel-builder' ); ?></span>
                </a>
            </div>
        </div>
		<?php WFOPP_Core()->optin_pages->admin->get_tabs_html( WFOPP_Core()->optin_pages->get_edit_id() ); ?>

        <div class="wffn-tabs-view-vertical wffn-widget-tabs">

            <div class="wffn-tabs-wrapper wffn-tab-center">
                <div class="wffn-tab-title wffn-tab-desktop-title validation_tab " id="tab-title-validation" data-tab="1" role="tab" aria-controls="wffn-tab-content-validation">
					<?php esc_html_e( 'Validation', 'funnel-builder' ); ?>
                </div>
                <div class="wffn-tab-title wffn-tab-desktop-title additional_redirection wffn-active" id="tab-title-additional_redirection" data-tab="2" role="tab" aria-controls="wffn-tab-content-additional_redirection">
					<?php esc_html_e( 'Custom Redirection', 'funnel-builder' ); ?>
                </div>
				<div class="wffn-tab-title wffn-tab-desktop-title additional_information_tab" id="tab-title-additional_information" data-tab="3" role="tab" aria-controls="wffn-tab-content-additional_information">
		            <?php esc_html_e( 'Custom CSS', 'funnel-builder' ); ?>
				</div>
				<div class="wffn-tab-title wffn-tab-desktop-title description_tab " id="tab-title-description" data-tab="4" role="tab" aria-controls="wffn-tab-content-description">
		            <?php esc_html_e( 'External Scripts', 'funnel-builder' ); ?>
				</div>
            </div>

            <div class="wffn-tabs-content-wrapper" id="wffn_wfop_settings">
                <div class="wffn_custom_op_setting_inner" id="wffn_global_setting">

                    <form class="wffn_forms_wrap wffn_forms_global_settings ">
                        <fieldset>
                            <vue-form-generator ref="form_mapping" :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
                        </fieldset>
                        <div style="display: none" id="modal-global-settings_success" data-iziModal-icon="icon-home">
                        </div>
                    </form>

                    <div class="bwf_form_button">
                        <button v-on:click.self="onSubmit" style="float: left;" class="wffn_save_btn_style button button-primary"><?php esc_html_e( 'Save Settings', 'funnel-builder' ); ?></button>
                        <span class="wffn_loader_global_save spinner" style="float: left;"></span>
                    </div>
                </div>
            </div>

        </div>

    </div>

<?php
include_once dirname( __DIR__ ) . '/models/wffn-edit-optin.php';
