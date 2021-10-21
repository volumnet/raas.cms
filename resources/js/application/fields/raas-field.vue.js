import InputMask from '../mixins/inputmask.vue.js';

/**
 * Поле RAAS
 */
export default {
    props: {
        /**
         * Тип либо объект поля
         * @param {String|Object}
         */
        type: {
            required: true,
            type: [String, Object]
        },
        /**
         * Значение
         */
        value: {},
        /**
         * Источник
         */
        source: {},
    },
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
        resolvedAttrs: function () {
            let result = Object.assign(this.$attrs, {
                is: 'raas-field-' + this.type.datatype
            });
            if (typeof this.type == 'object') {
                if (this.type.datatype) {
                    result.type = this.type.datatype;
                }
                if (this.type.urn) {
                    result.name = this.type.urn;
                }
                if (this.type.htmlId) {
                    result.id = this.type.htmlId;
                }
                if (this.type.stdSource) {
                    result.source = this.type.stdSource;
                }
                if (['number', 'range'].indexOf(this.type.datatype) != -1) {
                    if (this.type.min_val) {
                        result.min = this.type.min_val;
                    }
                    if (this.type.max_val) {
                        result.max = this.type.max_val;
                    }
                    if (this.type.step) {
                        result.step = this.type.step;
                    }
                }
                if (this.type.defval) {
                    if (['checkbox', 'radio'].indexOf(this.type.datatype) != -1) {
                        result.defval = this.type.defval;
                    }
                }
                if (this.type.required) {
                    result.required = true;
                }

                if (this.type.multiple) {
                    if (['radio'].indexOf(this.type.datatype) == -1) {
                        result.multiple = true;
                    }
                }
                if (this.type.placeholder) {
                    result.placeholder = this.type.placeholder;
                }
                if (this.type.maxlength) {
                    result.maxlength = this.type.maxlength;
                }
            }
            if (!result.type) {
                result.type = 'text';
            }
            // console.log(result)
            return result;
        },
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
                    this.$emit('input', $(event.target).val())
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
    watch: {
        value: function () {
            this.pValue = this.value;
        },
    },
}