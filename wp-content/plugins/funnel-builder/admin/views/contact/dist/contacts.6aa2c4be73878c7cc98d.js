(window.webpackJsonp=window.webpackJsonp||[]).push([[7],{414:function(e,t,n){"use strict";n.d(t,"a",(function(){return d})),n.d(t,"b",(function(){return O}));var c=n(8),a=n(147),r=n(0),l=n(11),s=n.n(l),o=n(443),i=n(426);var u={name:"8kj89b",styles:"flex-direction:row-reverse;"},f=Object(o.a)("div",{target:"eboqfv50",label:"Flex"})("box-sizing:border-box;display:flex;width:100%;",(function(e){var t=e.align,n={top:"flex-start",bottom:"flex-end"}[t]||t;return Object(i.b)({alignItems:n},"")})," ",(function(e){var t=e.justify,n=e.isReversed,c={left:"flex-start",right:"flex-end"},a=c[t]||t;return n&&c[t]&&(a="left"===t?c.right:c.left),Object(i.b)({justifyContent:a},"")})," ",(function(e){var t=e.gap,n=e.isReversed,c="number"==typeof t?4*t:4,a="margin-".concat(n?"left":"right");return Object(i.b)("> *{",a,":",c,"px;&:last-child{",a,":0;}}")})," ",(function(e){return e.isReversed?u:""}),""),b=Object(o.a)("div",{target:"eboqfv51",label:"Item"})({name:"13luw5d",styles:"box-sizing:border-box;min-width:0;max-width:100%;"}),m=Object(o.a)(b,{target:"eboqfv52",label:"Block"})({name:"1rr4qq7",styles:"flex:1;"});Object(r.forwardRef)((function(e,t){var n=e.className,l=Object(a.a)(e,["className"]),o=s()("components-flex__block",n);return Object(r.createElement)(m,Object(c.a)({},l,{className:o,ref:t}))}));var O=Object(r.forwardRef)((function(e,t){var n=e.className,l=Object(a.a)(e,["className"]),o=s()("components-flex__item",n);return Object(r.createElement)(b,Object(c.a)({},l,{className:o,ref:t}))}));var d=Object(r.forwardRef)((function(e,t){var n=e.align,l=void 0===n?"center":n,o=e.className,i=e.gap,u=void 0===i?2:i,b=e.justify,m=void 0===b?"space-between":b,O=e.isReversed,d=void 0!==O&&O,j=Object(a.a)(e,["align","className","gap","justify","isReversed"]),p=s()("components-flex",o);return Object(r.createElement)(f,Object(c.a)({},j,{align:l,className:p,ref:t,gap:u,justify:m,isReversed:d}))}))},453:function(e,t,n){},503:function(e,t,n){},504:function(e,t,n){},505:function(e,t,n){},506:function(e,t,n){},533:function(e,t,n){"use strict";n.r(t);var c=n(0),a=n(3),r=n(10),l=n(33),s=n(15),o=n(5),i=n(425),u=n(11),f=n.n(u),b=n(13),m=n(240),O=n(414),d=(n(453),n(417)),j=n(132),p=n.n(j),v=n(246),w=n.n(v),y=n(457),g=n.n(y),E=n(412),_=n.n(E),h=n(40);var N={name:"contacts",className:"bwf-search-bwf-contacts-result",options:function(e){return Promise.resolve([])},isDebounced:!0,getOptionIdentifier:function(e){return e.id},getOptionKeywords:function(e){return[e.first_name,e.last_name,e.email]},getFreeTextOptions:function(e,t){return[{key:"name",label:Object(c.createElement)("span",{key:"name",className:"bwf-search-result-name"},_()({mixedString:Object(a.__)("All contacts with names that include {{query /}}","funnel-builder"),components:{query:Object(c.createElement)("strong",{className:"components-form-token-field__suggestion-match"},e)}})),value:{id:e,name:e,contacts:t.map((function(e){return Object(o.has)(e,"value")?e.value:e})),searchTerm:e}}]},getOptionLabel:function(e,t){var n=function(e,t){if(!t)return null;var n=e.toLocaleLowerCase().indexOf(t.toLocaleLowerCase());return{suggestionBeforeMatch:Object(h.decodeEntities)(e.substring(0,n)),suggestionMatch:Object(h.decodeEntities)(e.substring(n,n+t.length)),suggestionAfterMatch:Object(h.decodeEntities)(e.substring(n+t.length))}}(Object(b.d)(e),t)||{};return Object(c.createElement)("span",{key:"name",className:"bwf-search-result-name","aria-label":Object(b.d)(e)},n.suggestionBeforeMatch,Object(c.createElement)("strong",{className:"components-form-token-field__suggestion-match"},n.suggestionMatch),n.suggestionAfterMatch)},getOptionCompletion:function(e){return e}};function P(e,t,n,c,a,r,l){try{var s=e[r](l),o=s.value}catch(e){return void n(e)}s.done?t(o):Promise.resolve(o).then(c,a)}var k=function(e){var t=e.query,n=t.hasOwnProperty("s")?t.s:"",r=p()(n)?[]:[{key:n,label:Object(a.__)("Search Term: ","funnel-builder")+n}],s=function(){var e,n=(e=regeneratorRuntime.mark((function e(n){var c,a,r;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(w()(n)){e.next=2;break}return e.abrupt("return");case 2:if(c=n.find((function(e){return g()(e,"searchTerm")})),a=Object(o.isUndefined)(c)?void 0:c.searchTerm,r=g()(t,"s")&&!p()(t.s)?t.s:"",a!==r){e.next=7;break}return e.abrupt("return");case 7:Object(l.j)({s:a},"/contacts",t);case 8:case"end":return e.stop()}}),e)})),function(){var t=this,n=arguments;return new Promise((function(c,a){var r=e.apply(t,n);function l(e){P(r,c,a,l,s,"next",e)}function s(e){P(r,c,a,l,s,"throw",e)}l(void 0)}))});return function(e){return n.apply(this,arguments)}}();return Object(c.createElement)(d.a,{autocompleter:N,multiple:!1,allowFreeTextSearch:!0,inlineTags:!0,selected:r,onChange:s,placeholder:Object(a.__)("Search by contact","funnel-builder"),showClearButton:!0,disabled:!1,bwfMaintainSingleTerm:!0})},C=n(435),S=n.n(C),D=(n(503),function(e){var t=e.name,n=e.first_name,a=e.last_name;return Object(o.isEmpty)(t)&&(t=Object(b.n)([n,a]," ")),Object(c.createElement)("div",{className:"bwf-c-name-initials"},Object(c.createElement)("span",null,Object(o.isEmpty)(S()(t))?"--":Object(b.h)(t)))});D.defaultProps={name:"",first_name:"",last_name:""};var x=D,I=(n(504),function(e){var t=e.contact,n=t.f_name,r=t.l_name,l=t.id,s=t.date,o=Object(b.n)([n,r]," ");return Object(c.createElement)(O.a,{className:"bwf-c-contact-basic-info-cell",justify:"flex-start"},Object(c.createElement)(O.b,{className:"bwf-c-avatar"},Object(c.createElement)(x,{name:o})),Object(c.createElement)(O.b,null,Object(c.createElement)(O.a,{style:{flexDirection:"column"},align:"flex-start"},Object(c.createElement)(O.b,{style:{padding:0}},Object(c.createElement)("span",{className:"bwf-c-contact-name"},p()(S()(o))?Object(a.__)("No name","funnel-builder"):o,0==l&&Object(c.createElement)("span",null," ( ",Object(a.__)("Dummy Data","funnel-builder")," )"))),Object(c.createElement)(O.b,null,Object(b.f)(s)))))});I.defaultProps={contact:{first_name:"",last_name:"",id:0,creation_date:""}};var T=I,A=n(151);function R(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var c=Object.getOwnPropertySymbols(e);t&&(c=c.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,c)}return n}function q(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?R(Object(n),!0).forEach((function(t){F(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):R(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function F(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function L(e,t){if(null==e)return{};var n,c,a=function(e,t){if(null==e)return{};var n,c,a={},r=Object.keys(e);for(c=0;c<r.length;c++)n=r[c],t.indexOf(n)>=0||(a[n]=e[n]);return a}(e,t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);for(c=0;c<r.length;c++)n=r[c],t.indexOf(n)>=0||Object.prototype.propertyIsEnumerable.call(e,n)&&(a[n]=e[n])}return a}var M=function(){var e=Object(A.a)("contacts"),t=e.getStateProp;return q(q({},L(e,["getStateProp"])),{},{getContacts:function(){return t("contacts_list")},getPageNumber:function(){return parseInt(t("page_no"))},getPerPageCount:function(){return parseInt(t("limit"))},getTotalContacts:function(){return parseInt(t("total"))}})},z=n(152);function B(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var c=Object.getOwnPropertySymbols(e);t&&(c=c.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,c)}return n}function Z(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?B(Object(n),!0).forEach((function(t){U(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):B(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function U(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function J(e,t){if(null==e)return{};var n,c,a=function(e,t){if(null==e)return{};var n,c,a={},r=Object.keys(e);for(c=0;c<r.length;c++)n=r[c],t.indexOf(n)>=0||(a[n]=e[n]);return a}(e,t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);for(c=0;c<r.length;c++)n=r[c],t.indexOf(n)>=0||Object.prototype.propertyIsEnumerable.call(e,n)&&(a[n]=e[n])}return a}var Q=function(){var e=Object(z.a)("contacts"),t=e.fetch,n=e.setStateProp;return Z(Z({},J(e,["fetch","setStateProp"])),{},{setStateProp:n,fetch:function(e,c,a){var r=arguments.length>3&&void 0!==arguments[3]&&arguments[3],l=e.s,s=e.edit,o={page_no:c,limit:a,s:l};r&&(o.total_count="yes"),t("GET",Object(b.c)("/woofunnels-analytics/funnels/"+parseInt(s)+"/contacts/"),o),n("page_no",parseInt(c)),n("limit",parseInt(a))},setContacts:function(e,t,c,a){n("contacts_list",e),n("page_no",parseInt(t)),n("limit",parseInt(c)),n("total",parseInt(a))},setTotalCount:function(e){return n("total",parseInt(e))}})},G=n(236),H=n(66),K=n(149),V=Object(c.memo)((function(e){var t=e.query,n=e.onContactClick,l=(e.resetBulkActions,t.s),s=Q().fetch,i=M(),u=i.getPageNumber,d=i.getPerPageCount,j=i.getLoading,p=i.getTotalContacts,v=i.getContacts,w=Object(b.e)(),y=v(),g=u(),E=d(),_=j(),h=p();Object(c.useEffect)((function(){s(t,1,E,!0)}),[l]);var N=function(e){return Object(c.createElement)("div",{onClick:function(){return n(e)}},Object(c.createElement)(T,{contact:e,onClick:function(){return n(e)}}))},P=function(t){return Object(c.createElement)(H.a,{label:Object(a.__)("Quick Actions","funnel-builder"),renderContent:function(l){var s=l.onToggle;return Object(c.createElement)(c.Fragment,null,Object(c.createElement)(K.a,{isClickable:!0,onInvoke:function(){s(),n(t)}},Object(c.createElement)(O.a,{justify:"flex-start"},Object(c.createElement)(O.b,null,Object(c.createElement)(r.Icon,{icon:"visibility",size:"16"})),Object(c.createElement)(O.b,null,Object(a.__)("View Details","funnel-builder")))),Object(c.createElement)(K.a,{isClickable:!0,onInvoke:function(){s(),e.onRequestDeleteContacts([t.id])}},Object(c.createElement)(O.a,{justify:"flex-start"},Object(c.createElement)(O.b,null,Object(c.createElement)(r.Icon,{icon:"trash",size:"16"})),Object(c.createElement)(O.b,null,Object(a.__)("Delete","funnel-builder")))))}})},C=function(e){return Object(c.createElement)("div",{className:"bwf-c-contact-details-cell"},e.email&&Object(c.createElement)(O.a,{justify:"start"},Object(c.createElement)(O.b,null,Object(c.createElement)(r.Icon,{icon:"email"})),Object(c.createElement)(O.b,null,0!=e.id?Object(c.createElement)(G.a,{href:"mailto:"+e.email,type:"external"},e.email):e.email)))},S=function(e){return Object(o.merge)({f_name:"",l_name:"",email:"",in_checkout:"N/A",in_optin:"N/A",in_bump:"N/A",in_upsell:"N/A",total_revenue:0,date:"N/A"},e)},D=[{key:"contact",label:Object(a.__)("Name","funnel-builder"),required:!0,cellClassName:"bwf-col-contact"},{key:"contact_details",label:Object(a.__)("Details","funnel-builder"),cellClassName:"bwf-col-contact-details"},{key:"optin",label:Object(a.__)("Optin","funnel-builder"),cellClassName:"bwf-col-stats-m"},{key:"checkout",label:Object(a.__)("Checkout","funnel-builder"),cellClassName:"bwf-col-stats-m"},{key:"bump",label:Object(a.__)("Bump","funnel-builder"),cellClassName:"bwf-col-stats-m"},{key:"upsell",label:Object(a.__)("Upsell","funnel-builder"),cellClassName:"bwf-col-stats-m"},{key:"total_revenue",label:Object(a.__)("Total Spend","funnel-builder"),cellClassName:"bwf-col-revenue bwf-col-stats-m"},{key:"delete",label:"",required:!0,cellClassName:"bwf-col-action-s"}],x=y.map((function(e){var t=S(e);return[{display:N(t),value:Object(b.n)([t.first_name,t.last_name]," ")},{display:C(t),value:t.email},{display:Object(b.b)(t,t.in_optin,""),value:t.in_optin},{display:Object(b.b)(t,t.in_checkout,t.aero_revenue),value:t.in_checkout},{display:Object(b.b)(t,t.in_bump,t.bump_revenue),value:t.in_bump},{display:Object(b.b)(t,t.in_upsell,t.upsell_revenue),value:t.in_upsell},{display:w.formatAmount(t.total_revenue),value:w.formatAmount(t.total_revenue)},{display:P(t),value:null}]})),I=function(e){e!==E&&s(t,1,e)},A=f()("bwfcrm-contacts-list",{"has-search":!0}),R=parseInt(h)>0?" ("+h+")":"",q=[{id:0,f_name:"John",l_name:"Doe",email:"johndoe@example.com",in_checkout:!0,in_optin:null,in_bump:!0,in_upsell:!0,total_revenue:121.6,date:"2020-10-31T07:37:16Z"}].map((function(e){var t=S(e);return[{display:N(t),value:Object(b.n)([t.first_name,t.last_name]," ")},{display:C(t),value:t.email},{display:Object(b.b)(t,t.in_optin,""),value:t.in_optin},{display:Object(b.b)(t,t.in_checkout,t.aero_revenue),value:t.in_checkout},{display:Object(b.b)(t,t.in_bump,t.bump_revenue),value:t.in_bump},{display:Object(b.b)(t,t.in_upsell,t.upsell_revenue),value:t.in_upsell},{display:w.formatAmount(t.total_revenue),value:w.formatAmount(t.total_revenue)},{display:"",value:null}]}));return Object(c.createElement)(m.a,{className:A,title:Object(a.__)("Contacts","funnel-builder")+R,rows:0==x.length?q:x,headers:D,query:{paged:g},rowsPerPage:E,totalRows:h,isLoading:_,onPageChange:function(e,n){s(t,e,E)},onQueryChange:function(e){return"per_page"!==e?function(){}:I},actions:[Object(c.createElement)(k,{key:"search",query:t})],rowHeader:!0,showMenu:!1})}));V.defaultProps={contacts:[]};var $=V;function W(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var c=Object.getOwnPropertySymbols(e);t&&(c=c.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,c)}return n}function X(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?W(Object(n),!0).forEach((function(t){Y(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):W(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function Y(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function ee(e,t){if(null==e)return{};var n,c,a=function(e,t){if(null==e)return{};var n,c,a={},r=Object.keys(e);for(c=0;c<r.length;c++)n=r[c],t.indexOf(n)>=0||(a[n]=e[n]);return a}(e,t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);for(c=0;c<r.length;c++)n=r[c],t.indexOf(n)>=0||Object.prototype.propertyIsEnumerable.call(e,n)&&(a[n]=e[n])}return a}var te=function(){var e=Object(z.a)("contactsDelete"),t=e.fetch,n=e.setStateProp,c=e.setLoading;return X(X({},ee(e,["fetch","setStateProp","setLoading"])),{},{setStateProp:n,setLoading:c,doDeleteContacts:function(e,n,a){if(Object(o.isArray)(e)){var r={page_no:1,limit:a,delete_cid:Object(o.join)(e,",")};c("isLoading",!0);var l=setTimeout((function(){t("GET",Object(b.c)("/woofunnels-analytics/funnels/"+parseInt(n)+"/contacts"),r),clearTimeout(l)}),1e3)}},clearAfterDeleteContacts:function(){return n("contacts_after_delete",[])},clearSuccess:function(){return n("success",!1)}})};function ne(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var c=Object.getOwnPropertySymbols(e);t&&(c=c.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,c)}return n}function ce(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?ne(Object(n),!0).forEach((function(t){ae(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):ne(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function ae(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function re(e,t){if(null==e)return{};var n,c,a=function(e,t){if(null==e)return{};var n,c,a={},r=Object.keys(e);for(c=0;c<r.length;c++)n=r[c],t.indexOf(n)>=0||(a[n]=e[n]);return a}(e,t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);for(c=0;c<r.length;c++)n=r[c],t.indexOf(n)>=0||Object.prototype.propertyIsEnumerable.call(e,n)&&(a[n]=e[n])}return a}var le=function(){var e=Object(A.a)("contactsDelete"),t=e.getStateProp;return ce(ce({},re(e,["getStateProp"])),{},{getContactsAfterDelete:function(){return t("contacts_after_delete")},getSuccess:function(){return t("success")}})},se=function(e){var t=e.onRequestClose,n=e.onDeleteSuccess,l=e.onDeleteFailed,s=e.deleteContacts,o=e.funnelId,i=e.isOpen,u=te(),f=u.doDeleteContacts,b=u.clearAfterDeleteContacts,m=u.clearError,O=u.clearSuccess,d=le(),j=d.getLoading,p=d.getContactsAfterDelete,v=d.getError,w=d.getSuccess,y=j(),g=p(),E=v(),_=w(),h=function(){y||t()};return Object(c.useEffect)((function(){if(null!==E||_)var e=setTimeout((function(){y||null===E||l(E),!y&&_&&n(g),b(),m(),O(),clearTimeout(e)}),2e3)}),[E,_]),i?Object(c.createElement)(r.Modal,{isDismissible:!0,className:"bwf-admin-modal bwf-admin-modal-medium bwf-admin-modal-no-header",onRequestClose:h},y&&Object(c.createElement)("div",{className:"bwf-t-center"},Object(c.createElement)("div",{className:"bwf-t-center"},Object(c.createElement)("div",{className:"bwf-spin-loader bwf-spin-loader-xl"}))),!y&&_&&Object(c.createElement)("div",{className:"bwf-t-center"},Object(c.createElement)("div",{style:{width:"100px",display:"inline-block"},dangerouslySetInnerHTML:{__html:wffn.icons.success_check}})),!y&&null!==E&&Object(c.createElement)("div",{className:"bwf-t-center"},Object(c.createElement)("div",{style:{width:"100px",display:"inline-block"},dangerouslySetInnerHTML:{__html:wffn.icons.error_cross}})),!y&&null===E&&!_&&Object(c.createElement)(c.Fragment,null,Object(c.createElement)("div",{className:"bwf_clear"}),Object(c.createElement)("div",{className:"bwf-t-center"},Object(c.createElement)("div",{className:"bwf-h1 bwf_align_center"},Object(a.__)("Are you sure you want to delete this contact?","funnel-builder")),Object(c.createElement)("div",{className:"bwf_clear_20"}),Object(c.createElement)("div",{className:"bwf-p"},Object(a.__)("This action can't be undone.","funnel-builder"))),Object(c.createElement)("div",{className:"bwf_clear_30"}),Object(c.createElement)("div",{className:"bwf-t-center bwf-buttons-wrapper"},Object(c.createElement)(r.Button,{isPrimary:!0,onClick:function(){y||f(s,o,25)}},Object(a.__)("Confirm","funnel-builder")),Object(c.createElement)(r.Button,{isSecondary:!0,onClick:h}," ",Object(a.__)("Cancel","funnel-builder")," "))),!y&&null!==E&&Object(c.createElement)(c.Fragment,null,Object(c.createElement)("div",{className:"bwf_clear_20"}),Object(c.createElement)("div",{className:"bwf-t-center"},Object(c.createElement)("div",{className:"bwf-p"}," ",Object(a.__)("Error: ","funnel-builder")+E.hasOwnProperty("message")?E.message:Object(a.__)("There are some technical difficulties while deleting step. Please contact support","funnel-builder")," "))),!y&&_&&Object(c.createElement)(c.Fragment,null,Object(c.createElement)("div",{className:"bwf_clear_20"}),Object(c.createElement)("div",{className:"bwf-t-center"},Object(c.createElement)("div",{className:"bwf-h1"}," ",Object(a.__)("Contact deleted successfully","funnel-builder"))))):null};se.defaultProps={deleteContacts:[],funnelId:0,onRequestClose:function(){},onDeleteSuccess:function(){},onDeleteFailed:function(){}};var oe=se,ie=(n(505),n(506),function(e){var t=e.size,n=e.isOpen,a=e.onRequestClose,r=e.children,l=n?"is-open":"",s=Object(c.useRef)(),o=Object(c.useRef)(n);return Object(c.useEffect)((function(){o.current=n}),[n]),Object(c.useEffect)((function(){jQuery("body").click((function(e){!(jQuery(s.current).find(e.target).length>0)&&o.current&&a()}))}),[]),Object(c.createElement)("div",{className:"wffn-side-panel "+l,ref:s,style:{width:t+"px"}},r)}),ue=function(){return Object(c.createElement)("div",{className:"wffn-cs-body"},Object(c.createElement)("div",{className:"wffn-cs-details"},Object(c.createElement)("div",{className:"wffn-cs-d-head wffn-cs-gap-border"},Object(c.createElement)("div",{className:"wffn-cs-gravatar is-preview"}),Object(c.createElement)("div",{className:"wffn-cs-name"},Object(c.createElement)("span",{className:"is-placeholder long"})),Object(c.createElement)("div",{className:"wffn-cs-email"},Object(c.createElement)("span",{className:"is-placeholder"}))),Object(c.createElement)("div",{className:"wffn-cs-d-data wffn-cs-gap-border"},Object(c.createElement)("div",{className:"wffn-cs-head"},"Additional"),Object(c.createElement)("div",{className:"wffn-cs-col2"},Object(c.createElement)("div",{className:"wffn-cs-list"},Object(c.createElement)("div",{className:"wffn-cs-label"},"Contact ID"),Object(c.createElement)("div",{className:"wffn-cs-value"},Object(c.createElement)("span",{className:"is-placeholder"}))),Object(c.createElement)("div",{className:"wffn-cs-list"},Object(c.createElement)("div",{className:"wffn-cs-label"},"Creation Date"),Object(c.createElement)("div",{className:"wffn-cs-value"},Object(c.createElement)("span",{className:"is-placeholder"})))))),Object(c.createElement)("div",{className:"wffn-cs-timeline"},Object(c.createElement)("div",{className:"wffn-cs-head"},"Conversions"),Object(c.createElement)("section",{className:"wffn-cs-card"},Object(c.createElement)("div",{className:"wffn-cs-card-l"},Object(c.createElement)("div",{className:"wffn-cs-card-title"},Object(c.createElement)("span",{className:"wffn-cs-status is-preview"})," ",Object(c.createElement)("span",{className:"is-placeholder"})),Object(c.createElement)("div",{className:"wffn-cs-card-subtitle"},Object(c.createElement)("span",{className:"is-placeholder long"})))),Object(c.createElement)("section",{className:"wffn-cs-card"},Object(c.createElement)("div",{className:"wffn-cs-card-l"},Object(c.createElement)("div",{className:"wffn-cs-card-title"},Object(c.createElement)("span",{className:"wffn-cs-status is-preview"})," ",Object(c.createElement)("span",{className:"is-placeholder"})),Object(c.createElement)("div",{className:"wffn-cs-card-subtitle"},Object(c.createElement)("span",{className:"is-placeholder long"}))))))};function fe(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var c=Object.getOwnPropertySymbols(e);t&&(c=c.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,c)}return n}function be(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?fe(Object(n),!0).forEach((function(t){me(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):fe(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function me(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function Oe(e,t){if(null==e)return{};var n,c,a=function(e,t){if(null==e)return{};var n,c,a={},r=Object.keys(e);for(c=0;c<r.length;c++)n=r[c],t.indexOf(n)>=0||(a[n]=e[n]);return a}(e,t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);for(c=0;c<r.length;c++)n=r[c],t.indexOf(n)>=0||Object.prototype.propertyIsEnumerable.call(e,n)&&(a[n]=e[n])}return a}var de=function(){var e=Object(A.a)("contactsSidePanel"),t=e.getStateProp;return be(be({},Oe(e,["getStateProp"])),{},{getContact:function(){return t("contact")},getCache:function(){return t("cache")}})};function je(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var c=Object.getOwnPropertySymbols(e);t&&(c=c.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,c)}return n}function pe(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?je(Object(n),!0).forEach((function(t){ve(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):je(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function ve(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function we(e,t){if(null==e)return{};var n,c,a=function(e,t){if(null==e)return{};var n,c,a={},r=Object.keys(e);for(c=0;c<r.length;c++)n=r[c],t.indexOf(n)>=0||(a[n]=e[n]);return a}(e,t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);for(c=0;c<r.length;c++)n=r[c],t.indexOf(n)>=0||Object.prototype.propertyIsEnumerable.call(e,n)&&(a[n]=e[n])}return a}var ye=function(){var e=Object(z.a)("contactsSidePanel"),t=e.fetch,n=e.setStateProp,c=we(e,["fetch","setStateProp"]),a=(0,de().getCache)();return pe(pe({},c),{},{fetch:function(e,c){a.hasOwnProperty(e)?n("contact",a[e]):t("GET",Object(b.c)("/woofunnels-analytics/funnels/"+parseInt(c)+"/contacts/"+parseInt(e)),{},{contactId:parseInt(e)})}})},ge=function(e){var t=e.object_id,n=e.date,r=e.object_name,l=e.link,s=Object(a.__)("Submitted","funnel-builder");return Object(c.createElement)(c.Fragment,null,Object(c.createElement)("section",{className:"wffn-cs-card"},Object(c.createElement)("div",{className:"wffn-cs-card-l"},Object(c.createElement)("div",{className:"wffn-cs-card-title"},Object(c.createElement)("span",{className:"wffn-cs-status wffn-cs-status-success"}),Object(c.createElement)("span",null,Object(a.__)("Optin ","funnel-builder"),r&&Object(c.createElement)(c.Fragment,null,Object(c.createElement)("a",{href:l},r)),!r&&Object(c.createElement)(c.Fragment,null,Object(c.createElement)("a",null,t))," "+s)),Object(c.createElement)("div",{className:"wffn-cs-card-subtitle",title:Object(b.f)(n,!0)},Object(b.g)(n).fromNow()))))};ge.defaultProps={object_id:0,date:"2020-09-23T06:02:59Z",object_name:null,type:"optin",link:""};var Ee=ge,_e=function(e){var t=e.object_id,n=e.date,r=e.object_name,l=e.link,s=e.action_type_id,o=e.value,i=Object(b.e)(),u=4===parseInt(s),f=u?"wffn-cs-status-success":"wffn-cs-status-failed",m=u?Object(a.__)("Accepted","funnel-builder"):Object(a.__)("Rejected","funnel-builder");return Object(c.createElement)(c.Fragment,null,Object(c.createElement)("section",{className:"wffn-cs-card"},Object(c.createElement)("div",{className:"wffn-cs-card-l"},Object(c.createElement)("div",{className:"wffn-cs-card-title"},Object(c.createElement)("span",{className:"wffn-cs-status "+f}),Object(c.createElement)("span",null,Object(a.__)("Upsell ","funnel-builder"),r&&Object(c.createElement)(c.Fragment,null,Object(c.createElement)("a",{href:l},r)),!r&&Object(c.createElement)(c.Fragment,null,Object(c.createElement)("a",null,t))," "+m)),Object(c.createElement)("div",{className:"wffn-cs-card-subtitle",title:Object(b.f)(n,!0)},Object(b.g)(n).fromNow())),u&&Object(c.createElement)("div",{className:"wffn-cs-card-r"},Object(c.createElement)("div",{className:"wffn-cs-card-date"},i.formatAmount(o)))))};_e.defaultProps={object_id:0,date:"2020-09-23T06:02:59Z",object_name:null,type:"upsell",link:"",action_type_id:6,value:0};var he=_e,Ne=function(e){var t=e.object_id,n=e.date,r=e.object_name,l=e.link,s=e.total_revenue,o=Object(b.e)(),i=Object(a.__)("Completed","funnel-builder");return Object(c.createElement)(c.Fragment,null,Object(c.createElement)("section",{className:"wffn-cs-card"},Object(c.createElement)("div",{className:"wffn-cs-card-l"},Object(c.createElement)("div",{className:"wffn-cs-card-title"},Object(c.createElement)("span",{className:"wffn-cs-status wffn-cs-status-success"}),Object(c.createElement)("span",null,Object(a.__)("Checkout ","funnel-builder"),r&&Object(c.createElement)(c.Fragment,null,Object(c.createElement)("a",{href:l},r)),!r&&Object(c.createElement)(c.Fragment,null,Object(c.createElement)("a",null,t))," "+i)),Object(c.createElement)("div",{className:"wffn-cs-card-subtitle",title:Object(b.f)(n,!0)},Object(b.g)(n).fromNow())),Object(c.createElement)("div",{className:"wffn-cs-card-r"},Object(c.createElement)("div",{className:"wffn-cs-card-date"},o.formatAmount(s)))))};Ne.defaultProps={object_id:0,date:"2020-09-23T06:02:59Z",object_name:null,type:"checkout",link:"",total_revenue:0};var Pe=Ne,ke=function(e){var t=e.object_id,n=e.date,r=e.object_name,l=e.link,s=e.total_revenue,o=e.is_converted,i=Object(b.e)(),u=parseInt(o)?"wffn-cs-status-success":"wffn-cs-status-failed",f=parseInt(o)?Object(a.__)("Accepted","funnel-builder"):Object(a.__)("Rejected","funnel-builder");return Object(c.createElement)("section",{className:"wffn-cs-card"},Object(c.createElement)("div",{className:"wffn-cs-card-l"},Object(c.createElement)("div",{className:"wffn-cs-card-title"},Object(c.createElement)("span",{className:"wffn-cs-status "+u}),Object(c.createElement)("span",null,Object(a.__)("Bump ","funnel-builder"),r&&Object(c.createElement)(c.Fragment,null,Object(c.createElement)("a",{href:l},r)),!r&&Object(c.createElement)(c.Fragment,null,Object(c.createElement)("a",null,t))," "+f)),Object(c.createElement)("div",{className:"wffn-cs-card-subtitle",title:Object(b.f)(n,!0)},Object(b.g)(n).fromNow())),1===parseInt(o)&&Object(c.createElement)("div",{className:"wffn-cs-card-r"},Object(c.createElement)("div",{className:"wffn-cs-card-date"},i.formatAmount(s))))};ke.defaultProps={object_id:0,date:"2020-09-23T06:02:59Z",object_name:null,type:"bump",link:"",total_revenue:0,is_converted:0};var Ce=ke,Se=function(e){switch(e.type){case"upsell":return Object(c.createElement)(he,e);case"optin":return Object(c.createElement)(Ee,e);case"checkout":return Object(c.createElement)(Pe,e);case"bump":return Object(c.createElement)(Ce,e);default:return null}};function De(){return(De=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var c in n)Object.prototype.hasOwnProperty.call(n,c)&&(e[c]=n[c])}return e}).apply(this,arguments)}var xe=function(e){var t=e.isOpen,n=e.onRequestClose,l=e.contact,s=e.funnelId,o=l?Object(b.n)([l.f_name,l.l_name]," "):" ",i=ye().fetch,u=de(),f=u.getError,m=u.getLoading,O=u.getContact,d=m(),j=O(),p=f();if(Object(c.useEffect)((function(){l&&l.hasOwnProperty("id")&&i(l.id,s)}),[l]),p)return Object(c.createElement)(ie,{size:400,isOpen:t,onRequestClose:n},p&&Object(c.createElement)(r.Notice,{status:"error"},p.hasOwnProperty("message")?p.message:Object(a.__)("Unable to load contacts","funnel-builder")));if(d)return Object(c.createElement)(ie,{size:400,isOpen:t,onRequestClose:n},Object(c.createElement)(ue,null));var v;return Object(c.createElement)(ie,{size:400,isOpen:t,onRequestClose:n},l&&l.id?Object(c.createElement)("div",{className:"wffn-cs-body"},Object(c.createElement)("div",{className:"wffn-cs-details"},Object(c.createElement)("div",{className:"wffn-cs-d-head wffn-cs-gap-border"},Object(c.createElement)("div",{className:"wffn-cs-gravatar"},Object(b.h)(o)),Object(c.createElement)("div",{className:"wffn-cs-name"},o),Object(c.createElement)("div",{className:"wffn-cs-email"},l&&l.email)),j&&j.hasOwnProperty("user_info")&&j.user_info.hasOwnProperty("additional")&&Object(c.createElement)("div",{className:"wffn-cs-d-data wffn-cs-gap-border"},Object(c.createElement)("div",{className:"wffn-cs-head"},"Additional"),Object(c.createElement)("div",{className:"wffn-cs-col2"},j.user_info.additional.map((function(e){return Object(c.createElement)("div",{className:"wffn-cs-list",key:e.name},Object(c.createElement)("div",{className:"wffn-cs-label"},e.name),Object(c.createElement)("div",{className:"wffn-cs-value"},"Creation Date"===e.name?Object(b.f)(e.value):e.value))}))))),Object(c.createElement)("div",{className:"wffn-cs-timeline"},Object(c.createElement)("div",{className:"wffn-cs-head"},"Conversions"),j&&j.hasOwnProperty("records")&&j.records.map((function(e){return Object(c.createElement)(Se,De({key:Object(b.i)("wffn-conversion")},e))})))):(v={user_info:{first_name:"John",last_name:"Doe",email:"johndoe@example.com",additional:[{name:"Contact ID",value:"0"},{name:"Creation Date",value:"2020-10-31T07:37:16Z"}]},records:[{object_id:"120",action_type_id:"0",value:"",date:"2020-11-07T07:37:31Z",object_name:"Sample Optin",type:"optin",link:""},{object_id:"62",object_name:"Sample Checkout",total_revenue:"85.5",date:"2020-11-05T07:37:16Z",type:"checkout",link:""},{object_id:"122",total_revenue:"17.1",object_name:"Sample Bump",is_converted:"1",date:"2020-11-01T07:37:16Z",type:"bump",link:""},{object_id:"64",action_type_id:"4",value:"19.00",date:"2020-10-31T07:37:24Z",object_name:"Sample Upsell",type:"upsell",link:""}]},Object(c.createElement)("div",{className:"wffn-cs-body"},Object(c.createElement)("div",{className:"wffn-cs-details"},Object(c.createElement)("div",{className:"wffn-cs-d-head wffn-cs-gap-border"},Object(c.createElement)("div",{className:"wffn-cs-gravatar"},Object(b.h)(o)),Object(c.createElement)("div",{className:"wffn-cs-name"},o,"( Dummy User )"),Object(c.createElement)("div",{className:"wffn-cs-email"},l&&l.email)),v&&v.hasOwnProperty("user_info")&&v.user_info.hasOwnProperty("additional")&&Object(c.createElement)("div",{className:"wffn-cs-d-data wffn-cs-gap-border"},Object(c.createElement)("div",{className:"wffn-cs-head"},"Additional"),Object(c.createElement)("div",{className:"wffn-cs-col2"},v.user_info.additional.map((function(e){return Object(c.createElement)("div",{className:"wffn-cs-list",key:e.name},Object(c.createElement)("div",{className:"wffn-cs-label"},e.name),Object(c.createElement)("div",{className:"wffn-cs-value"},"Creation Date"===e.name?Object(b.f)(e.value):e.value))}))))),Object(c.createElement)("div",{className:"wffn-cs-timeline"},Object(c.createElement)("div",{className:"wffn-cs-head"},"Conversions"),v&&v.hasOwnProperty("records")&&v.records.map((function(e){return Object(c.createElement)(Se,De({key:Object(b.i)("wffn-conversion")},e))}))))))};xe.defaultProps={isOpen:!1,onRequestClose:function(){}};var Ie=xe;function Te(e,t){if(null==e)return{};var n,c,a=function(e,t){if(null==e)return{};var n,c,a={},r=Object.keys(e);for(c=0;c<r.length;c++)n=r[c],t.indexOf(n)>=0||(a[n]=e[n]);return a}(e,t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);for(c=0;c<r.length;c++)n=r[c],t.indexOf(n)>=0||Object.prototype.propertyIsEnumerable.call(e,n)&&(a[n]=e[n])}return a}function Ae(e,t){return function(e){if(Array.isArray(e))return e}(e)||function(e,t){if("undefined"==typeof Symbol||!(Symbol.iterator in Object(e)))return;var n=[],c=!0,a=!1,r=void 0;try{for(var l,s=e[Symbol.iterator]();!(c=(l=s.next()).done)&&(n.push(l.value),!t||n.length!==t);c=!0);}catch(e){a=!0,r=e}finally{try{c||null==s.return||s.return()}finally{if(a)throw r}}return n}(e,t)||function(e,t){if(!e)return;if("string"==typeof e)return Re(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);"Object"===n&&e.constructor&&(n=e.constructor.name);if("Map"===n||"Set"===n)return Array.from(e);if("Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return Re(e,t)}(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function Re(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,c=new Array(t);n<t;n++)c[n]=e[n];return c}t.default=function(){var e=M(),t=Q(),n=e.getError,u=e.getTotalContacts,f=t.clearError,b=t.setContacts,m=u(),O=location&&location.search?Object(s.parse)(location.search.substring(1)):{},d=n(),j=Object(c.useRef)();Object(c.useEffect)((function(){j.current?f():j.current=d}));var p=Ae(Object(c.useState)(!1),2),v=p[0],w=p[1],y=Ae(Object(c.useState)(null),2),g=y[0],E=y[1],_=Ae(Object(c.useState)(!1),2),h=_[0],N=_[1];return Object(c.createElement)(c.Fragment,null,Object(c.createElement)(i.a,null,d&&Object(c.createElement)(r.Notice,{status:"error"},d.hasOwnProperty("message")?d.message:Object(a.__)("Unable to load contacts","funnel-builder")),Object(c.createElement)($,{query:O,onRequestDeleteContacts:function(e){Object(o.size)(e)>0&&w(e)},onContactClick:function(e){E(e),N(!0)}}),Object(c.createElement)(oe,{isOpen:!!v&&parseInt(O.edit)>0,deleteContacts:v,funnelId:parseInt(O.edit),onRequestClose:function(){w(!1)},onDeleteSuccess:function(e){O.s;var t=Te(O,["s"]);Object(l.j)(t,"/contacts",O),b(e,1,25,m-1),w(!1)},onDeleteFailed:function(e){w(!1)},closeAnimationInProgress:function(){return w(!1)}}),Object(c.createElement)(Ie,{funnelId:parseInt(O.edit),contact:g,size:400,isOpen:h,onRequestClose:function(){return N(!1)}})))}}}]);