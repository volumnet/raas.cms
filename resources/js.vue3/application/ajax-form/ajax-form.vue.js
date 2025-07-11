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
            default() {
                return {};
            },
        },
        /**
         * Начальные ошибки формы
         * @type {Object} <pre><code>object<
         *     string[] URN ошибки => string Текст ошибки
         * ></code></pre>
         */
        initialErrors: {
            type: Object,
            default() {
                return {};
            }
        },
        /**
         * Скроллить к предупреждениям в случае ошибки
         * @type {Object}
         */
        scrollToErrors: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['update:formData', 'submit', 'response', 'success', 'error'],
    data() {
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
            errors: (typeof this.initialErrors == 'object') 
                ? this.initialErrors
                : {},

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
                ? JSON.parse(JSON.stringify(this.initialFormData)) // Чтобы не было привязки объекта
                : {},

            /**
             * Старые данные формы
             * @type {Object}
             */
            oldFormData: (typeof this.initialFormData == 'object') 
                ? JSON.parse(JSON.stringify(this.initialFormData))
                : {},
        };
    },
    mounted() {
        var self = this;

        $(this.$el).on('submit', async function (e) {
            $(this).trigger('RAAS.AJAXForm.submit');
            $(this).trigger('raas.ajaxform.submit');
            self.$emit('submit', e);
            e.stopPropagation();
            e.preventDefault();

            self.loading = true;
            self.success = false;
            self.localError = {};
            const formData = self.getFormData();
            const url = $(this).attr('action') || window.location.href;
            const requestType = $(this).attr('enctype') || 'multipart/form-data';
            try {
                const response = await self.$root.api(
                    url, 
                    formData, 
                    self.blockId || null, 
                    'application/json', 
                    requestType
                );
                self.handle(response);
            } catch (err) {
                self.loading = false;
                self.$emit('serverError', err);
            }
            return false;
        });
        $('input, select, textarea', this.$el).change(function () {
            $(this).closest('.form-group').removeClass('text-danger');
            $(this).removeClass('text-danger');
            let newErrors = JSON.parse(JSON.stringify(self.errors));
            delete newErrors[$(this).attr('name')];
            self.errors = newErrors;
        });

    },
    methods: {
        /**
         * Получает данные формы
         * @return {FormData}
         */
        getFormData() {
            const formData = new FormData(this.$el);
            formData.append('AJAX', (this.blockId || 1));
            return formData;
        },
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
        handle(data) {
            this.loading = false;
            $(this.$el).trigger('raas.ajaxform.response', data);
            $(this.$el).trigger('RAAS.AJAXForm.response', data);
            this.$emit('response', data);
            let redirectUrl = (data.redirectUrl || data.redirectURL);
            if (redirectUrl) {
                window.location.href = redirectUrl;
            } else if (data.success) {
                this.success = true;
                this.errors = {};
                $(this.$el).trigger('RAAS.AJAXForm.success', data);
                $(this.$el).trigger('raas.ajaxform.success', data);
                this.$emit('success', data);
            } else if (data.localError) {
                this.errors = data.localError;
                $(this.$el).trigger('RAAS.AJAXForm.error', data.localError);
                $(this.$el).trigger('raas.ajaxform.error', data.localError);
                this.$emit('error', data.localError);
                if (this.scrollToErrors) {
                    window.setTimeout(() => {
                        // console.log(this.$refs.errors);
                        $.scrollTo(this.$refs.errors || this.$root.$el, 500);
                    }, 10); // Чтобы успела появиться плашка с ошибками
                }
            }
        },
        /**
         * Устанавливает данные на поле
         * @param {String} fieldURN Название поля
         * @param {mixed} value Значение
         */
        setData(fieldURN, value) {
            this.formData[fieldURN] = value;
        },
    },
    computed: {
        /**
         * Имеет ли форма ошибки
         * @return {Boolean}
         */
        hasErrors() {
            return Object.values(this.errors).length > 0;
        },
        /**
         * Распаковка текущего экземпляра для слота
         * @return {Object}
         */
        self() { 
            return { 
                blockId: this.blockId,
                initialFormData: this.initialFormData,
                initialErrors: this.initialErrors,
                scrollToErrors: this.scrollToErrors,
                loading: this.loading,
                errors: this.errors,
                success: this.success,
                formData: this.formData,
                oldFormData: this.oldFormData,
                getFormData: this.getFormData.bind(this),
                handle: this.handle.bind(this),
                setData: this.setData.bind(this),
                hasErrors: this.hasErrors,
            };
        },
    },
    watch: {
        initialFormData(newVal, oldVal) {
            if (JSON.stringify(newVal) != JSON.stringify(oldVal)) { 
                // Чтобы не обновлялась статика (например, в регистрации при изменении пользователя 
                // считается что изменились также и входные данные, а там снова подается старая статика)
                this.formData = JSON.parse(JSON.stringify(this.initialFormData)); // Чтобы не было привязки объекта
            }
        },
        formData: {
            handler() {
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
                this.$emit('update:formData', this.formData);
            },
            deep: true,
        }
    }
}