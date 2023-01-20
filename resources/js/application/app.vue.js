/**
 * Каркас приложения
 */
export default {
    data: function () {
        return {
            /**
             * Ширина экрана
             * @type {Number}
             */
            windowWidth: 0,

            /**
             * Ширина body
             * @type {Number}
             */
            bodyWidth: 0,

            /**
             * Высота экрана
             * @type {Number}
             */
            windowHeight: 0,

            /**
             * Смещение по вертикали
             * @type {Number}
             */
            scrollTop: 0,
            /**
             * Продолжительность скролла по scrollTo
             * 2022-04-07, AVS: Уменьшил до 0, т.к. конфликтует с Vue и скроллом браузера
             * @type {Number}
             */
            scrollToDuration: 0,
            /**
             * Селектор ссылок для scrollTo
             */
            scrollToSelector: 'a[href*="modal"][href*="#"], ' + 
                'a.scrollTo[href*="#"], ' + 
                '.menu-top__link[href*="#"], ' + 
                '.menu-bottom__link[href*="#"], ' + 
                '.menu-mobile__link[href*="#"]',
            /**
             * Медиа-типы (ширина в px)
             * @type {Object}
             */
            mediaTypes: {
                xxl: 1400,
                xl: 1200,
                lg: 992,
                md: 768,
                sm: 576,
                xs: 0
            },
        };
    },
    mounted: function () {
        let self = this;
        this.lightBoxInit();

        this.windowWidth = $(window).outerWidth();
        this.windowHeight = $(window).outerHeight();
        this.bodyWidth = $('body').outerWidth();
        this.fixHtml();
        $(window).on('resize', self.fixHtml);
        $(window).on('resize', () => {
            this.windowWidth = $(window).outerWidth();
            this.windowHeight = $(window).outerHeight();
            this.bodyWidth = $('body').outerWidth();
        });
        $(window).on('scroll', () => {
            this.scrollTop = $(window).scrollTop();
        });
        
        $(this.$el).on('click', this.scrollToSelector, function () {
            let currentUrl = window.location.pathname + window.location.search;
            let url = $(this).attr('href').split('#')[0];
            // if (url) {
            //     url = '#' + url;
            // }
            if (!url || (url == currentUrl)) {
                self.processHashLink(this.hash.replace(/#/gi, ''));
                return false;
            }
        });
        $(this.$el).on('show.bs.tab', 'a', function () {
            window.history.pushState({}, document.title, $(this).attr('href'));
        });
        $(window).on('load', () => {
            if (window.location.hash) {
                this.processHashLink(window.location.hash);
            }
        });

        // $('.menu-trigger').appendTo('.body__menu-mobile');

        // this.confirm = this.refs.confirm;
    },
    methods: {
        /**
         * Получает смещение по вертикали для scrollTo 
         * (для случая фиксированной шапки)
         * @return {Number}
         */
        getScrollOffset: function () {
            return 0;
        },

        /**
         * Получение объекта по хэш-тегу
         * @param {String} hash хэш-тег (первый символ #)
         * @return {jQuery|null} null, если не найден
         */
        getObjFromHash: function (hash) {
            if (hash[0] != '#') {
                hash = '#' + hash;
            }
            let $obj = $(hash);
            if ($obj.length) {
                return $obj;
            } 
            $obj = $('[name="' + hash.replace('#', '') + '"]');
            if ($obj.length) {
                return $obj;
            }
            return null;
        },

        /**
         * Обрабатывает хэш-ссылку
         * @param {String} hash хэш-тег (первый символ #)
         */
        processHashLink: function (hash) {
            let $obj = this.getObjFromHash(hash);
            if ($obj && $obj.length) {
                if ($obj.hasClass('modal')) {
                    $obj.modal('show');
                } else if ($obj.hasClass('tab-pane')) {
                    let $hashLink = $('a[href="' + hash + '"]');
                    if ($hashLink.length) {
                        $hashLink[0].click();
                    }
                } else {
                    $.scrollTo(
                        $obj.offset().top + this.getScrollOffset(), 
                        this.scrollToDuration
                    );
                }
            }
        },

        /**
         * Инициализация lightBox'а
         * (по умолчанию используется lightCase)
         */
        lightBoxInit: function (options = {}) {
            let defaults = {
                processAllImageLinks: true,
                swipe: true, 
                transition: 'scrollHorizontal',
            };
            let params = Object.assign({}, defaults, options)
            let rx = /\.(jpg|jpeg|pjpeg|png|gif)$/i;
            $('a:not([data-rel^=lightcase]):not([data-no-lightbox])').each(function () {
                if (params.processAllImageLinks) {
                    if (rx.test($(this).attr('href'))) {
                        $(this).attr('data-lightbox', 'true');
                    }
                }
                let g = $(this).attr('data-lightbox-gallery');
                if (g || $(this).attr('data-lightbox')) {
                    $(this).attr('data-rel', 'lightcase' + (g ? ':' + g : ''));
                    $(this).removeAttr('data-lightbox-gallery');
                    $(this).removeAttr('data-lightbox');
                }
            });
            $('a[data-rel^=lightcase]').lightcase({ 
                swipe: params.swipe, 
                transition: params.transition 
            });
        },


        /**
         * Фиксация HTML (хелпер для модификации верстки)
         * (абстрактный, для переопределения)
         */
        fixHtml: function () {
            // ...
        },


        /**
         * Обработчик отображения окна подтверждения
         * @param  {String} text       Текст запроса
         * @param  {String} okText     Текст кнопки "ОК"
         * @param  {String} cancelText Текст кнопки "Отмена"
         * @return {jQuery.Promise}
         */
        confirm: function (text, okText, cancelText) {
            return this.$refs.confirm.confirm(text, okText, cancelText);
        },

        /**
         * Форматирование цены
         * @param  {Number} x Цена
         * @return {String}
         */
        formatPrice: function (price) {
            return window.formatPrice(price);
        },

        /**
         * Форматирование числительных
         * @param  {Number} x Число
         * @param  {Array} forms <pre><code>[
         *     'товаров', 
         *     'товар', 
         *     'товара'
         * ]</code></pre> Словоформы
         * @return {String}
         */
        numTxt: function (x, forms) {
            return window.numTxt(x, forms);
        },

        /**
         * Генерирует jQuery-событие уровня документа
         * @param {String} eventName Наименование события
         * @param {mixed} data Данные для передачи
         */
        jqEmit: function (eventName, data = null, originalEvent = null) {
            window.setTimeout(function () {
                let result = $(document).trigger(eventName, data);
            }, 10);
        },
    },
    computed: {
        /**
         * Координаты нижней границы окна
         * @return {[type]} [description]
         */
        windowBottomPosition() {
            return this.scrollTop + this.windowHeight;
        },
    },
}