import RAASField from './raas-field.vue.js';

/**
 * Поле переключателя(переключателей)
 */
export default {
    mixins: [RAASField],
    props: {
        /**
         * Значение одиночного переключателя
         * @type {Object}
         */
        defval: {
            default: '',
        },
        /**
         * Название поля
         * @type {Object}
         */
        name: {
            type: String,
        },
        /**
         * Максимальное количество дочерних элементов для плоского списка
         * @type {Object}
         */
        flatMaxCounter: {
            type: Number,
            default: 5,
        },
    },
    computed: {
        /**
         * Установлен ли одиночный флажок
         * @return {Boolean}
         */
        checked() {
            return (this.pValue == this.defval);
        },
    },
};