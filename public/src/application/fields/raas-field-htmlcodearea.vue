<style lang="scss">
</style>


<template>
  <div v-if="!wysiwygUpdating">
    <component 
      :is="wysiwyg ? 'raas-field-htmlarea' : 'raas-field-codearea'" 
      :type="wysiwyg ? 'htmlarea' : 'codearea'" 
      v-bind="$attrs" 
      v-on="inputListeners" 
      :value="pValue"
      @input="pValue = $event;"
    ></component>
  </div>
</template>


<script>
import { html as htmlBeautifier } from 'js-beautify';
import RAASField from 'cms/application/fields/raas-field.vue.js';
export default {
    mixins: [RAASField],
    props: {
        /**
         * Имя флажка визуального редактора
         * @type {String}
         */
        wysiwygName: {
            type: String,
            default: 'wysiwyg',
        }
    },
    data() {
        return {
           wysiwyg: false,
           wysiwygUpdating: true,
        };
    },
    mounted() {
        this.updateWysiwyg();
        $('body').on('click', '[name="' + this.wysiwygName + '"]', () => {
            this.updateWysiwyg(true);
        })
    },
    methods: {
        /**
         * Обновляет состояние визуального редактора
         * @param {Boolean} beautifyHtml Бьютифицировать HTML
         */
        updateWysiwyg(beautifyHtml = false) {
            const $wysiwyg = $('[name="' + this.wysiwygName + '"]');
            this.wysiwygUpdating = true;
            this.wysiwyg = $wysiwyg.prop('checked');
            if (!this.wysiwyg && beautifyHtml) {
                this.pValue = this.beautifiedHTML;
                this.$emit('input', this.pValue);
            }
            window.setTimeout(() => {
                this.wysiwygUpdating = false;
            });
        },
    },
    computed: {
        beautifiedHTML() {
            return htmlBeautifier(this.pValue);
        },
        inputListeners() {
            const result = Object.assign({}, this.$listeners);
            delete result.input;
            return result;
        },
        self() {
            return {...this};
        },
    },
};
</script>