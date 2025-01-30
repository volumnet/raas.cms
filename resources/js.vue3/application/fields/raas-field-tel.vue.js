import RAASField from './raas-field.vue.js';

/**
 * Поле телефона
 */
export default {
    /**
     * Шаблон поля
     */
    props: {
        pattern: {
            type: String,
            default: '^\\+\\d \\(\\d{3}\\) \\d{3}-\\d{2}-\\d{2}$',
        }
    },
    mixins: [RAASField],
};