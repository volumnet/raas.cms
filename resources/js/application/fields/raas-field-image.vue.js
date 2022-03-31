import RAASFieldFile from './raas-field-file.vue.js';

/**
 * Поле изображения
 */
export default {
    mixins: [RAASFieldFile],
    data: function () {
        return {
            /**
             * Data URL файла
             * @type {String}
             */
            file: null,
        };
    },
    methods: {
        changeFile: function (e) {
            let self = this;
            let tgt = e.target || window.event.srcElement;
            let files = tgt.files;

            // FileReader support
            if (files && 
                files.length && 
                /^image\/(jpeg|png|gif)$/gi.test(files[0].type)
            ) {
                this.fileName = files[0].name;
                if (FileReader) {
                    let fr = new FileReader();
                    fr.onload = () => {
                        this.file = fr.result;
                    }
                    fr.readAsDataURL(files[0]);
                }
            } else {
                this.file = '';
                this.fileName = '';
                this.$refs.input.value = '';
                this.$emit('input', '')
            }
        },
        clearFile: function () {
            this.fileName = '';
            this.file = '';
            this.$emit('input', '')
        },
    },

};