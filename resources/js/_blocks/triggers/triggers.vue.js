/**
 * Компонент блока триггеров
 */
export default {
    data: function () {
        return {
            /**
             * Фильтр активен
             * @type {Boolean}
             */
            filterActive: false,
        };
    },
    mounted: function () {
        $(document).on('raas.shop.displayfiltertrigger', () => {
            this.filterActive = true;
        });
    },
    computed: {
        /**
         * Распаковка текущего экземпляра для слота
         * @return {Object}
         */
        self: function () { 
            return { ...this };
        },
    }
};
