/**
 * Компонент репозитория
 */
export default {
    props: {
        /**
         * Первый элемент обязателен
         * @type {Boolean}
         */
        required: {
            type: Boolean,
            required: false,
            default: false,
        },

        /**
         * Данные репозитория
         * @type {Array}
         */
        value: {
            required: true,
            default: [],
        },

        /**
         * Горизонтальное расположение
         * @type {Boolean}
         */
        horizontal: {
            type: Boolean,
            required: false,
            default: false,
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
         * Внутреннее представление данных репозитория
         * @type {Array} <pre><code>array<{
         *     id: ID# объекта (авто-инкремент),
         *     value: Значение
         * }></code></pre>
         */
        result.items = [];

        if (this.value instanceof Array) {
            for (let i = 0; i < this.value.length; i++) {
                result.items.push({
                    id: ++result.autoIncrement,
                    value: this.value[i],
                });
            }
        }
        if (this.required && !result.items.length) {
            result.items.push({
                id: ++result.autoIncrement,
                value: null,
            });
        }
        return result;
    },
    methods: {
        /**
         * Добавление элемента
         */
        addItem: function () {
            this.items.push({
                id: ++this.autoIncrement,
                value: null,
            });
        },
        /**
         * Удаление элемента по ID# объекта
         * @param  {Number} id ID# объекта (авто-инкремент)
         */
        deleteItem: function (id) {
            this.items = this.items.filter(item => item.id != id);
        },
    }
}