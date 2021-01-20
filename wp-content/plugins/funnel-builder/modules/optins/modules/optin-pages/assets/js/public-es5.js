"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/*global Cookies*/

/*global jQuery*/
(function ($) {
  var wffnOptin = {
    init: function init() {
      var self = this;
      $(document).ready(function ($) {
        self.renderForm();
        self.handleSubmit();
        self.renderPBPopUps();
        self.attachCloseBtn();
        self.initPhoneFlag();
      });
      $(document).on('wffn_reload_popups', function () {
        self.renderPBPopUps();
      });
      $(document).on('wffn_reload_phone_field', function () {
        self.initPhoneFlag();
      });
    },
    initPhoneFlag: function initPhoneFlag() {
      var elems = document.querySelectorAll(".phone_flag_code input[type='tel']");

      for (var i in elems) {
        if (_typeof(elems[i]) === 'object' && undefined !== window.intlTelInput) {
          window.intlTelInput(elems[i], {
            initialCountry: "auto",
            separateDialCode: true,
            geoIpLookup: function geoIpLookup(callback) {
              $.get('https://ipinfo.io', function () {}, "jsonp").always(function (resp) {
                var countryCode = resp && resp.country ? resp.country : "us";
                callback(countryCode);
              });
            }
          });
        }
      }
    },
    attachCloseBtn: function attachCloseBtn() {
      jQuery(document).on('click', '.bwf_pp_close', function (e) {
        e.preventDefault();
        jQuery('.bwf_pp_overlay').removeClass('show_popup_form');
      });
    },
    renderPBPopUps: function renderPBPopUps() {
      jQuery('.wfop_pb_widget_wrap').each(function () {
        var elem = this;
        jQuery(this).find(".bwf-custom-button a").click(function (e) {
          e.preventDefault();
          jQuery(elem).find('.bwf_pp_overlay').addClass('show_popup_form');
        });
      });
    },
    renderForm: function renderForm() {
      var self = this;

      if (jQuery('.bwf_pp_overlay').length > 0) {
        jQuery('a[href*="wfop-popup=yes"]').on('click', function (e) {
          e.preventDefault();
          jQuery('.bwf_pp_overlay').addClass('show_popup_form');
        });
      }
    },
    DoValidation: function DoValidation(formElem) {
      var valid = true;
      jQuery(formElem).find('.wfop_required').each(function () {
        var self = jQuery(this);
        var message = null;
        var error_msg = window.wffnfunnelVars.op_valid_text;
        var error_email = window.wffnfunnelVars.op_valid_email;

        if (jQuery.trim(self.val()) === '') {
          message = error_msg;
        } else if ('checkbox' === self.attr('type')) {
          if (!self.prop('checked')) {
            message = error_msg;
          }
        } else if ('radio' === self.attr('type')) {
          var radioName = self.attr("name");

          if (jQuery(formElem).find("input:radio[name=" + radioName + "]:checked").length === 0) {
            message = error_msg;
          }
        }

        if (jQuery.trim(self.val()) !== '' && 'wfop_optin_email' === self.attr('name')) {
          var pattern = /^([\w-.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(]?)$/;

          if (!jQuery.trim(self.val()).match(pattern)) {
            message = error_email;
          }
        }

        if (message !== null) {
          self.parents('.bwfac_form_sec').addClass('bwfac_error');

          if (self.parents('.bwfac_form_sec').find('.error').length === 0) {
            self.parents('.bwfac_form_sec').append('<span class="error">' + message + '</span>');
          }

          valid = false;
        }
      });
      return valid;
    },
    setUpClick: function setUpClick(FormElem) {
      var inst = this;
      jQuery(FormElem).find('#wffn_custom_optin_submit').on('click', function (e) {
        var valid = true;
        jQuery(this).removeAttr('disabled');
        var bwf_form = FormElem;
        jQuery(bwf_form).find('.bwfac_form_sec').removeClass('bwfac_error');
        jQuery(bwf_form).find('.bwfac_form_sec .error').remove();
        var is_admin = jQuery(bwf_form).find('input[name=optin_is_admin]').val();
        var is_ajax = jQuery(bwf_form).find('input[name=optin_is_ajax]').val();
        var is_preview = jQuery(bwf_form).find('input[name=optin_is_preview]').val();
        console.log('Is Admin: ' + is_admin + ' Is Ajax: ' + is_ajax + ' Is preview: ' + is_preview);

        if (is_admin || is_ajax || is_preview) {
          console.log('Returning');
          valid = false;
        }

        valid = inst.DoValidation(FormElem);
        e.preventDefault();

        if (valid) {
          jQuery(this).attr('disabled', 'disabled');
          var submitting_text = jQuery(this).attr('data-subitting-text');
          jQuery(FormElem).find("button.wfop_submit_btn .bwf_heading").html(submitting_text);

          if ("undefined" !== typeof window.intlTelInputGlobals && "undefined" !== jQuery(FormElem).find('input[name="wfop_optin_phone"]').get(0)) {
            var iti = window.intlTelInputGlobals.getInstance(jQuery(FormElem).find('input[name="wfop_optin_phone"]').get(0));
            var getCountryData = iti.getSelectedCountryData();
            jQuery(FormElem).find('input[name="wfop_optin_phone_dialcode"]').eq(0).val('+' + getCountryData.dialCode);
          }
          /**
           * XHR synchronous requests on the main threads are deprecated. We need to make it async, and after that trigger the form submission
           */


          jQuery.ajax({
            url: window.wffnfunnelVars.ajaxUrl + '?action=wffn_submit_custom_optin_form',
            data: jQuery(FormElem).serialize(),
            dataType: 'json',
            type: 'post'
          }).always(function (resp) {
            /* When there is no action for the form we reload the page manually so we won't mess up the redirects from WP */
            if (Object.prototype.hasOwnProperty.call(resp, 'mapped')) {
              for (var k in resp.mapped) {
                jQuery(".wfop_integration_form input[name='" + k + "']").val(resp.mapped[k]);
              }

              jQuery(".wfop_integration_form").trigger('submit');
              return;
            }

            if (Object.prototype.hasOwnProperty.call(resp, 'next_url') && '' !== resp.next_url) {
              window.location.href = resp.next_url;
              return;
            }
          });
        } else {
          console.log('form validation failed');
        }
      });
    },
    handleSubmit: function handleSubmit() {
      var inst = this;
      jQuery("form.wffn-custom-optin-from").each(function () {
        inst.setUpClick(this);
      });
    }
  };
  wffnOptin.init();
})(jQuery);
