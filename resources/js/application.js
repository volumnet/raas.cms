import 'jquery-ui/themes/base/all.css'
// import 'jquery-ui/jquery-ui.structure.css'
// import 'jquery-ui/jquery-ui.theme.css'
import 'jquery-ui-timepicker-addon/dist/jquery-ui-timepicker-addon.css'
import 'bootstrap-multiselect/dist/css/bootstrap-multiselect.css'
import 'lightcase/src/css/lightcase.css'
import 'wow.js/css/libs/animate.css'

import 'expose-loader?exposes[]=$&exposes[]=jQuery!jquery';
import 'jquery-form'
import 'jquery-ui'
import 'jquery-ui/ui/widgets/slider.js'
import 'jquery.event.swipe'
import 'jquery-mobile/js/events/touch.js'
import 'inputmask/dist/jquery.inputmask.js'
import 'bootstrap'
import 'bootstrap-multiselect'
import 'jquery-ui/ui/i18n/datepicker-ru.js'
import 'jquery-ui-timepicker-addon'
import 'jquery-ui-timepicker-addon/dist/i18n/jquery-ui-timepicker-ru.js'
import 'jquery.scrollto'
import 'lightcase'
import 'jcarousel'
import Vue from 'vue/dist/vue.js'
window.Vue = Vue;
import VueW3CValid from 'vue-w3c-valid'
window.VueW3CValid = VueW3CValid;
import Cookie from 'expose-loader?exposes[]=Cookie!js-cookie'
import WOW from 'expose-loader?exposes[]=WOW!wow.js'
import './application/head/jquery.event.move'
import prepareW3CVue from './application/head/prepare-w3c-vue.js';
window.prepareW3CVue = prepareW3CVue;
import './application/head/head.js'
import App from './application/app.vue';

new WOW().init();
prepareW3CVue();
$('cookies-notification, triggers, menu-mobile').appendTo('#top');

jQuery(document).ready(function($) {
    $.timepicker.setDefaults($.timepicker.regional['ru']);
    window.app = new Vue(App);

    $(window).on('load', function () {
        $('[data-role="slider"]').each(function () {
            var params = {};
            var key;
            for (var i = 0; i < $(this)[0].attributes.length; i++) {
                key = $(this)[0].attributes[i];
                if (/^data-slider-/i.test(key.name)) {
                    params[key.name.replace(/^data-slider-/i, '')] = key.value;
                }
            }
            if ($('[data-role="slider-next"]', $(this).parent()).length) {
                params.$next = $('[data-role="slider-next"]', $(this).parent());
            }
            if ($('[data-role="slider-prev"]', $(this).parent()).length) {
                params.$prev = $('[data-role="slider-prev"]', $(this).parent());
            }
            if ($('[data-role="slider-pagination"]', $(this).parent()).length) {
                params.$pagination = $('[data-role="slider-pagination"]', $(this).parent());
            }
            console.log(params);
            $(this).RAASslider(params);
        })
        
    });

});