import RAASField from './raas-field.vue.js';

/**
 * Поле HTML
 */
export default {
    mixins: [RAASField],
    data: function () {
        return {
            
        };
    },
    mounted: function () {
        this.checkCKEditor();
    },
    updated: function () {
        this.checkCKEditor();
    },
    methods: {
        /**
         * Проверка/установка CKEditor
         */
        checkCKEditor: function () {
            if (!$(this.$el).attr('data-ckeditor-applied')) {
                let ck = $(this.$el).ckeditor(this.ckEditorConfig);
                ck.editor.on('change', () => {
                    this.$emit('input', $(this.$el).val())
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
        ckEditorConfig: function () {
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
        value() {
            for (let instance of Object.values(CKEDITOR.instances)) {
                if ((instance.element.$ == this.$el) && (this.value != instance.getData())) {
                    instance.setData(this.value);
                }
            }
        }
    }
};