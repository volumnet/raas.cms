/**
 * Каркас приложения
 */
export default {
    data() {
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
             * Старое смещение по вертикали
             * @type {Number}
             */
            oldScrollTop: 0,

            /**
             * Происходит ли сейчас скроллинг
             * @type {Boolean}
             */
            isScrollingNow: false,

            /**
             * Происходит ли сейчас скроллинг (ID# таймаута)
             * @type {Number}
             */
            isScrollingNowTimeoutId: false,

            /**
             * Ожидание окончания скроллинга, мс
             * @type {Number}
             */
            isScrollingNowDelay: 250,

            /**
             * Селектор ссылок для scrollTo
             */
            scrollToSelector: 'a[href*="modal"][href*="#"], ' + 
                'a.scrollTo[href*="#"], ' + 
                'a[href^="#"]:not([href="#"]):not([data-toggle]):not([data-bs-toggle]), ' + 
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
    mounted() {
        let self = this;
        this.lightBoxInit();
        this.windowWidth = $(window).innerWidth();
        this.windowHeight = $(window).outerHeight();
        this.bodyWidth = $('body').outerWidth();
        this.fixHtml();
        $(window)
            .on('resize', self.fixHtml)
            .on('resize', () => {
                this.windowWidth = $(window).outerWidth();
                this.windowHeight = $(window).outerHeight();
                this.bodyWidth = $('body').outerWidth();
            })
            .on('scroll', () => {
                let oldScrollTop = this.scrollTop;
                this.scrollTop = $(window).scrollTop();
                if (this.isScrollingNowTimeoutId) {
                    window.clearTimeout(this.isScrollingNowTimeoutId);
                }
                if (!this.isScrollingNow) {
                    this.isScrollingNow = true;
                }
                this.isScrollingNowTimeoutId = window.setTimeout(() => {
                    this.oldScrollTop = oldScrollTop;
                    this.scrollTop = $(window).scrollTop();
                    this.isScrollingNowTimeoutId = 0;
                    this.isScrollingNow = false;
                }, this.isScrollingNowDelay);
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
        this.scrollTop = this.oldScrollTop = $(window).scrollTop();

        // $('.menu-trigger').appendTo('.body__menu-mobile');

        // this.confirm = this.refs.confirm;
    },
    methods: {
        /**
         * Отправляет запрос к API
         * 
         * @param  {String} url URL для отправки
         * @param  {mixed} postData POST-данные для отправки (если null, то GET-запрос)
         * @param  {Number} blockId ID# блока для добавления AJAX={blockId} и заголовка X-RAAS-Block-Id
         * @param  {String} responseType MIME-тип получаемого ответа (если присутствует слэш /, то отправляется также заголовок Accept)
         * @param  {String} requestType MIME-тип запроса (если присутствует слэш /, то отправляется также заголовок Content-Type)
         * @param  {Object} additionalHeaders Дополнительные заголовки
         * @return {mixed} Результат запроса
         */
        async api(
            url, 
            postData = null, 
            blockId = null, 
            responseType = 'application/json', 
            requestType = 'application/x-www-form-urlencoded',
            additionalHeaders = {}
        ) {
            let realUrl = url;
            if (!/\/\//gi.test(realUrl)) {
                if (realUrl[0] != '/') {
                    realUrl = '//' + window.location.host + window.location.pathname + realUrl;
                } else {
                    realUrl = '//' + window.location.host + realUrl;
                }
            }
            const headers = {...additionalHeaders};
            let rx;
            if (blockId) {
                if (!/(\?|&)AJAX=/gi.test(realUrl)) {
                    realUrl += (/\?/gi.test(realUrl) ? '&' : '?') + 'AJAX=' + blockId;
                }
                headers['X-RAAS-Block-Id'] = blockId;
            }
            if (/\//gi.test(responseType)) {
                headers['Accept'] = responseType;
            }
            if (/\//gi.test(requestType) && !!postData) {
                headers['Content-Type'] = requestType;
            }
            const fetchOptions = {
                headers,
            };
            if (!!postData) {
                fetchOptions.method = 'POST';
                if (/form/gi.test(requestType)) {
                    if (/multipart/gi.test(requestType)) {
                        let formData  = new FormData();
                        if (postData instanceof FormData) {
                            formData = postData;
                        } else {
                            formData = new FormData();
                            for (const name in postData) {
                                formData.append(name, postData[name]);
                            }
                        }
                        fetchOptions.body = formData;
                        delete headers['Content-Type']; // Там автоматически boundary ставится, без него фигня получается
                    } else {
                        fetchOptions.body = window.queryString.stringify(postData, { arrayFormat: 'bracket' });
                    }
                } else if ((typeof postData) == 'object') {
                    fetchOptions.body = JSON.stringify(postData);
                } else {
                    fetchOptions.body = postData;
                }
            } else {
                fetchOptions.method = 'GET';
            }
            // console.log(fetchOptions);
            const response = await fetch(realUrl, fetchOptions);
            let result;
            if (/json/gi.test(responseType)) {
                result = await response.json();
            } else {
                result = await response.text();
            }
            return result;

        },
        /**
         * Получает смещение по вертикали для scrollTo 
         * (для случая фиксированной шапки)
         * @return {Number}
         */
        getScrollOffset() {
            return 0;
        },

        /**
         * Получение объекта по хэш-тегу
         * @param {String} hash хэш-тег (первый символ #)
         * @return {jQuery|null} null, если не найден
         */
        getObjFromHash(hash) {
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
        processHashLink(hash) {
            let $obj = this.getObjFromHash(hash);
            if ($obj && $obj.length) {
                if ($obj.hasClass('modal')) {
                    $obj.modal('show');
                } else if ($obj.hasClass('tab-pane')) {
                    let $hashLink = $(
                        'a[href="' + hash + '"], ' + 
                        'a[href="' + window.location.pathname + window.location.search + hash + '"], ' +
                        'a[href="' + window.location.href + '"]'
                    );
                    if ($hashLink.length) {
                        $hashLink[0].click();
                    }
                } else {
                    this.scrollTo($obj);
                }
            }
        },

        /**
         * Инициализация lightBox'а
         * (по умолчанию используется lightCase)
         */
        lightBoxInit(options = {}) {
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
            $('a[data-rel^=lightcase]').lightcase(params);
            $('body').on('click.lightcase', 'a', function (e, data) {
                if (/youtu/gi.test($(this).attr('href'))) {
                    // Костыль, чтобы не дожидаться полной загрузки Youtube
                    // 2023-09-13, AVS: добавили параметр raas-lightcase-loaded чтобы обрабатывать галерею видео
                    let interval = window.setInterval(() => {
                        if ($('#lightcase-case iframe:not([raas-lightcase-loaded])').length) {
                            $('#lightcase-case iframe:not([raas-lightcase-loaded])').attr('raas-lightcase-loaded', '1').trigger('load');
                            window.clearInterval(interval);
                        }
                    }, 100);
                }
            });
        },


        /**
         * Фиксация HTML (хелпер для модификации верстки)
         * (абстрактный, для переопределения)
         */
        fixHtml() {
            // ...
        },


        /**
         * Обработчик отображения окна подтверждения
         * @param  {String} text       Текст запроса
         * @param  {String} okText     Текст кнопки "ОК"
         * @param  {String} cancelText Текст кнопки "Отмена"
         * @return {jQuery.Promise}
         */
        confirm(text, okText, cancelText) {
            return this.$refs.confirm.confirm(text, okText, cancelText);
        },

        /**
         * Форматирование цены
         * @param  {Number} x Цена
         * @return {String}
         */
        formatPrice(price) {
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
        numTxt(x, forms) {
            return window.numTxt(x, forms);
        },

        /**
         * Генерирует jQuery-событие уровня документа
         * @param {String} eventName Наименование события
         * @param {mixed} data Данные для передачи
         */
        jqEmit(eventName, data = null, originalEvent = null) {
            window.setTimeout(function () {
                let result = $(document).trigger(eventName, data);
            }, 10);
        },

        /**
         * Скроллит по вертикали к заданному объекту/позиции
         * @param  {Number|HTMLElement|jQuery} destination Назначение (точек по Y, либо элемент)
         * @param {Boolean} instant Немедленный скролл (плавный, если false)
         */
        scrollTo(destination, instant = false) {
            let destY = null;
            if (typeof(destination) == 'number') {
                destY = destination;
            } else if (destination instanceof HTMLElement) {
                destY = $(destination).offset().top;
            } else if (destination instanceof jQuery) {
                destY = destination.offset().top;
            }
            if (destY !== null) {
                let scrollToData = {
                    left: 0, 
                    top: Math.max(0, Math.round(destY + this.getScrollOffset())),
                    behavior: instant ? 'instant' : 'smooth',
                };
                // console.log(scrollToData);
                window.scrollTo(scrollToData);
                // 2023-09-19, AVS: сделаем защиту скроллинга
                let protectScrolling = window.setInterval(() => {
                    if (this.scrollTop == scrollToData.top) {
                        console.log('stop scrolling to ' + scrollToData.top);
                        window.clearInterval(protectScrolling);
                        protectScrolling = null;
                    } else if (!this.isScrollingNow) {
                        window.scrollTo(scrollToData);
                        console.log('continue scrolling to ' + scrollToData.top);
                    }
                }, this.isScrollingNowDelay)
                // $.scrollTo(scrollToData.top, instant ? this.isScrollingNowDelay : 0);
            }
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
        /**
         * Последнее смещение по скроллингу
         * @return {Number}
         */
        scrollDelta() {
            return this.scrollTop - this.oldScrollTop;
        },
    },
}