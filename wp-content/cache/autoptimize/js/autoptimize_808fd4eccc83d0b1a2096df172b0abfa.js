function klIdentifyBrowser(klUser){if(klUser.current_user_email){var _learnq=window._learnq||[];_learnq.push(["identify",{"$email":klUser.current_user_email}]);}else{if(klUser.commenter_email){_learnq.push(["identify",{"$email":klUser.commenter_email}]);}}}
window.addEventListener("load",function(){klIdentifyBrowser(klUser);});
/*!
 * JavaScript Cookie v2.1.4
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
!function(e){var n=!1;if("function"==typeof define&&define.amd&&(define(e),n=!0),"object"==typeof exports&&(module.exports=e(),n=!0),!n){var o=window.Cookies,t=window.Cookies=e();t.noConflict=function(){return window.Cookies=o,t}}}(function(){function e(){for(var e=0,n={};e<arguments.length;e++){var o=arguments[e];for(var t in o)n[t]=o[t]}return n}function n(o){function t(n,r,i){var c;if("undefined"!=typeof document){if(arguments.length>1){if("number"==typeof(i=e({path:"/"},t.defaults,i)).expires){var a=new Date;a.setMilliseconds(a.getMilliseconds()+864e5*i.expires),i.expires=a}i.expires=i.expires?i.expires.toUTCString():"";try{c=JSON.stringify(r),/^[\{\[]/.test(c)&&(r=c)}catch(m){}r=o.write?o.write(r,n):encodeURIComponent(String(r)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),n=(n=(n=encodeURIComponent(String(n))).replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent)).replace(/[\(\)]/g,escape);var f="";for(var s in i)i[s]&&(f+="; "+s,!0!==i[s]&&(f+="="+i[s]));return document.cookie=n+"="+r+f}n||(c={});for(var p=document.cookie?document.cookie.split("; "):[],d=/(%[0-9A-Z]{2})+/g,u=0;u<p.length;u++){var l=p[u].split("="),C=l.slice(1).join("=");'"'===C.charAt(0)&&(C=C.slice(1,-1));try{var g=l[0].replace(d,decodeURIComponent);if(C=o.read?o.read(C,g):o(C,g)||C.replace(d,decodeURIComponent),this.json)try{C=JSON.parse(C)}catch(m){}if(n===g){c=C;break}n||(c[g]=C)}catch(m){}}return c}}return t.set=t,t.get=function(e){return t.call(t,e)},t.getJSON=function(){return t.apply({json:!0},[].slice.call(arguments))},t.defaults={},t.remove=function(n,o){t(n,"",e(o,{expires:-1}))},t.withConverter=n,t}return n(function(){})});
jQuery(function(r){if("undefined"==typeof wc_cart_fragments_params)return!1;var t=!0,o=wc_cart_fragments_params.cart_hash_key;try{t="sessionStorage"in window&&null!==window.sessionStorage,window.sessionStorage.setItem("wc","test"),window.sessionStorage.removeItem("wc"),window.localStorage.setItem("wc","test"),window.localStorage.removeItem("wc")}catch(f){t=!1}function a(){t&&sessionStorage.setItem("wc_cart_created",(new Date).getTime())}function s(e){t&&(localStorage.setItem(o,e),sessionStorage.setItem(o,e))}var e={url:wc_cart_fragments_params.wc_ajax_url.toString().replace("%%endpoint%%","get_refreshed_fragments"),type:"POST",data:{time:(new Date).getTime()},timeout:wc_cart_fragments_params.request_timeout,success:function(e){e&&e.fragments&&(r.each(e.fragments,function(e,t){r(e).replaceWith(t)}),t&&(sessionStorage.setItem(wc_cart_fragments_params.fragment_name,JSON.stringify(e.fragments)),s(e.cart_hash),e.cart_hash&&a()),r(document.body).trigger("wc_fragments_refreshed"))},error:function(){r(document.body).trigger("wc_fragments_ajax_error")}};function n(){r.ajax(e)}if(t){var i=null;r(document.body).on("wc_fragment_refresh updated_wc_div",function(){n()}),r(document.body).on("added_to_cart removed_from_cart",function(e,t,r){var n=sessionStorage.getItem(o);null!==n&&n!==undefined&&""!==n||a(),sessionStorage.setItem(wc_cart_fragments_params.fragment_name,JSON.stringify(t)),s(r)}),r(document.body).on("wc_fragments_refreshed",function(){clearTimeout(i),i=setTimeout(n,864e5)}),r(window).on("storage onstorage",function(e){o===e.originalEvent.key&&localStorage.getItem(o)!==sessionStorage.getItem(o)&&n()}),r(window).on("pageshow",function(e){e.originalEvent.persisted&&(r(".widget_shopping_cart_content").empty(),r(document.body).trigger("wc_fragment_refresh"))});try{var c=r.parseJSON(sessionStorage.getItem(wc_cart_fragments_params.fragment_name)),_=sessionStorage.getItem(o),g=Cookies.get("woocommerce_cart_hash"),m=sessionStorage.getItem("wc_cart_created");if(null!==_&&_!==undefined&&""!==_||(_=""),null!==g&&g!==undefined&&""!==g||(g=""),_&&(null===m||m===undefined||""===m))throw"No cart_created";if(m){var d=+m+864e5,w=(new Date).getTime();if(d<w)throw"Fragment expired";i=setTimeout(n,d-w)}if(!c||!c["div.widget_shopping_cart_content"]||_!==g)throw"No fragment";r.each(c,function(e,t){r(e).replaceWith(t)}),r(document.body).trigger("wc_fragments_loaded")}catch(f){n()}}else n();0<Cookies.get("woocommerce_items_in_cart")?r(".hide_cart_widget_if_empty").closest(".widget_shopping_cart").show():r(".hide_cart_widget_if_empty").closest(".widget_shopping_cart").hide(),r(document.body).on("adding_to_cart",function(){r(".hide_cart_widget_if_empty").closest(".widget_shopping_cart").show()}),"undefined"!=typeof wp&&wp.customize&&wp.customize.selectiveRefresh&&wp.customize.widgetsPreview&&wp.customize.widgetsPreview.WidgetPartial&&wp.customize.selectiveRefresh.bind("partial-content-rendered",function(){n()})});
var AwsHooks=AwsHooks||{};AwsHooks.filters=AwsHooks.filters||{};(function($){"use strict";var selector='.aws-container';var instance=0;var pluginPfx='aws_opts';var translate={sale:aws_vars.sale,sku:aws_vars.sku,showmore:aws_vars.showmore,noresults:aws_vars.noresults};AwsHooks.add_filter=function(tag,callback,priority){if(typeof priority==="undefined"){priority=10;}
AwsHooks.filters[tag]=AwsHooks.filters[tag]||[];AwsHooks.filters[tag].push({priority:priority,callback:callback});};AwsHooks.apply_filters=function(tag,value,options){var filters=[];if(typeof AwsHooks.filters[tag]!=="undefined"&&AwsHooks.filters[tag].length>0){AwsHooks.filters[tag].forEach(function(hook){filters[hook.priority]=filters[hook.priority]||[];filters[hook.priority].push(hook.callback);});filters.forEach(function(AwsHooks){AwsHooks.forEach(function(callback){value=callback(value,options);});});}
return value;};$.fn.aws_search=function(options){var methods={init:function(){$('body').append('<div id="aws-search-result-'+instance+'" class="aws-search-result" style="display: none;"></div>');methods.addClasses();setTimeout(function(){methods.resultLayout();},500);},onKeyup:function(e){searchFor=$searchField.val();searchFor=searchFor.trim();searchFor=searchFor.replace(/<>\{\}\[\]\\\/]/gi,'');searchFor=searchFor.replace(/\s\s+/g,' ');for(var i=0;i<requests.length;i++){requests[i].abort();}
if(d.showPage=='ajax_off'){return;}
if(searchFor===''){$(d.resultBlock).html('').hide();methods.hideLoader();methods.resultsHide();return;}
if(cachedResponse.hasOwnProperty(searchFor)){methods.showResults(cachedResponse[searchFor]);return;}
if(searchFor.length<d.minChars){$(d.resultBlock).html('');methods.hideLoader();return;}
if(d.showLoader){methods.showLoader();}
clearTimeout(keyupTimeout);keyupTimeout=setTimeout(function(){methods.ajaxRequest();},300);},ajaxRequest:function(){var data={action:'aws_action',keyword:searchFor,aws_page:d.pageId,aws_tax:d.tax,lang:d.lang,pageurl:window.location.href,typedata:'json'};requests.push($.ajax({type:'POST',url:ajaxUrl,data:data,dataType:'json',success:function(response){cachedResponse[searchFor]=response;methods.showResults(response);methods.showResultsBlock();methods.analytics(searchFor);},error:function(jqXHR,textStatus,errorThrown){console.log("Request failed: "+textStatus);methods.hideLoader();}}));},showResults:function(response){var resultNum=0;var html='<ul>';if(typeof response.tax!=='undefined'){$.each(response.tax,function(i,taxes){if((typeof taxes!=='undefined')&&taxes.length>0){$.each(taxes,function(i,taxitem){resultNum++;html+='<li class="aws_result_item aws_result_tag">';html+='<a class="aws_result_link" href="'+taxitem.link+'" >';html+='<span class="aws_result_content">';html+='<span class="aws_result_title">';html+=taxitem.name;if(taxitem.count){html+='<span class="aws_result_count">&nbsp;('+taxitem.count+')</span>';}
html+='</span>';if((typeof taxitem.excerpt!=='undefined')&&taxitem.excerpt){html+='<span class="aws_result_excerpt">'+taxitem.excerpt+'</span>';}
html+='</span>';html+='</a>';html+='</li>';});}});}
if((typeof response.products!=='undefined')&&response.products.length>0){$.each(response.products,function(i,result){resultNum++;html+='<li class="aws_result_item">';html+='<a class="aws_result_link" href="'+result.link+'" >';if(result.image){html+='<span class="aws_result_image">';html+='<img src="'+result.image+'">';html+='</span>';}
html+='<span class="aws_result_content">';html+='<span class="aws_result_title">';if(result.featured){html+='<span class="aws_result_featured" title="Featured"><svg version="1.1" viewBox="0 0 20 21" xmlns="http://www.w3.org/2000/svg" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" xmlns:xlink="http://www.w3.org/1999/xlink"><g fill-rule="evenodd" stroke="none" stroke-width="1"><g transform="translate(-296.000000, -422.000000)"><g transform="translate(296.000000, 422.500000)"><path d="M10,15.273 L16.18,19 L14.545,11.971 L20,7.244 L12.809,6.627 L10,0 L7.191,6.627 L0,7.244 L5.455,11.971 L3.82,19 L10,15.273 Z"/></g></g></g></svg></span>';}
html+=result.title;html+='</span>';if(result.stock_status){var statusClass=result.stock_status.status?'in':'out';html+='<span class="aws_result_stock '+statusClass+'">';html+=result.stock_status.text;html+='</span>';}
if(result.sku){html+='<span class="aws_result_sku">'+translate.sku+result.sku+'</span>';}
if(result.excerpt){html+='<span class="aws_result_excerpt">'+result.excerpt+'</span>';}
if(result.price){html+='<span class="aws_result_price">'+result.price+'</span>';}
html+='</span>';if(result.on_sale){html+='<span class="aws_result_sale">';html+='<span class="aws_onsale">'+translate.sale+'</span>';html+='</span>';}
html+='</a>';html+='</li>';});if(d.showMore){html+='<li class="aws_result_item aws_search_more"><a href="#">'+translate.showmore+'</a></li>';}}
if(!resultNum){html+='<li class="aws_result_item aws_no_result">'+translate.noresults+'</li>';}
html+='</ul>';html=AwsHooks.apply_filters('aws_results_html',html,{response:response,data:d});methods.hideLoader();$(d.resultBlock).html(html);methods.showResultsBlock();if(eShowResults){self[0].dispatchEvent(eShowResults);}},showResultsBlock:function(){methods.resultLayout();methods.resultsShow();},showLoader:function(){$searchForm.addClass('aws-processing');},hideLoader:function(){$searchForm.removeClass('aws-processing');},resultsShow:function(){$(d.resultBlock).show();$searchForm.addClass('aws-form-active');},resultsHide:function(){$(d.resultBlock).hide();$searchForm.removeClass('aws-form-active');},onFocus:function(event){if(methods.isMobile()&&d.mobileScreen&&!$('body').hasClass('aws-overlay')){methods.showMobileLayout();}
if(searchFor!==''){methods.showResultsBlock();}},hideResults:function(event){if(!$(event.target).closest(".aws-container").length){methods.resultsHide();}},isResultsVisible:function(){return $(d.resultBlock).is(":visible");},removeHovered:function(){$(d.resultBlock).find('.aws_result_item').removeClass('hovered');},resultLayout:function(){var $resultsBlock=$(d.resultBlock);var offset=self.offset();var bodyOffset=$('body').offset();var bodyPosition=$('body').css('position');var bodyHeight=$(document).height();var resultsHeight=$resultsBlock.height();if(offset&&bodyOffset){var styles={width:self.outerWidth(),top:0,left:0};if(bodyPosition==='relative'||bodyPosition==='absolute'||bodyPosition==='fixed'){styles.top=offset.top+$(self).innerHeight()-bodyOffset.top;styles.left=offset.left-bodyOffset.left;}else{styles.top=offset.top+$(self).innerHeight();styles.left=offset.left;}
if(bodyHeight-offset.top<500){resultsHeight=methods.getResultsBlockHeight();if((bodyHeight-offset.top<resultsHeight)&&(offset.top>=resultsHeight)){styles.top=styles.top-resultsHeight-$(self).innerHeight();}}
styles=AwsHooks.apply_filters('aws_results_layout',styles,{resultsBlock:$resultsBlock,form:self});$resultsBlock.css(styles);}},getResultsBlockHeight:function(){var $resultsBlock=$(d.resultBlock);var resultsHeight=$resultsBlock.height();if(resultsHeight===0){var copied_elem=$resultsBlock.clone().attr("id",false).css({visibility:"hidden",display:"block",position:"absolute"});$("body").append(copied_elem);resultsHeight=copied_elem.height();copied_elem.remove();}
return resultsHeight;},showMobileLayout:function(){if(!methods.isFixed()){self.after('<div class="aws-placement-container"></div>');self.addClass('aws-mobile-fixed').prepend('<div class="aws-mobile-fixed-close"><svg width="17" height="17" viewBox="1.5 1.5 21 21"><path d="M22.182 3.856c.522-.554.306-1.394-.234-1.938-.54-.543-1.433-.523-1.826-.135C19.73 2.17 11.955 10 11.955 10S4.225 2.154 3.79 1.783c-.438-.371-1.277-.4-1.81.135-.533.537-.628 1.513-.25 1.938.377.424 8.166 8.218 8.166 8.218s-7.85 7.864-8.166 8.219c-.317.354-.34 1.335.25 1.805.59.47 1.24.455 1.81 0 .568-.456 8.166-7.951 8.166-7.951l8.167 7.86c.747.72 1.504.563 1.96.09.456-.471.609-1.268.1-1.804-.508-.537-8.167-8.219-8.167-8.219s7.645-7.665 8.167-8.218z"></path></svg></div>');$('body').addClass('aws-overlay').append('<div class="aws-overlay-mask"></div>').append(self);$searchField.focus();}},hideMobileLayout:function(){$('.aws-placement-container').after(self).remove();self.removeClass('aws-mobile-fixed');$('body').removeClass('aws-overlay');$('.aws-mobile-fixed-close').remove();$('.aws-overlay-mask').remove();},isFixed:function(){var $checkElements=self.add(self.parents());var isFixed=false;$checkElements.each(function(){if($(this).css("position")==="fixed"){isFixed=true;return false;}});return isFixed;},analytics:function(label){if(d.useAnalytics){try{if(typeof gtag!=='undefined'){gtag('event','AWS search',{'event_label':label,'event_category':'AWS Search Term'});}
if(typeof ga!=='undefined'){ga('send','event','AWS search','AWS Search Term',label);ga('send','pageview','/?s='+encodeURIComponent('ajax-search:'+label));}}
catch(error){}}},addClasses:function(){if(methods.isMobile()||d.showClear){$searchForm.addClass('aws-show-clear');}},isMobile:function(){var check=false;(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check=true;})(navigator.userAgent||navigator.vendor||window.opera);return check;},};var self=$(this),$searchForm=self.find('.aws-search-form'),$searchField=self.find('.aws-search-field'),$searchButton=self.find('.aws-search-btn'),haveResults=false,eShowResults=false,requests=Array(),searchFor='',keyupTimeout,cachedResponse=new Array();var ajaxUrl=(self.data('url')!==undefined)?self.data('url'):false;if(document.createEvent){eShowResults=document.createEvent("Event");eShowResults.initEvent('awsShowingResults',true,true);eShowResults.eventName='awsShowingResults';}
if(options==='relayout'){var d=self.data(pluginPfx);methods.resultLayout();return;}
instance++;self.data(pluginPfx,{minChars:(self.data('min-chars')!==undefined)?self.data('min-chars'):1,lang:(self.data('lang')!==undefined)?self.data('lang'):false,showLoader:(self.data('show-loader')!==undefined)?self.data('show-loader'):true,showMore:(self.data('show-more')!==undefined)?self.data('show-more'):true,showPage:(self.data('show-page')!==undefined)?self.data('show-page'):true,showClear:(self.data('show-clear')!==undefined)?self.data('show-clear'):false,mobileScreen:(self.data('mobile-screen')!==undefined)?self.data('mobile-screen'):false,useAnalytics:(self.data('use-analytics')!==undefined)?self.data('use-analytics'):false,instance:instance,resultBlock:'#aws-search-result-'+instance,pageId:(self.data('page-id')!==undefined)?self.data('page-id'):0,tax:(self.data('tax')!==undefined)?self.data('tax'):0});var d=self.data(pluginPfx);if($searchForm.length>0){methods.init.call(this);}
$searchField.on('keyup input',function(e){if(e.keyCode!=40&&e.keyCode!=38){methods.onKeyup(e);}});$searchField.on('focus',function(e){$searchForm.addClass('aws-focus');methods.onFocus(e);});$searchField.on('focusout',function(e){$searchForm.removeClass('aws-focus');});$searchForm.on('keypress',function(e){if(e.keyCode==13&&(!d.showPage||$searchField.val()==='')){e.preventDefault();}});$searchButton.on('click',function(e){if(d.showPage&&$searchField.val()!==''){$searchForm.submit();}});$searchForm.find('.aws-search-clear').on('click',function(e){$searchField.val('');$searchField.focus();methods.resultsHide();$(d.resultBlock).html('');searchFor='';});$(document).on('click',function(e){methods.hideResults(e);});$(window).on('resize',function(e){methods.resultLayout();});$(window).on('scroll',function(e){if($(d.resultBlock).css('display')=='block'){methods.resultLayout();}});$(d.resultBlock).on('mouseenter','.aws_result_item',function(){methods.removeHovered();$(this).addClass('hovered');$searchField.trigger('mouseenter');});$(d.resultBlock).on('mouseleave','.aws_result_item',function(){methods.removeHovered();});$(d.resultBlock).on('click','.aws_search_more',function(e){e.preventDefault();$searchForm.submit();});$(self).on('click','.aws-mobile-fixed-close',function(e){methods.hideMobileLayout();});$(window).on('keydown',function(e){if(e.keyCode==40||e.keyCode==38){if(methods.isResultsVisible()){e.stopPropagation();e.preventDefault();var $item=$(d.resultBlock).find('.aws_result_item');var $hoveredItem=$(d.resultBlock).find('.aws_result_item.hovered');var $itemsList=$(d.resultBlock).find('ul');if(e.keyCode==40){if($hoveredItem.length>0){methods.removeHovered();$hoveredItem.next().addClass('hovered');}else{$item.first().addClass('hovered');}}
if(e.keyCode==38){if($hoveredItem.length>0){methods.removeHovered();$hoveredItem.prev().addClass('hovered');}else{$item.last().addClass('hovered');}}
var scrolledTop=$itemsList.scrollTop();var position=$(d.resultBlock).find('.aws_result_item.hovered').position();if(position){$itemsList.scrollTop(position.top+scrolledTop)}}}});};$(document).ready(function(){$(selector).each(function(){$(this).aws_search();});$('[data-avia-search-tooltip]').on('click',function(){window.setTimeout(function(){$(selector).aws_search();},1000);});var $filters_widget=$('.woocommerce.widget_layered_nav_filters');var searchQuery=window.location.search;if($filters_widget.length>0&&searchQuery){if(searchQuery.indexOf('type_aws=true')!==-1){var $filterLinks=$filters_widget.find('ul li.chosen a');if($filterLinks.length>0){var addQuery='&type_aws=true';$filterLinks.each(function(){var filterLink=$(this).attr("href");if(filterLink&&filterLink.indexOf('post_type=product')!==-1){$(this).attr("href",filterLink+addQuery);}});}}}});})(jQuery);
(function(){const winPosition=()=>{const images=document.querySelectorAll('img[data-src]');const position=window.scrollY+window.outerHeight;images.forEach(image=>{if(image.src=='data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs='&&position>image.y){image.src=image.dataset.src;}});}
window.addEventListener('scroll',winPosition);window.addEventListener('load',winPosition);})();(function(){const mainNavBtn=document.getElementById('mainNavBtn');mainNavBtn.addEventListener('click',function(e){e.preventDefault();document.getElementById('mainNav').classList.toggle('active');this.classList.toggle('active');});})();(function(){const subscribeForm=document.getElementById('sForm');subscribeForm.addEventListener('submit',e=>{e.preventDefault();let formData=new FormData();formData.append('action','subscribeForm');formData.append('email',document.getElementById('s_email').value);let request=new XMLHttpRequest();request.open('POST',data.ajax,true);request.onload=function(){if(this.status>=200&&this.status<400){if(this.response=='sucess'){alert('Thank you for submitting. We will contact you shortly!');}else{alert('Something went wrong. Reload the page and try again!');}}else{alert('Request status: '+this.status);}}
request.onerror=function(){alert('Request error!');}
request.send(formData);});})();const contactForm=document.getElementById('contact');contactForm.addEventListener('submit',e=>{e.preventDefault();let formData=new FormData();formData.append('action','contactForm');formData.append('name',document.getElementById('name').value);formData.append('email',document.getElementById('email').value);formData.append('phone',document.getElementById('phone').value);formData.append('message',document.getElementById('message').value);let request=new XMLHttpRequest();request.open('POST',data.ajax,true);request.onload=function(){if(this.status>=200&&this.status<400){if(this.response=='sucess'){alert('Thank you for submitting. We will contact you shortly!');}else{alert('Something went wrong. Reload the page and try again!');}}else{alert('Request status: '+this.status);}}
request.onerror=function(){alert('Request error!');}
request.send(formData);});