import RAASField from './raas-field.vue.js';

/**
 * Поле файла
 */
export default {
    mixins: [RAASField],
    props: {
        /**
         * Подсказка
         * @type {Object}
         */
        placeholder: {
            type: String,
        },
        /**
         * Ограничение по типам файлов
         * @type {Object}
         */
        accept: {
            type: String,
        }
    },
    data: function () {
        return {
            /**
             * Имя файла
             * @type {String}
             */
            fileName: null,
        };
    },
    methods: {
        /**
         * Обработчик смены файла
         * @param  {Event} e Событие
         */
        changeFile: function (e) {
            let self = this;
            let tgt = e.target || window.event.srcElement;
            let files = tgt.files;
            // FileReader support
            if (files && files.length) {
                this.fileName = files[0].name;
                let fileChunks = this.fileName.split('.');
                let ext = (fileChunks.length > 1) ? fileChunks[fileChunks.length - 1] : '';
                let mime = files[0].type;
                if (!this.allowedTypes || 
                    !this.allowedTypes.length ||
                    (this.allowedTypes.indexOf(ext) != -1) ||
                    (this.allowedTypes.indexOf(mime) != -1)
                ) {
                    this.$emit('input', this.fileName)
                } else {
                    this.fileName = '';
                    this.$refs.input.value = '';
                    this.$emit('input', '')
                }
            } else {
                this.fileName = '';
                this.$refs.input.value = '';
                this.$emit('input', '')
            }
        },
        /**
         * Очистить файл
         */
        clearFile: function () {
            this.fileName = '';
            this.$refs.input.value = '';
            this.$emit('input', '')
        },
        /**
         * Выбрать файл
         */
        chooseFile: function () {
            this.$refs.input.click();
        },
    },
    computed: {
        /**
         * Допустимые типы (по атрибуту accept - mime-типы или расширения без точки)
         * @return {String[]|Null} null, если не задано
         */
        allowedTypes: function () {
            if (!this.accept) {
                return null;
            }
            let allowedTypes = this.accept.split(',');
            allowedTypes = allowedTypes.map(x => x.replace('.', '')).filter(x => !!x);
            return allowedTypes;
        },
        /**
         * CSS-класс иконки
         * @return {Object}
         */
        iconCSSClass: function () {
            let result = {};
            if (this.fileName) {
                let rx = /\.(\w+)\s*$/;
                if (rx.test(this.fileName)) {
                    let rxResult = rx.exec(this.fileName);
                    let ext = rxResult[1].toLowerCase();
                    result['raas-field-file__icon_' + ext] = true;
                }
            }
            return result;
        },
    },
};