<div class="wf_funnel_heading_choosen_template">
	<div class="wf_funnel_clear_30"></div>
	<?php esc_html_e( 'Next Link Settings', 'funnel-builder' ); ?>
	<div class="wf_funnel_clear_30"></div>
</div>
<div id="wffn_ty_design_vue_wrap" class="wffn-tabs-view-vertical wffn-widget-tabs wffn-ty-shortcodes-tab">
	<div class="wffn-tabs-wrapper wffn-tab-center">
		<div class="wffn-tab-title wffn-tab-desktop-title additional_information_tab wffn-active" id="tab-title-additional_information" data-tab="0" role="tab" aria-controls="wffn-tab-content-additional_information">
			<?php esc_html_e( 'Next Step Link', 'funnel-builder' ); ?>
		</div>
	</div>
	<div class="wffn-tabs-content-wrapper">
		<div id="wffn_global_setting" class="wffn-ty-shortcode-tab-area wffn_forms_global_settings wffn-opt-shortcode-tab-area" style="display: block;">
			<div class="wffn-scodes-row">
				<h4 class="wffn-scodes-label"><?php esc_html_e( 'Next Step Button Link', 'funnel-builder' ); ?></h4>
				<div class="wffn-scodes-value">
					<div class="wffn-scodes-value-in">
							<span class="wffn-scode-text wffn-scode-copy">
								<input readonly="readonly" type="text" value="<?php echo esc_url(site_url().'?wffn-next-link=yes'); ?>">
							</span>
						<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a>
					</div>
				</div>
			</div>
			<?php do_action( 'wffn_add_custom_html_admin_wflp' ); ?>
		</div>
		<div style="display: none" id="modal-global-settings_success" data-iziModal-icon="icon-home"></div>
	</div>
</div>
