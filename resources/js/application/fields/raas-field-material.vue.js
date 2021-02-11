import RAASField from './raas-field.vue.js';

/**
 * Поле материала
 */
export default {
    mixins: [RAASField],
    props: [
        /**
         * Материал для отображения
         */
        'material'
    ]
};