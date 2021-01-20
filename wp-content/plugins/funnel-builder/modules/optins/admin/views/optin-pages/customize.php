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

        <div class="wffn_inner_setting_wrap">
            <div class="wffn_p20_noside wffn_box_size clearfix">
                <div class="wffn_wrap_inner wfop_edit_form_wrap" style="margin-left: 0px;">
					<?php
					$optin_id          = WFOPP_Core()->optin_pages->get_edit_id();
					$design_data       = get_post_meta( $optin_id, '_wfop_selected_design', true );
					$selected_template = isset( $design_data['selected_type'] ) ? $design_data['selected_type'] : false;
					if ( ! $selected_template ) {
						$tab_links = WFOPP_Core()->optin_pages->admin->get_tabs_links( $optin_id );
						$tab_link  = WFOPP_Core()->optin_pages->admin->get_tab_link( $tab_links[0] ); ?>
                        <div class="wfop-no-embed-container">
                            <p class="no-settings">
                                <span class="dashicons dashicons-info"></span>
                                <span class="design_tab"><?php esc_html_e( 'Go to the', 'funnel-builder' ); ?></span>
                                <a href="<?php echo esc_url( $tab_link ) ?>"><?php esc_html_e( 'Design' ); ?></a>
                                <span class="design_tab"><?php esc_html_e( ' to select the template', 'funnel-builder' ); ?></span>
                            </p>
                        </div>
					<?php } else if ( 'wp_editor' === $selected_template ) { ?>
                        <div id="wf_funnel_optin" class="wffn-tabs-view-vertical wffn-widget-tabs">
                            <div class="wffn_accord">
                                <div class="wffn-tabs-wrapper wffn-tab-center">

                                    <div onclick="window.wfop_design.onChangeCustomizeMode('inline');" class="wfopp-tabs wfopp-tab-inline wffn-active">
										<?php esc_html_e( 'Inline', 'funnel-builder' ); ?>
                                    </div>

                                    <div v-on:Click="wfop.is_wffn_pro_active?window.wfop_design.onChangeCustomizeMode('popover'):wfop.show_pro_message(`popover`);" class="wfopp-tabs wfopp-tab-popover">
                                        <i v-bind:class="wfop.is_wffn_pro_active?``:`dashicons dashicons-lock`"></i>
										<?php esc_html_e( 'Popover', 'funnel-builder' ); ?>
                                    </div>

                                    <div class="bwf_form_button btn_right_bar">
                                        <span class="wffn_loader_global_save spinner" style="float: left;"></span>
                                        <button onclick="window.wfop_design.onsubmitForm()" class="wffn_save_btn_style"><?php esc_html_e( 'Save changes', 'funnel-builder' ); ?></button>
                                    </div>
                                </div>
                                <div class="wffn-tabs-content-wrapper">
                                    <div id="wffn_optin_setting" class="wffn_optin_setting_inner">
                                        <form class="wffn_forms_wrap wffn_forms_global_settings ">
                                            <div class="vue-form-generator">

                                                <fieldset class="valid wfopp-activeTab">
													<?php include_once __DIR__ . '/form-customize.phtml'; ?>
                                                </fieldset>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="wffn_right_optin">
                                <div class="wffn_billing_accordion_content wffn_op_preview bwfop_preview_loading">

                                    <link rel="stylesheet" id='wffn_google_font_optin' href="">
                                    <div class="bwfop_loader">
                                        <div class="bwf-spin-loader bwf-spin-loader-xl"></div>
                                    </div>
                                    <div class="wfop_form_preview_wrap">
                                        <div class="wfop_form_wrap"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
					<?php } else { ?>
                        <div class="wfop-no-embed-container">
                            <p class="no-settings">
                                <span class="dashicons dashicons-info"></span>
                                <span class="wffn-elementor-tab"><?php esc_html_e( 'Customize optins using Elementor widgets. Click ', 'funnel-builder' ); ?></span>
                                <a target="_blank" v-bind:href="get_edit_link()"><?php esc_html_e( 'here ' ); ?></a>
                                <span class="wffn-elementor-tab"><?php esc_html_e( ' to edit', 'funnel-builder' ); ?></span>
                            </p>
                        </div>
					<?php } ?>
                </div>


            </div>
        </div>
        <div style="display: none" id="modal-global-settings_success" data-iziModal-icon="icon-home">
        </div>
    </div>
<?php

include_once dirname( __DIR__ ) . '/models/wffn-edit-optin.php';
