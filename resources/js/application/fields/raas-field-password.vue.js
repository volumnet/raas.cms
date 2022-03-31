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
    data: function () {
        return {
            /**
             * Пароль открыт
             * @type {Boolean}
             */
            visible: false,
        };
    },
    mounted: function () {
        this.$el.classList.remove('form-control');
    },
    updated: function () {
        this.$el.classList.remove('form-control');
    },
    computed: {
        /**
         * Текущий тип input'а
         * @return {String} <pre><code>'type'|'password'</code></pre>
         */
        currentType: function () {
            return this.visible ? 'text' : 'password';
        }
    },
};