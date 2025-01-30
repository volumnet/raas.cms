/**
 * Мобильное меню
 */
export default {
    props: {
        /**
         * ID# страницы
         */
        pageId: {
            type: Number,
        },
        /**
         * Использовать AJAX-загрузку
         * @type {Boolean}
         */
        useAjax: {
            type: Boolean,
            default: false,
        },
        /**
         * ID# блока (использовать AJAX-загрузку с той же страницы)
         * @type {Number|null}
         */
        blockId: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            /**
             * Активность формы по кнопке
             * @type {Boolean}
             */
            active: false,
        };
    },
    mounted() {
        // console.log('menu-mobile mounted');
        if (this.useAjax) {
            // console.log('ajax used');
            // 2023-04-07, AVS: продублировал, а то на андроиде не вызывается window.load
            if (!this.ajaxLoaded) {
                // console.log('launched');
                this.getAJAXMenu();
                this.ajaxLoaded = true;
            }
            $(window).on('load', () => {
                // console.log('window loaded');
                window.setTimeout(() => {
                    if (!this.ajaxLoaded) {
                        // console.log('timeout launched');
                        this.getAJAXMenu();
                        this.ajaxLoaded = true;
                    }
                }, 50);
            });
        }
        // $('.logo2:eq(0)').clone().appendTo('.menu-mobile__logo');
        // $('.contacts-top-phones-list__item:eq(0) a')
        //     .clone()
        //     .addClass('menu-mobile__link menu-mobile__link_main menu-mobile__link_phone')
        //     .appendTo('.menu-mobile__item_phone');
                
        $(document).on('raas.openmobilemenu', () => {
            if ($('.menu-mobile__list_main').is('.menu-mobile__list_active')) {
                $('.menu-mobile__link').removeClass('menu-mobile__link_focused');
                $('.menu-mobile__item').removeClass('menu-mobile__item_focused');
                $('.menu-mobile__list').removeClass('menu-mobile__list_active');
                this.active = false;
            } else {
                $('.menu-mobile__list_main').addClass('menu-mobile__list_active');
                this.active = true;
            }
        })
        // $('.triggers-item_menu, .menu-mobile__trigger, [data-role="mobile-menu-trigger"]')
        //     .on('click', function () {
        //         $('.menu-mobile__list_main').toggleClass('menu-mobile__list_active');
        //         return false;
        //     });
        $(this.$el).on(
            'click', 
            '.menu-mobile__item:has(> .menu-mobile__list) > .menu-mobile__link', 
            function () {
                if ($(this).is(':not([href]), [href="' + $.escapeSelector('#') + '"]')) {
                    $(this)
                        .addClass('.menu-mobile__link_focused')
                        .closest('.menu-mobile__item')
                        .addClass('menu-mobile__item_focused')
                        .find('> .menu-mobile__list')
                        .addClass('menu-mobile__list_active');
                    return false;
                }
            }
        );

        // 2023-01-16, AVS: сделал чтобы закрывалось при переходе по ссылке
        $(this.$el).on(
            'click', 
            '.menu-mobile__item:not(:has(> .menu-mobile__list)) > .menu-mobile__link[href]', 
            () => {
                $('.menu-mobile__link').removeClass('menu-mobile__link_focused');
                $('.menu-mobile__item').removeClass('menu-mobile__item_focused');
                $('.menu-mobile__list').removeClass('menu-mobile__list_active');
                this.active = false;
            }
        );
        $(this.$el).on('click', '.menu-mobile__children-trigger', function () {
            let $item = $(this).closest('.menu-mobile__item');
            $item
                .toggleClass('menu-mobile__item_focused')
                .find('> .menu-mobile__list')
                .toggleClass('menu-mobile__list_active');
            $item.find('> .menu-mobile__link')
                .toggleClass('menu-mobile__link_focused')
            return false;
        });
        $(this.$el).on('click', '.menu-mobile__close-link', () => { 
            $('.menu-mobile__link').removeClass('menu-mobile__link_focused');
            $('.menu-mobile__item').removeClass('menu-mobile__item_focused');
            $('.menu-mobile__list').removeClass('menu-mobile__list_active');
            this.active = false;
            return false;
        });
        $(this.$el).on('click', '.menu-mobile__back-link', function() { 
            $(this)
                .closest('.menu-mobile__list')
                .removeClass('menu-mobile__list_active')
                .closest('.menu-mobile__item')
                .removeClass('menu-mobile__item_focused')
                .find('> .menu-mobile__link')
                .removeClass('menu-mobile__link_focused');
            return false;
        });
        // Продублируем сюда из app.vue, т.к. клик на .menu-mobile препятствует вызову из app
        $(this.$el).on('click', this.$root.scrollToSelector, function () {
            let currentUrl = window.location.pathname + window.location.search;
            let url = $(this).attr('href').split('#')[0];
            if (!url || (url == currentUrl)) {
                window.app.processHashLink(this.hash.replace(/#/gi, ''));
            }
        });
        $('.body').on('click', () => { 
            $('.menu-mobile__link').removeClass('menu-mobile__link_focused');
            $('.menu-mobile__item').removeClass('menu-mobile__item_focused');
            $('.menu-mobile__list').removeClass('menu-mobile__list_active');
            this.active = false;
        });
        $('.menu-mobile').on('click', function(e) { 
            e.stopPropagation();
        });
        if ($(window).outerWidth() < 992) {
            let self = this;
            $('.menu-mobile__list').on('movestart', function(e) { 
                if (e.distX <= -6) {
                    $(this)
                        .removeClass('menu-mobile__list_active')
                        .closest('.menu-mobile__item')
                        .removeClass('menu-mobile__item_focused')
                        .find('> .menu-mobile__link')
                        .removeClass('menu-mobile__link_focused');
                    if ($(this).is('.menu-mobile__list_main')) {
                        self.active = false;
                    }
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
        toggle() {
            this.active = !this.active;
            console.log(this.active)
        },
        /**
         * Получает полное меню через AJAX
         */
        async getAJAXMenu() {
            const response = await this.$root.api(this.ajaxURL, null, this.blockId, 'text/html');
            let $remoteMenu = $(response);
            let $localMenu = $(this.$el);

            let $localCatalogItem = $('.menu-mobile__item_main.menu-mobile__item_catalog', $localMenu);
            let $remoteCatalogItem = $('.menu-mobile__item_main.menu-mobile__item_catalog', $remoteMenu);
            $localCatalogItem.replaceWith($remoteCatalogItem);
        },
    },
    computed: {
        /**
         * Путь для AJAX-запроса
         * @return {String}
         */
        ajaxURL() {
            if (this.blockId) {
                return window.location.pathname;
            } else {
                return '/ajax/menu_mobile/?id=' + this.pageId;
            }
        },
        /**
         * Аналог this для привязки к слоту
         * @return {Object}
         */
        self() {
            return { ...this };
        },
    }

}