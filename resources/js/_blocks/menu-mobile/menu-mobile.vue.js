/**
 * Мобильно меню
 */
export default {
    props: {
        /**
         * ID# страницы
         */
        pageId: {
            type: Number,
        },
    },
    data: function () {
        return {
            /**
             * Активность формы по кнопке
             * @type {Boolean}
             */
            active: false,

            /**
             * Меню загружено по AJAX
             * @type {Boolean}
             */
            ajaxLoaded: false,

            /**
             * HTML-код меню, загруженного через AJAX
             * @type {String}
             */
            ajaxMenu: '',
        };
    },
    mounted: function () {
        $(window).one('load', () => {
            if (!this.ajaxLoaded) {
                window.setTimeout(() => {
                    this.getAJAXMenu();
                    this.ajaxLoaded = true;
                }, 50);
            }
        });
        // $('.logo2:eq(0)').clone().appendTo('.menu-mobile__logo');
        // $('.contacts-top-phones-list__item:eq(0) a')
        //     .clone()
        //     .addClass('menu-mobile__link menu-mobile__link_main menu-mobile__link_phone')
        //     .appendTo('.menu-mobile__item_phone');
                
        $('.triggers-item_menu, .menu-trigger').on('click', function () {
            $('.menu-mobile__list_main').toggleClass('menu-mobile__list_active');
            return false;
        });
        $(this.$el).on('click', '.menu-mobile__item:has(> .menu-mobile__list) > .menu-mobile__link', function () {
            if ($(this).is(':not([href]), [href="' + $.escapeSelector('#') + '"]')) {
                $(this).closest('.menu-mobile__item').find('> .menu-mobile__list').addClass('menu-mobile__list_active');
                return false;
            }
        })
        $(this.$el).on('click', '.menu-mobile__children-trigger', function () {
            $(this).closest('.menu-mobile__item').find('> .menu-mobile__list').addClass('menu-mobile__list_active');
            return false;
        })
        $(this.$el).on('click', '.menu-mobile__close-link', function(e) { 
            $('.menu-mobile__list').removeClass('menu-mobile__list_active');
            return false;
        });
        $(this.$el).on('click', '.menu-mobile__back-link', function(e) { 
            $(this).closest('.menu-mobile__list').removeClass('menu-mobile__list_active');
            return false;
        });
        $('.body').on('click', function(e) { 
            $('.menu-mobile__list').removeClass('menu-mobile__list_active');
        });
        $('.menu-mobile').on('click', function(e) { 
            e.stopPropagation();
        });
        if ($(window).outerWidth() < 992) {
            $('.menu-mobile__list').on('movestart', function(e) { 
                if (e.distX <= -6) {
                    $(this).removeClass('menu-mobile__list_active');
                    return false;
                }
                e.preventDefault()
                return false;
            });
        }
    },
    methods: {
        /**
         * Разворачивает/скрывает меню
         */
        toggle: function () {
            this.active = !this.active;
        },
        /**
         * Получает полное меню через AJAX
         */
        getAJAXMenu: function () {
            $.get(this.ajaxURL, (result) => {
                let $remoteMenu = $(result);
                let $localMenu = $(this.$el);

                let $localCatalogItem = $('.menu-mobile__item_main.menu-mobile__item_catalog', $localMenu);
                let $remoteCatalogItem = $('.menu-mobile__item_main.menu-mobile__item_catalog', $remoteMenu);
                this.ajaxMenu = $remoteCatalogItem.html();
                console.log(this.ajaxMenu)
                // $localCatalogItem.replaceWith($remoteCatalogItem);
            })
        },
    },
    computed: {
        /**
         * Путь для AJAX-запроса
         * @return {String}
         */
        ajaxURL: function () {
            return '/ajax/menu_mobile/?id=' + this.pageId;
        },
        /**
         * Аналог this для привязки к слоту
         * @return {Object}
         */
        self: function () {
            return { ...this };
        },
    }

}