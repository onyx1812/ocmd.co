<div class="wfacp_form_templates_outer" id="wfacp_design_setting">
    <div class="wfacp-short-code-wrapper" v-if="selected_template_type()=='embed_forms' && template_active()=='yes'">
        <div class="wfacp_fsetting_table_head wfacp-scodes-head wfacp_shotcode_tab_wrap">
            <div class="wfacp_clear_20"></div>
            <div class="wfacp-fsetting-header"><?php _e( 'Checkout Form', 'woofunnels-aero-checkout' ); ?></div>
            <div class="wfacp_clear_20"></div>
        </div>
        <div class="wfacp_global_settings_wrap wfacp_page_col2_wrap wfacp_shortcodes_designs">
            <div class="wfacp_page_left_wrap">
                <div class="wfacp-product-tabs-view-vertical wfacp-product-widget-tabs">
                    <div class="wfacp-product-tabs-wrapper wfacp-tab-center">
                        <div class="wfacp_embed_form_design_tab wfacp-tab-title wfacp-tab-desktop-title wfacp-active" data-tab="wfacp_shortcode_field">
							<?php _e( 'Shortcode', 'wordpress' ) ?>
                        </div>
                        <div class="wfacp_embed_form_design_tab wfacp-tab-title wfacp-tab-desktop-title" data-tab="wfacp_mixed_style_field">
							<?php _e( 'Form Style', 'woofunnels-aero-checkout' ) ?>
                        </div>
                        <div class="wfacp_embed_form_design_tab wfacp-tab-title wfacp-tab-desktop-title" data-tab="wfacp_dropdown_style_field">
							<?php _e( 'Field Width', 'wordpress' ) ?>
                        </div>
                    </div>
                    <div class="wfacp-product-widget-container">
                        <div class="wfacp-product-tabs wfacp-tabs-style-line" role="tablist">
                            <div class="wfacp-product-tabs-content-wrapper">
                                <div class="wfacp_vue_forms">
                                    <form data-bwf-action="design_setting" data-bwf-action="design_setting" v-on:submit.prevent="onSubmit" v-on:change="onChange">
                                        <div class="wfacp_show_design_style_fields wfacp_shortcode_field">
                                            <div class="vue-form-generator">
                                                <fieldset class="wfacp_embed_fieldset wfacp-activeTab wfacp-shortcode-fieldset" style="display: block;">
                                                    <legend><?php _e( 'Form Shortcode', 'wordpress' ) ?></legend>
                                                    <div class="wfacp-scodes-row ">
                                                        <div class="wfacp-scodes-value">
															<?php
															$wfacp_id = WFACP_Common::get_id();
															$url      = admin_url( 'post.php?post=' . $wfacp_id . '&action=edit' );
															$link     = "<a href='$url'>WordPress Editor</a>";
															?>
                                                            <div class="wfacp-scodes-value-in">
                                                                <div class="wfacp_description">
                                                                    <input type="text" value="[wfacp_forms]" style="width:100%;" readonly>
                                                                </div>
                                                                <a href="javascript:void(0)" class="wfacp_copy_text"><?php _e( 'Copy' ); ?></a>
                                                            </div>
                                                            <p class="hint"><?php _e( 'Use this shortcode to embed the checkout form on this page. Switch to ' . $link . '.', 'woofunnels-aero-checkout' ) ?></p>
                                                        </div>
                                                    </div>

													<?php
													$should_show_shortcode_with_id = filter_input( INPUT_GET, 'wffn_funnel_ref', FILTER_SANITIZE_NUMBER_INT );
													if ( ! $should_show_shortcode_with_id ) {
														?>
                                                        <legend><?php _e( 'Embed Form Shortcode', 'woofunnel-aero-checkout' ) ?></legend>
                                                        <div class="wfacp-scodes-row">
                                                        <div class="wfacp-scodes-value">
                                                            <div class="wfacp-scodes-value-in">
                                                                <div class="wfacp_description">
                                                                    <input type="text" value="[wfacp_forms id='<?php echo $wfacp_id ?>']" style="width:100%;" readonly>
                                                                </div>
                                                                <a href="javascript:void(0)" class="wfacp_copy_text"><?php _e( 'Copy' ); ?></a>
                                                            </div>
                                                            <p class="hint"><?php _e( 'Use this shortcode to embed the checkout form on other page(s).', 'woofunnels-aero-checkout' ) ?></p>
                                                        </div>
                                                        </div><?php } ?>

                                                </fieldset>
                                            </div>
                                        </div>

                                        <div class="wfacp_show_design_style_fields wfacp_mixed_style_field">
                                            <vue-form-generator :schema="schema" :model="model"></vue-form-generator>
                                        </div>
                                        <div class="wfacp_show_design_style_fields wfacp_dropdown_style_field">
                                            <div class="vue-form-generator">
												<?php

												$fields_dropdown_class = [];

												$data               = WFACP_Common::get_fieldset_data( $this->wfacp_id );
												$steps              = $data['fieldsets'];
												$do_not_show_fields = WFACP_Common::get_html_excluded_field();
												foreach ( $steps as $step_key => $fieldsets ) {
													foreach ( $fieldsets as $section_key => $section_data ) {
														if ( empty( $section_data['fields'] ) ) {
															continue;
														}

														$count            = count( $section_data['fields'] );
														$html_field_count = 0;
														if ( ! empty( $section_data['html_fields'] ) ) {
															foreach ( $do_not_show_fields as $h_key ) {
																if ( isset( $section_data['html_fields'][ $h_key ] ) ) {
																	$html_field_count ++;
																}
															}
														}

														if ( $html_field_count == $count ) {
															continue;
														}
														$section_title = __( 'No Section Heading', 'woofunnels-aero-checkout' );
														if ( isset( $section_data['name'] ) && "" !== $section_data['name'] ) {
															$section_title = $section_data['name'];
														}
														?>
                                                        <fieldset class="wfacp_design_accordion">
                                                            <div class="form-group wfacp_main_design_heading  field-label" status="close">
                                                                <label for="form-style"><span><?php esc_html_e( $section_title ); ?></span></label>
                                                            </div>
															<?php
															$fields = $section_data['fields'];
															foreach ( $fields as $loop_key => $field ) {
																if ( isset( $field['type'] ) && 'wfacp_html' == $field['type'] ) {
																	continue;
																}

																if ( in_array( $loop_key, [ 'wfacp_start_divider_billing', 'wfacp_start_divider_shipping' ], true ) ) {
																	$address_key_group = ( $loop_key == 'wfacp_start_divider_billing' ) ? __( 'Billing Address', 'woocommerce' ) : __( 'Shipping Address', 'woocommerce' );
																	?>
                                                                    <div class="form-group wfacp_main_design_subheading  field-label">
                                                                        <label for="form-style"><span><?php esc_html_e( $address_key_group ); ?></span></label>
                                                                    </div>
																	<?php
																}
																if ( in_array( $loop_key, [
																	'wfacp_start_divider_billing',
																	'wfacp_start_divider_shipping',
																	'wfacp_end_divider_shipping',
																	'wfacp_end_divider_billing'
																], true ) ) {

																	continue;
																}
																$field_key = $field['id'];
																$skipKey   = [ 'billing_same_as_shipping', 'shipping_same_as_billing' ];
																if ( in_array( $field_key, $skipKey ) ) {
																	continue;
																}
																?>
                                                                <div class="form-group wfacp_design_setting field-select">
                                                                    <label for="<?php esc_html_e( 'wfacp_form_form_fields_1_embed_forms_2_' . $field['id'] ); ?>"><span><?php esc_html_e( $field['label'] ); ?></span></label>
                                                                    <div class="field-wrap">
                                                                        <select id="<?php esc_html_e( 'wfacp_form_form_fields_1_embed_forms_2_' . $field['id'] ); ?>" class="form-control" v-model="model.<?php esc_html_e( 'wfacp_form_form_fields_1_embed_forms_2_' . $field['id'] ); ?>">
                                                                            <option value='wfacp-col-full'><?php esc_html_e( 'Full', 'woofunnel-aero-checkout' ) ?></option>
                                                                            <option value='wfacp-col-left-half'><?php esc_html_e( 'One Half', 'woofunnel-aero-checkout' ) ?></option>
                                                                            <option value='wfacp-col-left-third'><?php esc_html_e( 'One Third', 'woofunnel-aero-checkout' ) ?></option>
                                                                            <option value='wfacp-col-two-third'><?php esc_html_e( 'Two Third', 'woofunnel-aero-checkout' ) ?></option>
                                                                        </select>
                                                                    </div>
                                                                </div>
																<?php
															}
															?>
                                                        </fieldset>
														<?php

													}
												}


												?>
                                            </div>
                                        </div>


                                        <div class="bwf_form_submit wfacp_tc">
                                            <div class="wfacp_clear_10"></div>
                                            <input type="submit" class="button button-primary wfacp_btn wfacp_btn_primary" value="<?php _e( 'Save', 'woofunnels-aero-checkout' ); ?>"/>
                                            <span class="wfacp_spinner spinner"></span>
                                        </div>
                                        <div class="wfacp_clear_10"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>