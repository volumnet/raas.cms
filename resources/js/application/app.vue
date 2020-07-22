<script>
// import Shop from './shop/shop.app.vue';
import Confirm from './confirm.vue';
import Vue from 'vue/dist/vue.js'
import CookiesNotification from './cookies-notification.vue';
import Triggers from './triggers.vue';
import MenuMobile from './menu-mobile.vue';

export default {
    el: '#top',
    // mixins: [Shop],
    components: {
        'cookies-notification': CookiesNotification,
        'triggers': Triggers,
        'menu-mobile': MenuMobile,
    },
    data: function () {
        return {
            confirm: null,
        };
    },
    mounted: function () {
        $('select[multiple]').multiselect({
            buttonText: function (options, select) {
                if (options.length == 0) {
                  return '--';
                }
                else {
                  var selected = '';
                  var i = 0;
                  options.each(function () {
                      if (i < 3) {
                          selected += $(this).text() + ', ';
                      }
                      i++;
                  });
                  selected = selected.substr(0, selected.length -2);
                  return selected + (options.length > 3 ? '...' : '');
                }
            },
            maxHeight: 200
        });

        $('[data-role="raas-repo-container"]').parent().each(function () {
            $(this).RAAS_repo({ 
                onAfterAdd: function () { 
                    $(this).show(); 
                    $(this)
                        .find('select:disabled, input:disabled, textarea:disabled')
                        .removeAttr('disabled'); 
                } 
            })
        });

        $('[data-role="raas-ajaxform"]').each(function () { 
            $(this).AJAXForm(); 
        });

        $('input[pattern]').each(function () {
            var pattern = $(this).attr('pattern');
            $(this)
                .attr('data-inputmask-pattern', pattern)
                .inputmask({regex: pattern}, { showMaskOnHover: false });
        });
        $('input[type="tel"]')
            .not('[pattern]')
            .attr('data-inputmask-pattern', '+9 (999) 999-99-99')
            .inputmask('+9 (999) 999-99-99', { showMaskOnHover: false });
        $('input[type="email"]')
            .not('[pattern]')
            .attr('data-inputmask-pattern', '*{+}@*{+}.*{+}')
            .inputmask('*{+}@*{+}.*{+}', { showMaskOnHover: false });

        window.lightBoxInit(true);

        var fixHtml = function () {
            if ($(window).outerWidth() >= 992) {
                $('.search-form, .search-form-trigger').appendTo('.body__search-form');
            } else {
                $('.search-form, .search-form-trigger').prependTo('.body__cart');
            }
            $('.menu-trigger').appendTo('.body__cart');
        };
        fixHtml();
        $(window).on('resize', fixHtml);
        
        var getObjFromHash = function(hash) {
            let $obj = $('#' + hash);
            if ($obj.length) {
                return $obj;
            } 
            $obj = $('[name="' + hash + '"]');
            if ($obj.length) {
                return $obj;
            }
            return null;
        };
        
        var processHashLink = function(hash) {
            let $obj = getObjFromHash(hash);
            if ($obj && $obj.length) {
                if ($obj.hasClass('modal')) {
                    $obj.modal('show');
                } else {
                    $.scrollTo($obj.offset().top, 500);
                }
            }
        }

        $('a[href*="modal"][href*="#"], a.scrollTo[href*="#"], .menu-top__link[href*="#"], .menu-bottom__link[href*="#"], .menu-mobile__link[href*="#"]').click(function() {
            var url = $(this).attr('href').split('#')[0];
            var currentUrl = window.location.pathname + window.location.search;
            if (!url || (url == currentUrl)) {
                processHashLink(this.hash.replace(/#/gi, ''));
                return false;
            }
        });
        (function () {
            if (document.location.hash) {
                processHashLink(document.location.hash.replace(/#/gi, ''));
            }
        })();

        $('.menu-trigger').appendTo('.body__menu-mobile');

        let $confirm = $('<div></div>');
        let vueConfirm = new (Vue.component('confirm', Confirm))();
        $('body').append($confirm);
        vueConfirm.$mount($confirm[0]);
        this.confirm = vueConfirm.confirm.bind(vueConfirm);
    },
}
</script>