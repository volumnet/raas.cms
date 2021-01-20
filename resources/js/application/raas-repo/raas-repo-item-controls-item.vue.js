/**
 * Компонент элемента панели управления элемента репозитория
 */
export default {
    props: {
        /**
         * Элемент перемещаемый
         * @type {String}
         */
        type: {
            type: String,
            required: false,
            default: '',
        },
    },
}