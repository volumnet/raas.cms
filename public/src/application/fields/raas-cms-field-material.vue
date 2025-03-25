<template>
  <raas-field-ajax 
    v-bind="$attrs" 
    :name="name"
    :multiple="multiple"
    :autocomplete-url="'ajax.php?p=cms&sub=main&action=get_materials_by_field&id=' + fieldId + '&search_string='"
    :model-value="pValue"
    @update:model-value="$emit('update:modelValue', pValue = $event)"
  ></raas-field-ajax>
</template>


<script>
import RAASField from 'cms/application/fields/raas-field.vue.js';
export default {
    mixins: [RAASField],
    props: {
        /**
         * Наименование поля
         * @type {String}
         */
        name: {
            type: String,
        },
        /**
         * Множественное поле (убирает возможность очистки)
         * @type {Boolean}
         */
        multiple: {
            type: Boolean,
            default: false,
        },
        /**
         * ID# поля
         * @type {String}
         */
        fieldId: {
            type: Number,
            default: 0,
        },
    },
};
</script>