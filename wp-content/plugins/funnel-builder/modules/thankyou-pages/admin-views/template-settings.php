<div class="wf_funnel_heading_choosen_template">
	<div class="wf_funnel_clear_20"></div>
    <?php esc_html_e( 'Thank you Page Settings', 'funnel-builder' ); ?>
	<div class="wf_funnel_clear_20"></div>
</div>
<div id="wffn_ty_design_vue_wrap" class="wffn-tabs-view-vertical wffn-widget-tabs wffn-ty-shortcodes-tab">

	<div class="wffn-tabs-wrapper wffn-tab-center">
		<div class="wffn-tab-title wffn-tab-desktop-title additional_information_tab wffn-active" id="tab-title-additional_information" data-tab="0" role="tab" aria-controls="wffn-tab-content-additional_information">
            <?php esc_html_e( 'Shortcodes', 'funnel-builder' ); ?>
		</div>
		<div v-if="`yes`==selected_template.show_shortcode" class="wffn-tab-title wffn-tab-desktop-title additional_information_tab " id="tab-title-additional_information" data-tab="1" role="tab" aria-controls="wffn-tab-content-additional_information">
            <?php esc_html_e( 'Design', 'funnel-builder' ); ?>
		</div>
	</div>

	<div class="wffn-tabs-content-wrapper">
		<div class="wffn-ty-shortcode-tab-area wffn_forms_global_settings wffn-opt-shortcode-tab-area" id="wffn_global_setting">
			<div class="wffn-scodes-products">
				<div class="wfty-thankyou-shortcodes" v-if="`yes`==selected_template.show_shortcode">
					<fieldset>
						<legend><?php esc_html_e( 'Order Shortcodes', 'funnel-builder' ); ?></legend>
					</fieldset>
					<div class="wffn-scodes-row">
						<h4 class="wffn-scodes-label"><?php esc_html_e( 'Order Details', 'funnel-builder' ); ?></h4>
						<div class="wffn-scodes-value">
							<div class="wffn-scodes-value-in"><span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" value="[wfty_order_details]" type="text"></span>
								<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a></div>
						</div>
					</div>

					<div class="wffn-scodes-row">
						<h4 class="wffn-scodes-label"><?php esc_html_e( 'Customer Details', 'funnel-builder' ); ?></h4>
						<div class="wffn-scodes-value">
							<div class="wffn-scodes-value-in"><span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" type="text" value="[wfty_customer_details]"></span>
								<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a></div>
						</div>
					</div>

				</div>

				<div class="wf_funnel_clear_20"></div>
				<fieldset>
					<legend><?php esc_html_e( 'Personalization Shortcodes', 'funnel-builder' ); ?></legend>
				</fieldset>
				<div class="wffn-scodes-row">
					<h4 class="wffn-scodes-label"><?php esc_html_e( 'Customer Email', 'funnel-builder' ); ?></h4>
					<div class="wffn-scodes-value">
						<div class="wffn-scodes-value-in"><span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" type="text" value="[wfty_customer_email]"></span>
							<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a></div>
					</div>
				</div>
				<div class="wffn-scodes-row">
					<h4 class="wffn-scodes-label"><?php esc_html_e( 'Customer First Name', 'funnel-builder' ); ?></h4>
					<div class="wffn-scodes-value">
						<div class="wffn-scodes-value-in"><span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" type="text" value="[wfty_customer_first_name]"></span>
							<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a></div>
					</div>
				</div>
				<div class="wffn-scodes-row">
					<h4 class="wffn-scodes-label"><?php esc_html_e( 'Customer Last Name', 'funnel-builder' ); ?></h4>
					<div class="wffn-scodes-value">
						<div class="wffn-scodes-value-in"><span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" type="text" value="[wfty_customer_last_name]"></span>
							<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a></div>
					</div>
				</div>

				<div class="wffn-scodes-row">
					<h4 class="wffn-scodes-label"><?php esc_html_e( 'Customer Phone Number', 'funnel-builder' ); ?></h4>
					<div class="wffn-scodes-value">
						<div class="wffn-scodes-value-in"><span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" type="text" value="[wfty_customer_phone_number]"></span>
							<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a></div>
					</div>
				</div>

				<div class="wffn-scodes-row">
					<h4 class="wffn-scodes-label"><?php esc_html_e( 'Order Number', 'funnel-builder' ); ?></h4>
					<div class="wffn-scodes-value">
						<div class="wffn-scodes-value-in"><span class="wffn-scode-text wffn-scode-copy"><input readonly="readonly" type="text" value="[wfty_order_number]"></span>
							<a href="javascript:void(0)" v-on:click="copy" class="wffn_copy_text scode"><?php esc_html_e( 'Copy', 'funnel-builder' ); ?></a></div>
					</div>
				</div>
			</div>
		</div>

		<div v-if="`yes`==selected_template.show_shortcode" class="wffn-ty-shortcode-tab-area" id="wffn_global_setting">
			<form id="wffn_design_setting" class="wffn_forms_wrap">
				<fieldset class="show_fieldset">
					<vue-form-generator :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
				</fieldset>
			</form>
			<div style="display: none" id="modal-global-settings_success" data-iziModal-icon="icon-home">
			</div>
			<div class="bwf_form_button">
				<span class="wffn_loader_global_save" style="float: left;"></span>
				<button v-on:click.self="onSubmitShortCodeForm" style="float: left;" class="wffn_save_btn_style button button-primary"><?php esc_html_e( 'Save Settings', 'funnel-builder' ); ?></button>

			</div>
		</div>

	</div>

</div>