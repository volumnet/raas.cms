import RAASField from './raas-field.vue.js';

/**
 * Поле даты
 */
export default {
    mixins: [RAASField],
    props: {
        /**
         * Наименование поля
         */
        name: {
            type: String,
        },
    },
    data: function () {
        let lang = $('html').attr('lang') || 'ru';
        let result = {
            /**
             * Двухбуквенный код языка
             * @type {String}
             */
            lang: lang,
            /**
             * Канонический формат значения (формат jQuery UI datepicker)
             * @type {String}
             */
            canonicalDatePickerFormat: 'yy-mm-dd',
            /**
             * Канонический формат значения (формат Moment.JS)
             * @type {String}
             */
            canonicalMomentFormat: 'YYYY-MM-DD',
            /**
             * Календарь отображается
             * @type {Boolean}
             */
            pickerIsShown: false,
        };
        switch (lang) {
            case 'en':
                /**
                 * Шаблон поля
                 * @type {String}
                 */
                result.pattern = '^[0-1][0-9]\\/[0-3][0-9]\\/[1-2][0-9]{3}$';
                /**
                 * Формат jQuery UI datepicker
                 * @type {String}
                 */
                result.datePickerFormat = 'mm/dd/yy';
                /**
                 * Формат Moment.JS
                 * @type {String}
                 */
                result.momentFormat = 'MM/DD/YYYY';
                break;
            default: // ru
                result.pattern = '^[0-3][0-9]\\.[0-1][0-9]\\.[1-2][0-9]{3}$';
                result.datePickerFormat = 'dd.mm.yy';
                result.momentFormat = 'DD.MM.YYYY';
                break;
        }
        return result;
    },
    mounted: function () {
        this.$el.classList.remove('form-control');
        this.checkDatePicker();
    },
    updated: function () {
        this.$el.classList.remove('form-control');
        this.checkDatePicker();
    },
    methods: {
        /**
         * Проверка/установка выбора даты
         */
        checkDatePicker: function () {
            var self = this;
            if (!$(this.$refs.field).attr('data-datepicker-applied')) {
                $(this.$refs.field).datepicker(this.datePickerParams)
                    .attr('data-datepicker-applied', 'true');
            } else {
                $(this.$refs.field).datepicker('refresh');
            }
        },
        applyInputMaskListeners: function () {
            let self = this;
            let $objects = $(this.$el).add($('input', this.$el));
            $objects
                .filter('[data-inputmask-pattern]:not([data-inputmask-events])')
                .on('blur', function (e) {
                    if (e.target.value) {
                        let value = window
                            .moment(e.target.value, self.momentFormat, true)
                            .format(self.canonicalMomentFormat);
                        if (!/invalid/gi.test(value)) {
                            self.pValue = value;
                            self.$emit('input', value);
                            self.$emit('change', value);
                        } else {
                            self.pValue = self.value;
                            self.$emit('input', self.value);
                            self.$forceUpdate();
                        }
                    } else {
                        self.$emit('input', '');
                    }
                })
                .on('input', function (e) {
                    if (e.target.value) {
                        let value = window
                            .moment(e.target.value, self.momentFormat, true)
                            .format(self.canonicalMomentFormat);
                        if (!/invalid/gi.test(value)) {
                            self.pValue = value;
                            self.$emit('input', value);
                            self.$emit('change', value);
                        }
                    } else {
                        self.pValue = '';
                        self.$emit('input', '');
                    }
                })
                .attr('data-inputmask-events', 'true');
        },
        /**
         * Отображает/скрывает календарь
         */
        togglePicker: function () {
            if (this.pickerIsShown) {
                $(this.$refs.field).datepicker('hide');
                this.pickerIsShown = false;
            } else if (!this.$attrs.disabled) {
                $(this.$refs.field).datepicker('show');
                this.pickerIsShown = true;
            }
        },

    },
    computed: {
        /**
         * Локальное значение
         * @return {String}
         */
        localValue: function () {
            return window.moment(this.pValue, this.canonicalMomentFormat)
                .format(this.momentFormat);
        },
        /**
         * Параметры календаря
         * @return {Object}
         */
        datePickerParams: function () {
            return Object.assign(
                {},
                $.datepicker.regional[this.lang],
                {
                    dateFormat: this.datePickerFormat,
                    altField: $(this.$refs.valueField),
                    altFormat: this.canonicalDatePickerFormat,
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '-100:+100',
                    onClose: () => {
                        this.pickerIsShown = false;
                    },
                    onSelect: (targetValue) => {
                        if (targetValue) {
                            let value = window.moment(targetValue, this.momentFormat)
                                .format(this.canonicalMomentFormat);
                            if (!/invalid/gi.test(value)) {
                                this.pValue = value;
                                this.$emit('input', value);
                                this.$emit('change', value);
                            } else {
                                this.pValue = this.value;
                                this.$emit('input', this.value);
                                this.$forceUpdate();
                            }
                        } else {
                            this.$emit('input', '');
                        }
                    }
                }
            );
        }
    },
};