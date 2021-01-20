/**
 * Компонент элемента репозитория
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

        /**
         * Данные элемента репозитория
         */
        value: {
            required: true
        },
    },
    mounted: function () {
        $('*', this.$refs.inputContainer).on('input', ($event) => {
            this.$emit('input', $event.target.value);
        });
    }
}