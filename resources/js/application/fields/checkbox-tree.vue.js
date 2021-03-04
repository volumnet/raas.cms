import RAASField from './raas-field.vue.js';

export default {
    mixins: [RAASField],
    props: {
        /**
         * Значение по умолчанию для одиночного флажка
         * @type {Object}
         */
        defval: {
            type: String,
            default: '1',
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
         * Значение, принудительно приведенное к массиву
         * @return {Array}
         */
        arrayValue: function () {
            let value = this.value;
            if (!(value instanceof Array)) {
                value = (value !== null) ? [value] : [];
            }
            return value;
        },
    }
};