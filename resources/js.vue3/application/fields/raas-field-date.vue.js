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
    data() {
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
            /**
             * Локальное значение
             */
            localValue: '',
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
    mounted() {
        this.localValue = this.getLocalValue(this.pValue);
        this.$el.classList.remove('form-control');
        this.checkDatePicker();
    },
    updated() {
        this.$el.classList.remove('form-control');
        this.checkDatePicker();
    },
    methods: {
        /**
         * Проверка/установка выбора даты
         */
        checkDatePicker() {
            var self = this;
            if (!$(this.$refs.field).attr('data-datepicker-applied')) {
                $(this.$refs.field).datepicker(this.datePickerParams)
                    .attr('data-datepicker-applied', 'true');
            } else {
                // 2022-03-23, AVS: убрал, т.к. в репе выбор по селектору блокируется обновлением
                // $(this.$refs.field).datepicker('refresh');
            }
        },
        applyInputMaskListeners() {
            let $objects = $(this.$el).add($('input', this.$el));
            // 2025-02-03, AVS: в десктопе inputmask заглушает стандартную прослушку input
            $objects
                .filter('[data-inputmask-pattern]:not([data-inputmask-events])')
                .on('blur', (e) => {
                    if (e.target.value) {
                        const canonicalValue = this.getCanonicalValue(e.target.value);
                        if (canonicalValue) {
                            this.pValue = canonicalValue;
                            this.$emit('update:modelValue', this.pValue);
                        } else {
                            this.pValue = this.value;
                            this.localValue = this.getLocalValue(this.pValue);
                            this.$emit('update:modelValue', this.pValue);
                            this.$forceUpdate();
                        }
                    } else {
                        this.pValue = this.localValue = '';
                        this.$emit('update:modelValue', this.pValue);
                    }
                })
                .on('input', (e) => {
                    // 2025-02-03, AVS: костыль для inputmask - иногда при удалении текста делает значение __.__.____
                    if (e.target.value && !!/\d/g.test(e.target.value)) {
                        const canonicalValue = this.getCanonicalValue(e.target.value);
                        if (canonicalValue) {
                            this.pValue = canonicalValue;
                            this.$emit('update:modelValue', this.pValue);
                        }
                    } else {
                        this.pValue = this.localValue = '';
                        this.$emit('update:modelValue', this.pValue);
                    }
                })
                .attr('data-inputmask-events', 'true');
        },
        /**
         * Возвращает локальное значение из канонического
         * @param  {String} canonicalValue Каноническое значение
         * @return {String}
         */
        getLocalValue(canonicalValue) {
            const m = window.moment(canonicalValue, this.canonicalMomentFormat);
            if (m.isValid()) {
                return m.format(this.momentFormat);
            }
            return '';
        },
        /**
         * Возвращает каноническое значение из локального
         * @param  {String} localValue Локальное значение
         * @return {String}
         */
        getCanonicalValue(localValue) {
            const m = window.moment(localValue, this.momentFormat, true);
            if (m.isValid()) {
                return m.format(this.canonicalMomentFormat);
            }
            return '';
        },
        /**
         * Отображает/скрывает календарь
         */
        togglePicker() {
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
         * Параметры календаря
         * @return {Object}
         */
        datePickerParams() {
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
                                this.$emit('update:modelValue', this.pValue = value);
                            } else {
                                this.$emit('update:modelValue', this.pValue = this.modelValue);
                                this.$forceUpdate();
                            }
                        } else {
                            this.$emit('update:modelValue', this.pValue = '');
                        }
                    }
                }
            );
        }
    },
    watch: {
        pValue(newVal, oldVal) {
            // 2023-11-14, AVS: заменил, чтобы не вызывалось при одинаковых значениях 
            // (которые по какой-то причине обновились)
            if (JSON.stringify(newVal) != JSON.stringify(oldVal)) {
                this.localValue = this.getLocalValue(this.pValue);
            }
        },
    },
};