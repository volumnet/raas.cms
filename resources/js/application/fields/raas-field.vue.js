import InputMask from '../mixins/inputmask.vue.js';

/**
 * Поле RAAS
 */
export default {
    props: [
        /**
         * Тип поля
         */
        'type',
        /**
         * Значение
         */
        'value',
        /**
         * Источник
         */
        'source',
    ],
    mixins: [InputMask],
    inheritAttrs: false,
    data: function () {
        return {
            pValue: this.value,
        };
    },
    mounted: function () {
        this.inputMask();
        this.applyInputMaskListeners();
    },
    updated: function () {
        this.inputMask();  
        this.applyInputMaskListeners();
    },
    computed: {
        /**
         * Тег текущего компонента
         * @return {String}
         */
        currentComponent: function () {
            return 'raas-field-' + this.type;
        },
        /**
         * Слушатели событий полей (с учетом v-model)
         * @return {Object}
         */
        inputListeners: function () {
            return Object.assign({}, this.$listeners, {
                input: (event) => {
                    this.$emit('input', event.target.value)
                },
            });
        },
    }
}