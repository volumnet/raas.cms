import RAASField from './raas-field.vue.js';
import CheckboxMixin from './checkbox.mixin.vue.js';

export default {
    mixins: [RAASField, CheckboxMixin],
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
};