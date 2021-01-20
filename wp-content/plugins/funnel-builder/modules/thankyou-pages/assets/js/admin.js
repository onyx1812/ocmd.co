/*global wftp*/
/*global jQuery*/
/*global Vue*/
/*global VueFormGenerator*/
/*global wp_admin_ajax*/
/*global wffn_swal*/
/*global wftp_localization*/
/*global _*/
/*global wffn*/
(function ($) {
	'use strict';

	class wftp_design {
		constructor() {

			this.id = wftp.id;
			this.selected = wftp.selected;
			this.selected_type = wftp.selected_type;
			this.designs = wftp.designs;
			this.design_types = wftp.design_types;
			this.template_active = wftp.template_active;

			this.main();
			this.globalSettings();
			this.model();

			$(".wf_funnel_card_switch input[type='checkbox']").click(function () {
				let wp_ajax = new wp_admin_ajax();
				let toggle_data = {
					'toggle_state': this.checked,
					'wftp_id': wftp.id,
					'_nonce': wftp.nonce_toggle_state,
				};
				wp_ajax.ajax('tp_toggle_state', toggle_data);

			});

			if ($('#modal-global-settings_success').length > 0) {
				$("#modal-global-settings_success").iziModal(
					{
						title: wftp.texts.settings_success,
						icon: 'icon-check',
						headerColor: '#6dbe45',
						background: '#6dbe45',
						borderBottom: false,
						width: 600,
						timeout: 4000,
						timeoutProgressbar: true,
						transitionIn: 'fadeInUp',
						transitionOut: 'fadeOutDown',
						bottom: 0,
						loop: true,
						pauseOnHover: true,
						overlay: false
					}
				);
			}

			var wftp_obj = this;

			/**
			 * Trigger async event on plugin install success as we are execuring wp native js API to update/install a plugin
			 */
			$(document).on('wp-plugin-install-success', function (event, response) {
				wftp_obj.main.afterInstall(event, response);
			});

		}

		getGlobalSettingsSchema() {
			/**
			 * handling of localized label/description coming from php to form fields in vue
			 */
			let global_settings_global_css_fields = [{
				type: "textArea",
				label: "",
				model: "css",
				inputName: 'css',
			}];
			for (let keyfields in global_settings_global_css_fields) {
				let model = global_settings_global_css_fields[keyfields].model;
				if (Object.prototype.hasOwnProperty.call(wftp.global_setting_fields.fields, model)) {
					$.extend(global_settings_global_css_fields[keyfields], wftp.global_setting_fields.fields[model]);
				}
			}
			/**
			 * handling of localized label/description coming from php to form fields in vue
			 */
			let global_settings_global_script_fields = [{
				type: "textArea",
				label: "",
				model: "script",
				inputName: 'script',
			}];
			for (let keyfields in global_settings_global_script_fields) {
				let model = global_settings_global_script_fields[keyfields].model;
				if (Object.prototype.hasOwnProperty.call(wftp.global_setting_fields.fields, model)) {
					$.extend(global_settings_global_script_fields[keyfields], wftp.global_setting_fields.fields[model]);
				}
			}

			/**
			 * handling of localized label/description coming from php to form fields in vue
			 */
			let global_settings_tan_fields = [
				{
					type: "label",
					styleClasses: "bwf_wrap_custom_html_tracking_general",
					model: "custom_html_tracking_general_",
					hint: ""
				},
			];
			for (let keyfields in global_settings_tan_fields) {
				let model = global_settings_tan_fields[keyfields].model;
				if (Object.prototype.hasOwnProperty.call(wftp.global_setting_fields.fields, model)) {
					$.extend(global_settings_tan_fields[keyfields], wftp.global_setting_fields.fields[model]);
				}
			}
			return [
				{
					legend: wftp.global_setting_fields.legends_texts.tracking_n_analytics,
					fields: global_settings_tan_fields
				},
				{
					legend: wftp.global_setting_fields.legends_texts.global_css,
					fields: global_settings_global_css_fields
				},
				{
					legend: wftp.global_setting_fields.legends_texts.global_script,
					fields: global_settings_global_script_fields
				},

			];
		}

		getCustomSettingsSchema() {
			let self = this;
			let custom_settings_custom_css_fields = [{
				type: "textArea",
				label: "",
				model: "custom_css",
				inputName: 'custom_css',
			}];
			for (let keyfields in custom_settings_custom_css_fields) {
				let model = custom_settings_custom_css_fields[keyfields].model;
				if (Object.prototype.hasOwnProperty.call(wftp.custom_setting_fields.fields, model)) {
					$.extend(custom_settings_custom_css_fields[keyfields], wftp.custom_setting_fields.fields[model]);
				}
			}
			//handling of localized label/description coming from php to form fields in vue
			let custom_settings_custom_js_fields = [{
				type: "textArea",
				inputType: 'text',
				label: "",
				model: "custom_js",
				inputName: 'custom_js',
			}];
			for (let keyfields in custom_settings_custom_js_fields) {
				let model = custom_settings_custom_js_fields[keyfields].model;
				if (Object.prototype.hasOwnProperty.call(wftp.custom_setting_fields.fields, model)) {
					$.extend(custom_settings_custom_js_fields[keyfields], wftp.custom_setting_fields.fields[model]);
				}
			}

			let custom_redirect_fields = [
				{
					type: 'radios',
					label: "",
					default: false,
					model: 'custom_redirect',
					styleClasses: ['wffn_field_space'],
					values: () => {
						return wftp.radio_fields;
					},
				},
				{
					type: "vueMultiSelect",
					label: "",
					model: "custom_redirect_page",
					styleClasses: ['wffn_field_space multiselect_cs'],
					required: true,
					selectOptions: {
						multiple: false,
						key: "id",
						label: "product",
						onSearch: function (searchQuery) {
							let query = searchQuery;
							$('.multiselect_cs .multiselect__spinner').show();
							let no_page = wftp.custom_options.not_found;
							if ($(".multiselect_cs .multiselect__content li.no_found").length === 0) {
								$(".multiselect_cs .multiselect__content").append('<li class="no_found"><span class="multiselect__option">' + no_page + '</span></li>');
							}
							$(".multiselect_cs .multiselect__content li.no_found").hide();

							if (query !== "") {
								clearTimeout(self.search_timeout);
								self.search_timeout = setTimeout((query) => {
									let wp_ajax = new wp_admin_ajax();
									let product_query = {'term': query, '_nonce': wftp.nonce_page_search};
									wp_ajax.ajax('page_search', product_query);
									wp_ajax.success = (rsp) => {
										if (typeof rsp !== 'undefined' && rsp.length > 0) {
											wftp.custom_options.pages = rsp;
											$('.multiselect_cs .multiselect__spinner').hide();
										} else {
											$(".multiselect_cs .multiselect__content li:not(.multiselect__element)").hide();
											$(".multiselect_cs .multiselect__content li.no_found").show();
										}
									};
									wp_ajax.complete = () => {
										$('.multiselect_cs .multiselect__spinner').hide();
									};
								}, 800, query);
							} else {
								$('.multiselect_cs .multiselect__spinner').hide();
							}

						}
					},
					onChanged: function (model, newVal) {
						return model.custom_redirect_page = newVal
					},
					values: (model) => {
						return model.pages;
					},
					visible: (model) => {
						return (model.custom_redirect === 'true');
					}
				},
			];
			for (let keyfields in custom_redirect_fields) {
				let model = custom_redirect_fields[keyfields].model;
				if (Object.prototype.hasOwnProperty.call(wftp.custom_setting_fields.fields, model)) {
					$.extend(custom_redirect_fields[keyfields], wftp.custom_setting_fields.fields[model]);
				}
			}

			return [
				{
					legend: wftp.custom_setting_fields.legends_texts.custom_redirect,
					fields: custom_redirect_fields
				},
				{
					legend: wftp.custom_setting_fields.legends_texts.custom_css,
					fields: custom_settings_custom_css_fields
				},
				{
					legend: wftp.custom_setting_fields.legends_texts.custom_js,
					fields: custom_settings_custom_js_fields
				},
			]
		}

		getShortCodeSettingsSchema() {
			/**
			 * handling of localized label/description coming from php to form fields in vue
			 */
			let general_fields = [
				{
					type: "label",
					label: "",
					model: "main_head_gen",
					styleClasses: ['wffn_main_design_heading'],
				},
				{
					type: "label",
					label: "",
					model: "typography",
					styleClasses: ['wffn_main_design_sub_heading'],
				},
				{
					type: "select",
					label: "",
					model: "txt_fontfamily",
					inputName: 'txt_fontfamily',
					styleClasses: ['wffn_design_setting_50'],
					selectOptions: {
						hideNoneSelectedText: true,
					},
				},
				{
					type: "input",
					inputType: 'color',
					label: "",
					model: "txt_color",
					styleClasses: ['wffn_design_setting_50'],
					inputName: 'txt_color',
				},
				{
					type: "input",
					inputType: 'number',
					label: "",
					model: "txt_font_size",
					styleClasses: ['wffn_design_setting_50'],
					inputName: 'txt_font_size',
					hint: wftp.px_hint,
				},
				{
					type: "input",
					inputType: 'color',
					label: "",
					model: "head_color",
					styleClasses: ['wffn_design_setting_50'],
					inputName: 'head_color',
				},
				{
					type: "input",
					inputType: 'number',
					label: "",
					model: "head_font_size",
					styleClasses: ['wffn_design_setting_50'],
					inputName: 'head_font_size',
					hint: wftp.px_hint,
				},
				{
					type: "select",
					label: "",
					model: "head_font_weight",
					styleClasses: ['wffn_design_setting_50'],
					inputName: 'head_font_weight',
					selectOptions: {
						hideNoneSelectedText: true,
					},
				},
				{
					type: "label",
					label: "",
					model: "order_details_shortcode",
					styleClasses: ['wffn_main_design_heading'],
				},
				{
					type: 'switch',
					label: "",
					default: 'true',
					model: 'order_details_img',
					styleClasses: ['wffn_design_setting_50'],
					textOn: wftp.switch_fields.true,
					textOff: wftp.switch_fields.false,
				},
				{
					type: "label",
					label: "",
					model: "order_downloads_shortcode",
					styleClasses: ['wffn_main_design_sub_heading'],
				},
				{
					type: "input",
					inputType: 'text',
					label: "",
					model: "order_downloads_btn_text",
					styleClasses: ['wffn_design_setting_50'],
					inputName: 'order_downloads_btn_text',
				},
				{
					type: 'switch',
					label: "",
					default: 'true',
					model: 'order_downloads_show_file_downloads',
					textOn: wftp.switch_fields.true,
					textOff: wftp.switch_fields.false,
					styleClasses: ['wffn_design_setting_50'],
				},
				{
					type: 'switch',
					label: "",
					default: 'true',
					textOn: wftp.switch_fields.true,
					textOff: wftp.switch_fields.false,
					model: 'order_downloads_show_file_expiry',
					styleClasses: ['wffn_design_setting_50', 'clear_left'],
				},
				{
					type: "label",
					label: "",
					model: "customer_details_shortcode",
					styleClasses: ['wffn_main_design_heading'],
				},
				{
					type: "select",
					label: "",
					model: "layout_settings",
					styleClasses: ['wffn_design_setting_50'],
					inputName: 'layout_settings',
					selectOptions: {
						hideNoneSelectedText: true,
					},
				},
			];
			let order_details_fields = [];
			let customer_details_fields = [];
			let downloads_details_fields = [];

			for (let keyfields in general_fields) {
				let model = general_fields[keyfields].model;
				if (Object.prototype.hasOwnProperty.call(wftp.shortcode_fields.fields, model)) {
					$.extend(general_fields[keyfields], wftp.shortcode_fields.fields[model]);
				}
			}
			for (let keyfields in order_details_fields) {
				let model = order_details_fields[keyfields].model;
				if (Object.prototype.hasOwnProperty.call(wftp.shortcode_fields.fields, model)) {
					$.extend(order_details_fields[keyfields], wftp.shortcode_fields.fields[model]);
				}
			}
			for (let keyfields in customer_details_fields) {
				let model = customer_details_fields[keyfields].model;
				if (Object.prototype.hasOwnProperty.call(wftp.shortcode_fields.fields, model)) {
					$.extend(customer_details_fields[keyfields], wftp.shortcode_fields.fields[model]);
				}
			}
			for (let keyfields in downloads_details_fields) {
				let model = downloads_details_fields[keyfields].model;
				if (Object.prototype.hasOwnProperty.call(wftp.shortcode_fields.fields, model)) {
					$.extend(downloads_details_fields[keyfields], wftp.shortcode_fields.fields[model]);
				}
			}

			return [
				{
					fields: general_fields,
				},
			]
		}

		/**
		 * Updating thankyou starts
		 */
		get_thankyou_vue_fields() {
			let update_thankyou = [
				{
					type: "input",
					inputType: "text",
					label: "",
					model: "title",
					inputName: 'title',
					featured: true,
					required: true,
					placeholder: "",
					validator: ["string", "required"],
				}, {
					type: "input",
					inputType: "text",
					label: "",
					model: "slug",
					inputName: 'slug',
					featured: true,
					required: true,
					placeholder: "",
					validator: ["string", "required"],
				},];

			for (let keyfields in update_thankyou) {
				let model = update_thankyou[keyfields].model;
				_.extend(update_thankyou[keyfields], wftp.update_popups.label_texts[model]);
			}
			return update_thankyou;
		}

		main() {
			let self = this;
			if (_.isUndefined(this.selected_type)) {
				return;
			}
			this.selected_template = this.designs[this.selected_type][this.selected];

			const wffnIZIDefault = {
				headerColor: '#6dbe45',
				background: '#efefef',
				borderBottom: false,
				width: 600,
				radius: 3,
				overlayColor: 'rgba(0, 0, 0, 0.6)',
				transitionIn: 'fadeInDown',
				transitionOut: 'fadeOutDown',
				navigateArrows: false,
				history: false,

			};

			var wfabtVueMixin = {
				data: {
					is_initialized: '1',
				},
				methods: {
					decodeHtml: function (html) {
						var txt = document.createElement("textarea");
						txt.innerHTML = html;
						return txt.value;
					},
				}
			};

			Vue.component(
				'tp_custom_settings',
				{
					data: function () {
						return {
							model: wftp.custom_options,
							countries: [],
							search_timeout: false,
							isLoading: "name",
							schema: {
								groups: self.getCustomSettingsSchema(),
							},
							formOptions: {
								validateAfterLoad: false,
								validateAfterChanged: true,
							},
						}
					},
					components: {
						"vue-form-generator": VueFormGenerator.component,
					},
					template: '#tp_custom_settings_form',
					methods: {
						onSubmit: function () {
							$(".wffn_save_btn_style").addClass('disabled');
							$('.wffn_loader_global_save').addClass('ajax_loader_show');
							let tempSetting = JSON.stringify(this.model);
							tempSetting = JSON.parse(tempSetting);
							let data = {"data": tempSetting, 'thankyou_id': wftp.id, '_nonce': wftp.nonce_custom_settings};

							let wp_ajax = new wp_admin_ajax();
							wp_ajax.ajax("tp_custom_settings_update", data);
							wp_ajax.success = function (rsp) {
								if (typeof rsp === "string") {
									rsp = JSON.parse(rsp);
								}
								$('#modal-global-settings_success').iziModal('open');
								$(".wffn_save_btn_style").removeClass('disabled');
								$('.wffn_loader_global_save').removeClass('ajax_loader_show');
							};
							return false;
						},

					},
				}
			);

			this.main = new Vue({
				el: "#wffn_ty_edit_vue_wrap",
				components: {
					"vue-form-generator": VueFormGenerator.component,
				},
				methods: {
					copy: function (event) {
						let title = wftp.texts.copy_success;
						if (jQuery(event.target).attr('class') === 'wffn_copy_text scode') {
							title = wftp.texts.shortcode_copy_success;
						}
						var getInput = event.target.parentNode.querySelector('.wffn-scode-copy input')
						getInput.select();
						document.execCommand("copy");
						$("#modal-global-settings_success").iziModal('setTitle', title);
						$("#modal-global-settings_success").iziModal('open');
					},
					get_edit_link() {
						return wftp.design_template_data[this.selected_type].edit_url;
					},
					get_button_text() {
						return wftp.design_template_data[this.selected_type].button_text;
					},
					get_builder_title() {
						return wftp.design_template_data[this.selected_type].title;
					},
					setTemplateType(template_type) {
						Vue.set(this, 'current_template_type', template_type);
					},
					setTemplate(selected, type, el) {
						Vue.set(this, 'selected', selected);
						Vue.set(this, 'selected_type', type);
						this.template_active = 'yes';
						return this.save('yes', el);
					},

					removeDesign(cb) {
						let wp_ajax = new wp_admin_ajax();
						let save_layout = {
							'wftp_id': self.id,
							'_nonce': wftp.nonce_remove_design,
						};
						wp_ajax.ajax('tp_remove_design', save_layout);

						wp_ajax.success = (rsp) => {
							if (typeof cb == "function") {
								cb(rsp);
							}
						};
						wp_ajax.error = () => {

						};
					},
					GetFirstTemplateGroup() {
						let ret = false;
						let i = 0
						_.each(this.design_types, function (k, v) {

							if (i === 0) {
								console.log(v);
								ret = v;
							}
							i++;
						});
						return ret;
					},
					showFailedImport(warning_text) {
						wffn_swal({
							'html': warning_text,
							'title': wffn.pageBuildersTexts[this.current_template_type].title,
							'type': 'warning',
							'allowEscapeKey': true,
							'showCancelButton': false,
							'confirmButtonText': wffn.pageBuildersTexts[this.current_template_type].close_btn,
						});
					},
					importTemplate(template, type, current_target_element) {

						let wp_ajax = new wp_admin_ajax();
						let save_layout = {
							'builder': type,
							'template': template.slug,
							'wftp_id': self.id,
							'pro': template.pro ? template.pro : 'no',
							'_nonce': wftp.nonce_import_design,
						};
						this.swalLoadiingText(wffn.i18n.importing);
						wp_ajax.ajax('tp_import_design', save_layout);
						wp_ajax.success = (rsp) => {
							if (true === rsp.status) {
								this.setTemplate(template.slug, type, current_target_element);
							} else {
								setTimeout((msg) => {
									this.showFailedImport(msg);
								}, 200, rsp.error);
							}
						};
					},
					swalLoadiingText(text) {
						if ($(".swal2-actions.swal2-loading .loading-text").length === 0) {
							$(".swal2-actions.swal2-loading").append("<div class='loading-text'></div>");

						}
						$(".swal2-actions.swal2-loading .loading-text").text(text);
					},
					maybeInstallPlugin(template, type, cb) {

						this.cb = cb;
						let page_builder_plugins = wffn.pageBuildersOptions[this.current_template_type]['plugins'];
						let pluginToInstall = 0;
						var c = this;
						$.each(page_builder_plugins, function (index, plugin) {
							if ('install' === plugin.status) {
								pluginToInstall++;
								c.swalLoadiingText(wffn.i18n.plugin_install);
								// Add each plugin activate request in Ajax queue.
								// @see wp-admin/js/updates.js
								window.wp.updates.queue.push({
									action: 'install-plugin', // Required action.
									data: {
										slug: plugin.slug
									}
								});
							}
						});

						// Required to set queue.
						window.wp.updates.queueChecker();

						let currentObj = this;
						if (0 === pluginToInstall) {
							$.each(page_builder_plugins, function (index, plugin) {
								if ('activate' === plugin.status) {
									currentObj.activatePlugin(plugin.init);

								}
							});
						}
					},

					afterInstall(event, response) {
						let currentObj = this;
						var page_builder_plugins = wffn.pageBuildersOptions[this.current_template_type]['plugins'];

						$.each(page_builder_plugins, function (index, plugin) {
							if ('install' === plugin.status && response.slug === plugin.slug) {
								currentObj.activatePlugin(plugin.init);

							}
						});
					},
					activatePlugin(plugin_slug) {
						this.swalLoadiingText(wffn.i18n.plugin_activate);
						let currentObj = this;
						$.ajax({
							url: window.ajaxurl,
							type: 'POST',
							data: {
								action: 'wffn_activate_plugin',
								plugin_init: plugin_slug,
								_nonce: wffn.nonce_activate_plugin
							},
						})
							.done(function (rsp) {
								_.delay(function () {
									currentObj.cb();
									currentObj.importTemplate(currentObj.templateOnReqest, currentObj.typeOnRequest, currentObj.CurrenttargetElem);
									var page_builder_plugins = wffn.pageBuildersOptions[currentObj.current_template_type].plugins;
									$.each(page_builder_plugins, function (index, plugin) {
										if (plugin.init === rsp.data.init) {
											if ('install' === plugin.status || 'activate' === plugin.status) {
												wffn.pageBuildersOptions[currentObj.current_template_type].plugins[index].status = null;
											}
										}
									});
								}, 500);
							});
					},
					get_remove_template() {
						wffn_swal({
							'title': wftp_localization.importer.remove_template.heading,
							'type': 'warning',
							'allowEscapeKey': false,
							'showCancelButton': true,
							'confirmButtonColor': '#0073aa',
							'cancelButtonColor': '#e33b3b',
							'confirmButtonText': wftp_localization.importer.remove_template.button_text,
							'text': wftp_localization.importer.remove_template.sub_heading,
							'showLoaderOnConfirm': true,
							'preConfirm': () => {
								$('button.swal2-cancel.swal2-styled').css({'display': 'none'});
								return new Promise((resolve) => {
									this.removeDesign((rsp) => {
										this.template_active = 'no';
										resolve(rsp);
									});
								});
							}
						});
					},

					triggerImport(template, slug, type, el) {
						this.templateOnReqest = template;
						this.slugOnRequest = slug;
						this.typeOnRequest = type;
						let title = wftp_localization.importer.activate_template.heading;
						let sub_heading = wftp_localization.importer.activate_template.sub_heading;
						let button_text = wftp_localization.importer.activate_template.button_text;
						if ('yes' === template.show_import_popup) {
							title = wftp_localization.importer.add_template.heading;
							sub_heading = wftp_localization.importer.add_template.sub_heading;
							button_text = wftp_localization.importer.add_template.button_text;
						}

						/**
						 * Loop over the plugin dependency for the every page builder
						 * If we found any dependency plugin inactive Or not installed we need to hold back the import process and
						 * Alert user about missing dependency and futher proccess to install and activate
						 */
						var page_builder_plugins = wffn.pageBuildersOptions[this.current_template_type]['plugins'];

						var anyPluginInactive = true;

						$.each(page_builder_plugins, function (index, plugin) {

							if (anyPluginInactive) {
								if ('install' === plugin.status || 'activate' === plugin.status) {
									anyPluginInactive = false;
								}
							}
						});

						$('.wffn_steps_btn_green').hide();
						let current_target_element = $(el.target);
						current_target_element.closest('.wffn_temp_middle_align').find('.wffn_import_template').show();
						if (current_target_element.closest('.wffn_template_sec').hasClass('wffn_template_importing')) {
							console.log('Importer already running');
							return;
						}
						let parent = current_target_element.closest('.wffn_template_sec');
						parent.addClass('wffn_template_importing');
						this.CurrenttargetElem = current_target_element;

						if (false === anyPluginInactive) {
							wffn_swal({
								'title': wffn.pageBuildersTexts[this.current_template_type].title,
								'type': 'warning',
								'allowEscapeKey': false,
								'confirmButtonText': wffn.pageBuildersTexts[this.current_template_type].ButtonText,
								'showCancelButton': true,
								'cancelButtonText': wffn.pageBuildersTexts[this.current_template_type].close_btn,
								'allowOutsideClick': false,
								'cancelButtonColor': '#e33b3b',
								'html': wffn.pageBuildersTexts[this.current_template_type].text,
								'showLoaderOnConfirm': true,
								'preConfirm': () => {
									if ('no' === wffn.pageBuildersTexts[this.current_template_type].noInstall) {
										$('button.swal2-cancel.swal2-styled').css({'display': 'none'});
										return new Promise((resolve) => {
											this.maybeInstallPlugin(template, type, resolve);
										})
									}
								},
								onClose: () => {
									let self = this;
									$(document).on("click", function(event){
										if( 'swal2-cancel swal2-styled' === event.target.className){
											self.restoreButtonState(current_target_element, false);
										}
									});
								}
							});
							return;
						}
						if ('yes' === template.multistep || 'yes' === template.import) {
							this.importTemplate(template, type, current_target_element);
						} else {
							this.setTemplate(slug, type, current_target_element);
						}
					},
					restoreButtonState: function (elem, state = true) {
						let parent = elem.closest('.wffn_template_sec');
						parent.removeClass('wffn_template_importing');
						$('.wffn_steps_btn_green').show();
						if (state === true) {
							this.template_active = 'yes';
						}
					},
					save(template_active = 'yes', cb = '') {
						let wp_ajax = new wp_admin_ajax();
						let save_layout = {
							'selected_type': this.current_template_type,
							'selected': this.selected,
							'wftp_id': self.id,
							'template_active': template_active,
							'_nonce': wftp.nonce_save_design,
						};

						wp_ajax.ajax('tp_save_design', save_layout);
						wp_ajax.success = (rsp) => {
							this.selected_type = this.current_template_type;
							this.selected_template = this.designs[this.selected_type][this.selected];
							$('#wfacp_control > .wfacp_p20').show();
							if (typeof cb == "function") {
								cb(rsp);
							}
							this.scrollToTop();
						};
						wp_ajax.error = () => {

						};
					},
					updateThankyou: function () {
						let thankyou_edit = "#wf_thankyou_edit_modal";
						let parsedData = _.extend({}, wffnIZIDefault, {});
						$(thankyou_edit).iziModal(
							_.extend(
								{
									onOpening: function (modal) {
										modal.startLoading();
										wftp_edit_thankyou_vue(modal);
									},
									onOpened: (modal) => {
										modal.stopLoading();
									},
									onClosed: function () {
										//self.wffn_popups_vue.$destroy();
										$(thankyou_edit).iziModal('resetContent');
										$(thankyou_edit).iziModal('destroy');
										$(thankyou_edit).iziModal('close');
									},
								},
								parsedData
							),
						);
						$(thankyou_edit).iziModal('open');
					},
					onSubmitShortCodeForm: function () {
						$(".wffn_save_btn_style").addClass('disabled');
						$('.wffn_loader_global_save').addClass('ajax_loader_show');

						let tempSetting = JSON.stringify(this.model);
						tempSetting = JSON.parse(tempSetting);
						let data = {"data": tempSetting, '_nonce': wftp.nonce_shortcode_settings, "id": wftp.id};

						let wp_ajax = new wp_admin_ajax();
						wp_ajax.ajax("tp_shortcode_settings_update", data);
						wp_ajax.success = function (rsp) {
							if (typeof rsp === "string") {
								rsp = JSON.parse(rsp);
							}
							$('#modal-global-settings_success').iziModal('open');
							$(".wffn_save_btn_style").removeClass('disabled');
							$('.wffn_loader_global_save').removeClass('ajax_loader_show');
						};
						return false;
					},
					scrollToTop: function () {
						if (_.size($('#wffn-customizer-design')) > 0) {
							$('html, body').animate({scrollTop: $('#wffn-customizer-design').offset().top - 20}, 500);
						}
						return false;
					},
					initializeColopicker(v) {
						$('.wffn_color_pick input').wpColorPicker(
							{
								change: function (event, ui) {
									var element = event.target;
									var name = element.name;
									v.model[name] = ui.color.toString();
								},
								clear: function (event) {

									let picker = $(event.target).parent().find('input.wp-color-picker');
									if (typeof picker == 'undefined' || picker.length === 0) {
										return;
									}
									var name = picker.get(0).name;
									v.model[name] = '';
								}
							}
						);
						$(document).on('click', function () {
							if ($('.iris-picker:visible').length > 0) {
								$('.iris-picker').hide();
							}
						});
					},
				},

				mounted: function () {
					if (this.template_active === 'no') {
						this.setTemplateType(this.GetFirstTemplateGroup());
					}
					this.scrollToTop();
					var cb = this;
					this.initializeColopicker(cb)
				},
				created: function () {
					setTimeout(() => {
						$('.wfty_design_accordion').attr('status', 'close');
						$('.wfty_design_accordion').find('.form-group').not('.wfty_main_design_heading').slideUp();
					}, 200);
				},
				watch: {
					selected_template: function (newVal) {
						let v = this;
						setTimeout(function () {
							v.initializeColopicker(v);
						}, 500);

					}

				},
				data: {
					current_template_type: this.selected_type,
					selected_type: this.selected_type,
					designs: this.designs,
					design_types: this.design_types,
					selected: this.selected,
					selected_template: this.selected_template,
					template_active: this.template_active,
					temp_template_type: '',
					temp_template_slug: '',
					view_url: wftp.view_url,
					ty_title: wftp.ty_title,
					cb: null,
					model: wftp.optionsShortCode,
					schema: {
						groups: wftp.schema,
					},
					formOptions: {
						validateAfterLoad: false,
						validateAfterChanged: true
					},
				}
			});

			//Update thankyou page
			/** wftp_edit_thankyou_vue started  **/
			const wftp_edit_thankyou_vue = function (iziMod) {
				self.wftp_edit_thankyou_vue = new Vue(
					{
						mixins: [wfabtVueMixin],
						components: {
							"vue-form-generator": VueFormGenerator.component,
						},
						data: {
							modal: false,
							model: wftp.update_popups.values,
							schema: {
								fields: self.get_thankyou_vue_fields(),
							},
							formOptions: {
								validateAfterLoad: false,
								validateAfterChanged: true,
							},
							current_state: 1,
						},
						methods: {
							updateThankyou: function () {
								if (false === this.$refs.update_thankyou_ref.validate()) {
									console.log('Validation Error');
									return;
								}
								iziMod.startLoading();
								let thankyou_id = wftp.id;
								let data = JSON.stringify(self.wftp_edit_thankyou_vue.model);

								let wp_ajax = new wp_admin_ajax();

								wp_ajax.ajax("update_thankyou_page", {"_nonce": wftp.wftp_edit_nonce, "thankyou_id": thankyou_id, 'data': data});
								wp_ajax.success = function (rsp) {
									if (_.isEqual(true, rsp.status)) {

										$(".bwf_breadcrumb ul li:last-child").html(rsp.title);
										$(".bwf-header-bar .bwf-bar-navigation > span:last-child").html(rsp.title);
										Vue.set(self.wftp_edit_thankyou_vue.model, 'title', rsp.title);
										wftp.update_popups.label_texts.title.value = rsp.title;
										$('.wffn_page_title').text(rsp.title);
										iziMod.stopLoading();
										$('#wf_thankyou_edit_modal').iziModal('close');

									}
								};
							}
						},
					}
				).$mount('#part-update-thankyou');
			};

			return this.main;
		}

		globalSettings() {
			this.globalSettings = new Vue({
				el: "#wffn_ty_settings_vue_wrap",
				components: {
					"vue-form-generator": VueFormGenerator.component,
				},
				methods: {
					onSubmit: function () {
						$(".wffn_save_btn_style").addClass('disabled');
						$('.wffn_loader_global_save').addClass('ajax_loader_show');
						let tempSetting = JSON.stringify(this.model);
						tempSetting = JSON.parse(tempSetting);
						let data = {"data": tempSetting, '_nonce': wftp.nonce_global_settings};

						let wp_ajax = new wp_admin_ajax();
						wp_ajax.ajax("tp_global_settings_update", data);
						wp_ajax.success = function (rsp) {
							if (typeof rsp === "string") {
								rsp = JSON.parse(rsp);
							}
							$('#modal-global-settings_success').iziModal('open');
							$(".wffn_save_btn_style").removeClass('disabled');
							$('.wffn_loader_global_save').removeClass('ajax_loader_show');
						};
						return false;
					},
				},
				data: {
					model: wftp.options,
					schema: {
						groups: this.getGlobalSettingsSchema(),

					},
					formOptions: {
						validateAfterLoad: false,
						validateAfterChanged: true
					},
				}
			});

			if ($(".wffn-widget-tabs").length > 0) {
				let wfctb = $('.wffn-widget-tabs .wffn-tab-title');
				wfctb.on(
					'click', function (event) {
						if ($(event.target).hasClass('hide_bwf_btn')) {
							$(this).parents('.wffn-widget-tabs').addClass("hide_bwf");
						} else {
							$('.wffn-widget-tabs').removeClass("hide_bwf");
						}
						let $this = $(this).closest('.wffn-widget-tabs');
						let tabindex = $(this).attr('data-tab');

						$this.find('.wffn-tab-title').removeClass('wffn-active');

						$this.find('.wffn-tab-title[data-tab=' + tabindex + ']').addClass('wffn-active');

						$($this).find('.wffn-tab-content').removeClass('wffn-activeTab');
						$($this).find('.wffn_forms_global_settings .vue-form-generator fieldset').hide();
						$($this).find('.wffn_forms_global_settings .vue-form-generator fieldset').eq(tabindex - 1).addClass('wffn-activeTab');

						$($this).find('.wffn_forms_global_settings .vue-form-generator fieldset').eq(tabindex - 1).show();


					}
				);

				wfctb.eq(0).trigger('click');
			}
		}

		model() {
			this.show_design_model = $("#modal-show-design-template");
			if (this.show_design_model.length > 0) {
				this.show_design_model.iziModal({
						headerColor: '#6dbe45',
						background: '#efefef',
						borderBottom: false,
						width: 600,
						overlayColor: 'rgba(0, 0, 0, 0.6)',
						transitionIn: 'fadeInDown',
						transitionOut: 'fadeOutDown',
						navigateArrows: "false",
						onOpening: (modal) => {
							modal.startLoading();
						},
						onOpened: (modal) => {
							modal.stopLoading();

						},
					}
				);
			}
			$(document.body).on('click', '.wfty_design_accordion .wfty_main_design_heading', function () {
				let status = $(this).attr('status');
				$(this).parent('.wfty_design_accordion').removeClass('wfty_accordion_open');
				if ('close' === status || undefined === status) {
					$(this).parent('.wfty_design_accordion').find('.form-group').slideDown();
					$(this).parent('.wfty_design_accordion').attr('status', 'open');
					$(this).attr('status', 'open');
					$(this).parent('.wfty_design_accordion').addClass('wfty_accordion_open');
				} else if ('open' === status) {
					$(this).parent('.wfty_design_accordion').find('.form-group').not('.wfty_main_design_heading').slideUp();
					$(this).attr('status', 'close');
					$(this).parent('.wfty_design_accordion').attr('status', 'close');
				}
			});
		}


	}

	window.addEventListener('load', () => {

		window.wftp_design = new wftp_design();

	});
})(jQuery);
