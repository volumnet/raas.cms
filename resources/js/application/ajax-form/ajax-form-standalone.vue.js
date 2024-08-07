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
    data() {
        let translations = {
            ASTERISK_MARKED_FIELDS_ARE_REQUIRED: 'Поля, помеченные звездочкой (*), обязательны для заполнения',
            FEEDBACK_SUCCESSFULLY_SENT: 'Спасибо! Ваш запрос успешно отправлен.',
            CANCEL: 'Отмена',
            SEND: 'Отправить',
            AGREE_BY_CLICKING_SEND: 'Отправляя форму, вы даете согласие на <a href="/privacy/" target="_blank">обработку персональных данных</a>',
        };
        if (typeof window.translations == 'object') {
            Object.assign(translations, window.translations);
        }
        let result = {
            translations,
            autoFormControlClassFields: [], // Автоматически добавлять класс form-control ко этим типам
            formControlClassFieldsExceptions: [
                'checkbox', 
                'radio', 
                'htmlarea', 
                'material',
                'hidden',
                'rating',
                'video',
                'image',
            ], // Автоматически добавлять класс form-control ко всем типам кроме заданных
        };
        return result;
    },
    methods: {
        /**
         * Набор атрибутов поля
         * @param {Object} field Данные поля
         * @return {Object}
         */
        fieldAttrs(field) {
            let result = { 
                type: field, 
                'class': {
                    'is-invalid': field && !!this.errors[field.urn]
                },
                title: ((field && this.errors[field.urn]) || ''),
            };
            if (field && field.datatype) {
                if (this.autoFormControlClassFields && this.autoFormControlClassFields.length && (this.autoFormControlClassFields.indexOf(field.datatype) != -1)) {
                    result['class']['form-control'] = true;
                } else if (this.formControlClassFieldsExceptions.indexOf(field.datatype) == -1) {
                    result['class']['form-control'] = true;
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
        asteriskHintHTML() {
            let result = this.translations.ASTERISK_MARKED_FIELDS_ARE_REQUIRED;
            result = result.replace(
                '*', 
                '<span class="feedback__asterisk">*</span>'
            );
            return result;
        },
    },
};