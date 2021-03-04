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
    methods: {
        /**
         * Получает список опций источника в плоском виде
         * @param {Array} source <pre><code>array<{
         *     value: String Значение,
         *     name: String Текст,
         *     children:? {Array} Рекурсивно
         * }></code></pre> Источник
         * @param {Number} level Уровень вложенности
         * @return {Array} <pre><code>array<{
         *     value: String Значение,
         *     name: String Текст,
         *     level: Number Уровень вложенности
         * }></code></pre>
         */
        getFlatSource: function (source, level = 0) {
            let result = [];
            for (let option of source) {
                let newOption = {
                    value: option.value,
                    name: option.name,
                    level: level,
                };
                result.push(newOption);
                if (option.children) {
                    result = result.concat(this.getFlatSource(option.children, level + 1));
                }
            }
            return result;
        },
    },
    computed: {
        /**
         * Опции в плоском виде
         * @return {Array} <pre><code>array<{
         *     value: String Значение,
         *     name: String Текст,
         *     level: Number Уровень вложенности
         * }></code></pre>
         */
        flatSource: function () {
            let source = this.source;
            if (!(source instanceof Array)) {
                source = [];
            }
            return this.getFlatSource(source);
        },
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
        /**
         * Многоуровневый источник
         * @return {Boolean}
         */
        multilevel: function () {
            return this.flatSource.filter(x => (x.level > 0)).length > 0;
        },
    },
}