/**
 * Компонент подтверждения
 */
export default {
    data: function () {
        return {
            /**
             * Текст запроса
             * @type {String}
             */
            text: '',

            /**
             * Текст кнопки "OK"
             * @type {String}
             */
            okText: 'OK',

            /**
             * Текст кнопки "Отмена"
             * @type {String}
             */
            cancelText: 'Отмена',
        };
    },
    methods: {
        /**
         * Обработчик отображения окна подтверждения
         * @param  {String} text       Текст запроса
         * @param  {String} okText     Текст кнопки "ОК"
         * @param  {String} cancelText Текст кнопки "Отмена"
         * @return {jQuery.Promise}
         */
        confirm: function (text, okText, cancelText) {
            this.text = text + '';
            this.okText = okText || 'OK';
            this.cancelText = cancelText || 'Отмена';
            this.promise = new $.Deferred();
            $(this.$el).modal('show');
            return this.promise;
        },

        /**
         * Обработчик кнопки "ОК"
         */
        doConfirm: function () {
            $(this.$el).modal('hide');
            this.promise.resolve(true);
        },

        /**
         * Обработчик кнопки "Отмена"
         */
        doCancel: function () {
            $(this.$el).modal('hide');
            this.promise.reject(false);
        },
    }
}