/**
 * Компонент AJAX-формы
 */
export default {
    props: {
        /**
         * ID# блока
         * @type {Number}
         */
        blockId: {
            type: Number
        },
        /**
         * Начальные POST-данные
         * @type {Object}
         */
        initialFormData: {
            type: Object,
            default: function () {
                return {};
            },
        }
    },
    data: function () {
        return {
            /**
             * Маркер загрузки
             * @type {Boolean}
             */
            loading: false,

            /**
             * Ошибки формы
             * @type {Object} <pre><code>object<
             *     string[] URN ошибки => string Текст ошибки
             * ></code></pre>
             */
            errors: {},

            /**
             * Маркер успешной отправки формы
             * @type {Boolean}
             */
            success: false,

            /**
             * Данные формы
             * @type {Object}
             */
            formData: (typeof this.initialFormData == 'object') 
                ? Object.assign({}, this.initialFormData)
                : {},

            /**
             * Старые данные формы
             * @type {Object}
             */
            oldFormData: (typeof this.initialFormData == 'object') 
                ? Object.assign({}, this.initialFormData)
                : {},
        };
    },
    mounted: function () {
        var self = this;

        $(this.$el).submit(function (e) {
            $(this).trigger('RAAS.AJAXForm.submit');
            $(this).trigger('raas.ajaxform.submit');
            self.$emit('submit', e);

            self.loading = true;
            self.success = false;
            self.localError = {};
            $(this).ajaxSubmit({ 
                dataType: 'json', 
                'url': $(this).attr('action'), 
                success: self.handle.bind(self), 
                error: function () {
                    self.loading = false;
                },
                data: { 
                    AJAX: (self.blockId || 1) 
                } 
            });
            return false;
        });
        $('input, select, textarea', this.$el).change(function () {
            $(this).closest('.form-group').removeClass('text-danger');
            $(this).removeClass('text-danger');
        });

    },
    methods: {
        /**
         * Обработчик формы
         * @param  {Object} data <pre><code>{
         *     success: ?bool Маркер успешной отправки формы,
         *     localError: ?object<
         *         string[] URN ошибки => string Текст ошибки
         *     >,
         * }</code></pre>
         * @return {[type]}      [description]
         */
        handle: function (data) {
            this.loading = false;
            $(this.$el).trigger('raas.ajaxform.response', data);
            $(this.$el).trigger('RAAS.AJAXForm.response', data);
            this.$emit('response', data);
            if (data.success) {
                this.success = true;
                $(this.$el).trigger('RAAS.AJAXForm.success', data);
                $(this.$el).trigger('raas.ajaxform.success', data);
                this.$emit('success', data);
            } else if (data.localError) {
                this.errors = data.localError;
                $(this.$el).trigger('RAAS.AJAXForm.error', data.localError);
                $(this.$el).trigger('raas.ajaxform.error', data.localError);
                this.$emit('error', data.localError);
            }
        },
    },
    computed: {
        /**
         * Имеет ли форма ошибки
         * @return {Boolean}
         */
        hasErrors: function () {
            return Object.values(this.errors).length > 0;
        },
        /**
         * Распаковка текущего экземпляра для слота
         * @return {Object}
         */
        self: function () { 
            return { ...this };
        },
    },
    watch: {
        formData: {
            handler: function () {
                // console.log(this.formData, this.oldFormData);
                for (let key in this.errors) {
                    // console.log(key, this.formData[key], this.oldFormData[key])
                    if (this.formData[key] instanceof Array) {
                        for (let i = 0; i < this.formData[key].length; i++) {
                            // console.log(key, i, this.formData[key][i], this.oldFormData[key][i])
                            if (this.formData[key][i] != this.oldFormData[key][i]) {
                                if (this.errors[key]) {
                                    if (this.errors[key] instanceof Array) {
                                        if (this.errors[key][i]) {
                                            delete this.errors[key][i];
                                        }
                                    } else {
                                        delete this.errors[key];
                                    }
                                }
                            }
                        }
                    } else {
                        // console.log(key, this.formData[key], this.oldFormData[key])
                        if (this.formData[key] != this.oldFormData[key]) {
                            delete this.errors[key];
                        }
                    }
                }
                this.errors = Object.assign({}, this.errors);
                // console.log(this.formData);
                this.oldFormData = JSON.parse(JSON.stringify(this.formData));
                this.$emit('input', this.formData);
            },
            deep: true,
        }
    }
}