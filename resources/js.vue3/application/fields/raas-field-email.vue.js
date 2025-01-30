import RAASField from './raas-field.vue.js';

/**
 * Поле e-mail
 */
export default {
    props: {
        /**
         * Шаблон поля
         */
        pattern: {
            type: String,
            default: '^[\\w\\-\\.]+@[A-Za-z0-9\\-]+\\.([A-Za-z\\.]+)$',
        },
    },
    mixins: [RAASField],
};