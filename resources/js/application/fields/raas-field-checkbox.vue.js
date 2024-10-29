import RAASField from './raas-field.vue.js';
import CheckboxMixin from './checkbox.mixin.vue.js';

/**
 * Поле флажка (флажков)
 */
export default {
    mixins: [RAASField, CheckboxMixin],
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
         * Максимальное количество дочерних элементов для плоского списка
         * @type {Object}
         */
        flatMaxCounter: {
            type: Number,
            default: 5,
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
            });
        },
        /**
         * Переключение опции
         * @param {Object} $event <pre><code>{
         *     value: Значение,
         *     checked?: Boolean Установлено ли значение (если undefined, тогда считаем что меняется)
         * }</code></pre>
         */
        toggleOption($event) {
            // 2024-05-28, AVS: приводим к string, чтобы не было путаницы при различных типах
            // 2024-10-10, AVS: заменил value на pValue, чтобы работало со статикой
            // const oldValue = (this.pValue || []).map(x => x.toString());
            let newValue = JSON.parse(JSON.stringify(this.pValue));

            // const matchingOptions = this.flatSource.filter(option => option.value.toString() == $event.value.toString());
            // if (!matchingOptions.length) {
            //     return; // Нет таких опций, ничего не делаем
            // }

            const oldChecked = !!(this.pValue || []).filter(x => x.toString() == $event.value.toString()).length;
            const newChecked = ($event.checked !== undefined) ? $event.checked : !oldChecked;
            
            if (oldChecked == newChecked) {
                return; // Совпадает, ничего не делаем
            }

            if (newChecked) {
                newValue.push($event.value);
            } else {
                newValue = newValue.filter(x => x.toString() != $event.value.toString());
            }

            // if ($event.checked || (($event.checked == undefined) && !oldChecked)) {
            //     newValue.push()
            // }
            
            // for (let option of this.flatSource) {
            //     let optionChecked;
            //     // 2024-05-28, AVS: приводим к string, чтобы не было путаницы при различных типах
            //     const optionValue = option.value.toString();
            //     if ($event.value == option.value) {
            //         // Если совпадает, смотрим по обновляемому значению
            //         optionChecked = $event.checked;
            //     } else {
            //         optionChecked = (oldValue.indexOf(optionValue) != -1);
            //     }
            //     if (optionChecked) {
            //         if (newValue.map(x => x.toString()).indexOf(optionValue) == -1) {
            //             // 2024-05-28, AVS: приводим к string, чтобы не было путаницы при различных типах
            //             newValue.push(option.value);
            //         }
            //     }
            // }
            // console.log($event, this.flatSource, this.pValue, newValue)
            // 2024-10-10, AVS: добавил, чтобы pValue сразу обновлялось
            this.pValue = newValue;
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