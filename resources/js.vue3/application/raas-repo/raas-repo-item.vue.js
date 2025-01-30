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
        modelValue: {
            required: true
        },
    },
    emits: ['update:modelValue', 'delete'],
    methods: {
        /**
         * Обертка для метода $emit для использования в слоте
         * @param {String} eventName Наименование события
         * @param {mixed} data Данные, передаваемые в событие
         */
        slotEmit(eventName, data) {
            this.$emit(eventName, data);
        },
    },
}