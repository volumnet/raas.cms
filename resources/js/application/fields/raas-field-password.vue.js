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
    computed: {
        /**
         * Слушатели событий полей (с учетом v-model)
         * @return {Object}
         */
        inputListeners: function () {
            let newListeners = {};
            if (this.mask) {
                newListeners.input = (event) => {
                    this.$emit('input', this.mask.repeat(event.target.value.length));
                };
            } else {
                newListeners.input = (event) => {
                    this.$emit('input', event.target.value);
                };
            }
            return Object.assign({}, this.$listeners, newListeners);
        },
        /**
         * Текущий тип input'а
         * @return {String} <pre><code>'type'|'password'</code></pre>
         */
        currentType: function () {
            return this.visible ? 'text' : 'password';
        }
    },
};