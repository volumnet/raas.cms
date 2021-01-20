/**
 * Компонент уведомлений о Cookies
 */
export default {
    data: function () {
        return {
            /**
             * Уведомление активно
             * @type {Boolean}
             */
            active: false,
        };
    },
    mounted: function () {
        if (!Cookie.get('cookies-notification')) {
            this.active = true;
        }
    },
    methods: {
        /**
         * Закрытие уведомления
         * @param  {Object} $event Событие
         */
        close: function ($event) {
            Cookie.set(
                'cookies-notification', 
                '1', 
                { expires: 3650, path: '/' }
            );
            this.active = false;
            $event.preventDefault();
        }
    },
};