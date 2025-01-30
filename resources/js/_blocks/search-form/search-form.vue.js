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
        },
        /**
         * Форма сворачивается на десктопе
         * @type {Boolean}
         */
        foldable: {
            type: Boolean,
            default: false,
        },
    },
    data() {
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
            /**
             * Форма отправлена
             * @type {Boolean}
             */
            sent: false,
        };
        return result;
    },
    mounted() {
        $(this.$el).on('submit', () => {
            this.onFormSubmit();
        });
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
        if (this.foldable) {
            $('.body').on('click', (e) => {
                if (window.app.windowWidth >= window.app.mediaTypes.lg) {
                    this.active = false;
                }
            });
            $(this.$el).on('click', (e) => {
                this.active = true;
                e.stopPropagation();
            });
        }
    },
    methods: {
        /**
         * Событие при изменении текста
         * @param {String} value Новое значение поисковой строки
         */
        change(value) {
            window.clearTimeout(this.timeoutId);
            if (!this.sent && (value.length > this.minLength)) {
                var url = this.autocompleteURL + value;
                this.timeoutId = window.setTimeout(async () => { 
                    this.searchString = value;
                    this.busy = true;
                    this.autocomplete = null;
                    const data = await this.$root.api(url, null, this.blockId);
                    this.busy = false;
                    this.autocomplete = data;
                }, this.showInterval);
            }
        },
        /**
         * Разворачивает/скрывает форму
         */
        toggle() {
            this.active = !this.active;
        },
        /**
         * Активирует форму
         */
        activate() {
            this.active = true;
        },
        /**
         * Деактивирует форму
         */
        deactivate() {
            this.active = false;
        },
        /**
         * Очищает автозаполнение
         */
        clearAutocomplete() {
            this.autocomplete = null;
        },
        /**
         * Очищает поле ввода и автозаполнение
         */
        clearSearch() {
            $('[data-role="search-string"]', this.$el).val('');
            this.autocomplete = null;
        },
        /**
         * При отправке формы
         */
        onFormSubmit() {
            this.busy = false;
            this.sent = true;
            window.clearTimeout(this.timeoutId);
            this.timeoutId = null;
        },
    },
    computed: {
        /**
         * URL автоподстановки
         * @return {String}
         */
        autocompleteURL() {
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
        self() {
            return { ...this };
        },
    },
}