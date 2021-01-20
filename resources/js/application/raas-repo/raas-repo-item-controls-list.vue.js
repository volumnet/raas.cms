/**
 * Компонент панели управления элемента репозитория
 */
export default {
    props: {
        /**
         * Элемент перемещаемый
         * @type {Boolean}
         */
        draggable: {
            type: Boolean,
            required: false,
            default: false,
        },

        /**
         * Элемент удаляемый
         * @type {Boolean}
         */
        removable: {
            type: Boolean,
            required: false,
            default: false,
        },
    },
}