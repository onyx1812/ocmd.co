!function(C,d,i,n){var t=function(t){var a=this;a.$form=t,a.$attributeFields=t.find(".variations select"),a.$singleVariation=t.find(".single_variation"),a.$singleVariationWrap=t.find(".single_variation_wrap"),a.$resetVariations=t.find(".reset_variations"),a.$product=t.closest(".product"),a.variationData=t.data("product_variations"),a.useAjax=!1===a.variationData,a.xhr=!1,a.loading=!0,a.$singleVariationWrap.show(),a.$form.off(".wc-variation-form"),a.getChosenAttributes=a.getChosenAttributes.bind(a),a.findMatchingVariations=a.findMatchingVariations.bind(a),a.isMatch=a.isMatch.bind(a),a.toggleResetLink=a.toggleResetLink.bind(a),t.on("click.wc-variation-form",".reset_variations",{variationForm:a},a.onReset),t.on("reload_product_variations",{variationForm:a},a.onReload),t.on("hide_variation",{variationForm:a},a.onHide),t.on("show_variation",{variationForm:a},a.onShow),t.on("click",".single_add_to_cart_button",{variationForm:a},a.onAddToCart),t.on("reset_data",{variationForm:a},a.onResetDisplayedVariation),t.on("reset_image",{variationForm:a},a.onResetImage),t.on("change.wc-variation-form",".variations select",{variationForm:a},a.onChange),t.on("found_variation.wc-variation-form",{variationForm:a},a.onFoundVariation),t.on("check_variations.wc-variation-form",{variationForm:a},a.onFindVariation),t.on("update_variation_values.wc-variation-form",{variationForm:a},a.onUpdateAttributes),setTimeout(function(){t.trigger("check_variations"),t.trigger("wc_variation_form",a),a.loading=!1},100)};t.prototype.onReset=function(t){t.preventDefault(),t.data.variationForm.$attributeFields.val("").change(),t.data.variationForm.$form.trigger("reset_data")},t.prototype.onReload=function(t){var a=t.data.variationForm;a.variationData=a.$form.data("product_variations"),a.useAjax=!1===a.variationData,a.$form.trigger("check_variations")},t.prototype.onHide=function(t){t.preventDefault(),t.data.variationForm.$form.find(".single_add_to_cart_button").removeClass("wc-variation-is-unavailable").addClass("disabled wc-variation-selection-needed"),t.data.variationForm.$form.find(".woocommerce-variation-add-to-cart").removeClass("woocommerce-variation-add-to-cart-enabled").addClass("woocommerce-variation-add-to-cart-disabled")},t.prototype.onShow=function(t,a,i){t.preventDefault(),i?(t.data.variationForm.$form.find(".single_add_to_cart_button").removeClass("disabled wc-variation-selection-needed wc-variation-is-unavailable"),t.data.variationForm.$form.find(".woocommerce-variation-add-to-cart").removeClass("woocommerce-variation-add-to-cart-disabled").addClass("woocommerce-variation-add-to-cart-enabled")):(t.data.variationForm.$form.find(".single_add_to_cart_button").removeClass("wc-variation-selection-needed").addClass("disabled wc-variation-is-unavailable"),t.data.variationForm.$form.find(".woocommerce-variation-add-to-cart").removeClass("woocommerce-variation-add-to-cart-enabled").addClass("woocommerce-variation-add-to-cart-disabled")),wp.mediaelement&&t.data.variationForm.$form.find(".wp-audio-shortcode, .wp-video-shortcode").not(".mejs-container").filter(function(){return!C(this).parent().hasClass("mejs-mediaelement")}).mediaelementplayer(wp.mediaelement.settings)},t.prototype.onAddToCart=function(t){C(this).is(".disabled")&&(t.preventDefault(),C(this).is(".wc-variation-is-unavailable")?d.alert(wc_add_to_cart_variation_params.i18n_unavailable_text):C(this).is(".wc-variation-selection-needed")&&d.alert(wc_add_to_cart_variation_params.i18n_make_a_selection_text))},t.prototype.onResetDisplayedVariation=function(t){var a=t.data.variationForm;a.$product.find(".product_meta").find(".sku").wc_reset_content(),a.$product.find(".product_weight, .woocommerce-product-attributes-item--weight .woocommerce-product-attributes-item__value").wc_reset_content(),a.$product.find(".product_dimensions, .woocommerce-product-attributes-item--dimensions .woocommerce-product-attributes-item__value").wc_reset_content(),a.$form.trigger("reset_image"),a.$singleVariation.slideUp(200).trigger("hide_variation")},t.prototype.onResetImage=function(t){t.data.variationForm.$form.wc_variations_image_update(!1)},t.prototype.onFindVariation=function(t,a){var i,e=t.data.variationForm,r=void 0!==a?a:e.getChosenAttributes(),o=r.data;r.count&&r.count===r.chosenCount?e.useAjax?(e.xhr&&e.xhr.abort(),e.$form.block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),o.product_id=parseInt(e.$form.data("product_id"),10),o.custom_data=e.$form.data("custom_data"),e.xhr=C.ajax({url:wc_add_to_cart_variation_params.wc_ajax_url.toString().replace("%%endpoint%%","get_variation"),type:"POST",data:o,success:function(t){t?e.$form.trigger("found_variation",[t]):(e.$form.trigger("reset_data"),r.chosenCount=0,e.loading||(e.$form.find(".single_variation").after('<p class="wc-no-matching-variations woocommerce-info">'+wc_add_to_cart_variation_params.i18n_no_matching_variations_text+"</p>"),e.$form.find(".wc-no-matching-variations").slideDown(200)))},complete:function(){e.$form.unblock()}})):(e.$form.trigger("update_variation_values"),(i=e.findMatchingVariations(e.variationData,o).shift())?e.$form.trigger("found_variation",[i]):(e.$form.trigger("reset_data"),r.chosenCount=0,e.loading||(e.$form.find(".single_variation").after('<p class="wc-no-matching-variations woocommerce-info">'+wc_add_to_cart_variation_params.i18n_no_matching_variations_text+"</p>"),e.$form.find(".wc-no-matching-variations").slideDown(200)))):(e.$form.trigger("update_variation_values"),e.$form.trigger("reset_data")),e.toggleResetLink(0<r.chosenCount)},t.prototype.onFoundVariation=function(t,a){var i,e,r=t.data.variationForm,o=r.$product.find(".product_meta").find(".sku"),n=r.$product.find(".product_weight, .woocommerce-product-attributes-item--weight .woocommerce-product-attributes-item__value"),s=r.$product.find(".product_dimensions, .woocommerce-product-attributes-item--dimensions .woocommerce-product-attributes-item__value"),c=r.$singleVariationWrap.find(".quantity"),_=!0,d=!1,m="";a.sku?o.wc_set_content(a.sku):o.wc_reset_content(),a.weight?n.wc_set_content(a.weight_html):n.wc_reset_content(),a.dimensions?s.wc_set_content(C.parseHTML(a.dimensions_html)[0].data):s.wc_reset_content(),r.$form.wc_variations_image_update(a),a.variation_is_visible?(d=l("variation-template"),a.variation_id):d=l("unavailable-variation-template"),m=(m=(m=d({variation:a})).replace("/*<![CDATA[*/","")).replace("/*]]>*/",""),r.$singleVariation.html(m),r.$form.find('input[name="variation_id"], input.variation_id').val(a.variation_id).change(),"yes"===a.is_sold_individually?(c.find("input.qty").val("1").attr("min","1").attr("max","").change(),c.hide()):(i=c.find("input.qty"),e=parseFloat(i.val()),e=isNaN(e)||(e=e>parseFloat(a.max_qty)?a.max_qty:e)<parseFloat(a.min_qty)?a.min_qty:e,i.attr("min",a.min_qty).attr("max",a.max_qty).val(e).change(),c.show()),a.is_purchasable&&a.is_in_stock&&a.variation_is_visible||(_=!1),C.trim(r.$singleVariation.text())?r.$singleVariation.slideDown(200).trigger("show_variation",[a,_]):r.$singleVariation.show().trigger("show_variation",[a,_])},t.prototype.onChange=function(t){var a=t.data.variationForm;a.$form.find('input[name="variation_id"], input.variation_id').val("").change(),a.$form.find(".wc-no-matching-variations").remove(),a.useAjax||a.$form.trigger("woocommerce_variation_select_change"),a.$form.trigger("check_variations"),a.$form.trigger("woocommerce_variation_has_changed")},t.prototype.addSlashes=function(t){return t=(t=t.replace(/'/g,"\\'")).replace(/"/g,'\\"')},t.prototype.onUpdateAttributes=function(t){var y=t.data.variationForm,F=y.getChosenAttributes().data;y.useAjax||(y.$attributeFields.each(function(t,a){var i,e,r=C(a),o=r.data("attribute_name")||r.attr("name"),n=C(a).data("show_option_none"),s=":gt(0)",c=C("<select/>"),_=r.val()||"",d=!0;r.data("attribute_html")||((e=r.clone()).find("option").removeAttr("disabled attached").removeAttr("selected"),r.data("attribute_options",e.find("option"+s).get()),r.data("attribute_html",e.html())),c.html(r.data("attribute_html"));var m=C.extend(!0,{},F);m[o]="";var l=y.findMatchingVariations(y.variationData,m);for(var v in l)if("undefined"!=typeof l[v]){var g=l[v].attributes;for(var u in g)if(g.hasOwnProperty(u)){var f=g[u],h="";if(u===o)if(l[v].variation_is_active&&(h="enabled"),f){f=C("<div/>").html(f).text();var p=c.find("option");if(p.length)for(var w=0,b=p.length;w<b;w++){var $=C(p[w]);if(f===$.val()){$.addClass("attached "+h);break}}}else c.find("option:gt(0)").addClass("attached "+h)}}i=c.find("option.attached").length,_&&(d=!1,0!==i&&c.find("option.attached.enabled").each(function(){var t=C(this).val();if(_===t)return!(d=!0)})),0<i&&_&&d&&"no"===n&&(c.find("option:first").remove(),s=""),c.find("option"+s+":not(.attached)").remove(),r.html(c.html()),r.find("option"+s+":not(.enabled)").prop("disabled",!0),_?d?r.val(_):r.val("").change():r.val("")}),y.$form.trigger("woocommerce_update_variation_values"))},t.prototype.getChosenAttributes=function(){var i={},e=0,r=0;return this.$attributeFields.each(function(){var t=C(this).data("attribute_name")||C(this).attr("name"),a=C(this).val()||"";0<a.length&&r++,e++,i[t]=a}),{count:e,chosenCount:r,data:i}},t.prototype.findMatchingVariations=function(t,a){for(var i=[],e=0;e<t.length;e++){var r=t[e];this.isMatch(r.attributes,a)&&i.push(r)}return i},t.prototype.isMatch=function(t,a){var i,e,r=!0;for(var o in t){t.hasOwnProperty(o)&&(i=t[o],e=a[o],i!==n&&e!==n&&0!==i.length&&0!==e.length&&i!==e&&(r=!1))}return r},t.prototype.toggleResetLink=function(t){t?"hidden"===this.$resetVariations.css("visibility")&&this.$resetVariations.css("visibility","visible").hide().fadeIn():this.$resetVariations.css("visibility","hidden")},C.fn.wc_variation_form=function(){return new t(this),this},C.fn.wc_set_content=function(t){n===this.attr("data-o_content")&&this.attr("data-o_content",this.text()),this.text(t)},C.fn.wc_reset_content=function(){n!==this.attr("data-o_content")&&this.text(this.attr("data-o_content"))},C.fn.wc_set_variation_attr=function(t,a){n===this.attr("data-o_"+t)&&this.attr("data-o_"+t,this.attr(t)?this.attr(t):""),!1===a?this.removeAttr(t):this.attr(t,a)},C.fn.wc_reset_variation_attr=function(t){n!==this.attr("data-o_"+t)&&this.attr(t,this.attr("data-o_"+t))},C.fn.wc_maybe_trigger_slide_position_reset=function(t){var a=C(this),i=a.closest(".product").find(".images"),e=!1,r=t&&t.image_id?t.image_id:"";a.attr("current-image")!==r&&(e=!0),a.attr("current-image",r),e&&i.trigger("woocommerce_gallery_reset_slide_position")},C.fn.wc_variations_image_update=function(t){var a=this,i=a.closest(".product"),e=i.find(".images"),r=i.find(".flex-control-nav"),o=r.find("li:eq(0) img"),n=e.find(".woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder").eq(0),s=n.find(".wp-post-image"),c=n.find("a").eq(0);if(t&&t.image&&t.image.src&&1<t.image.src.length){0<r.find('li img[data-o_src="'+t.image.gallery_thumbnail_src+'"]').length&&a.wc_variations_image_reset();var _=r.find('li img[src="'+t.image.gallery_thumbnail_src+'"]');if(0<_.length)return _.trigger("click"),a.attr("current-image",t.image_id),void d.setTimeout(function(){C(d).trigger("resize"),e.trigger("woocommerce_gallery_init_zoom")},20);s.wc_set_variation_attr("src",t.image.src),s.wc_set_variation_attr("height",t.image.src_h),s.wc_set_variation_attr("width",t.image.src_w),s.wc_set_variation_attr("srcset",t.image.srcset),s.wc_set_variation_attr("sizes",t.image.sizes),s.wc_set_variation_attr("title",t.image.title),s.wc_set_variation_attr("data-caption",t.image.caption),s.wc_set_variation_attr("alt",t.image.alt),s.wc_set_variation_attr("data-src",t.image.full_src),s.wc_set_variation_attr("data-large_image",t.image.full_src),s.wc_set_variation_attr("data-large_image_width",t.image.full_src_w),s.wc_set_variation_attr("data-large_image_height",t.image.full_src_h),n.wc_set_variation_attr("data-thumb",t.image.src),o.wc_set_variation_attr("src",t.image.gallery_thumbnail_src),c.wc_set_variation_attr("href",t.image.full_src)}else a.wc_variations_image_reset();d.setTimeout(function(){C(d).trigger("resize"),a.wc_maybe_trigger_slide_position_reset(t),e.trigger("woocommerce_gallery_init_zoom")},20)},C.fn.wc_variations_image_reset=function(){var t=this.closest(".product"),a=t.find(".images"),i=t.find(".flex-control-nav").find("li:eq(0) img"),e=a.find(".woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder").eq(0),r=e.find(".wp-post-image"),o=e.find("a").eq(0);r.wc_reset_variation_attr("src"),r.wc_reset_variation_attr("width"),r.wc_reset_variation_attr("height"),r.wc_reset_variation_attr("srcset"),r.wc_reset_variation_attr("sizes"),r.wc_reset_variation_attr("title"),r.wc_reset_variation_attr("data-caption"),r.wc_reset_variation_attr("alt"),r.wc_reset_variation_attr("data-src"),r.wc_reset_variation_attr("data-large_image"),r.wc_reset_variation_attr("data-large_image_width"),r.wc_reset_variation_attr("data-large_image_height"),e.wc_reset_variation_attr("data-thumb"),i.wc_reset_variation_attr("src"),o.wc_reset_variation_attr("href")},C(function(){"undefined"!=typeof wc_add_to_cart_variation_params&&C(".variations_form").each(function(){C(this).wc_variation_form()})});var l=function(t){var a=i.getElementById("tmpl-"+t).textContent;return/<#\s?data\./.test(a)||/{{{?\s?data\.(?!variation\.).+}}}?/.test(a)||/{{{?\s?data\.variation\.[\w-]*[^\s}]/.test(a)?wp.template(t):function(t){var o=t.variation||{};return a.replace(/({{{?)\s?data\.variation\.([\w-]*)\s?(}}}?)/g,function(t,a,i,e){if(a.length!==e.length)return"";var r=o[i]||"";return 2===a.length?d.escape(r):r})}}}(jQuery,window,document);