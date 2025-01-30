/**
 * Компонент репозитория
 */
export default {
    props: {
        /**
         * Первый элемент обязателен
         */
        required: {
            type: Boolean,
            required: false,
            default: false,
        },

        /**
         * Данные репозитория
         */
        modelValue: {
            default() {
                return [];
            },
        },

        /**
         * Значение нового элемента по умолчанию
         */
        defval: {
            default: null,
        },

        /**
         * Горизонтальное расположение
         */
        horizontal: {
            type: Boolean,
            required: false,
            default: false,
        },

        /**
         * Сортируемый репозиторий (вызывается событие по сортировке)
         */
        sortable: {
            type: Boolean,
            default: true,
        },
        /**
         * Возможность вставки
         * @type {Object}
         */
        insertable: {
            type: Boolean,
            default: true,
        }
    },
    emits: ['update:modelValue'],
    data() {
        let result = {};
        /**
         * Авто-инкремент для уникальности записей
         * @type {Number}
         */
        result.autoIncrement = 0;
        result.pValue = [...this.modelValue];
        /**
         * Массив элементов
         * @type {Array} <pre><code>array<{
         *     id: Number ID# элемента,
         *     value: Значение элемента
         * }></code></pre>
         */
        result.items = [];
        return result;
    },
    mounted() {
        this.initItems();
    },
    methods: {
        /**
         * Инициализирует массив items
         */
        initItems() {
            this.autoIncrement = 0;
            this.items = [];
            if (this.pValue instanceof Array) {
                for (let i = 0; i < this.pValue.length; i++) {
                    this.items.push({
                        id: ++this.autoIncrement,
                        value: this.pValue[i],
                    });
                }
            }
            if (this.required && !this.items.length) {
                this.items.push({
                    id: ++this.autoIncrement,
                    value: null,
                });
            }
        },
        /**
         * Изменение элемента
         * @param {Object} <pre><code>{
         *     target: {
         *         id: ID# объекта (авто-инкремент),
         *         value: Значение
         *     } Объект, к которому применяется изменение,
         *     value: Новое значение
         * }</code></pre>
         */
        changeItem($event) {
            $event.target.value = $event.value; 
            this.pValue = this.items.map(x => x.value);
            this.$emit('update:modelValue', this.pValue);
        },
        /**
         * Сортировка
         * @param {Object} $event <pre><code>{
         *     originalPosition: Number начальная позиция элемента в списке
         *     position: Конечная позиция элемента в списке
         * }</code></pre>
         */
        sort($event) {
            this.items.splice(
                $event.position, 
                0, 
                this.items.splice($event.originalPosition, 1)[0]
            );
            this.pValue = this.items.map(x => x.value);
            this.$emit('update:modelValue', this.pValue);
        },
        /**
         * Добавление элемента
         */
        addItem() {
            this.items.push({
                id: ++this.autoIncrement,
                value: this.defval,
            });
            this.pValue = this.items.map(x => x.value);
            this.$emit('update:modelValue', this.pValue);
        },
        /**
         * Удаление элемента по ID# объекта
         * @param  {Number} id ID# объекта (авто-инкремент)
         */
        deleteItem(item) {
            this.items = this.items.filter(x => x.id != item.id);
            this.pValue = this.items.map(x => x.value);
            this.$emit('update:modelValue', this.pValue);
        },
    },
    watch: {
        modelValue(newValue, oldValue) {
            // 2023-11-14, AVS: заменил, чтобы не вызывалось при одинаковых значениях 
            // (которые по какой-то причине обновились)
            if (JSON.stringify(newValue) != JSON.stringify(oldValue)) {
                this.pValue = this.modelValue;
                this.initItems();
            }
        },
    },
}