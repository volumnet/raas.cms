import RAASField from './raas-field.vue.js';

/**
 * Поле цвета
 */
export default {
    mixins: [RAASField],
    mounted() {
        this.$el.classList.remove('form-control');
        this.checkColorPicker()
    },
    updated() {
        this.$el.classList.remove('form-control');
        this.checkColorPicker();
    },
    methods: {
        /**
         * Проверка/установка выбора цвета
         */
        checkColorPicker() {
            var self = this;
            if (!$(this.$refs.picker).attr('data-colorpicker-applied')) {
                $(this.$refs.picker).spectrum({
                    color: this.modelValue,
                    showInput: true,
                    showInitial: true,
                    preferredFormat: 'hex',
                    replacerClassName: 'btn btn-outline-secondary raas-field-color__picker',
                }).on('change', function () {
                    self.$emit('update:modelValue', self.pValue = $(this).val());
                }).attr('data-colorpicker-applied', 'true');
            } else {
                $(this.$refs.picker).spectrum('set', self.pValue);
            }
        },
    },
    watch: {
        pValue() {
            window.setTimeout(() => {
                $(this.$refs.picker).spectrum('set', this.pValue);
            }, 0)
        },
    }
};