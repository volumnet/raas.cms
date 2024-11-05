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

        /**
         * Сортируемый репозиторий (вызывается событие по сортировке)
         */
        sortable: {
            type: Boolean,
            default: true,
        },
    },
    data: function () {
        return {
            /**
             * Счетчик сортировок (для создания уникального ключа элементов списка)
             * @type {Number}
             */
            sortCounter: 0,
        };
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
            let originalPosition = null;
            let result = {
                // containment: 'parent', // 2024-11-05, AVS: неудобно, т.к. нельзя поместить перед первым и после последнего
                revert: true,
                start: (event, ui) => {
                    originalPosition = ui.item.parent().children().index(ui.item);
                },
                stop: (event, ui) => {
                    let position = ui.item.parent().children().index(ui.item);
                    if (position != originalPosition) {
                        this.$emit('sort', { originalPosition, position });
                    }
                    originalPosition = null;
                    this.sortCounter++;
                },
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
            return this.sortable && (this.value.length > 1);
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