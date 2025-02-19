import RAASFieldFile from './raas-field-file.vue.js';

/**
 * Поле изображения
 */
export default {
    mixins: [RAASFieldFile],
    data() {
        return {
            /**
             * Data URL файла
             * @type {String}
             */
            file: null,
        };
    },
    methods: {
        changeFile(e) {
            const tgt = e.target || window.event.srcElement;
            const files = tgt.files;
            this.handleFilesChange(files)
        },
        /**
         * Обрабатывает входящий набор файлов
         * @param {File[]} files Файлы
         */
        handleFilesChange(files) {
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
        clearFile() {
            this.fileName = '';
            this.file = '';
            this.$emit('input', '')
        },
        handleDrop(e) {
            const files = e.dataTransfer.files;
            let filesArr = Array.from(files);
            filesArr = filesArr.filter(file => /^image\/(jpeg|png|gif)$/gi.test(files[0].type));
            if (filesArr.length) {
                const dataTransfer = new DataTransfer();
                if (this.multiple) {
                    for (let file of filesArr) {
                        dataTransfer.items.add(file);
                    }
                } else {
                    dataTransfer.items.add(filesArr[0]);
                }
                this.$refs.input.files = dataTransfer.files;
                this.handleFilesChange(filesArr);
            } else {
                clearFile();
            }
        },
    },

};