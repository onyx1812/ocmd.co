<?php
/**
 * Without template
 */
defined( 'ABSPATH' ) || exit; //Exit if accessed directly
 BWF_Admin_Breadcrumbs::render_sticky_bar();
?>
    <div class="wrap" id="wffn_lp_edit_vue_wrap">
        <div class="bwf_breadcrumb">
            <div class="bwf_before_bre"></div>
            <div class="wf_funnel_card_switch">
                <label class="wf_funnel_switch">
                    <input type="checkbox" <?php checked( 'publish', WFFN_Core()->landing_pages->get_status() ); ?>>
                    <div class="wf_funnel_slider"></div>
                </label>
            </div>
			<?php echo BWF_Admin_Breadcrumbs::render(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <div class="bwf_after_bre">
                <a v-on:click="updateLanding()" href="javascript:void(0);" class="bwf_edt">
                    <i class="dashicons dashicons-edit"></i> <?php esc_html_e( 'Edit', 'funnel-builder' ); ?>
                </a>
                <a v-bind:href="view_url" target="_blank" class="wffn-step-preview wffn-step-preview-admin">
                    <i class="dashicons dashicons-visibility wffn-dash-eye"></i>
                    <span class="preview_text"><?php esc_html_e( 'View', 'funnel-builder' ); ?></span>
                </a>
            </div>
        </div>
		<?php WFFN_Core()->landing_pages->admin->get_tabs_html( WFFN_Core()->landing_pages->get_edit_id() ); ?>
        <div class="wffn-tabs-view-vertical wffn-widget-tabs">
            <div class="wffn-tabs-wrapper wffn-tab-center">
				<div class="wffn-tab-title wffn-tab-desktop-title additional_information_tab wffn-active" id="tab-title-additional_information" data-tab="1" role="tab" aria-controls="wffn-tab-content-additional_information">
		            <?php esc_html_e( 'Custom CSS', 'funnel-builder' ); ?>
				</div>
                <div class="wffn-tab-title wffn-tab-desktop-title description_tab " id="tab-title-description" data-tab="2" role="tab" aria-controls="wffn-tab-content-description">
					<?php esc_html_e( 'External Scripts', 'funnel-builder' ); ?>
                </div>
            </div>
            <div class="wffn-tabs-content-wrapper">
                <div class="wffn_custom_lp_setting_inner" id="wffn_global_setting">
                    <form class="wffn_forms_wrap wffn_forms_global_settings ">
                        <fieldset>
                            <vue-form-generator :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
                        </fieldset>
                        <div style="display: none" id="modal-global-settings_success" data-iziModal-icon="icon-home">
                        </div>
                    </form>
                    <div class="bwf_form_button">
                        <span class="wffn_loader_global_save spinner" style="float: left;"></span>
                        <button v-on:click.self="onSubmit" class="wffn_save_btn_style"><?php esc_html_e( 'Save changes', 'funnel-builder' ); ?></button>

                    </div>
                </div>
            </div>
        </div>

    </div>
<?php
include_once dirname( __DIR__ ) . '/models/wffn-edit-landing.php';