(function ($) {

    window.increaseItmQty = (currentEle, aero_key = '', callback = '') => {
        let $this = $(currentEle);
        let qtyEle = $this.siblings(".wfacp_product_quantity_number_field");
        let value = parseInt(qtyEle.val(), 10);
        value = isNaN(value) ? 0 : value;
        let max = qtyEle.attr('max');
        if (undefined != max && '' != max) {
            max = parseInt(max, 10);
            if (value >= max) {
                wfacp_frontend.hooks.doAction('wfacp_max_quantity_reached', value, qtyEle);
                return;
            }
        }
        if ('' !== aero_key) {
            //trigger aerocheckout page
            let el = $('.wfacp_increase_item[data-item-key=' + aero_key + ']');

            if (el.length > 0) {

                increaseItmQty(el[0], '', function (rsp) {
                    if (rsp.hasOwnProperty('error')) {
                        let parent = $this.parents('.product-name-area');
                        wfacp_show_error(parent, rsp);
                        return;
                    }
                });
                return;
            }
        }

        value++;
        qtyEle.val(value);

        qtyEle.trigger("change", [callback]);

    };
    window.decreaseItmQty = (currentEle, aero_key = '') => {
        let $this = $(currentEle);
        let qtyEle = $this.siblings(".wfacp_product_quantity_number_field");


        var value = parseInt(qtyEle.val(), 10);
        value = isNaN(value) ? 0 : value;
        if (value < 1) {
            value = 1;
        }

        let min = qtyEle.attr('min');

        if (undefined != min && '' != min) {
            min = parseInt(min, 10);
            if (value <= min) {
                wfacp_frontend.hooks.doAction('wfacp_min_quantity_reached', value, qtyEle);
                return;
            }
        }
        if ('' !== aero_key && value > 1) {
            let el = $('.wfacp_decrease_item[data-item-key=' + aero_key + ']');
            if (el.length > 0) {
                el.click();
                return;
            }
        }

        value--;
        qtyEle.val(value);
        qtyEle.trigger("change");
    };
    window.wfacp_product_switch = function (data, cb) {
        let switch_panel = $('.wfacp-product-switch-panel');
        switch_panel.aero_block();
        data.you_save = switch_panel.data('you-save-text');
        data.is_hide = switch_panel.data('is-hide');
        set_aero_data({
            'action': 'switch_product_addon',
            'type': 'post',
            'data': data
        }, cb);
    };
    let cart_goes_zero = (parseFloat(wfacp_frontend.cart_total) === 0);
    let cart_is_virtual = ('1' == wfacp_frontend.cart_is_virtual) ? true : false;
    let remove_coupon_div = (coupon) => {
        let dat_coupon = $('[data-coupon="' + coupon + '"]');
        if (dat_coupon.length > 0) {
            dat_coupon.parents('tr').remove();
            dat_coupon.parents('.woocommerce-message1.wfacp_coupon_success').remove();
            dat_coupon.parents('.wfacp_single_coupon_msg').remove();
        }
    };
    let wc_checkout_coupons_main = {
        init: function () {
            $(document.body).off('click', '.woocommerce-remove-coupon').on('click', '.woocommerce-remove-coupon', this.remove_coupon);
            $(document.body).on('click', '.wfacp_main_showcoupon', wc_checkout_coupons_main.show_coupon_form);
            $('form.checkout_coupon').off('submit').on('submit', this.submit);
            wfacp_frontend.hooks.addAction('wfacp_ajax_apply_coupon_main', this.response);
        },

        response: function (rsp) {
            let wfacp_coupon_msg = $('.wfacp_coupon_msg');
            wfacp_coupon_msg.html('').show();
            let $form = $('form.checkout_coupon');

            if ($form.hasClass('wfacp_layout_shopcheckout')) {
                $form.removeClass('processing').children(".wfacp_coupon_row").aero_unblock();
            } else {
                $form.removeClass('processing').aero_unblock();
            }
            $('.woocommerce-error, .woocommerce-message').remove();

            $(document.body).trigger('wfacp_coupon_apply', [rsp]);
            if (rsp.hasOwnProperty('message')) {
                let message = rsp.message;
                if (message.hasOwnProperty('error')) {
                    //for error message;
                    let error = message.error;
                    if (error.length > 0) {
                        for (let i = 0; i < error.length > 0; i++) {
                            let message = error[i];
                            let error_message = error[i];
                            if (typeof message == "object" && message.hasOwnProperty('notice')) {
                                error_message = message.notice;
                            }
                            wfacp_coupon_msg.prepend('<div class="woocommerce-error wfacp_error" role="alert">' + error_message + "</div>");
                        }
                    }
                }

                if (message.hasOwnProperty('success')) {
                    //for error message;
                    let success = message.success;
                    if (success.length > 0) {
                        for (let j = 0; j < success.length > 0; j++) {
                            let message = success[j];
                            let success_message = success[j];
                            if (typeof message == "object" && message.hasOwnProperty('notice')) {
                                success_message = message.notice;
                            }
                            wfacp_coupon_msg.prepend('<div class="woocommerce-message wfacp_success" role="alert">' + success_message + "</div>");
                        }
                    }
                }
            }
        },

        submit: function (e) {
            e.preventDefault();
            if (typeof wc_checkout_params == "undefined") {
                console.log('Coupon functionality not working in preview mode');
                return;
            }
            let $form = $(this);
            let coupon_code = $form.find('input[name="coupon_code"]').val();

            if ('' === coupon_code) {
                $form.find('input[name="coupon_code"]').addClass("wfacp_coupon_failed_error");
                return false;
            }
            $form.find('input[name="coupon_code"]').removeClass("wfacp_coupon_failed_error");

            if ($form.is('.processing')) {
                return false;
            }
            $form.addClass('processing');
            let wfacp_coupon_msg = $('.wfacp_coupon_msg');
            wfacp_coupon_msg.html('').show();
            set_aero_data({
                'action': 'apply_coupon_main',
                'type': 'post',
                'coupon_code': $form.find('input[name="coupon_code"]').val(),
                'wfacp_id': $('._wfacp_post_id').val()
            });
        },
        show_coupon_form: function (e) {
            e.preventDefault();


            let field = $(this).parents('.wfacp_woocommerce_form_coupon').find('form.woocommerce-form-coupon');

            if (field.hasClass('wfacp_display_block')) {

                field.removeClass('wfacp_display_block');
                let classes = ['wfacp_desktop_view', 'wfacp_tablet_view', 'wfacp_mobile_view'];
                for (let i in classes) {
                    if (field.parent('.wfacp_mini_cart_classes').hasClass(classes[i])) {
                        field.parent('.wfacp_mini_cart_classes').removeClass(classes[i]);
                    }
                }
            }

            field.slideToggle(400, function () {
                field.find('.wfacp-form-control').focus();
            });
            return false;
        },
        remove_coupon: function (e) {
            e.preventDefault();
            if (typeof wc_checkout_params == "undefined") {
                console.log('Coupon functionality not working in preview mode');
                return;
            }
            let container = $(this).parents('.woocommerce-checkout-review-order');
            let coupon = $(this).data('coupon');
            container.addClass('processing');
            show_blocked_mini_cart();
            let data = {
                security: wc_checkout_params.remove_coupon_nonce,
                coupon: coupon
            };
            let wfacp_coupon_msg = $('.wfacp_coupon_msg');

            $.ajax({
                type: 'POST',
                url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'remove_coupon'),
                data: data,
                success: function (code) {
                    $('.woocommerce-error, .woocommerce-message').remove();
                    container.removeClass('processing');
                    show_unblocked_mini_cart();
                    if (code) {
                        remove_coupon_div(coupon);
                        if (wfacp_coupon_msg.length > 0) {
                            wfacp_coupon_msg.html(code);

                        } else {
                            $('.wfacp_layout_9_coupon_error_msg').html(code);
                        }
                        $(document.body).trigger('update_checkout', {update_shipping_method: false});
                        let coupon_field = $('form.checkout_coupon').find('input[name="coupon_code"]');
                        // Remove coupon code from coupon field
                        coupon_field.val('');
                        coupon_field.parent('.wfacp-input-form').removeClass('wfacp-anim-wrap');
                    }
                    if ($('form.woocommerce-form-coupon:visible').length > 0) {
                        $.scroll_to_notices($(".wfacp_main_form"));
                    }
                    $(document.body).trigger('wfacp_coupon_form_removed', [code]);
                },
                error: function () {
                    if (wc_checkout_params.debug_mode) {
                        /* jshint devel: true */
                    }
                },
                dataType: 'html'
            });
        }
    };
    let cart_virtual_fnc = (rsp) => {
        if (rsp.hasOwnProperty('cart_is_virtual')) {
            if (cart_is_virtual !== rsp.cart_is_virtual) {
                let shipping_checkout = $('#shipping_same_as_billing_field');

                if (shipping_checkout.length > 0) {
                    if (true == shipping_checkout.is(':visible') && true == rsp.cart_is_virtual) {
                        shipping_checkout.hide();
                        $('#shipping_same_as_billing').prop('checked', false).trigger('change');
                    } else {
                        shipping_checkout.show();
                    }
                }
                let action_data = {'previous': cart_is_virtual, 'current': rsp.cart_is_virtual};
                wfacp_frontend.hooks.doAction('wfacp_cart_behaviour_changed', action_data);
                cart_is_virtual = rsp.cart_is_virtual;
            }
        }
    };
    wfacp_frontend.hooks.addAction('wfacp_ajax_response', global_ajax_response);

    function global_ajax_response(rsp, fragments) {
        if (typeof rsp !== "object") {
            rsp = {'status': false};
        }
        if (rsp.hasOwnProperty('force_redirect')) {
            // Force redirect after removing last item
            window.location.href = rsp.force_redirect;
            return;
        }

        if (rsp.hasOwnProperty('cart_contains_subscription') && true == rsp.cart_contains_subscription) {
            let create_account = $('#createaccount');
            if (create_account.length > 0) {
                if (!create_account.is(":checked")) {
                    create_account.trigger('click');
                }
            }
        }

        cart_virtual_fnc(rsp);
        $(document.body).trigger('wfacp_update_fragments', rsp);
        if (rsp.hasOwnProperty('cart_total') && parseInt(rsp.cart_total) === 0) {
            cart_goes_zero = true;
            $(document.body).trigger('wfacp_cart_goes_empty', {'cart_total': parseInt(rsp.cart_total)});
        } else {
            cart_goes_zero = false;
        }
        if (cart_goes_zero) {
            cart_goes_zero = false;
            update_checkout();
            return;
        } else {
            if (fragments && fragments.hasOwnProperty('.cart_total') && parseFloat(fragments['.cart_total']) === 0) {
                $(document.body).trigger('wfacp_cart_goes_empty', {'cart_total': parseFloat(rsp.cart_total)});
                update_checkout();
            }
        }
        $(document.body).trigger('wfacp_after_fragment_received', {'response': rsp});
        if (rsp.hasOwnProperty('analytics_data')) {
            $(document.body).trigger('wfacp_checkout_data', {'checkout': rsp.analytics_data});
        }
    }

    function product_switchers() {
        // product switching event end here
        $(document.body).on('change', '.wfacp_mini_cart_update_qty', function () {
            let el = $(this);
            let qty = el.val();
            let delete_enabled = el.parents('.wfacp_min_cart_widget').data('delete-enabled');
            let parent = el.parent('.cart_item');
            let cart_key = el.attr('cart_key');
            let action = 'update_cart_item_quantity';
            let old_val = el.data('value');

            let max = el.attr('max');
            let old_qty = el.attr('data-value');

            if (undefined != max && '' != max) {
                max = parseInt(max, 10);
                if (qty > max) {
                    wfacp_frontend.hooks.doAction('wfacp_max_quantity_reached', qty, el);
                    $(this).val(old_qty);
                    return;
                }
            }

            let min = $(this).attr('min');

            if (undefined != min && '' != min) {
                min = parseInt(min, 10);
                if (qty <= min) {
                    $(this).val(old_qty);
                    wfacp_frontend.hooks.doAction('wfacp_min_quantity_reached', qty, el);
                    return;
                }
            }


            if (qty == undefined || qty == '' || qty == 0) {
                if ('1' != delete_enabled) {
                    el.val(old_val);
                    return;
                }
            }
            let switch_panel = $('.wfacp-product-switch-panel');
            switch_panel.aero_block();

            let cb = (rsp) => {
                if (rsp.hasOwnProperty('error') && rsp.hasOwnProperty('cart_key')) {
                    $(this).val(old_val);
                    parent = $('.wfacp_min_cart_widget .cart_item[cart_key="' + rsp.cart_key + '"] .product-name-area');
                    wfacp_show_error(parent, rsp);
                }
            };
            set_aero_data({
                'action': action,
                'type': 'post',
                'data': {
                    'cart_key': cart_key,
                    'quantity': qty,
                    'old_qty': old_val,
                    'by': 'mini_cart',
                    'wfacp_id': $("._wfacp_post_id").val()
                }
            }, cb);
        });
        $(document.body).on('click', '.wfacp_mini_cart_remove_item_from_cart', function (e) {
            e.preventDefault();
            let el = $(this);
            let parent = $(this).parents('.cart_item');
            let cart_key = $(this).data('cart_key');
            let item_key = $(this).data('item_key');
            let action = 'remove_cart_item';
            let switch_panel = $('.wfacp-product-switch-panel');
            switch_panel.aero_block();
            let cb = (rsp) => {
                if (rsp.hasOwnProperty('error') && rsp.hasOwnProperty('cart_key')) {
                    let elementor = el.parents('.wfacp_elementor_mini_cart_widget');
                    parent = $('.wfacp_min_cart_widget .cart_item[cart_key="' + rsp.cart_key + '"] .product-name');
                    if (elementor.length > 0) {
                        parent = $('.wfacp_min_cart_widget .cart_item[cart_key="' + rsp.cart_key + '"] .product-name-area');
                    }
                    wfacp_show_error(parent, rsp);
                }
            };
            set_aero_data({
                'action': action,
                'type': 'post',
                'data': {
                    'cart_key': cart_key,
                    'item_key': item_key,
                    'by': 'mini_cart',
                    'wfacp_id': $("._wfacp_post_id").val()
                }
            }, cb);
        });
        $(document.body).on('click', '.wfacp_remove_item_from_cart', function (e) {
            e.preventDefault();
            let el = $(this);
            let parent = $(this).parents('.cart_item');
            let cart_key = $(this).data('cart_key');
            let item_key = $(this).data('item_key');
            let action = 'remove_cart_item';
            let switch_panel = $('.wfacp-product-switch-panel');
            switch_panel.aero_block();
            let cb = (rsp) => {
                if (rsp.hasOwnProperty('error') && rsp.hasOwnProperty('cart_key')) {
                    let s_parent = el.parents('.wfacp_min_cart_widget');
                    parent = $('.wfacp_product_row[cart_key="' + rsp.cart_key + '"]');
                    //mini cart
                    if (s_parent.length > 0) {
                        parent = el.parents('td');
                    }

                    if (parent.length > 0) {
                        show_unblocked_mini_cart();
                        wfacp_show_error(parent, rsp);
                    }
                }
            };
            set_aero_data({
                'action': action,
                'data': {
                    'cart_key': cart_key,
                    'item_key': item_key,
                    'by': 'product_switcher',
                    'wfacp_id': $("._wfacp_post_id").val()
                }
            }, cb);
        });
        $(document.body).on('click', '.wfacp_remove_item_from_order_summary', function (e) {
            e.preventDefault();
            let el = $(this);
            let cart_key = $(this).data('cart_key');
            let action = 'remove_cart_item';
            let switch_panel = $('.wfacp-product-switch-panel');
            let order_summary = $('.wfacp_order_summary_container');
            switch_panel.aero_block();
            order_summary.aero_block();
            let cb = (rsp) => {
                show_unblocked_mini_cart();
                switch_panel.aero_unblock();
                order_summary.aero_unblock();
            };

            set_aero_data({
                'action': action,
                'data': {
                    'cart_key': cart_key,
                    'by': 'mini_cart',
                    'wfacp_id': $("._wfacp_post_id").val()
                }
            }, cb);
        });
        $(document.body).on('click', '.wfacp_restore_cart_item', function (e) {
            e.preventDefault();
            let cart_key = $(this).data('cart_key');
            let action = 'undo_cart_item';
            let switch_panel = $('.wfacp-product-switch-panel');
            switch_panel.aero_block();
            let by = 'product_switcher';
            if ($(this).parents('.wfacp_min_cart_widget').length > 0) {
                by = 'mini_cart';
            }
            let order_summary = $('.wfacp_order_summary_container');
            order_summary.aero_block();
            set_aero_data({
                'action': action,
                'type': 'post',
                'data': {
                    'cart_key': cart_key,
                    'wfacp_id': $("._wfacp_post_id").val(),
                    'by': by
                }
            }, function () {
                switch_panel.aero_unblock();
                order_summary.aero_unblock();
            });
        });
    }

    $(document.body).on('wfacp_setup', function () {
        console.log('Aero JS Setup ');
        product_switchers();
        wc_checkout_coupons_main.init();
    });
})(jQuery);