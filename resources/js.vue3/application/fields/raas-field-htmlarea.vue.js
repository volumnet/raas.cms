import RAASField from './raas-field.vue.js';

/**
 * Поле HTML
 */
export default {
    mixins: [RAASField],
    data() {
        return {
            
        };
    },
    mounted() {
        this.checkCKEditor();
    },
    updated() {
        this.checkCKEditor();
    },
    methods: {
        /**
         * Проверка/установка CKEditor
         */
        checkCKEditor() {
            if (!$(this.$el).attr('data-ckeditor-applied')) {
                let ck = $(this.$el).ckeditor(this.ckEditorConfig);
                ck.editor.on('change', () => {
                    this.$emit('update:modelValue', $(this.$el).val());
                });
                $(this.$el).attr('data-ckeditor-applied', 'true');
            }
        },
    },
    computed: {
        /**
         * Конфигурация CKEditor
         * @return {Object}
         */
        ckEditorConfig() {
            return {
                autoParagraph: false,
                language: 'ru',
                height: 320,
                skin: 'moono',

                toolbar: [
                    { 
                        name: 'basicstyles', 
                        items: [
                            'Bold', 
                            'Italic', 
                            'Underline', 
                            'Strike', 
                            '-', 
                            'RemoveFormat',
                        ],
                    },
                    { 
                        name: 'insert', 
                    },
                ],
                removeButtons: '',
                allowedContent: 'p; br; img[src, alt]; strong; em; b; i; u; s',
            };
        },
    },
    watch: {
        modelValue() {
            if (window.CKEDITOR) {
                for (let instance of Object.values(window.CKEDITOR.instances)) {
                    if ((instance.element.$ == this.$el) && (this.modelValue != instance.getData())) {
                        instance.setData(this.modelValue);
                    }
                }
            }
        }
    }
};