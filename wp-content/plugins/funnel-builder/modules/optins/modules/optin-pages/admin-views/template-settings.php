<div class="wf_funnel_templates_outer wffn-hide" v-bind:class="'yes'===template_active?'wffn-show':''">
	<div class="wf_funnel_heading_choosen_template">
		<div class="wf_funnel_clear_20"></div>
		<?php esc_html_e( 'Optin Page Settings', 'funnel-builder' ); ?>
		<div class="wf_funnel_clear_20"></div>
	</div>

	<div class="wffn-tabs-view-vertical wffn-widget-tabs">
		<div class="wffn-tabs-wrapper wffn-tab-center">
			<div class="wffn-tab-title wffn-tab-desktop-title shortcode_tab" id="tab-title-shortcode" data-tab="0" role="tab" aria-controls="wffn-tab-shortcode">
				<?php esc_html_e( 'Shortcodes', 'funnel-builder' ); ?>
			</div>
		</div>

		<div class="wffn-tabs-content-wrapper wffn-optin-forms-container">
			<!-- Shortcode tab content -->
			<div class="wffn-opt-shortcode-tab-area wffn-tab-content" id="wffn_optin_shortcode_setting">

				<div class="wffn-optin-shortcode">
                    <fieldset v-if="`yes`===selected_template.show_shortcodes">
                        <legend><?php esc_html_e( 'Form Shortcodes', 'funnel-builder' ); ?></legend>
                    </fieldset>
					<div v-if="`yes`===selected_template.show_shortcodes" class="wffn-scodes-row">
						<h4 class="wffn-scodes-label"><?php esc_html_e( 'Optin Form Shortcode', 'funnel-builder' ); ?></h4>
						<div class="wffn-scodes-value">
							<div class="wffn-scodes-value-in">
								<span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" value="[wfop_form]" type="text"></span>
								<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a>
							</div>
						</div>

					</div>
					<div v-if="`yes`===selected_template.show_shortcodes" class="wffn-scodes-row">
						<h4 class="wffn-scodes-label"><?php esc_html_e( 'Optin Popup Link', 'funnel-builder' ); ?></h4>
						<div class="wffn-scodes-value">
                            <div class="wffn-scodes-value-in">
                                <?php if(WFFN_Common::wffn_is_funnel_pro_active()){ ?>
                                    <span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" value="<?php echo esc_attr( WFOPP_Core()->optin_pages->get_open_popup_url() ); ?>" type="text"></span>
                                    <a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a>
                                <?php }else{ ?>
                                    <span class="wffn-scode-text wffn-scode-copy"><input class="wffn_blur_text" readonly="readonly" value="<?php echo esc_attr( WFOPP_Core()->optin_pages->get_open_popup_url() ); ?>" type="text"></span>
                                    <a target="_blank" href="<?php echo esc_url( 'https://buildwoofunnels.com/exclusive-offer/' ) ?>" class="wffn_copy_text scode"><?php esc_html_e( 'Get Pro', 'funnel-builder' ); ?></a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="wf_funnel_clear_20"></div>
					</div>
                    <fieldset>
                        <legend><?php esc_html_e( 'Personalization Shortcodes', 'funnel-builder' ); ?></legend>
                    </fieldset>
					<div class="wffn-scodes-row">
						<h4 class="wffn-scodes-label"><?php esc_html_e( 'Optin ID', 'funnel-builder' ); ?></h4>
						<div class="wffn-scodes-value">
							<div class="wffn-scodes-value-in"><span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" type="text" value="[wfop_id]"></span>
								<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a></div>
						</div>
					</div>
					<div class="wffn-scodes-row">
						<h4 class="wffn-scodes-label"><?php esc_html_e( 'Optin First Name', 'funnel-builder' ); ?></h4>
						<div class="wffn-scodes-value">
							<div class="wffn-scodes-value-in"><span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" type="text" value="[wfop_first_name]"></span>
								<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a></div>
						</div>
					</div>
					<div class="wffn-scodes-row">
						<h4 class="wffn-scodes-label"><?php esc_html_e( 'Optin Last Name', 'funnel-builder' ); ?></h4>
						<div class="wffn-scodes-value">
							<div class="wffn-scodes-value-in"><span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" type="text" value="[wfop_last_name]"></span>
								<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a></div>
						</div>
					</div>
					<div class="wffn-scodes-row">
						<h4 class="wffn-scodes-label"><?php esc_html_e( 'Optin Email', 'funnel-builder' ); ?></h4>
						<div class="wffn-scodes-value">
							<div class="wffn-scodes-value-in"><span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" type="text" value="[wfop_email]"></span>
								<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a></div>
						</div>
					</div>
					<div class="wffn-scodes-row">
						<h4 class="wffn-scodes-label"><?php esc_html_e( 'Optin Phone', 'funnel-builder' ); ?></h4>
						<div class="wffn-scodes-value">
							<div class="wffn-scodes-value-in"><span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" type="text" value="[wfop_phone]"></span>
								<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a></div>
						</div>
					</div>
					<div class="wffn-scodes-row">
						<h4 class="wffn-scodes-label"><?php esc_html_e( 'Optin Custom Fields', 'funnel-builder' ); ?></h4>
						<div class="wffn-scodes-value">
							<div class="wffn-scodes-value-in"><span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" type="text" value="[wfop_custom key='Label']"></span>
								<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a></div>
						</div>
					</div>
				</div>
			</div>

			<div style="display: none" id="modal-global-settings_success" data-iziModal-icon="icon-home"></div>

		</div>

	</div>
</div>
