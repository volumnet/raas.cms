/**
 * Форма поиска
 */
export default {
    props: {
        /**
         * ID блока для включения автозаполнения
         * @type {Number}
         */
        blockId: {
            type: Number,
            required: false,
        },
        /**
         * Минимальная длина поисковой строки для автозаполнения
         * @type {Number}
         */
        minLength: {
            type: Number,
            default: 3
        },
        /**
         * Интервал после ввода поисковой строки до инициализации автозаполнения
         * @type {Number}
         */
        showInterval: {
            type: Number,
            default: 1000,
        }
    },
    data: function () {
        let result = {
            /**
             * Активность формы по кнопке
             * @type {Boolean}
             */
            active: false,
            /**
             * Поисковая строка для автозаполнения
             * @type {String}
             */
            searchString: '',
            /**
             * Происходит ли в данный момент автозаполнение
             * @type {Boolean}
             */
            busy: false,
            /**
             * Результат автозаполнения
             * @type {Object|null}
             */
            autocomplete: null,
            /**
             * Поисковая строка
             * @type {String}
             */
            value: '',
            /**
             * ID# таймаута автозаполнения
             * @type {Number|null}
             */
            timeoutId: null,
        };
        return result;
    },
    mounted: function () {
        if (this.blockId) {
            this.searchString = $('[data-role="search-string"]', this.$el).val();
            $('[data-role="search-string"]', this.$el).on('keyup', (e) => {
                let newVal = $(e.target).val();
                if (newVal != this.searchString) {
                    this.change(newVal);
                }
            })
            $('body').on('click', () => {
                this.autocomplete = null;
            });
            $(this.$el).on('click', e => e.stopPropagation());
        }

    },
    methods: {
        /**
         * Событие при изменении текста
         * @param {String} value Новое значение поисковой строки
         */
        change: function (value) {
            window.clearTimeout(this.timeoutId);
            if (value.length > this.minLength) {
                var url = this.autocompleteURL + value;
                this.timeoutId = window.setTimeout(() => { 
                    this.searchString = value;
                    this.busy = true;
                    this.autocomplete = null;
                    $.get(url, (data) => {
                        this.busy = false;
                        this.autocomplete = data;
                    });
                }, this.showInterval);
            }
        },
        /**
         * Разворачивает/скрывает форму
         */
        toggle: function () {
            this.active = !this.active;
        }
    },
    computed: {
        /**
         * URL автоподстановки
         * @return {String}
         */
        autocompleteURL: function () {
            let result = this.$attrs.action;
            if (/\?/gi.test(result)) {
                result += '&';
            } else {
                result += '?';
            }
            let inputName = $('[data-role="search-string"]', this.$el)
                .attr('name');
            result += 'AJAX=' + this.blockId + '&' + inputName + '=';
            return result;
        },
        /**
         * Аналог this для привязки к слоту
         * @return {Object}
         */
        self: function () {
            return { ...this };
        },
    },
}