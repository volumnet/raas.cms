import RAASField from './raas-field.vue.js';

/**
 * Поле числа
 */
export default {
    props: {
        /**
         * Минимальное значение
         * @type {Number}
         */
        min: {
            default: 1,
        },
        /**
         * Максимальное значение
         * @type {Number}
         */
        max: {
            default: 5,
        },
        /**
         * Шаг значения
         * @type {Number}
         */
        step: {
            default: 1,
        },
    },
    mixins: [RAASField],
    data() {
        return {
            hoveredValue: 0,
        };
    },
    mounted() {
        $(this.$el).on('keydown', (e) => {
            let newValue = null;
            switch (e.keyCode) {
                case 37:
                case 40:
                    newValue = Math.max(0, (parseInt(this.pValue) || parseInt(this.modelValue) || 0) - 1);
                    break;
                case 38:
                case 39:
                    newValue = Math.min(this.max, (parseInt(this.pValue) || parseInt(this.modelValue) || 0) + 1);
                    break;
            }
            if (newValue) {
                this.$emit('update:modelValue', newValue);
                e.stopPropagation();
                e.preventDefault();
            }
        });
    },
    methods: {
        /**
         * CSS-класс звезды рейтинга
         * @param {Number} starNum Номер звезды (1-5)
         * @param {Array} starClasses <pre><code>[
         *     String Класс пустой звезды,
         *     String Класс половины звезды,
         *     String Класс полной звезды,
         * ]</code></pre> Классы, присваиваемые звезде
         * @return {String}
         */
        starClass(starNum, starClasses) {
            let result = {}
            let halfStar = Math.min(
                2, 
                Math.max(0, parseInt((this.currentValue - starNum + 1) * 2))
            );
            result[starClasses[halfStar]] = true;
            return result;
        },
    },
    computed: {
        /**
         * Текущее значение (подсвеченное или выбранное)
         * @return {Number}
         */
        currentValue() {
            return this.hoveredValue || parseInt(this.pValue) || parseInt(this.modelValue) || 0;
        },
    }
};