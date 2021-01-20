<?php
/**
 * Integration tab setting
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

    <?php
    $form_builders = WFOPP_Core()->form_controllers->get_supported_form_controllers();
    $forms         = array(
        'active-campaign' => __( 'ActiveCampaign', 'funnel-builder' ),
        'drip'            => __( 'Drip', 'funnel-builder' ),
        'convert-git'     => __( 'ConvertKit', 'funnel-builder' ),
        'infusion-soft'   => __( 'InfusionSoft', 'funnel-builder' ),
        'mailchimp'       => __( 'Mailchimp', 'funnel-builder' ),
    );

    $optin_form_options = WFOPP_Core()->optin_pages->get_optin_form_integration_option();
    $is_inti            = isset( $optin_form_options['optin_form_enable'] ) ? $optin_form_options['optin_form_enable'] : 'false';
    $formBuilder        = isset( $optin_form_options['formBuilder'] ) ? $optin_form_options['formBuilder'] : '';
    $optin_form_id      = isset( $optin_form_options['optinFormId'] ) ? $optin_form_options['optinFormId'] : 0;
    $optinPageId        = isset( $optin_form_options['optinPageId'] ) ? $optin_form_options['optinPageId'] : 0;
    $html_code          = isset( $optin_form_options['html_code'] ) ? $optin_form_options['html_code'] : '';
    $form_controller    = WFOPP_Core()->form_controllers->get_integration_object( $formBuilder );
    $form_title         = __( 'Form Title', 'funnel-builder' );
    if ( $form_controller instanceof WFFN_Optin_Form_Controller ) {
        $form_title = $form_controller->get_form_title( $optin_form_id, $optinPageId );
    }
    $lms_obj = WFOPP_Core()->optin_actions->get_integration_object( WFFN_Optin_Action_Assign_LD_Course::get_slug() );

    $lms_active     = false;
    $hide_class_lms = '';
    $hide_class_crm = '';
    if ( $lms_obj instanceof WFFN_Optin_Action ) {
        $lms_active = $lms_obj->should_register();
    }
    if ( ! $lms_active ) {
        $hide_class_lms = 'hide_bwf_btn';
    }
    if ( ! WFFN_Common::wffn_is_funnel_pro_active() ) {
        $hide_class_crm = 'hide_bwf_btn';
    }

    ?>
	<div class="wffn-tabs-view-vertical wffn-widget-tabs">

		<div class="wffn-tabs-wrapper wffn-tab-center">
			<div class="wffn-tab-title wffn-tab-desktop-title wffn-active" data-tab="1" role="tab">
                <?php esc_html_e( 'Notifications', 'funnel-builder' ); ?>
			</div>
			<div class="wffn-tab-title wffn-tab-desktop-title crm_only <?php echo esc_attr( $hide_class_crm ); ?>" data-tab="2" role="tab"><?php esc_html_e( 'CRM', 'funnel-builder' ); ?></div>
			<div class="wffn-tab-title wffn-tab-desktop-title <?php echo esc_attr( $hide_class_lms ); ?>" data-tab="3" role="tab"><?php esc_html_e( 'Learndash', 'funnel-builder' ); ?></div>
		</div>

		<div class="wffn-tabs-content-wrapper" id="wffn_actions_container">
			<div class="wffn_custom_op_setting_inner" id="wffn_global_setting">

				<form class="wffn_forms_wrap wffn_forms_global_settings">
					<fieldset>

						<!-- vue form generator responsible for the notifications settings -->

						<vue-form-generator ref="action_ref" :schema="schemaAction" :model="modelAction" :options="formOptions"></vue-form-generator>

						<!-- fieldsets for CRM integration settings -->
						<div class="wffn-integration">
							<div class="vue-form-generator">
								<fieldset>
									<legend><?php esc_html_e( 'CRM', 'funnel-builder' ); ?></legend>
									<div v-if="!wfop.is_wffn_pro_active">
										<p class="no-pro">
                                            <?php esc_html_e( 'Get pro to enable CRM integration.' ); ?>
											<a target="_blank" href="<?php echo esc_url( 'https://buildwoofunnels.com/exclusive-offer/' ) ?>"><?php esc_html_e( 'Get Pro', 'funnel-builder' ); ?></a>
										</p>
									</div>

									<div v-if="wfop.is_wffn_pro_active" class="form-group valid field-radios">
										<label for="optin-form-enable"><span><?php esc_html_e( 'Enable Integration', 'funnel-builder' ); ?></span></label>
										<div class="field-wrap">
											<div class="radio-list">
												<label class=""><input id="optin-form-enable-1" type="radio" name="optin_form_enable" value="true" <?php if ( $is_inti === 'true' ) {
                                                        echo "checked";
                                                    } ?>><?php esc_html_e( 'Yes', 'funnel-builder' ); ?></label>
												<label class=""><input id="optin-form-enable-2" type="radio" name="optin_form_enable" value="false" <?php if ( $is_inti === 'false' ) {
                                                        echo "checked";
                                                    } ?>><?php esc_html_e( 'No', 'funnel-builder' ); ?></label>
											</div>
										</div>
									</div>
									<div class="action-crm-container">
										<div class="init_form wffn-hide">
											<div class="form-group valid field-select">
												<label for="optin-form-builder"><span><?php esc_html_e( 'Send contacts to', 'funnel-builder' ); ?></span></label>
												<div class="field-wrap">
													<select name="optin_form_builder" id="optin-form-builder" class="form-control">
														<option value=""><?php esc_html_e( 'Select Services', 'funnel-builder' ); ?></option>
                                                        <?php
                                                        foreach ( $forms as $key => $value ) { ?>
															<option data-form-group="<?php echo esc_attr( $key ) ?>" value="<?php echo esc_attr( $key ) ?>" <?php if ( $formBuilder === $key ) {
                                                                echo 'selected';
                                                            } ?> ><?php esc_html_e( $value ); ?></option>
                                                        <?php } ?>
													</select>
												</div>
											</div>

											<div class="form-group wffn-paste-form-html wffn-hide">
												<label></label>
												<div class="field-wrap">
													<textarea id="wffn_lead_generation_code" rows="10" cols="68" name="wffn-form-html" placeholder="<?php esc_attr_e( 'Paste CRM form embed code.', 'funnel-builder' ); ?>"><?php echo stripslashes_deep( $html_code ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></textarea>
													<div class="html_err_print" style="display:none;"></div>
													<a id="wffn_generate_form" href="javascript:void(0);" class="button-primary button wffn-gen-form" disabled><?php esc_html_e( 'Continue to map fields', 'funnel-builder' ); ?></a>
													<p class="wffn-title-error wffn-hide"><?php esc_html_e( 'Form title must not be empty.', 'funnel-builder' ); ?></p>
												</div>
											</div>

											<div class="form-group valid wffn-map-fields wffn-hide">
												<label></label>

												<div class="field-wrap wffn-field-heads">
													<div class="fields">
														<h4 class="field-head"><?php esc_html_e( 'Map Fields', 'funnel-builder' ); ?></h4>
													</div>
												</div>
												<div class="field-wrap" id="wffn_form_fields"></div>


											</div>
										</div>
									</div>

								</fieldset>

								<!-- learndsh integration html below, if environment found we show the form generator, else show message only
								Since vue-form-generator comes with its own fieldset and legend tags then we are managing it accordingly
								 -->

								<div v-if="!wfop.is_wffn_pro_active" class="no-learndash">
									<fieldset>
										<legend><?php esc_html_e( 'Learndash', 'funnel-builder' ); ?></legend>
										<p class="no-pro">
                                            <?php esc_html_e( 'Get pro to enable Learndash integration.' ); ?>
											<a target="_blank" href="<?php echo esc_url( 'https://buildwoofunnels.com/exclusive-offer/' ) ?>"><?php esc_html_e( 'Get Pro', 'funnel-builder' ); ?></a>
										</p>
									</fieldset>
								</div>
								<div v-else-if="!wfop_action.lms_active" class="no-learndash">
									<fieldset>
										<legend><?php esc_html_e( 'Learndash', 'funnel-builder' ); ?></legend>
										<p class="no-pro">
                                            <?php esc_html_e( 'Note: Learndash plugin needs to be activated to enable integration.', 'funnel-builder' ); ?>
										</p>
									</fieldset>
								</div>
								<div v-else>
									<vue-form-generator ref="learndash_ref" :schema="schemaLMS" :model="modelLMS" :options="formOptions"></vue-form-generator>

								</div>


							</div>
						</div>
					</fieldset>
				</form>
				<div style="display: none" id="modal-global-settings_success" data-iziModal-icon="icon-home">
				</div>
				<div class="bwf_form_button">
					<button style="float: left" v-on:click.self="onSubmitActions" id="wffn_optin_form_submit" class="wffn_save_btn_style button button-primary"><?php esc_html_e( 'Save Settings', 'funnel-builder' ); ?></button>
					<span class="wffn_loader_global_save spinner" style="float: left;"></span>
					<span class="wffn_success_msg wffn-hide"></span>
				</div>
			</div>
		</div>
	</div>

</div>
<?php
include_once dirname( __DIR__ ) . '/models/wffn-edit-optin.php';
?>
<script id="vue-f-button" type="text/x-template" xmlns="http://www.w3.org/1999/html">
	{{schema.type}}
	<a onclick="window.wfop_design.main.onSubmitActions('test')" style="float: left;" class=" button button-primary">Test Email</a>
	{{schema.type}}
</script>

