/*global wflp*/
/*global jQuery*/
/*global Vue*/
/*global VueFormGenerator*/
/*global wp_admin_ajax*/
/*global wffn_swal*/
/*global wflp_localization*/
/*global _*/
/*global wffn*/
(function ($) {
    'use strict';

    class wflp_design {
        constructor() {

            this.id = wflp.id;
            this.selected = wflp.selected;
            this.selected_type = wflp.selected_type;
            this.designs = wflp.designs;
            this.design_types = wflp.design_types;
            this.template_active = wflp.template_active;
            this.main();
            this.model();
            this.globalSettings();

            $("#wffn_lp_edit_vue_wrap .wf_funnel_card_switch input[type='checkbox']").click(function () {
                let wp_ajax = new wp_admin_ajax();
                let toggle_data = {
                    'toggle_state': this.checked,
                    'wflp_id': wflp.id,
                    '_nonce': wflp.nonce_toggle_state,
                };

                wp_ajax.ajax('lp_toggle_state', toggle_data);

            });
            if ($('#modal-global-settings_success').length > 0) {

                $("#modal-global-settings_success").iziModal(
                    {
                        title: wflp.texts.settings_success,
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

            var wflp_obj = this;

            /**
             * Trigger async event on plugin install success as we are executing wp native js API to update/install a plugin
             */
            $(document).on('wp-plugin-install-success', function (event, response) {
                wflp_obj.main.afterInstall(event, response);
            });
            $(document).on('wp-plugin-install-error', function (event, response) {
                wflp_obj.main.afterInstallError(event, response);
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
                if (Object.prototype.hasOwnProperty.call(wflp.global_setting_fields.fields, model)) {
                    $.extend(global_settings_global_css_fields[keyfields], wflp.global_setting_fields.fields[model]);
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
                if (Object.prototype.hasOwnProperty.call(wflp.global_setting_fields.fields, model)) {
                    $.extend(global_settings_global_script_fields[keyfields], wflp.global_setting_fields.fields[model]);
                }
            }

            return [

                {
                    legend: wflp.global_setting_fields.legends_texts.global_css,
                    fields: global_settings_global_css_fields
                },
                {
                    legend: wflp.global_setting_fields.legends_texts.global_script,
                    fields: global_settings_global_script_fields
                },
            ]
        }

        getCustomSettingsSchema() {
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
                if (Object.prototype.hasOwnProperty.call(wflp.custom_setting_fields.fields, model)) {
                    $.extend(custom_settings_custom_js_fields[keyfields], wflp.custom_setting_fields.fields[model]);
                }
            }
            let custom_settings_custom_css_fields = [{
                type: "textArea",
                label: "",
                model: "custom_css",
                inputName: 'custom_css',
            }];
            for (let keyfields in custom_settings_custom_css_fields) {
                let model = custom_settings_custom_css_fields[keyfields].model;
                if (Object.prototype.hasOwnProperty.call(wflp.custom_setting_fields.fields, model)) {
                    $.extend(custom_settings_custom_css_fields[keyfields], wflp.custom_setting_fields.fields[model]);
                }
            }

            return [
                {
                    legend: wflp.custom_setting_fields.legends_texts.custom_css,
                    fields: custom_settings_custom_css_fields
                },
                {
                    legend: wflp.custom_setting_fields.legends_texts.custom_js,
                    fields: custom_settings_custom_js_fields
                },
            ]
        }

        /**
         * Updating landing starts
         */
        get_landing_vue_fields() {
            let update_landing = [
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

            for (let keyfields in update_landing) {
                let model = update_landing[keyfields].model;
                _.extend(update_landing[keyfields], wflp.update_popups.label_texts[model]);
            }
            return update_landing;
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
                el: "#wffn_lp_edit_vue_wrap",
                components: {
                    "vue-form-generator": VueFormGenerator.component,
                },
                data: {
                    current_template_type: this.selected_type,
                    selected_type: this.selected_type,
                    designs: this.designs,
                    design_types: this.design_types,
                    selected: this.selected,
                    view_url: wflp.view_url,
                    lp_title: wflp.lp_title,
                    selected_template: this.selected_template,
                    template_active: this.template_active,
                    temp_template_type: '',
                    temp_template_slug: '',
                    model: wflp.custom_options,
                    is_importing: false,
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
                        $(".wffn_save_btn_style").addClass('disabled');
                        $('.wffn_loader_global_save').addClass('ajax_loader_show');
                        let tempSetting = JSON.stringify(this.model);
                        tempSetting = JSON.parse(tempSetting);
                        let data = {"data": tempSetting, 'landing_id': wflp.id, '_nonce': wflp.nonce_custom_settings};
                        let wp_ajax = new wp_admin_ajax();
                        wp_ajax.ajax("lp_custom_settings_update", data);
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
                        return wflp.design_template_data[this.selected_type].edit_url;
                    },
                    copy: function (event) {
                        let title = wflp.texts.copy_success;
                        if (jQuery(event.target).attr('class') === 'wffn_copy_text scode') {
                            title = wflp.texts.shortcode_copy_success;
                        }
                        var getInput = event.target.parentNode.querySelector('.wffn-scode-copy input')
                        getInput.select();
                        document.execCommand("copy");
                        $("#modal-global-settings_success").iziModal('setTitle', title);
                        $("#modal-global-settings_success").iziModal('open');
                    },
                    get_builder_title() {
                        return wflp.design_template_data[this.selected_type].title;
                    },
                    get_button_text() {
                        return wflp.design_template_data[this.selected_type].button_text;
                    },
                    setTemplateType(template_type) {
                        Vue.set(this, 'current_template_type', template_type);
                    },
                    setTemplate(selected, type, el) {
                        Vue.set(this, 'selected', selected);
                        Vue.set(this, 'selected_type', type);

                        return this.save('yes', el);
                    },

                    removeDesign(cb) {
                        let wp_ajax = new wp_admin_ajax();
                        let save_layout = {
                            'wflp_id': self.id,
                            '_nonce': wflp.nonce_remove_design,
                        };
                        wp_ajax.ajax('lp_remove_design', save_layout);

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
                    showFailedInstall(warning_text) {
                        wffn_swal({
                            'html': warning_text,
                            'title': wffn.pageBuildersTexts[this.current_template_type].install_fail,
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
                    importTemplate(template, type, current_target_element) {
                        let wp_ajax = new wp_admin_ajax();
                        let save_layout = {
                            'builder': type,
                            'template': template.slug,
                            'wflp_id': self.id,
                            '_nonce': wflp.nonce_import_design,
                        };
                        this.swalLoadiingText(wffn.i18n.importing);
                        wp_ajax.ajax('lp_import_template', save_layout);
                        wp_ajax.success = (rsp) => {
                            if (true === rsp.status) {
                                this.setTemplate(template.slug, type, current_target_element);
                            } else {
                                let parent = current_target_element.parents('.wffn_template_sec');
                                if ( parent.hasClass('wffn_template_importing') ) {
                                    parent.removeClass('wffn_template_importing');
                                }
                                $('.wffn_steps_btn_green').show();
                                setTimeout((msg) => {
                                    this.showFailedImport(msg);
                                }, 200, rsp.error);
                            }
                        };
                    },
                    GetFirstTemplateGroup() {
                        let ret = false;
                        let i = 0
                        _.each(this.design_types, function (k, v) {

                            if (i === 0) {
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
                    afterInstallError(event, response) {
                        this.showFailedInstall(response.errorMessage);
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
                            'title': wflp_localization.importer.remove_template.heading,
                            'type': 'warning',
                            'allowEscapeKey': false,
                            'showCancelButton': true,
                            'confirmButtonColor': '#0073aa',
                            'cancelButtonColor': '#e33b3b',
                            'confirmButtonText': wflp_localization.importer.remove_template.button_text,
                            'text': wflp_localization.importer.remove_template.sub_heading,
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
                    getClasses(template) {
                        let classes = '';

                        if(template.is_pro) {
                            classes += 'wffn_template_sec_design_pro';
                        }else{
                            classes += 'wffn_template_sec_design';
                        }

                        if(this.is_importing) {
                            classes += ' wffn_import_start';
                        }
                        return classes;
                    },
                    triggerImport(template, slug, type, el) {
                        this.templateOnReqest = template;
                        this.slugOnRequest = slug;
                        this.typeOnRequest = type;
                        this.is_importing = true;
                        let title = wflp_localization.importer.activate_template.heading;
                        let sub_heading = wflp_localization.importer.activate_template.sub_heading;
                        let button_text = wflp_localization.importer.activate_template.button_text;
                        if ('yes' === template.show_import_popup) {
                            title = wflp_localization.importer.add_template.heading;
                            sub_heading = wflp_localization.importer.add_template.sub_heading;
                            button_text = wflp_localization.importer.add_template.button_text;
                        }
                        /**
                         * Loop over the plugin dependency for the every page builder
                         * If we found any dependency plugin inactive Or not installed we need to hold back the import process and
                         * Alert user about missing dependency and further process to install and activate
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
                            this.is_importing = false;
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
                        let inst = this;
                        let wp_ajax = new wp_admin_ajax();
                        let save_layout = {
                            'selected_type': this.current_template_type,
                            'selected': this.selected,
                            'wflp_id': self.id,
                            'template_active': template_active,
                            '_nonce': wflp.nonce_save_design,
                        };

                        wp_ajax.ajax('lp_save_design', save_layout);
                        wp_ajax.success = (rsp) => {
                            this.selected_type = this.current_template_type;
                            this.selected_template = this.designs[this.selected_type][this.selected];
                            $('#wfacp_control > .wfacp_p20').show();
                            this.restoreButtonState(el);
                            inst.is_importing = false;

                        };
                        wp_ajax.error = () => {
                            inst.is_importing = false;
                        };

                    },
                    updateLanding: function () {
                        let landing_edit = "#wf_landing_edit_modal";
                        let parsedData = _.extend({}, wffnIZIDefault, {});
                        $(landing_edit).iziModal(
                            _.extend(
                                {
                                    onOpening: function (modal) {
                                        modal.startLoading();
                                        wffn_edit_landing_vue(modal);
                                    },
                                    onOpened: (modal) => {
                                        modal.stopLoading();
                                    },
                                    onClosed: function () {
                                        $(landing_edit).iziModal('resetContent');
                                        $(landing_edit).iziModal('destroy');
                                        $(landing_edit).iziModal('close');
                                    },
                                },
                                parsedData
                            ),
                        );
                        $(landing_edit).iziModal('open');
                    }
                },
                mounted: function () {
                    if (this.template_active === 'no') {
                        this.setTemplateType(this.GetFirstTemplateGroup());
                    }
                },
            });

            //Update landing page
            /** wffn_edit_landing_vue started  **/
            const wffn_edit_landing_vue = function (iziMod) {
                self.wffn_edit_landing_vue = new Vue(
                    {
                        mixins: [wfabtVueMixin],
                        components: {
                            "vue-form-generator": VueFormGenerator.component,
                        },
                        data: {
                            modal: false,
                            model: wflp.update_popups.values,
                            schema: {
                                fields: self.get_landing_vue_fields(),
                            },
                            formOptions: {
                                validateAfterLoad: false,
                                validateAfterChanged: true,
                            },
                            current_state: 1,
                        },
                        methods: {
                            updateLanding: function () {
                                if (false === this.$refs.update_landing_ref.validate()) {
                                    console.log('Validation Error');
                                    return;
                                }
                                iziMod.startLoading();
                                let landing_id = wflp.id;
                                let data = JSON.stringify(self.wffn_edit_landing_vue.model);

                                let wp_ajax = new wp_admin_ajax();

                                wp_ajax.ajax("update_landing_page", {"_nonce": wflp.wflp_edit_nonce, "landing_id": landing_id, 'data': data});
                                wp_ajax.success = function (rsp) {
                                    if (_.isEqual(true, rsp.status)) {

                                        $(".bwf_breadcrumb ul li:last-child").html(rsp.title);
                                        $(".bwf-header-bar .bwf-bar-navigation > span:last-child").html(rsp.title);
                                        Vue.set(self.wffn_edit_landing_vue.model, 'title', rsp.title);
                                        wflp.update_popups.label_texts.title.value = rsp.title;
                                        iziMod.stopLoading();
                                        $('#wf_landing_edit_modal').iziModal('close');
                                    }
                                };
                            }
                        },
                    }
                ).$mount('#part-update-landing');
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

        globalSettings() {
            this.globalSettings = new Vue({
                el: "#wffn_lp_settings_vue_wrap",
                components: {
                    "vue-form-generator": VueFormGenerator.component,
                },
                methods: {
                    onSubmit: function () {
                        $(".wffn_save_btn_style").addClass('disabled');
                        $('.wffn_loader_global_save').addClass('ajax_loader_show');
                        let tempSetting = JSON.stringify(this.model);
                        tempSetting = JSON.parse(tempSetting);
                        let data = {"data": tempSetting, '_nonce': wflp.nonce_global_settings};

                        let wp_ajax = new wp_admin_ajax();
                        wp_ajax.ajax("lp_global_settings_update", data);
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
                    model: wflp.options,
                    schema: {
                        groups: this.getGlobalSettingsSchema(),

                    },
                    formOptions: {
                        validateAfterLoad: false,
                        validateAfterChanged: true
                    },
                }
            });
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
        window.wflp_design = new wflp_design();
    });

})(jQuery);