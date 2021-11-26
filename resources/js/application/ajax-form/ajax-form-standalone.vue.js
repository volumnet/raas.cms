/**
 * Mixin компонента standalone-формы AJAX (без slot'а)
 * @requires AJAXForm
 */
export default {
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
                    'is-invalid': !!this.errors[field.urn]
                },
                title: (this.errors[field.urn] || ''),
            };
            if ([
                'checkbox', 
                'radio', 
                'htmlarea', 
                'material'
            ].indexOf(field.datatype) == -1) {
                result['class']['form-control'] = true
            }
            // console.log(result);
            return result;
        },
    },
};