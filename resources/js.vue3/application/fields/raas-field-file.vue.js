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
    data() {
        return {
            /**
             * Имя файла
             * @type {String}
             */
            fileName: null,
            /**
             * Перетаскивание над полем
             * @type {Boolean}
             */
            dragOver: false,
        };
    },
    methods: {
        /**
         * Возвращает dataURL файла
         * @requires FileReader
         * @param {File} file Файл для обработки
         * @return {String[]}
         */
        getFileDataURL(file) {
            return new Promise((resolve, reject) => {
                var fr = new FileReader();  
                fr.onload = () => {
                    resolve(fr.result)
                };
                fr.onerror = reject;
                fr.readAsDataURL(file);
            });
        },
        /**
         * Получает расширение для файла в нижнем регистре
         * @param  {String} filename Имя (или путь) файла
         * @return {String}
         */
        getExtension(filename) {
            const fileChunks = filename.split('.');
            const ext = ((fileChunks.length > 1) ? fileChunks[fileChunks.length - 1] : '').toLowerCase();
            return ext;
        },
        /**
         * Проверяет, допустим ли данный файл
         * @param  {File} file Файл для проверки
         * @return {Boolean}
         */
        checkIfFileIsAllowed(file) {
            const ext = this.getExtension(file.name);
            const mime = file.type.toLowerCase();
            if (!this.allowedTypes || 
                !this.allowedTypes.length ||
                (this.allowedTypes.indexOf(ext) != -1) ||
                (this.allowedTypes.indexOf(mime) != -1)
            ) {
                return true;
            } else {
                return false;
            }
        },
        /**
         * Обработчик смены файла
         * @param  {Event} e Событие
         */
        changeFile(e) {
            let self = this;
            let tgt = e.target || window.event.srcElement;
            let files = tgt.files;
            // FileReader support
            if (files && files.length) {
                if (this.checkIfFileIsAllowed(files[0])) {
                    this.fileName = files[0].name;
                    this.$emit('update:modelValue', this.fileName)
                } else {
                    this.fileName = '';
                    this.$refs.input.value = '';
                    this.$emit('update:modelValue', '')
                }
            } else {
                this.fileName = '';
                this.$refs.input.value = '';
                this.$emit('update:modelValue', '')
            }
        },
        /**
         * Очистить файл
         */
        clearFile() {
            this.fileName = '';
            this.$refs.input.value = '';
            this.$emit('update:modelValue', '')
        },
        /**
         * Выбрать файл
         */
        chooseFile() {
            this.$refs.input.click();
        },
        /**
         * Обработка помещения файлов перетаскиванием
         * @param Event e Оригинальное событие
         */
        handleDrop(e) {
            // Требуется переопределение
        },
    },
    computed: {
        /**
         * Допустимые типы (по атрибуту accept - mime-типы или расширения без точки)
         * @return {String[]|Null} null, если не задано
         */
        allowedTypes() {
            if (!this.accept) {
                return null;
            }
            let allowedTypes = this.accept.split(',');
            allowedTypes = allowedTypes.map(x => x.replace('.', '')).filter(x => !!x).map(x => x.toLowerCase());
            return allowedTypes;
        },
        /**
         * CSS-класс иконки
         * @return {Object}
         */
        iconCSSClass() {
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