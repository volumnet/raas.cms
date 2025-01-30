/**
 * Компонент блока триггеров
 */
export default {
    data() {
        return {
            /**
             * Фильтр активен
             * @type {Boolean}
             */
            filterActive: false,
        };
    },
    mounted() {
        $(document).on('raas.shop.displayfiltertrigger', () => {
            this.filterActive = true;
        });
    },
    computed: {
        /**
         * Распаковка текущего экземпляра для слота
         * @return {Object}
         */
        self() { 
            return { 
                filterActive: this.filterActive,
            };
        },
    }
};
