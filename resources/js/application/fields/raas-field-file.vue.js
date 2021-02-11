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