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
        value: {
            default: function () {
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
    },
    data: function () {
        let result = {};
        /**
         * Авто-инкремент для уникальности записей
         * @type {Number}
         */
        result.autoIncrement = 0;
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
    mounted: function () {
        this.initItems();
    },
    methods: {
        /**
         * Инициализирует массив items
         */
        initItems: function () {
            this.autoIncrement = 0;
            this.items = [];
            if (this.value instanceof Array) {
                for (let i = 0; i < this.value.length; i++) {
                    this.items.push({
                        id: ++this.autoIncrement,
                        value: this.value[i],
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
        changeItem: function ($event) {
            $event.target.value = $event.value; 
            this.$emit('input', this.items.map(x => x.value));
        },
        /**
         * Сортировка
         * @param {Object} $event <pre><code>{
         *     originalPosition: Number начальная позиция элемента в списке
         *     position: Конечная позиция элемента в списке
         * }</code></pre>
         */
        sort: function ($event) {
            this.items.splice(
                $event.position, 
                0, 
                this.items.splice($event.originalPosition, 1)[0]
            );
            this.$emit('input', this.items.map(x => x.value));
        },
        /**
         * Добавление элемента
         */
        addItem: function () {
            this.items.push({
                id: ++this.autoIncrement,
                value: this.defval,
            });
            this.$emit('input', this.items.map(x => x.value));
        },
        /**
         * Удаление элемента по ID# объекта
         * @param  {Number} id ID# объекта (авто-инкремент)
         */
        deleteItem: function (item) {
            this.items = this.items.filter(x => x.id != item.id);
            this.$emit('input', this.items.map(x => x.value));
        },
    },
    watch: {
        value: function () {
            this.initItems();
        },
    },
}