/*global wfoty*/
/*global jQuery*/
/*global Vue*/
/*global VueFormGenerator*/
/*global wp_admin_ajax*/
/*global wffn_swal*/
/*global wfoty_localization*/
/*global _*/
/*global wffn*/
(function ($) {
	'use strict';

	class wfoty_design {
		constructor() {

			this.id = wfoty.id;
			this.selected = wfoty.selected;
			this.selected_type = wfoty.selected_type;
			this.designs = wfoty.designs;
			this.design_types = wfoty.design_types;
			this.template_active = wfoty.template_active;
			this.main();
			this.model();

			$("#wffn_oty_edit_vue_wrap .wf_funnel_card_switch input[type='checkbox']").click(function () {
				let wp_ajax = new wp_admin_ajax();
				let toggle_data = {
					'toggle_state': this.checked,
					'wfoty_id': wfoty.id,
					'_nonce': wfoty.nonce_toggle_state,
				};

				wp_ajax.ajax('oty_toggle_state', toggle_data);

			});
			if ($('#modal-global-settings_success').length > 0) {

				$("#modal-global-settings_success").iziModal(
					{
						title: wfoty.texts.settings_success,
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

			var wfoty_obj = this;

			/**
			 * Trigger async event on plugin install success as we are execuring wp native js API to update/install a plugin
			 */
			$(document).on('wp-plugin-install-success', function (event, response) {
				wfoty_obj.main.afterInstall(event, response);
			});
		}

		getCustomSettingsSchema() {
			let custom_settings_custom_css_fields = [{
				type: "textArea",
				label: "",
				model: "custom_css",
				inputName: 'custom_css',
			}];
			for (let keyfields in custom_settings_custom_css_fields) {
				let model = custom_settings_custom_css_fields[keyfields].model;
				if (Object.prototype.hasOwnProperty.call(wfoty.custom_setting_fields.fields, model)) {
					$.extend(custom_settings_custom_css_fields[keyfields], wfoty.custom_setting_fields.fields[model]);
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
				if (Object.prototype.hasOwnProperty.call(wfoty.custom_setting_fields.fields, model)) {
					$.extend(custom_settings_custom_js_fields[keyfields], wfoty.custom_setting_fields.fields[model]);
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
						return wfoty.radio_fields;
					},
				},
				{
					type: "vueMultiSelect",
					label: "",
					model: "custom_redirect_page",
					styleClasses: ['wffn_field_space multiselect_cs'],
					required: true,
					hint: wfoty.custom_setting_fields.fields.search_hint,
					selectOptions: {
						multiple: false,
						key: "id",
						label: "name",
						onSearch: function (searchQuery) {
							let query = searchQuery;
							$('.multiselect_cs .multiselect__spinner').show();
							let no_page = wfoty.custom_options.not_found;
							if ($(".multiselect_cs .multiselect__content li.no_found").length === 0) {
								$(".multiselect_cs .multiselect__content").append('<li class="no_found"><span class="multiselect__option">' + no_page + '</span></li>');
							}
							$(".multiselect_cs .multiselect__content li.no_found").hide();

							if (query !== "" && query.length >= 3) {
								clearTimeout(self.search_timeout);
								self.search_timeout = setTimeout((query) => {
									let wp_ajax = new wp_admin_ajax();
									let product_query = {'term': query, '_nonce': wfoty.nonce_page_search};
									wp_ajax.ajax('oty_page_search', product_query);
									wp_ajax.success = (rsp) => {
										if (typeof rsp !== 'undefined' && rsp.length > 0) {
											wfoty.custom_options.pages = rsp;
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
				if (Object.prototype.hasOwnProperty.call(wfoty.custom_setting_fields.fields, model)) {
					$.extend(custom_redirect_fields[keyfields], wfoty.custom_setting_fields.fields[model]);
				}
			}

			return [
				{
					legend: wfoty.custom_setting_fields.legends_texts.custom_redirect,
					fields: custom_redirect_fields
				},
				{
					legend: wfoty.custom_setting_fields.legends_texts.custom_css,
					fields: custom_settings_custom_css_fields
				},
				{
					legend: wfoty.custom_setting_fields.legends_texts.custom_js,
					fields: custom_settings_custom_js_fields
				},
			]
		}

		/**
		 * Updating oty starts
		 */
		get_oty_vue_fields() {
			let update_oty = [
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

			for (let keyfields in update_oty) {
				let model = update_oty[keyfields].model;
				_.extend(update_oty[keyfields], wfoty.update_popups.label_texts[model]);
			}
			return update_oty;
		}

		main() {
			let self = this;
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

			if (_.isUndefined(this.selected_type)) {
				return;
			}

			this.selected_template = this.designs[this.selected_type][this.selected];

			this.main = new Vue({
				el: "#wffn_oty_edit_vue_wrap",
				components: {
					"vue-form-generator": VueFormGenerator.component,
				},
				data: {
					current_template_type: this.selected_type,
					selected_type: this.selected_type,
					designs: this.designs,
					design_types: this.design_types,
					selected: this.selected,
					view_url: wfoty.view_url,
					oty_title: wfoty.oty_title,
					selected_template: this.selected_template,
					template_active: this.template_active,
					temp_template_type: '',
					temp_template_slug: '',
					model: wfoty.custom_options,
					search_timeout: false,
					schema: {
						groups: this.getCustomSettingsSchema(),
					},
					formOptions: {
						validateAfterLoad: false,
						validateAfterChanged: true
					},
				},
				methods: {
					onSubmit: function () {
						if (_.isEqual(false, this.$refs.oty_setting.validate())) {
							console.log('validation error');
							return;
						}
						$(".wffn_save_btn_style").addClass('disabled');
						$('.wffn_loader_global_save').addClass('ajax_loader_show');
						let tempSetting = JSON.stringify(this.model);
						tempSetting = JSON.parse(tempSetting);
						let data = {"data": tempSetting, 'oty_id': wfoty.id, '_nonce': wfoty.nonce_custom_settings};

						let wp_ajax = new wp_admin_ajax();
						wp_ajax.ajax("oty_custom_settings_update", data);
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
					get_edit_link() {
						return wfoty.design_template_data[this.selected_type].edit_url;
					},
					copy: function (event) {
						let title = wfoty.texts.copy_success;
						if (jQuery(event.target).attr('class') === 'wffn_copy_text scode') {
							title = wfoty.texts.shortcode_copy_success;
						}
						var getInput = event.target.parentNode.querySelector('.wffn-scode-copy input')
						getInput.select();
						document.execCommand("copy");
						$("#modal-global-settings_success").iziModal('setTitle', title);
						$("#modal-global-settings_success").iziModal('open');
					},
					get_builder_title() {
						return wfoty.design_template_data[this.selected_type].title;
					},
					get_button_text() {
						return wfoty.design_template_data[this.selected_type].button_text;
					},
					setTemplateType(template_type) {
						Vue.set(this, 'current_template_type', template_type);
					},
					setTemplate(selected, type, el = '') {
						Vue.set(this, 'selected', selected);
						Vue.set(this, 'selected_type', type);
						this.template_active = 'yes';
						return this.save('yes', el);
					},

					removeDesign(cb) {
						let wp_ajax = new wp_admin_ajax();
						let save_layout = {
							'wfoty_id': self.id,
							'_nonce': wfoty.nonce_remove_design,
						};
						wp_ajax.ajax('oty_remove_design', save_layout);

						wp_ajax.success = (rsp) => {
							if (typeof cb == "function") {
								cb(rsp);
							}
						};
						wp_ajax.error = () => {

						};
					},
					swalLoadiingText(text) {
						if ($(".swal2-actions.swal2-loading .loading-text").length === 0) {
							$(".swal2-actions.swal2-loading").append("<div class='loading-text'></div>");

						}
						$(".swal2-actions.swal2-loading .loading-text").text(text);
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
					showFailedActivate(warning_text) {
						wffn_swal({
							'html': warning_text,
							'title': wffn.pageBuildersTexts[this.current_template_type].activate_fail,
							'type': 'warning',
							'allowEscapeKey': true,
							'showCancelButton': false,
							'confirmButtonText': wffn.pageBuildersTexts[this.current_template_type].close_btn,
						});
					},
					importTemplate(template, type, el) {
						let wp_ajax = new wp_admin_ajax();
						let save_layout = {
							'builder': type,
							'template': template.slug,
							'wfoty_id': self.id,
							'_nonce': wfoty.nonce_import_design,
						};
						this.swalLoadiingText(wffn.i18n.importing);
						wp_ajax.ajax('oty_import_template', save_layout);
						wp_ajax.success = (rsp) => {
							if (true === rsp.status) {
								this.setTemplate(template.slug, type, el);
							} else {
								setTimeout((msg) => {
									this.showFailedImport(msg);
								}, 200, rsp.error);
							}
							if (typeof cb == "function" && true === rsp.status) {
								cb(rsp);
							}
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
					maybeInstallPlugin(template, type, cb) {
						let currentObj = this;
						this.cb = cb;
						let page_builder_plugins = wffn.pageBuildersOptions[this.current_template_type].plugins;
						let pluginToInstall = 0;
						$.each(page_builder_plugins, function (index, plugin) {
							if ('install' === plugin.status) {
								pluginToInstall++;
								currentObj.swalLoadiingText(wffn.i18n.plugin_install);
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
						let currentObj = this;
						currentObj.swalLoadiingText(wffn.i18n.plugin_activate);
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
								if (!_.isEqual(false, rsp.success)) {
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
								} else {
									currentObj.restoreButtonState(currentObj.CurrenttargetElem);
									currentObj.showFailedActivate(rsp.data.message);
								}
							});
					},

					get_remove_template() {
						wffn_swal({
							'title': wfoty_localization.importer.remove_template.heading,
							'type': 'warning',
							'allowEscapeKey': false,
							'showCancelButton': true,
							'confirmButtonColor': '#0073aa',
							'cancelButtonColor': '#e33b3b',
							'confirmButtonText': wfoty_localization.importer.remove_template.button_text,
							'text': wfoty_localization.importer.remove_template.sub_heading,
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
						let title = wfoty_localization.importer.activate_template.heading;
						let sub_heading = wfoty_localization.importer.activate_template.sub_heading;
						let button_text = wfoty_localization.importer.activate_template.button_text;
						if ('yes' === template.show_import_popup) {
							title = wfoty_localization.importer.add_template.heading;
							sub_heading = wfoty_localization.importer.add_template.sub_heading;
							button_text = wfoty_localization.importer.add_template.button_text;
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
								showLoaderOnConfirm: true,
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
					save(template_active = 'yes', el = '') {
						let wp_ajax = new wp_admin_ajax();
						let save_layout = {
							'selected_type': this.current_template_type,
							'selected': this.selected,
							'wfoty_id': self.id,
							'template_active': template_active,
							'_nonce': wfoty.nonce_save_design,
						};

						wp_ajax.ajax('oty_save_design', save_layout);
						wp_ajax.success = (rsp) => {
							this.selected_type = this.current_template_type;
							this.selected_template = this.designs[this.selected_type][this.selected];
							$('#wfacp_control > .wfacp_p20').show();
							this.restoreButtonState(el);
						};
						wp_ajax.error = () => {

						};

					},
					updateOty: function () {
						let oty_edit = "#wf_oty_edit_modal";
						let parsedData = _.extend({}, wffnIZIDefault, {});
						$(oty_edit).iziModal(
							_.extend(
								{
									onOpening: function (modal) {
										modal.startLoading();
										wffn_edit_oty_vue(modal);
									},
									onOpened: (modal) => {
										modal.stopLoading();
									},
									onClosed: function () {
										$(oty_edit).iziModal('resetContent');
										$(oty_edit).iziModal('destroy');
										$(oty_edit).iziModal('close');
									},
								},
								parsedData
							),
						);
						$(oty_edit).iziModal('open');
					}
				},
				mounted: function () {
					if (this.template_active === 'no') {
						this.setTemplateType(this.GetFirstTemplateGroup());
					}
				},
			});

			//Update oty page
			/** wffn_edit_oty_vue started  **/
			const wffn_edit_oty_vue = function (iziMod) {
				self.wffn_edit_oty_vue = new Vue(
					{
						mixins: [wfabtVueMixin],
						components: {
							"vue-form-generator": VueFormGenerator.component,
						},
						data: {
							modal: false,
							model: wfoty.update_popups.values,
							schema: {
								fields: self.get_oty_vue_fields(),
							},
							formOptions: {
								validateAfterLoad: false,
								validateAfterChanged: true,
							},
							current_state: 1,
						},
						methods: {
							updateOty: function () {
								if (false === this.$refs.update_oty_ref.validate()) {
									console.log('Validation Error');
									return;
								}
								iziMod.startLoading();
								let oty_id = wfoty.id;
								let data = JSON.stringify(self.wffn_edit_oty_vue.model);

								let wp_ajax = new wp_admin_ajax();

								wp_ajax.ajax("update_oty_page", {"_nonce": wfoty.wfoty_edit_nonce, "oty_id": oty_id, 'data': data});
								wp_ajax.success = function (rsp) {
									if (_.isEqual(true, rsp.status)) {

										$(".bwf_breadcrumb ul li:last-child").html(rsp.title);
										$(".bwf-header-bar .bwf-bar-navigation > span:last-child").html(rsp.title);
										Vue.set(self.wffn_edit_oty_vue.model, 'title', rsp.title);
										wfoty.update_popups.label_texts.title.value = rsp.title;
										$('.wffn_page_title').text(rsp.title);
										iziMod.stopLoading();
										$('#wf_oty_edit_modal').iziModal('close');

									}
								};
							}
						},
					}
				).$mount('#part-update-oty');
			};


			if ($(".wffn-widget-tabs").length > 0) {
				let wfctb = $('.wffn-widget-tabs .wffn-tab-title');
				wfctb.on(
					'click', function () {
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
			return this.main;
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
						onClosed: () => {

						}
					}
				);
			}
		}

	}

	window.addEventListener('load', () => {
		window.wfoty_design = new wfoty_design();
	});

})(jQuery);