import RAASField from './raas-field.vue.js';

/**
 * Поле пароля
 */
export default {
    mixins: [RAASField],
    props: {
        /**
         * Пароль замаскирован от внешних обработчиков
         * @type {String} символ маскировки
         */
        mask: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            /**
             * Пароль открыт
             * @type {Boolean}
             */
            visible: false,
        };
    },
    mounted() {
        this.$el.classList.remove('form-control');
    },
    updated() {
        this.$el.classList.remove('form-control');
    },
    computed: {
        /**
         * Текущий тип input'а
         * @return {String} <pre><code>'type'|'password'</code></pre>
         */
        currentType() {
            return this.visible ? 'text' : 'password';
        }
    },
};