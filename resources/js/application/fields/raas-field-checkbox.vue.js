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
            // 2024-05-28, AVS: убрал проверку типа, т.к. может передаваться через HTML-атрибут
            default: false,
        }
    },
    methods: {
        /**
         * Переключение одиночного флажка
         */
        toggleCheckbox() {
            window.setTimeout(() => {
                let val;
                if (this.checked) {
                    val = this.mask || '';
                } else {
                    val = this.defval;
                }
                this.pValue = val;
                this.$emit('input', val);
            }, 0)
        },
        /**
         * Переключение опции
         * @param {Object} $event <pre><code>{
         *     value: Значение,
         *     checked: Boolean Установлено ли значение
         * }</code></pre>
         */
        toggleOption($event) {
            let newValue = [];
            for (let option of this.flatSource) {
                let checked;
                if ($event.value == option.value) {
                    checked = $event.checked;
                } else {
                    checked = this.value && (this.value.map(x => x.toString()).indexOf(option.value.toString()) != -1);
                    // 2024-05-28, AVS: приводим к string, чтобы не было путаницы при различных типах
                }
                if (checked) {
                    if (newValue.map(x => x.toString()).indexOf(option.value.toString()) == -1) {
                        // 2024-05-28, AVS: приводим к string, чтобы не было путаницы при различных типах
                        newValue.push(option.value);
                    }
                }
            }
            // console.log($event, this.flatSource, this.value, newValue)
            this.$emit('input', newValue);
        },
    },
    computed: {
        /**
         * Установлен ли одиночный флажок
         * @return {Boolean}
         */
        checked() {
            return !!this.pValue && (this.pValue != this.mask);
        },
    },
};