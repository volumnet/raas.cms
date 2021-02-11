import RAASField from './raas-field.vue.js';

/**
 * Поле флажка (флажков)
 */
export default {
    mixins: [RAASField],
    props: {
        /**
         * Значение по умолчанию для одиночного флажка
         * @type {Object}
         */
        defval: {
            type: String,
            default: '1',
        },
    },
    methods: {
        /**
         * Переключение одиночного флажка
         */
        toggleCheckbox: function () {
            let val = (this.value ? '' : this.defval);
            this.$emit('input', val);
        }
    },
    computed: {
        /**
         * Установлен ли одиночный флажок
         * @return {Boolean}
         */
        checked: function () {
            return !!this.value;
        },
    },
};