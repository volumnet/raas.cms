/**
 * Форма поиска
 */
export default {
    data: function () {
        return {
            /**
             * Активность формы по кнопке
             * @type {Boolean}
             */
            active: false,
        };
    },
    methods: {
        /**
         * Разворачивает/скрывает форму
         */
        toggle: function () {
            this.active = !this.active;
        }
    },
    computed: {
        /**
         * Аналог this для привязки к слоту
         * @return {Object}
         */
        self: function () {
            return { ...this };
        },
    },
}