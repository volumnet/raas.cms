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
    },
    computed: {
        /**
         * Установлен ли одиночный флажок
         * @return {Boolean}
         */
        checked: function () {
            return (this.value == this.defval);
        },
    },
};