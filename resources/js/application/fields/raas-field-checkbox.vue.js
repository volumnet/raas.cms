import RAASField from './raas-field.vue.js';

/**
 * Поле флажка (флажков)
 */
export default {
    mixins: [RAASField],
    props: {
        /**
         * Значение по умолчанию для одиночного флажка
         */
        defval: {
            default: '1',
        },
        /**
         * Название поля
         * @type {String}
         */
        name: {
            type: String,
        },
        /**
         * Маскировка значения в случае неактивного флажка
         * @type {String}
         */
        mask: {
            type: String,
            default: null,
        },
        /**
         * Множественное поле
         * @type {Boolean}
         */
        multiple: {
            type: Boolean,
            default: false,
        }
    },
    methods: {
        /**
         * Переключение одиночного флажка
         */
        toggleCheckbox: function () {
            let val;
            if (this.checked) {
                val = this.mask || '';
            } else {
                val = this.defval;
            }
            this.$emit('input', val);
        },
        /**
         * Переключение опции
         * @param {Object} $event <pre><code>{
         *     value: Значение,
         *     checked: Boolean Установлено ли значение
         * }</code></pre>
         */
        toggleOption: function ($event) {
            let newValue = [];
            for (let option of this.flatSource) {
                let checked;
                if ($event.value == option.value) {
                    checked = $event.checked;
                } else {
                    checked = (this.value.indexOf(option.value) != -1)
                }
                if (checked) {
                    if (newValue.indexOf(option.value) == -1) {
                        newValue.push(option.value);
                    }
                }
            }
            this.$emit('input', newValue);
        },
    },
    computed: {
        /**
         * Установлен ли одиночный флажок
         * @return {Boolean}
         */
        checked: function () {
            return !!this.value && (this.value != this.mask);
        },
    },
};