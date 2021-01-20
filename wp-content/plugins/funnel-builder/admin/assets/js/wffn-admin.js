/*global wp_admin_ajax*/
/*global wffnParams*/
/*global wffn*/
/*global jQuery*/
/*global Vue*/
/*global _*/
/*global VueFormGenerator*/
/*global wffn_swal*/
Vue.config.devtools = true;
Vue.config.debug = true;


(function ($, doc, win) {
    'use strict';
    Vue.component('multiselect', window.VueMultiselect.default);
    $(window).on('load', function () {
        setTimeout(function () {
            $('.wffn_global_loader').hide();
        }, 600);
    });


    let wffnBuilderCommons = {
        hooks: {action: {}, filter: {}},
        tools: {
            /**
             * Convert destroy refresh and reconvert into in json without refrence
             * @param obj
             * @returns {*}
             */
            jsp: function (obj) {
                if (typeof obj === 'object') {
                    let doc = JSON.stringify(obj);
                    doc = JSON.parse(doc);
                    return doc;
                } else {
                    return obj;
                }
            },
        },
        addAction: function (action, callable, priority, tag) {
            this.addHook('action', action, callable, priority, tag);
        },
        addFilter: function (action, callable, priority, tag) {
            this.addHook('filter', action, callable, priority, tag);
        },
        doAction: function (action) {
            this.doHook('action', action, arguments);
        },
        applyFilters: function (action) {
            return this.doHook('filter', action, arguments);
        },
        removeAction: function (action, tag) {
            this.removeHook('action', action, tag);
        },
        removeFilter: function (action, priority, tag) {
            this.removeHook('filter', action, priority, tag);
        },
        addHook: function (hookType, action, callable, priority, tag) {
            if (undefined == this.hooks[hookType][action]) {
                this.hooks[hookType][action] = [];
            }
            var hooks = this.hooks[hookType][action];
            if (undefined == tag) {
                tag = action + '_' + hooks.length;
            }
            if (priority == undefined) {
                priority = 10;
            }

            this.hooks[hookType][action].push({tag: tag, callable: callable, priority: priority});
        },
        doHook: function (hookType, action, args) {

            // splice args from object into array and remove first index which is the hook name
            args = Array.prototype.slice.call(args, 1);
            if (undefined !== this.hooks[hookType][action]) {
                var hooks = this.hooks[hookType][action], hook;
                //sort by priority
                hooks.sort(
                    function (a, b) {
                        return a["priority"] - b["priority"]
                    }
                );
                for (var i = 0; i < hooks.length; i++) {
                    hook = hooks[i].callable;
                    if (typeof hook != 'function') {
                        hook = window[hook];
                    }
                    if ('action' === hookType) {
                        hook.apply(null, args);
                    } else {
                        args[0] = hook.apply(null, args);
                    }
                }
            }
            if ('filter' === hookType) {
                return args[0];
            }
        },
        removeHook: function (hookType, action, priority, tag) {
            if (undefined !== this.hooks[hookType][action]) {
                var hooks = this.hooks[hookType][action];
                for (var i = hooks.length - 1; i >= 0; i--) {
                    if ((undefined === tag || tag == hooks[i].tag) && (undefined === priority || priority === hooks[i].priority)) {
                        hooks.splice(i, 1);
                    }
                }
            }
        },
        editorConfig: {
            //'mediaButtons': true,
            "tinymce": {
                "theme": "modern",
                "skin": "lightgray",
                "language": "en",
                "formats": {
                    "alignleft": [{"selector": "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", "styles": {"textAlign": "left"}}, {"selector": "img,table,dl.wp-caption", "classes": "alignleft"}],
                    "aligncenter": [{"selector": "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", "styles": {"textAlign": "center"}}, {"selector": "img,table,dl.wp-caption", "classes": "aligncenter"}],
                    "alignright": [{"selector": "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", "styles": {"textAlign": "right"}}, {"selector": "img,table,dl.wp-caption", "classes": "alignright"}],
                    "strikethrough": {"inline": "del"}
                },
                "relative_urls": false,
                "remove_script_host": false,
                "convert_urls": false,
                "browser_spellcheck": true,
                "fix_list_elements": true,
                "entities": "38,amp,60,lt,62,gt",
                "entity_encoding": "raw",
                "keep_styles": false,
                "cache_suffix": "wp-mce-4800-20180716",
                "resize": "vertical",
                "menubar": false,
                "branding": false,
                "preview_styles": "font-family font-size font-weight font-style text-decoration text-transform",
                "end_container_on_empty_block": true,
                "wpeditimage_html5_captions": true,
                "wp_lang_attr": "en-US",
                "wp_keep_scroll_position": false,
                "wp_shortcut_labels": {
                    "Heading 1": "access1",
                    "Heading 2": "access2",
                    "Heading 3": "access3",
                    "Heading 4": "access4",
                    "Heading 5": "access5",
                    "Heading 6": "access6",
                    "Paragraph": "access7",
                    "Blockquote": "accessQ",
                    "Underline": "metaU",
                    "Strikethrough": "accessD",
                    "Bold": "metaB",
                    "Italic": "metaI",
                    "Code": "accessX",
                    "Align center": "accessC",
                    "Align right": "accessR",
                    "Align left": "accessL",
                    "Justify": "accessJ",
                    "Cut": "metaX",
                    "Copy": "metaC",
                    "Paste": "metaV",
                    "Select all": "metaA",
                    "Undo": "metaZ",
                    "Redo": "metaY",
                    "Bullet list": "accessU",
                    "Numbered list": "accessO",
                    "Insert/edit image": "accessM",
                    "Remove link": "accessS",
                    "Toolbar Toggle": "accessZ",
                    "Insert Read More tag": "accessT",
                    "Insert Page Break tag": "accessP",
                    "Distraction-free writing mode": "accessW",
                    "Add Media": "accessM",
                    "Keyboard Shortcuts": "accessH"
                },
                "toolbar1": "bold,italic,bullist,numlist, alignleft,aligncenter,alignright,link,forecolor",
                //"toolbar1": "formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,wp_adv,dfw",
                //"toolbar2": "strikethrough,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
                "wpautop": false,
                "indent": true,
                "elementpath": false,
                "plugins": "charmap,colorpicker,hr,lists,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wptextpattern",

            }, "quicktags": {"buttons": "strong,em,link,ul,ol,li,code"},

        }
    };

    let wffn_builder = function () {
        const self = this;
        let admin_ajax = new wp_admin_ajax();
        const flexPopElem = '#flex_all_popups';
        /****** Declaring vue objects ******/
        this.wffn_flex_vue = null;
        this.wffn_popups_vue = null;
        this.wffn_listing_vue = null;
        this.wffn_breadcrumb_vue = null;
        this.wffn_edit_funnel_vue = null;
        this.wffn_edit_landing_vue = null;

        const wffnIZIDefault = {
            headerColor: '#6dbe45',
            background: '#fff',
            borderBottom: false,
            history: false,
            overlayColor: 'rgba(0, 0, 0, 0.6)',
            navigateCaption: true,
            // padding:20,
            width: 680,
            timeoutProgressbar: true,
            radius: 10,
            bottom: 'auto',
            closeButton: true,
            pauseOnHover: false,
            overlay: true,
            transitionIn: 'fadeIn',
            navigateArrows: false,
        };

        if ($('#modal-global-settings_success').length > 0) {
            $("#modal-global-settings_success").iziModal(
                {
                    title: wffn.texts.settings_success,
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
                    overlay: false,
                    navigateArrows: false
                }
            );
        }

        var wfabtVueMixin = {
            data: {
                is_initialized: '1',
                taskFinished: false,
            },
            computed: {
                getSteps() {
                    return this.funnel_steps;
                },
                listTwo() {
                    return this.items.filter(item => item.list === 2)
                }
            },
            methods: {
                decodeHtml: function (html) {
                    var txt = document.createElement("textarea");
                    txt.innerHTML = html;
                    return txt.value;
                },
                prettyJSON: function (json) {
                    if (json) {
                        json = JSON.stringify(json, undefined, 4);
                        json = json.replace(/&/g, '&').replace(/</g, '<').replace(/>/g, '>');
                        /* eslint-disable no-useless-escape */
                        return json.replace(
                            /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
                            function (match) {
                                var cls = 'number';
                                if (/^"/.test(match)) {
                                    if (/:$/.test(match)) {
                                        cls = 'key';
                                    } else {
                                        cls = 'string';
                                    }
                                } else if (/true|false/.test(match)) {
                                    cls = 'boolean';
                                } else if (/null/.test(match)) {
                                    cls = 'null';
                                }
                                return '<span class="' + cls + '">' + match + '</span>';
                            }
                        );
                    }
                },
                initiateReorder: function () {
                    self.wffn_flex_vue.handle_reorder();
                },
                getColorByIndex: function (currentStep, allSteps) {
                    let index = this.getIndex(currentStep, allSteps);
                    if (_.isUndefined(index)) {
                        index = 0;
                    }
                    return this.getAllColorsConfig()[index];

                },
                getIndex: function (currentStep, allSteps) {
                    let foundKey = -1;
                    _.find(allSteps, function (v) {
                        foundKey++;
                        return v.id === currentStep.id
                    });
                    return foundKey;
                },
                getAllColorsConfig: function () {
                    return wffn.step_colors;
                },
                openiziPopup: function (popup_data) {
                    if ($(flexPopElem).length > 0) {
                        var parsedData = _.extend({}, wffnIZIDefault, popup_data.modal);
                        $(flexPopElem).iziModal(
                            _.extend(
                                {
                                    icon: 'icon-check',
                                    closeOnEscape: false,
                                    overlayClose: true,
                                    onOpening: function () {
                                        if (_.has(popup_data, 'onOpened')) {
                                            popup_data.onOpened.call(this, popup_data);
                                        }
                                        self.wffn_popups_vue.taskFinished = false;
                                    },
                                    onClosed: function () {
                                        $(flexPopElem).iziModal('resetContent');
                                        $(flexPopElem).iziModal('destroy');
                                        $(flexPopElem).iziModal('close');
                                    },
                                },
                                parsedData
                            ),
                        );
                        wffn_popups_vue(popup_data);
                        self.wffn_popups_vue.update(popup_data, 'new');


                    }
                }
            }
        };

        /****** Vue component registration started ******/


        /**
         * Adding new funnel_step starts here
         */
        function get_add_step_fields() {
            let add_step = [{
                type: "input",
                inputType: "text",
                label: "",
                model: "title",
                inputName: 'title',
                featured: true,
                required: true,
                placeholder: "",
                validator: ["string", "required"],
            },
                {
                    type: "label",
                    label: wffn.add_step_form.label_texts.design.label,
                    styleClasses: 'wffn_design_cont hide_web',
                    visible: (model) => {
                        return model && (!_.has(self.wffn_popups_vue.popup_data, 'is_funnel') || _.has(self.wffn_popups_vue.popup_data, 'is_funnel') && !self.wffn_popups_vue.popup_data.is_funnel)
                    }
                },
                {
                    type: "checkbox",
                    model: "existing",
                    label: wffn.add_step_form.label_texts.design.values[1].name,
                    styleClasses: 'wffn_design_lab',
                    default: false,
                    help: "",
                    visible: (model) => {
                        return model && (!_.has(self.wffn_popups_vue.popup_data, 'is_funnel') || _.has(self.wffn_popups_vue.popup_data, 'is_funnel') && !self.wffn_popups_vue.popup_data.is_funnel)
                    }
                },
                {
                    type: "vueMultiSelect",
                    label: "",
                    model: "design_name",
                    inputName: 'design_name',
                    required: true,
                    styleClasses: 'wffn_multiselect_addnew',
                    placeholder: "Type to search...",
                    visible: function (model) {
                        return model && model.existing === true;
                    },
                    selectOptions: {
                        multiSelect: false,
                        closeOnSelect: true,
                        showLabels: true,
                        searchable: true,
                        loading: false,
                        internalSearch: false,
                        optionsLimit: 5,
                        key: "id",
                        label: "name",
                        onSearch: function (searchQuery) {
                            let query = searchQuery;
                            $('.wffn_multiselect_addnew .multiselect__spinner').show();
                            let no_page = wffn.add_step_form.not_found;
                            if ($(".wffn_multiselect_addnew .multiselect__content li.no_found").length === 0) {
                                $(".wffn_multiselect_addnew .multiselect__content").append('<li class="no_found"><span class="multiselect__option">' + no_page + '</span></li>');
                            }
                            $(".wffn_multiselect_addnew .multiselect__content li.no_found").hide();

                            if (query !== "") {
                                clearTimeout(self.search_timeout);
                                self.search_timeout = setTimeout((query) => {

                                    let type = self.wffn_popups_vue.popup_data.type;
                                    admin_ajax.ajax("get_funnel_step_designs", {
                                        'type': type,
                                        'query': query,
                                        'is_substep': self.wffn_popups_vue.popup_data.is_substep,
                                        "_nonce": wffnParams.ajax_nonce_get_funnel_step_designs
                                    });
                                    admin_ajax.success = function (rsp) {
                                        if (_.isEqual(rsp.status, true) && rsp.designs.length > 0) {
                                            wffn.add_step_form.default_design_model.allDesigns = rsp.designs;
                                            $('.wffn_multiselect_addnew .multiselect__spinner').hide();
                                        } else {
                                            $(".wffn_multiselect_addnew .multiselect__content li:not(.multiselect__element)").hide();
                                            $(".wffn_multiselect_addnew .multiselect__content li.no_found").show();
                                            $('.wffn_multiselect_addnew .multiselect__spinner').hide();

                                        }
                                    };

                                }, 800, query);
                            } else {
                                $('.wffn_multiselect_addnew .multiselect__spinner').hide();
                            }

                        }
                    },
                    validator: ["required"],
                    values: function (model) {
                        return model.allDesigns;
                    },

                }];

            for (let keyfields in add_step) {
                let model = add_step[keyfields].model;
                _.extend(add_step[keyfields], wffn.add_step_form.label_texts[model]);
            }

            return window.wffnBuilderCommons.applyFilters(
                'wffn_add_step_fields',
                add_step
            )
        }

        Vue.component(
            'flex-add-step-form',
            {
                mixins: [wfabtVueMixin],
                props: ['popup'],
                data: function () {
                    return {
                        model: wffn.add_step_form.default_design_model,
                        schema: {
                            fields: get_add_step_fields()
                        },
                        formOptions: {
                            validateAfterLoad: false,
                            validateAfterChanged: true,
                        },
                    }
                },
                components: {
                    "vue-form-generator": VueFormGenerator.component,
                    Multiselect: window.VueMultiselect.default,
                },
                template: '#flex_add_step_form',
                methods: {
                    add_submit: function () {
                        if (_.isEqual(false, this.$refs.add_step_ref.validate())) {
                            console.log('validation error');
                            return;
                        }
                        /**
                         * validate Few things if require
                         */
                        let funnel_id = self.wffn_flex_vue.funnel_id;
                        let type = self.wffn_popups_vue.popup_data.type;
                        let step_id = self.wffn_popups_vue.popup_data.id;
                        let curr = this;
                        this.adding_step_popup(type);

                        /**
                         * Prepare a data to send to the AJAX
                         * @type {{}}
                         */
                        let fasp = this;
                        let step_data = {};
                        step_data = _.clone(this.model);

                        if (_.isEqual(true, self.wffn_popups_vue.popup_data.is_substep)) {
                            fasp.add_substep(funnel_id, fasp, step_data, type, step_id);
                            return;
                        }

                        if (_.isEqual(true, self.wffn_popups_vue.popup_data.is_funnel)) {
                            fasp.addFunnel(funnel_id, fasp, step_data, type, step_id);
                            return;
                        }
                        if (_.has(step_data, 'allDesigns')) {
                            delete step_data.allDesigns;
                        }

                        admin_ajax.ajax("add_step", {'funnel_id': funnel_id, 'type': type, "_nonce": wffnParams.ajax_nonce_add_step, "_data": step_data});
                        admin_ajax.success = function (rsp) {
                            if (_.isEqual(rsp.status, true)) {
                                let funnel_steps = self.wffn_flex_vue.funnel_steps;
                                //funnel_steps = JSON.parse(JSON.stringify(funnel_steps));
                                funnel_steps.push(rsp.data);
                                Vue.set(self.wffn_flex_vue, 'funnel_steps', funnel_steps);
                                fasp.step_added_popup(type, rsp);
                                curr.model = wffn.add_step_form.default_design_model;
                            }
                        };
                    },
                    adding_step_popup: function () {
                        let loader_data = {};
                        loader_data['popup_type'] = wffn.loader_popups.popup_type;
                        loader_data['img_url'] = wffn.images.readiness_loader;
                        self.wffn_popups_vue.update(loader_data);
                    },
                    step_added_popup: function (type, resp) {
                        let success_data = {};
                        success_data['title'] = wffn.success_popups[type].title;
                        success_data['popup_type'] = wffn.success_popups.popup_type;
                        success_data['icon'] = wffn.icons.success_check;
                        self.wffn_popups_vue.update(success_data);
                        if (_.isEqual(true, self.wffn_popups_vue.popup_data.is_funnel)) {
                            _.delay(
                                function () {
                                    if (_.isUndefined(resp.redirect_url)) {
                                        window.location.reload();
                                    } else {
                                        window.location.href = resp.redirect_url;
                                    }
                                },
                                1000
                            );
                        } else {
                            Vue.set(self.wffn_flex_vue, 'current_state', 'second');
                            _.delay(
                                function () {
                                    let popUpCloseFunc = _.bind(self.wffn_popups_vue.closePopup, self.wffn_popups_vue);

                                    if (self.wffn_popups_vue.taskFinished) {
                                        popUpCloseFunc();
                                    }
                                },
                                1000
                            );
                        }
                    },

                    add_substep: function (funnel_id, fasp, substep_data, type, step_id) {
                        admin_ajax.ajax("add_substep", {'funnel_id': funnel_id, 'type': type, 'step_id': step_id, "_nonce": wffnParams.ajax_nonce_add_substep, "_data": substep_data});
                        admin_ajax.success = function (rsp) {
                            if (rsp.status === true) {
                                let funnel_steps = self.wffn_flex_vue.funnel_steps;

                                let search = funnel_steps.findIndex(x => x.id === step_id);
                                let substeps = _.isUndefined(funnel_steps[search].substeps) ? {} : funnel_steps[search].substeps;
                                substeps = (_.size(substeps) < 1) ? {} : substeps;

                                let type_substeps = _.isUndefined(substeps) ? {} : _.clone(substeps);
                                type_substeps[type] = _.isUndefined(funnel_steps[search].substeps[type]) ? [] : _.clone(funnel_steps[search].substeps[type]);

                                funnel_steps[search].tags = rsp.data.tags;

                                type_substeps[type].push(rsp.data);
                                funnel_steps[search].substeps = type_substeps;
                                Vue.set(self.wffn_flex_vue, 'funnel_steps', funnel_steps);
                                fasp.step_added_popup(type, rsp);

                            }
                        };
                    },

                    addFunnel: function (funnel_id, fasp, funnel_data, type) {
                        admin_ajax.ajax("add_funnel", {'funnel_id': funnel_id, 'type': type, "_nonce": wffnParams.ajax_nonce_add_funnel, "_data": funnel_data});
                        admin_ajax.success = function (rsp) {
                            if (rsp.status === true) {
                                if (rsp.funnel_id > 0) {
                                    fasp.step_added_popup(type, rsp);
                                }
                            }
                        };
                    },
                    openChooseStep: function () {
                        let popup_data = wffn.choose_step_popup;
                        popup_data.steps = wffn.steps_data;
                        popup_data.img_url = wffn.images.check;
                        popup_data = window.wffnBuilderCommons.applyFilters('wffn_step_list_data', popup_data);
                        self.wffn_popups_vue.showLoader();
                        _.delay(
                            function () {
                                self.wffn_popups_vue.update(popup_data);
                            },
                            1000
                        );
                    }
                },
                mounted: function () {

                }
            }
        );

        Vue.component(
            'flex-arrow',
            {
                props: ['classes', 'funnel_step'],
                mixins: [wfabtVueMixin],
                methods: {
                    onExpandAndCollapse: function (funnel_step, e) {
                        let expandData = JSON.parse(JSON.stringify(self.wffn_flex_vue.expandData));
                        let $this = $(e.target);
                        if (!$this.hasClass('wffn-disabled')) {
                            if ($this.hasClass('wf_funnel_dash_active')) {
                                $this.removeClass('wf_funnel_dash_active');
                                $this.siblings('.accordion-content').slideUp(
                                    'fast',
                                    function () {
                                        if (_.isEmpty(wffn.steps_data[funnel_step.type].substeps)) {
                                            $this.siblings('.accordion-content').find('.accordion-content-inner').addClass('wffn-min-h-150');
                                        }
                                        $this.siblings('.accordion-content').find('.accordion-content-inner').html(wffn.accordion_placeholder);
                                    }
                                );
                            } else {
                                if (_.isEmpty(wffn.steps_data[funnel_step.type].substeps)) {
                                    $this.siblings('.accordion-content').find('.accordion-content-inner').addClass('wffn-min-h-150');
                                }
                                $this.addClass('wf_funnel_dash_active');
                                $this.siblings('.accordion-content').slideDown('fast');
                                if (expandData.length > 0 && !_.isUndefined(expandData[funnel_step.id]) && !_.isNull(expandData[funnel_step.id])) {
                                    $('.accordion-content.wffn_accordion_' + funnel_step.id).find('.accordion-content-inner').html(expandData[funnel_step.id]);
                                } else {
                                    admin_ajax.ajax("get_expand_info", {type: funnel_step.type, funnel_id: wffn.funnel_id, step_id: funnel_step.id, "_nonce": wffnParams.ajax_nonce_get_expand_info});
                                    admin_ajax.success = function (rsp) {
                                        if (_.isEqual(rsp.status, true)) {
                                            $('.accordion-content.wffn_accordion_' + rsp.step_id).find('.accordion-content-inner').html(rsp.data.html);
                                            expandData = JSON.parse(JSON.stringify(self.wffn_flex_vue.expandData));
                                            expandData[rsp.step_id] = rsp.data.html;
                                            Vue.set(self.wffn_flex_vue, 'expandData', expandData);
                                        }
                                    };
                                }
                            }
                        }
                    },
                    getExpandData: function (step_id, step_type) {
                        let expandData = JSON.parse(JSON.stringify(self.wffn_flex_vue.expandData));
                        if (!Object.prototype.hasOwnProperty.call(expandData, step_id)) {
                            admin_ajax.ajax("get_expand_info", {type: step_type, funnel_id: wffn.funnel_id, step_id: step_id, "_nonce": wffnParams.ajax_nonce_get_expand_info});
                            admin_ajax.success = function (rsp) {
                                if (_.isEqual(rsp.status, true) && Object.prototype.hasOwnProperty.call(rsp, 'data') && Object.prototype.hasOwnProperty.call(rsp.data, 'html')) {
                                    expandData = JSON.parse(JSON.stringify(self.wffn_flex_vue.expandData));
                                    expandData[rsp.step_id] = rsp.data.html;
                                    $('.accordion-content.wffn_accordion_' + rsp.step_id).find('.accordion-content-inner').html(rsp.data.html);
                                    Vue.set(self.wffn_flex_vue, 'expandData', expandData);
                                }
                            };
                        }
                    }
                },
                mounted: function () {
                    this.getExpandData(this.funnel_step.id, this.funnel_step.type);
                },
                template: ''
            }
        );

        Vue.component(
            'flex-flag',
            {
                props: ['label', 'label_class'],
                template: '<div class="wf_funnel_label wf_funnel_pull_left"><div v-bind:class="label_class" class="wf_funnel_btn_small">{{label}}</div></div>',
            }
        );

        Vue.component(
            'flex-accordion',
            {
                mixins: [wfabtVueMixin],
                props: ['step_data'],
                component: ['draggable'],
                data: function () {
                    return {
                        substeps: wffn.steps_data[this.step_data.type].substeps,
                        onExpandLoaded: self.wffn_flex_vue.onExpandLoaded,

                    }
                },
                created() {
                    this.$on(
                        'onExpandLoaded',
                        function (id) {
                            console.log('Event from parent component emitted', id)
                        }
                    );
                },
                template: '#flex_accordion_template',
            }
        );

        Vue.component(
            'flex-sub-step',
            {
                mixins: [wfabtVueMixin],
                props: ['step_data', 'substeps', 'substep'],
                data: function () {
                    return {
                        substep_data: wffn.substeps[this.substep],
                        sort: {
                            'nochange': true,
                            'before': [],
                            'after': [],
                        },
                    }
                },
                template: '#flex_sub_step',
                methods: {

                    onEnd: function () {
                        let funnel_steps = _.clone(self.wffn_flex_vue.funnel_steps);
                        let step_id = this.step_data.id;
                        let search = funnel_steps.findIndex(x => x.id === step_id);
                        let funnel_id = self.wffn_flex_vue.funnel_id;
                        funnel_steps[search].substeps[this.substep] = this.substeps;
                        admin_ajax.ajax(
                            "reposition_substeps",
                            {
                                "_nonce": wffnParams.ajax_nonce_reposition_substeps,
                                "funnel_id": funnel_id,
                                'step_id': step_id,
                                'type': this.substep,
                                'substeps': this.substeps
                            }
                        );
                        admin_ajax.success = function () {

                        };

                    },

                    getSubStepTitle: function (title) {
                        return title + 's';
                    },
                    addSubtep: function (step_data) {
                        let popup_data = this.substep_data;
                        popup_data.popup_type = wffn.add_step_form.popup_type;
                        popup_data.id = step_data.id;
                        popup_data.is_substep = true;
                        this.openiziPopup(popup_data);
                    },


                },
                mounted: function () {
                },

            }
        );


        Vue.component(
            'flex_popup',
            {
                props: ['popup'],
                mixins: [wfabtVueMixin],
                methods: {
                    closePopup: function () {
                        self.wffn_popups_vue.closePopup();
                    },
                },
                mounted: function () {
                    setTimeout(
                        function () {
                            $('.placeholder_img').hide();
                        }
                    );

                },
                template: '#flex_popup_template',
            }
        );



        /** wffn_flex_vue started  **/
        const wffn_flex_vue = function () {
            self.wffn_flex_vue = new Vue(
                {
                    mixins: [wfabtVueMixin],
                    el: '#wffn_flex_container_vue',
                    component: ['draggable', 'transition-group'],
                    data: {
                        current_state: "",
                        isWelcome: "",
                        funnel_id: '',
                        onExpandLoaded: false,
                        funnel_steps: [],
                        steps_data: [],
                        is_sorting: false,
                        sort: {
                            'nochange': true,
                            'before': [],
                            'after': [],
                        },
                        allDesigns: [],
                        selected_type: "",
                        designs: wffn.templates,
                        current_template_type: wffn.default_template_type,
                        design_types: wffn.templates_types,
                        currentStepsFilter: wffn.currentStepsFilter,
                        filters: wffn.filters,
                        cb: null,
                        expandCount: 1,
                        expandData: [],
                        importer_running: false,
                        drag: false
                    },
                    methods: {

                        openChooseStep: function () {
                            if (_.isEqual(this.is_sorting, true)) {
                                return false;
                            }

                            let popup_data = wffn.choose_step_popup;

                            popup_data.steps = wffn.steps_data;
                            popup_data.img_url = wffn.images.check;
                            popup_data = window.wffnBuilderCommons.applyFilters('wffn_step_list_data', popup_data);
                            this.openiziPopup(popup_data);
                        }
                        ,
                        openTemplateUI: function () {
                            this.current_state = `template`;
                        }
                        ,
                        get_button_text() {
                            return wffn.design_template_data[this.selected_type].button_text;
                        }
                        ,
                        setTemplateType(template_type) {

                            Vue.set(this, 'current_template_type', template_type);

                        }
                        ,
                        setTemplate(selected, type, funnel_id = 0, el) {
                            Vue.set(this, 'selected', selected);
                            Vue.set(this, 'selected_type', type);
                            this.template_active = 'yes';
                            this.importer_running = false;
                            return this.save(funnel_id, el);
                        }
                        ,

                        removeDesign(cb) {
                            let wp_ajax = new wp_admin_ajax();
                            let save_layout = {
                                'wffn_id': self.id,
                                '_nonce': wffn.nonce_remove_design,
                            };
                            wp_ajax.ajax('tp_remove_design', save_layout);

                            wp_ajax.success = (rsp) => {
                                if (typeof cb == "function") {
                                    cb(rsp);
                                }
                            };
                            wp_ajax.error = () => {

                            };
                        }
                        ,
                        showFailedImport(warning_text) {
                            wffn_swal({
                                'html': warning_text,
                                'title': wffn.pageBuildersTexts[this.current_template_type].title,
                                'type': 'warning',
                                'allowEscapeKey': true,
                                'showCancelButton': false,
                                'confirmButtonText': wffn.pageBuildersTexts[this.current_template_type].close_btn,
                            });
                        }
                        ,
                        importTemplate(template, type, el) {
                            let wp_ajax = new wp_admin_ajax();
                            let funnel_name = $('#new_funnel_name').val();
                            let save_layout = {
                                'builder': type,
                                'template': template.slug,
                                'funnel_name': funnel_name,
                                'funnel_id': wffn.funnel_id,
                                '_nonce': wffn.nonce_import_design,
                            };
                            this.buttonLoadingText(wffn.i18n.importing, el);
                            wp_ajax.ajax('import_design', save_layout);
                            wp_ajax.success = (rsp) => {
                                if (true === rsp.success) {
                                    self.wffn_flex_vue.funnel_id = rsp.data.funnel_id;
                                    Vue.set(self.wffn_flex_vue, 'funnel_id', rsp.data.funnel_id);
                                    this.funnel_id = rsp.data.funnel_id;
                                    this.setTemplate(template.slug, type, rsp.data.funnel_id, el);
                                } else {
                                    setTimeout((msg) => {
                                        this.restoreButtonsState(el);
                                        this.showFailedImport(msg);
                                    }, 200, rsp.data.msg);
                                }
                            };
                        }
                        ,
                        swalLoadiingText(text) {
                            if ($(".swal2-actions.swal2-loading .loading-text").length === 0) {
                                $(".swal2-actions.swal2-loading").append("<div class='loading-text'></div>");

                            }
                            $(".swal2-actions.swal2-loading .loading-text").text(text);
                        },
                        buttonLoadingText(text, elem) {

                            $(elem).find(".wffn_importing_text").eq(0).html(text);

                        }
                        ,
                        maybeInstallPlugin(template, type, cb) {

                            this.cb = cb;
                            let page_builder_plugins = wffn.pageBuildersOptions[this.current_template_type]['plugins'];
                            let pluginToInstall = 0;
                            var c = this;
                            $.each(
                                page_builder_plugins,
                                function (index, plugin) {
                                    if ('install' === plugin.status) {
                                        pluginToInstall++;
                                        // Add each plugin activate request in Ajax queue.
                                        // @see wp-admin/js/updates.js
                                        c.swalLoadiingText(wffn.i18n.plugin_install);
                                        window.wp.updates.queue.push(
                                            {
                                                action: 'install-plugin', // Required action.
                                                data: {
                                                    slug: plugin.slug
                                                }
                                            }
                                        );
                                    }
                                }
                            );

                            // Required to set queue.
                            window.wp.updates.queueChecker();

                            let currentObj = this;
                            if (0 === pluginToInstall) {
                                $.each(
                                    page_builder_plugins,
                                    function (index, plugin) {
                                        if ('activate' === plugin.status) {
                                            currentObj.activatePlugin(plugin.init);
                                        }
                                    }
                                );
                            }
                        }
                        ,

                        afterInstall(event, response) {
                            let currentObj = this;
                            var page_builder_plugins = wffn.pageBuildersOptions[this.current_template_type]['plugins'];

                            $.each(
                                page_builder_plugins,
                                function (index, plugin) {
                                    if ('install' === plugin.status && response.slug === plugin.slug) {
                                        currentObj.activatePlugin(plugin.init);

                                    }
                                }
                            );
                        },
                        afterInstallError(event, response) {
                            var page_builder_plugins = wffn.pageBuildersOptions[this.current_template_type]['plugins'];
                            this.showFailedInstall(response, page_builder_plugins);
                        },
                        showFailedInstall(response, page_builder_plugins) {
                            wffn_swal(
                                {
                                    'title': wffn.pageBuildersTexts[page_builder_plugins[0].slug].install_fail,
                                    'type': 'warning',
                                    'allowEscapeKey': false,
                                    'confirmButtonText': wffn.pageBuildersTexts[page_builder_plugins[0].slug].close_btn,
                                    'html': wffn.pageBuildersTexts[page_builder_plugins[0].slug].server_error,
                                    'showLoaderOnConfirm': true,
                                }
                            );
                        },
                        activatePlugin(plugin_slug) {
                            this.swalLoadiingText(wffn.i18n.plugin_activate);
                            let currentObj = this;
                            $.ajax(
                                {
                                    url: window.ajaxurl,
                                    type: 'POST',
                                    data: {
                                        action: 'wffn_activate_plugin',
                                        plugin_init: plugin_slug,
                                        _nonce: wffn.nonce_activate_plugin
                                    },
                                }
                            )
                                .done(
                                    function () {
                                        _.delay(function () {
                                            currentObj.cb();
                                            currentObj.importTemplate(currentObj.templateOnReqest, currentObj.typeOnRequest, currentObj.CurrenttargetElem)

                                        }, 500);
                                    }
                                );
                        }
                        ,
                        get_remove_template() {
                            wffn_swal(
                                {
                                    'title': wffn.importer.remove_template.heading,
                                    'type': 'warning',
                                    'allowEscapeKey': false,
                                    'confirmButtonText': wffn.importer.remove_template.button_text,
                                    'text': wffn.importer.remove_template.sub_heading,
                                    'showLoaderOnConfirm': true,
                                    'preConfirm': () => {
                                        $('button.swal2-cancel.swal2-styled').css({'display': 'none'});
                                        return new Promise(
                                            (resolve) => {
                                                this.removeDesign(
                                                    (rsp) => {
                                                        this.template_active = 'no';
                                                        resolve(rsp);
                                                    }
                                                );
                                            }
                                        );
                                    }
                                }
                            );

                        }
                        ,
                        isFirstTemplateGroup(group) {
                            let ret = false;
                            let i = 0;
                            _.each(
                                this.design_types,
                                function (k, v) {

                                    if (v === group && 0 === i) {
                                        ret = true;
                                        return true;
                                    }
                                    i++;
                                }
                            );
                            return ret;
                        }
                        ,
                        getRibbonName(template) {
                            if ('yes' === template.pro) {
                                return wffn.i18n.ribbons.pro;
                            } else {
                                return wffn.i18n.ribbons.lite;
                            }
                        },
                        triggerImport(template, slug, type, el) {
                            this.templateOnReqest = template;
                            this.slugOnRequest = slug;
                            this.typeOnRequest = type;
                            if (true == this.importer_running) {
                                console.log('Importer already running');
                                return ;
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
                                    'showCancelButton': true,
                                    'confirmButtonText': wffn.pageBuildersTexts[this.current_template_type].ButtonText,
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
                                                self.restoreButtonsState(current_target_element, false);
                                            }
                                        });
                                    }
                                });
                                return;
                            }


                            this.importer_running = true;


                            if ('yes' === template.multistep || 'yes' === template.import) {
                                this.importTemplate(template, type, current_target_element);
                            } else {
                                this.setTemplate(slug, type, current_target_element);
                            }
                        },
                        restoreButtonsState: function (current_target_element) {
                            let parent = current_target_element.closest('.wffn_template_sec');
                            parent.removeClass('wffn_template_importing');
                            $('.wffn_steps_btn_green').show();

                        },
                        CheckTemplateStatus(funnel_id, el) {
                            let wp_ajax = new wp_admin_ajax();
                            this.funnel_id = (_.isUndefined(this.funnel_id) || this.funnel_id < 1) ? funnel_id : this.funnel_id;
                            let save_layout = {
                                'wffn_id': this.funnel_id,
                                '_nonce': wffn.nonce_get_import_status,
                            };
                            var curr = this;
                            wp_ajax.ajax('get_import_status', save_layout);
                            wp_ajax.success = (rsp) => {

                                if (true === rsp.success) {
                                    window.location = rsp.data.redirect;
                                } else {

                                    _.delay(
                                        function () {
                                            curr.CheckTemplateStatus(curr.funnel_id);
                                        },
                                        2000
                                    );
                                }
                            };
                        },
                        save(funnel_id, el) {
                            this.CheckTemplateStatus(funnel_id, el);
                        },

                        GetFirstTemplateGroup() {
                            let ret = false;
                            let i = 0
                            _.each(
                                this.design_types,
                                function (k, v) {
                                    if (i === 0) {
                                        ret = v;
                                    }
                                    i++;
                                }
                            );
                            return ret;
                        }
                        ,
                        createStarter(link, evnt) {
                            if ('disabled' === evnt.target.className) {
                                return;
                            }
                            if (true == this.importer_running) {
                                console.log('Importer already running');
                                return ;
                            }
                            evnt.target.className = 'disabled';
                            evnt.target.style.cursor = 'default';

                            if (!_.isUndefined(link)) {
                                $(".wffn_build_from_scratch .wffn_temp_middle_align .wffn_p.witha a").html(wffn.i18n.importing);
                                let funnel_name = $('#new_funnel_name').val();
                                link = link + '&funnel_name=' + funnel_name;
                                if (wffn.funnel_id > 0) {
                                    link = link + '&funnel_id=' + wffn.funnel_id;
                                }
                                this.importer_running=true;
                                window.location = link;
                            }
                        },

                        checkInArray: function (group, value) {
                            return _.contains(group, value);
                        },

                    }
                    ,

                    watch: {
                        funnel_steps: function (newVal) {
                            if (newVal.length > 0 || false === this.isWelcome) {
                                $('.wffn-sec-head_wrap .bwf_menu_list_primary').removeClass('wffn-hide');
                            } else {
                                $('.wffn-sec-head_wrap .bwf_menu_list_primary').addClass('wffn-hide');
                            }
                        }
                        ,
                    }
                    ,
                    computed: {
                        // a computed getter
                        has_steps: function () {
                            // `this` points to the vm instance
                            return this.funnel_steps.length > 0
                        }
                    }
                    ,
                    mounted: function () {
                        this.funnel_id = wffn.funnel_id;
                        this.current_state = wffn.current_state;
                        this.funnel_steps = wffn.funnel_steps;
                        this.steps_data = wffn.steps_data;
                        this.isWelcome = wffn.isWelcome;
                        if (this.template_active === 'no') {
                            this.setTemplateType(this.GetFirstTemplateGroup());
                        }
                        if (0 == this.funnel_id) {
                            _.delay(
                                function () {
                                    $('#new_funnel_name').focus();
                                },
                                1000
                            )
                        }
                    }
                    ,
                }
            )
            ;
        };
        wffn_flex_vue();
        /** wffn_flex_vue ends  **/

        /** wffn_popups_vue started  **/
        const wffn_popups_vue = function () {

            self.wffn_popups_vue = new Vue(
                {
                    mixins: [wfabtVueMixin],
                    data: {
                        popup_data: {},
                        'defaultProps': {
                            'title': '',
                            'subtitle': '',
                            'icon': '',
                            'html': '',
                            'type': '',
                            'popup_type': '',
                            'img_url': '',
                            'popup_title': '',
                            'height': 400,
                            'onOpened': function () {
                            },
                        },
                        sustainPopUpData: {}
                    },
                    methods: {
                        update: function (data, isNew) {
                            let parsedData = _.extend({}, this.defaultProps, data);
                            _.each(
                                parsedData,
                                function (element, index) {
                                    Vue.set(self.wffn_popups_vue.popup_data, index, parsedData[index]);
                                }
                            );
                            if (parsedData['popup_type'] === 'info') {
                                self.wffn_popups_vue.taskFinished = true;
                            }

                            if (typeof isNew !== "undefined" && isNew === 'new') {
                                $(flexPopElem).iziModal('open');
                            }
                        },
                        showLoader: function () {
                            this.sustainPopUpData = this.popup_data;
                            let newConfig = {
                                popup_type: 'loader',
                                img_url: wffn.images.readiness_loader,
                            }
                            this.update(newConfig);
                        },
                        hideLoader() {
                            this.update(this.sustainPopUpData);
                        },
                        closePopup: function () {
                            $(flexPopElem).iziModal('resetContent');
                            $(flexPopElem).iziModal('close');
                        },
                        deleteStep: function (popup) {
                            if (_.isEqual(true, popup.is_substep)) {
                                this.deleteSubstep(popup);
                                return;
                            }
                            let step_id = popup.id;
                            let flxpp = this;
                            let type = popup.type;
                            let funnel_id = self.wffn_flex_vue.funnel_id;

                            if (_.isEqual(true, popup.is_funnel)) {
                                this.deleteFunnel(flxpp, step_id, type);
                                return;
                            }

                            let delete_data = {};
                            delete_data['img_url'] = wffn.images.readiness_loader;
                            delete_data['popup_type'] = wffn.loader_popups.popup_type;
                            self.wffn_popups_vue.update(delete_data);

                            admin_ajax.ajax("delete_step", {'funnel_id': funnel_id, 'type': type, "_nonce": wffnParams.ajax_nonce_delete_step, "step_id": step_id});
                            admin_ajax.success = function (rsp) {
                                if (_.isEqual(rsp.status, true)) {
                                    _.delay(
                                        function () {
                                            let funnel_steps = self.wffn_flex_vue.funnel_steps;
                                            //funnel_steps = JSON.parse(JSON.stringify(funnel_steps));

                                            let search = funnel_steps.findIndex(x => x.id === step_id);
                                            funnel_steps.splice(search, 1);
                                            Vue.set(self.wffn_flex_vue, 'funnel_steps', funnel_steps);
                                            if (_.isEqual(_.size(funnel_steps), 0)) {
                                                Vue.set(self.wffn_flex_vue, 'isWelcome', false);
                                                Vue.set(self.wffn_flex_vue, 'current_state', 'first');
                                            }
                                            flxpp.deleted(type);
                                        },
                                        1000
                                    );
                                }
                            };
                        },
                        deleted: function (type) {
                            let success_data = {};
                            success_data['title'] = wffn.success_popups[type].deleted;
                            success_data['popup_type'] = wffn.success_popups.popup_type;
                            success_data['subtitle'] = wffn.success_popups.subtitle;
                            success_data['icon'] = wffn.icons.success_check;
                            self.wffn_popups_vue.update(success_data);
                            if (_.isEqual(true, self.wffn_popups_vue.popup_data.is_funnel)) {
                                _.delay(
                                    function () {
                                        window.location.reload();
                                    },
                                    1000
                                )
                            }
                            _.delay(
                                function () {
                                    if (self.wffn_popups_vue.taskFinished) {
                                        self.wffn_popups_vue.closePopup();
                                    }
                                },
                                1000
                            );
                        },
                        deleteSubstep: function (popup_data) {
                            let step_id = popup_data.step_id;
                            let substep_id = popup_data.id;
                            let substep = popup_data.substep;
                            let funnel_id = self.wffn_flex_vue.funnel_id;
                            let flxpp = this;

                            let delete_data = {};
                            delete_data['img_url'] = wffn.images.readiness_loader;
                            delete_data['popup_type'] = wffn.loader_popups.popup_type;
                            delete_data['subtitle'] = wffn.loader_popups.subtitle;
                            self.wffn_popups_vue.update(delete_data);

                            admin_ajax.ajax("delete_substep", {
                                'funnel_id': funnel_id,
                                'substep': substep,
                                "_nonce": wffnParams.ajax_nonce_delete_substep,
                                "step_id": step_id,
                                "substep_id": substep_id
                            });
                            admin_ajax.success = function (rsp) {
                                if (_.isEqual(rsp.status, true)) {
                                    let funnel_steps = self.wffn_flex_vue.funnel_steps;
                                    //funnel_steps = JSON.parse(JSON.stringify(funnel_steps));

                                    let search = funnel_steps.findIndex(x => x.id === step_id);
                                    let substeps = funnel_steps[search].substeps[substep];
                                    let subsearch = substeps.findIndex(x => x.id === substep_id);

                                    funnel_steps[search].tags = rsp.tags;
                                    funnel_steps[search].substeps[substep].splice(subsearch, 1);
                                    flxpp.deleted(substep);
                                    _.delay(
                                        function () {
                                            Vue.set(self.wffn_flex_vue, 'funnel_steps', funnel_steps);
                                        },
                                        1000
                                    );
                                }
                            };
                        },
                    },

                }
            ).$mount('#wffn_all_popups_vue');
        };
        /** wffn_popups_vue ends  **/

        /** wffn_listing_vue started  **/
        const wffn_listing_vue = function () {
            self.wffn_listing_vue = new Vue(
                {
                    mixins: [wfabtVueMixin],
                    el: '#wffn_flex_listing_vue',
                    methods: {
                        add_funnel: function () {
                            let popup_data = wffn.flexes.funnel_data;
                            popup_data.submit_btn = wffn.add_step_form.submit_btn;
                            popup_data.popup_type = wffn.add_step_form.popup_type;
                            this.openiziPopup(popup_data);
                        },
                        deleteFunnel(funnel_id) {
                            let delete_data = wffn.delete_popup;
                            delete_data['id'] = funnel_id;
                            delete_data['icon'] = wffn.icons.delete_alert;
                            delete_data['popup_type'] = wffn.delete_popup.popup_type;
                            delete_data['subtitle'] = wffn.delete_popup.funnel.subtitle;
                            delete_data['is_funnel'] = wffn.delete_popup.funnel.is_funnel;
                            delete_data['cta_call'] = this.deleteFunnelProcess;
                            delete_data['cta_class'] = 'wf_funnel_btn_success';
                            delete_data['title'] = wffn.delete_popup.funnel.title;
                            delete_data['type'] = wffn.delete_popup.funnel.type;
                            this.openiziPopup(delete_data);
                        },
                        deleteFunnelProcess: function (funnelData) {
                            let delete_data = {};
                            delete_data['img_url'] = wffn.images.readiness_loader;
                            delete_data['popup_type'] = wffn.loader_popups.popup_type;
                            self.wffn_popups_vue.update(delete_data);

                            admin_ajax.ajax("delete_funnel", {"_nonce": wffnParams.ajax_nonce_delete_funnel, "funnel_id": funnelData.id});
                            admin_ajax.success = function (rsp) {
                                if (_.isEqual(rsp.status, true)) {
                                    _.delay(
                                        function () {
                                            let success_data = {};
                                            success_data['title'] = wffn.success_popups.funnel_deleted;
                                            success_data['popup_type'] = wffn.success_popups.popup_type;
                                            success_data['subtitle'] = wffn.success_popups.subtitle;
                                            success_data['icon'] = wffn.icons.success_check;
                                            self.wffn_popups_vue.update(success_data);
                                            _.delay(
                                                function () {
                                                    window.location.reload();
                                                },
                                                1000
                                            )
                                        },
                                        1000
                                    );
                                }
                            };
                        },
                        duplicateFunnel(funnel_id) {
                            let duplicate_funnel_data = wffn.duplicate_funnel_popup;
                            duplicate_funnel_data['id'] = funnel_id;
                            duplicate_funnel_data['icon'] = wffn.icons.delete_alert;
                            duplicate_funnel_data['popup_type'] = wffn.duplicate_funnel_popup.popup_type;
                            duplicate_funnel_data['subtitle'] = wffn.duplicate_funnel_popup.funnel.subtitle;
                            duplicate_funnel_data['cta_call'] = this.duplicateFunnelProcess;
                            duplicate_funnel_data['cta_class'] = 'wf_funnel_btn_success';
                            duplicate_funnel_data['title'] = wffn.duplicate_funnel_popup.funnel.title;
                            duplicate_funnel_data['type'] = wffn.duplicate_funnel_popup.funnel.type;
                            duplicate_funnel_data['onOpened'] = this.duplicateFunnelProcess;
                            this.openiziPopup(duplicate_funnel_data);

                        },
                        duplicateFunnelProcess: function (funnelData) {
                            let duplicate_funnel_data = {};
                            duplicate_funnel_data['img_url'] = wffn.images.readiness_loader;
                            duplicate_funnel_data['popup_type'] = wffn.loader_popups.popup_type;
                            self.wffn_popups_vue.update(duplicate_funnel_data);

                            admin_ajax.ajax("duplicate_funnel", {"_nonce": wffnParams.ajax_nonce_duplicate_funnel, "funnel_id": funnelData.id});
                            admin_ajax.success = function (rsp) {
                                if (_.isEqual(rsp.status, true)) {
                                    _.delay(
                                        function () {
                                            let success_data = {};
                                            success_data['title'] = wffn.success_popups.funnel_duplicated;
                                            success_data['popup_type'] = wffn.success_popups.popup_type;
                                            success_data['subtitle'] = wffn.success_popups.subtitle;
                                            success_data['icon'] = wffn.icons.success_check;
                                            self.wffn_popups_vue.update(success_data);
                                            _.delay(
                                                function () {
                                                    window.location.reload();
                                                },
                                                1000
                                            )

                                            _.delay(
                                                function () {
                                                    if (self.wffn_popups_vue.taskFinished) {
                                                        self.wffn_popups_vue.closePopup();
                                                    }
                                                },
                                                1000
                                            );
                                        },
                                        1000
                                    );
                                }
                            };
                        },
                    },
                }
            );
        };
        wffn_listing_vue();

        /** wffn_listing_vue ends  **/

        /**
         * Updating funnel starts
         */
        function get_funnel_vue_fields() {
            let update_funnel = [
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
                    type: "textArea",
                    label: "",
                    model: "desc",
                    inputName: 'desc',
                    featured: true,
                    rows: 3,
                    placeholder: ""
                },];

            for (let keyfields in update_funnel) {
                let model = update_funnel[keyfields].model;
                _.extend(update_funnel[keyfields], wffn.update_funnel.label_texts[model]);
            }
            return update_funnel;
        }

        if (wffn.funnel_id !== 0) {
            /** wffn_breadcrumb_vue started  **/
            const wffn_breadcrumb_vue = function () {
                self.wffn_breadcrumb_vue = new Vue(
                    {
                        'el': '#wffn_breadcrumb_container_vue',
                        mixins: [wfabtVueMixin],
                        data: {
                            title: wffn.update_funnel.label_texts.title.value,
                            bulkDisabled: []
                        },
                        methods: {
                            getViewLink: function () {
                                return (false === _.isUndefined(self.wffn_flex_vue.funnel_steps[0])) ? self.wffn_flex_vue.funnel_steps[0]['_data']['view'] : false;
                            },
                            switchRecurstive: function (status) {
                                let steps = self.wffn_flex_vue.funnel_steps;
                                let current = this;
                                if (0 == status) {

                                    _.each(
                                        self.wffn_flex_vue.funnel_steps,
                                        function (elem, index) {
                                            if (1 == elem._data.status) {
                                                current.bulkDisabled.push(index);
                                                elem._data.status = "0";
                                                steps[index] = elem;
                                            }
                                        }
                                    );

                                } else {
                                    _.each(
                                        self.wffn_flex_vue.funnel_steps,
                                        function (elem, index) {
                                            if (-1 !== current.bulkDisabled.indexOf(index)) {
                                                elem._data.status = "1";
                                                steps[index] = elem;
                                            }
                                        }
                                    );
                                    current.bulkDisabled = [];
                                }

                                Vue.set(self.wffn_flex_vue, 'funnel_steps', steps);
                            },
                            updateFunnel: function () {
                                let funnel_edit = $("#wf_funnel_edit_modal");
                                let parsedData = _.extend({}, wffnIZIDefault, {});
                                $(funnel_edit).iziModal(
                                    _.extend(
                                        {
                                            onOpening: function (modal) {
                                                modal.startLoading();
                                                wffn_edit_funnel_vue(modal);
                                            },
                                            onOpened: (modal) => {
                                                modal.stopLoading();
                                            },
                                            onClosed: function () {
                                                //self.wffn_popups_vue.$destroy();
                                                $(funnel_edit).iziModal('resetContent');
                                                $(funnel_edit).iziModal('destroy');
                                                $(funnel_edit).iziModal('close');
                                            },
                                        },
                                        parsedData
                                    ),
                                );
                                $(funnel_edit).iziModal('open');
                            },
                        },
                        mounted: function () {
                            $('.bwf_breadcrumb').removeClass('wffn-hide');
                        }
                    }
                );
            };
            wffn_breadcrumb_vue();
        }

        $(document).on('wp-plugin-install-success', function (event, response) {
            if ($("#wffn_flex_container_vue").length > 0) {
                self.wffn_flex_vue.afterInstall(event, response);
            }
        });

        $(document).on('wp-plugin-install-error', function (event, response) {
            if ($("#wffn_flex_container_vue").length > 0) {
                self.wffn_flex_vue.afterInstallError(event, response);
            }
        });
        /** wffn_edit_funnel_vue started  **/
        const wffn_edit_funnel_vue = function () {
            self.wffn_edit_funnel_vue = new Vue(
                {
                    mixins: [wfabtVueMixin],
                    components: {
                        "vue-form-generator": VueFormGenerator.component,
                    },
                    data: {
                        modal: false,
                        model: {
                            title: wffn.update_funnel.label_texts.title.value,
                            desc: wffn.update_funnel.label_texts.desc.value,
                        },
                        schema: {
                            fields: get_funnel_vue_fields(),
                        },
                        formOptions: {
                            validateAfterLoad: false,
                            validateAfterChanged: true,
                        },
                        current_state: 1,
                    },
                    methods: {
                        updateFunnel: function () {
                            if (false === this.$refs.update_funnel_ref.validate()) {
                                console.log('Validation Error');
                                return;
                            }
                            let funnel_id = self.wffn_flex_vue.funnel_id;
                            let funnel_name = self.wffn_edit_funnel_vue.model.title;
                            let funnel_desc = self.wffn_edit_funnel_vue.model.desc;
                            let vueCurrent = this;
                            vueCurrent.current_state = '2';

                            admin_ajax.ajax("update_funnel", {"_nonce": wffnParams.ajax_nonce_update_funnel, "funnel_id": funnel_id, 'funnel_name': funnel_name, 'funnel_desc': funnel_desc});
                            admin_ajax.success = function (rsp) {
                                if (_.isEqual(true, rsp.status)) {
                                    _.delay(
                                        function () {
                                            vueCurrent.current_state = '3';
                                            $(".bwf_breadcrumb ul li:last-child").html(rsp.data.title);
                                            Vue.set(self.wffn_breadcrumb_vue, 'title', rsp.data.title);
                                            wffn.update_funnel.label_texts.title.value = rsp.data.title;
                                            wffn.update_funnel.label_texts.desc.value = rsp.data.desc;
                                        },
                                        1000
                                    );
                                    _.delay(
                                        function () {
                                            $('#wf_funnel_edit_modal').iziModal('resetContent');
                                            $('#wf_funnel_edit_modal').iziModal('destroy');
                                            $('#wf_funnel_edit_modal').iziModal('close');
                                        },
                                        1000
                                    );
                                }
                            };
                        }
                    },
                }
            ).$mount('#part-update-funnel');
        };

        function wffn_tabs() {

            let wfctb = $('.wffn-widget-tabs .wffn-tab-title');
            $(document.body).on(
                'click', '.wffn-widget-tabs .wffn-tab-title',
                function () {
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
            if (wfctb.length > 0) {
                wfctb.eq(0).trigger('click');
            }

        }

        wffn_tabs();

        function wffn_ty_shortcode_tab() {

            let wfctb = $('.wffn-ty-shortcodes-tab .wffn-tab-title');
            $(document.body).on('click',
                '.wffn-ty-shortcodes-tab .wffn-tab-title',
                function () {
                    let tabindex = $(this).attr('data-tab');

                    $('.wffn-ty-shortcode-tab-area').hide();
                    $('.wffn-ty-shortcode-tab-area').eq(tabindex).show();

                }
            );
            if ($(".wffn-ty-shortcodes-tab").length > 0) {
                wfctb.eq(0).trigger('click');
            }
        }

        wffn_ty_shortcode_tab();

        $(document).ready(
            function () {
                $('.wrap.wffn-funnel-common').removeClass('wffn-hide');

                /* Adding upsell after checkout if upsell is not available */
                window.wffnBuilderCommons.addFilter(
                    'wffn_step_list_data',
                    function (popup_data) {
                        if (false === wffn.upsell_exist) {
                            let upsell_type = wffn.wc_upsells.type;
                            let stps = popup_data.steps;
                            let new_stps = {};
                            for (let stp_type in stps) {
                                new_stps[stp_type] = stps[stp_type];
                                if ('wc_checkout' === stp_type) {
                                    new_stps[upsell_type] = wffn.wc_upsells;
                                }
                            }
                            popup_data.steps = new_stps;
                        }
                        return popup_data;
                    }
                );

                /** Ellipses popover */
                $(document).on('click', '.wf_card_action .dashicons-ellipsis', function () {
                    let cartActionElem = $(this).parents('.wf_card_action');
                    if (cartActionElem.hasClass('wf_card_action_open')) {
                        return;
                    }
                    setTimeout(function () {
                        if (cartActionElem.attr('data-closed') == '0') {
                            cartActionElem.removeAttr('data-closed');
                            return;
                        }
                        cartActionElem.addClass('wf_card_action_open');
                        $('.wf_card_action').removeAttr('data-closed');
                    }, 50);
                });

                $('body').click(function () {
                    let $this = $(this);
                    if ($('.wf_card_action').length === 0) {
                        return;
                    }
                    if ($('.wf_card_action_open').length === 0) {
                        return;
                    }
                    if ($this.parents('.wf_card_ellipses_popover').length > 0) {
                        return;
                    }
                    $('.wf_card_action_open').attr('data-closed', '0');
                    $('.wf_card_action_open').removeClass('wf_card_action_open');
                    setTimeout(function () {
                        $('.wf_card_action').removeAttr('data-closed');
                    }, 200);
                });

                $('body').on('click', '.wf_card_open_sub_step', function () {
                    let $this = $(this);
                    let card = $this.parents('.wf_funnel_card');
                    let arrowIcon = card.find('.wf_funnel_arrow_icon');
                    let accordionCont = card.find('.accordion-content');

                    /** scrolling 200 pixels down */
                    let offsetTop = $this.offset().top;
                    offsetTop = parseInt(offsetTop) + 200;
                    if (false === accordionCont.is(":visible")) {
                        arrowIcon.trigger("click");
                        setTimeout(function () {
                            $([document.documentElement, document.body]).animate({
                                scrollTop: offsetTop
                            }, 500);
                        }, 400);
                    }
                });
            }
        );

    }; //let wffn_builder = function ()
    $(win).load(
        function () {
            window.wffnBuilder = new wffn_builder();
        }
    );
    window.wffnBuilderCommons = wffnBuilderCommons;
})(jQuery, document, window);