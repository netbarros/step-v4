"use strict";var KTIntro=function(){var e=function(e,t,o){var i=new Date,n=(i.getTime(),1296e6);if(!KTCookie.get(e+"_counter")||parseInt(KTCookie.get(e+"_counter"))<3)return KTCookie.get(e+"_counter")?"1"!=KTCookie.get(e+"_counter")||KTCookie.get(e+"_show_1")?"2"==KTCookie.get(e+"_counter")&&!KTCookie.get(e+"_show_2")&&(setTimeout(t,o),KTCookie.set(e+"_show_3","1",{expires:new Date(i.getTime()+n)}),KTCookie.set(e+"_counter","3",{expires:new Date(i.getTime()+n)}),!0):(setTimeout(t,o),KTCookie.set(e+"_show_2","1",{expires:new Date(i.getTime()+6048e5)}),KTCookie.set(e+"_counter","2",{expires:new Date(i.getTime()+18144e5)}),!0):(setTimeout(t,o),KTCookie.set(e+"_show_1","1",{expires:new Date(i.getTime()+1728e5)}),KTCookie.set(e+"_counter","1",{expires:new Date(i.getTime()+2592e6)}),!0)},t=function(){var e=document.querySelector("#kt_header_search_toggle");if(e){var t=KTApp.initBootstrapPopover(e,{customClass:"popover-dark",container:"body",trigger:"manual",boundary:"window",placement:"left",dismiss:!0,html:!0,title:"Quick Buscar",content:"Fully functional search with advance options and preferences setup"});t.show(),setTimeout((function(){t&&t.dispose()}),1e4),e.addEventListener("click",(function(e){t.dispose()}))}},o=function(){var e=document.querySelector("#kt_toolbar_primary_button");if(e){var t=KTApp.initBootstrapPopover(e,{customClass:"popover-dark",container:"body",boundary:"window",trigger:"manual",placement:"left",dismiss:!0,html:!0,title:"Quick Notifications",content:"Seamless access to updates and notifications in various formats"});t.show(),setTimeout((function(){t&&t.dispose()}),1e4),e.addEventListener("click",(function(e){t.dispose()}))}},i=function(){var e=document.querySelector("#kt_header_user_menu_toggle");if(e){var t=KTApp.initBootstrapPopover(e,{customClass:"popover-dark",container:"body",boundary:"window",placement:"left",trigger:"manual",dismiss:!0,html:!0,title:"Advanced User Menu",content:"With quick links to user profile and account settings pages"});t.show(),setTimeout((function(){t&&t.dispose()}),1e4),e.addEventListener("click",(function(e){t.dispose()}))}};return{init:function(){var n;n="metronic",!1===KTUtil.inIframe()&&(e("kt_"+n+"_intro_1",t,5e3)||e("kt_"+n+"_intro_2",o,5e3)||e("kt_"+n+"_intro_3",i,5e3))}}}();"undefined"!=typeof module&&(module.exports=KTIntro),KTUtil.onDOMContentLoaded((function(){KTIntro.init()}));