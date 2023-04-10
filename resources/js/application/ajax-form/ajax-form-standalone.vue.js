/**
 * Mixin компонента standalone-формы AJAX (без slot'а)
 * @requires AJAXForm
 */
export default {
    props: {
        /**
         * Данные формы
         * @type {Object}
         */
        form: {
            type: Object,
            default() {
                return {};
            },
        },
    },
    data: function () {
        let translations = {
            ASTERISK_MARKED_FIELDS_ARE_REQUIRED: 'Поля, помеченные звездочкой (*), обязательны для заполнения',
            FEEDBACK_SUCCESSFULLY_SENT: 'Спасибо! Ваш запрос успешно отправлен.',
            CANCEL: 'Отмена',
            SEND: 'Отправить',
        };
        if (typeof window.translations == 'object') {
            Object.assign(translations, window.translations);
        }
        let result = {
            translations,
        };
        return result;
    },
    methods: {
        /**
         * Набор атрибутов поля
         * @param {Object} field Данные поля
         * @return {Object}
         */
        fieldAttrs: function (field) {
            let result = { 
                type: field, 
                'class': {
                    'is-invalid': field && !!this.errors[field.urn]
                },
                title: ((field && this.errors[field.urn]) || ''),
            };
            if (field && field.datatype) {
                if ([
                    'checkbox', 
                    'radio', 
                    'htmlarea', 
                    'material'
                ].indexOf(field.datatype) == -1) {
                    result['class']['form-control'] = true
                }
            }
            // console.log(result);
            return result;
        },
    },
    computed: {
        /**
         * HTML-код подсказки об обязательных полях
         * @return {String}
         */
        asteriskHintHTML: function () {
            let result = this.translations.ASTERISK_MARKED_FIELDS_ARE_REQUIRED;
            result = result.replace(
                '*', 
                '<span class="feedback__asterisk">*</span>'
            );
            return result;
        },
    },
};