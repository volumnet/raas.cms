/**
 * Компонент блока триггеров
 */
export default {
    data: function () {
        return {
            /**
             * Позиция скролла по вертикали от верхнего края документа в px
             * @type {Number}
             */
            scrollTop: 0,

            /**
             * Фильтр активен
             * @type {Boolean}
             */
            filterActive: false,
        };
    },
    mounted: function () {
        var self = this;
        this.doScroll();
        $(window).on('scroll', this.doScroll.bind(this));
        $(document).on('raas.shop.displayfiltertrigger', function () {
            self.filterActive = true;
        });
    },
    methods: {
        /**
         * Обработчик скролла
         */
        doScroll: function () {
            this.scrollTop = $(window).scrollTop();
        },

        /**
         * Команда открытия фильтра
         * @param  {Object} $event Событие
         */
        openFilter: function ($event) {
            $(document).trigger('raas.shop.openfilter');
            $event.stopPropagation();
            $event.preventDefault();
        }
    },
    computed: {
        /**
         * Маппинг CSS-класса пункта "Наверх"
         * @return {Object}
         */
        toTopClass: function () {
            let result = {
                'triggers-list__item': true,
                'triggers-list__item_totop': true,
                'triggers-list__item_active': (this.scrollTop > 500),
            };
            return result;
        },
        /**
         * Маппинг CSS-класса пункта "Фильтр"
         * @return {Object}
         */
        filterClass: function () {
            let result = {
                'triggers-list__item': true,
                'triggers-list__item_filter': true,
                'triggers-list__item_active': this.filterActive,
            };
            return result;
        },
    }
};
