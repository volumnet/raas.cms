import RAASField from './raas-field.vue.js';

/**
 * Поле числа
 */
export default {
    props: {
        /**
         * Минимальное значение
         * @type {Number}
         */
        min: {
            default: 0,
        },
        /**
         * Максимальное значение
         * @type {Number}
         */
        max: {
            default: Infinity,
        },
        /**
         * Шаг значения
         * @type {Number}
         */
        step: {
            default: 1,
        },
    },
    mixins: [RAASField],
    methods: {
        /**
         * Проверка значения по min/max
         * @param {Number} value Входное значение
         * @return {String} Проверенное значение
         */
        checkValue(value) {
            if ((value !== '') && (value !== null)) {
                value = parseFloat(value) || 0;
                if (this.min !== null) {
                    value = Math.max(value, this.min);
                }
                if (this.max) {
                    value = Math.min(value, this.max);
                }
            }
            return value;
        },
    },
};