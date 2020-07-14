import RAAS_repo from './raas.repo.js';
import RAAS_queryString from './raas.query-string.js';
import AJAXForm from './ajax-form.js';
import adjustHeight from './adjust-height.js';
import menuTrigger from './menu-trigger.js';
import menuWrapping from './menu-wrapping.js';
import lightBoxInit from './light-box-init.js';
import numTxt from './num-txt.js';
import formatPrice from './format-price.js';

window.RAAS_queryString = RAAS_queryString;
window.numTxt = numTxt;
window.formatPrice = formatPrice;
window.lightBoxInit = lightBoxInit;

jQuery(function($) {
    $.fn.extend({
        RAAS_repo,
        AJAXForm,
        adjustHeight,
        menuTrigger,
        menuWrapping,
    });
    
    $.extend({
        RAAS_queryString
    });
});
