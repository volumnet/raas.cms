/**
 * Компонент уведомлений о Cookies
 */
export default {
    data() {
        return {
            /**
             * Уведомление активно
             * @type {Boolean}
             */
            active: false,
        };
    },
    mounted() {
        if (!Cookie.get('cookies-notification')) {
            this.active = true;
        }
    },
    methods: {
        /**
         * Закрытие уведомления
         * @param  {Object} $event Событие
         */
        close($event) {
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