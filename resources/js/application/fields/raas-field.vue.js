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
            type: [String, Object],
            default: 'text',
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
    data() {
        return {
            pValue: this.value,
        };
    },
    mounted() {
        this.inputMask();
        this.applyInputMaskListeners();
    },
    updated() {
        this.inputMask();  
        this.applyInputMaskListeners();
    },
    methods: {
        /**
         * Устанавливает внутреннее значение
         * @param {mixed} value Значение
         */
        setPValue(value) {
            this.pValue = value;
        },
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
        getFlatSource(source, level = 0) {
            let result = [];
            for (let option of source) {
                let newOption = {
                    value: option.value,
                    name: option.name || option.caption,
                    level: level,
                };
                if (option.disabled) {
                    newOption.disabled = true;
                }
                result.push(newOption);
                if (option.children) {
                    result = result.concat(this.getFlatSource(option.children, level + 1));
                }
            }
            return result;
        },
    },
    computed: {
        resolvedAttrs() {
            let result = {...this.$attrs};
            if (typeof this.type == 'object') {
                result.is = 'raas-field-' + (this.type.datatype || 'text');
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
                if (this.type.accept) {
                    result.accept = this.type.accept;
                }
                if (this.type.pattern) {
                    result.pattern = this.type.pattern;
                }
                if (this.type['class']) {
                    result['class'] = Object.assign({}, result['class'] || {}, this.type['class']);
                }
                if (this.type.className) {
                    result['class'] = Object.assign({}, result['class'] || {}, this.type.className);
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
        flatSource() {
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
        currentComponent() {
            return 'raas-field-' + (this.type || 'text');
        },
        /**
         * Слушатели событий полей (с учетом v-model)
         * @return {Object}
         */
        inputListeners() {
            // Работает только в десктопе, в мобиле inputmask заглушает значение
            return Object.assign({}, this.$listeners, {
                input: (event) => {
                    this.$emit('input', this.pValue = event.target.value)
                },
            });
        },
        /**
         * Многоуровневый источник
         * @return {Boolean}
         */
        multilevel() {
            return this.flatSource.filter(x => (x.level > 0)).length > 0;
        },
    },
    watch: {
        value(newVal, oldVal) {
            // 2023-11-14, AVS: заменил, чтобы не вызывалось при одинаковых значениях 
            // (которые по какой-то причине обновились)
            if (JSON.stringify(newVal) != JSON.stringify(oldVal)) {
                this.pValue = this.value;
            }
        },
    },
}