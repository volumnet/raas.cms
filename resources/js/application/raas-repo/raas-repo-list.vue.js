/**
 * Компонент списка репозитория
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
         * @type {Array} <pre><code>array<{
         *     id: ID# объекта (авто-инкремент),
         *     value: Значение
         * }></code></pre>
         */
        value: {
            type: Array,
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
    mounted: function () {
        $(this.$el).sortable(this.sortableParams);
    },
    computed: {
        /**
         * Параметры jQueryUI-виджета Sortable
         * @type {Object}
         */
        sortableParams: function () {
            let result = {
                containment: 'parent',
                revert: true,
            };
            if (!this.horizontal) {
                result.axis = 'y';
                result.handle = '.raas-repo-item-controls-item_drag';
            }
            return result;
        },
        /**
         * Можно ли сортировать элементы
         * @type {Boolean}
         */
        draggable: function () {
            return this.value.length > 1;
        },
        /**
         * Можно ли удалять элементы
         * @type {Boolean}
         */
        removable: function () {
            return !this.required || (this.value.length > 1);
        },
    },
}