import RAASField from './raas-field.vue.js';

/**
 * Поле цвета
 */
export default {
    mixins: [RAASField],
    mounted: function () {
        this.$el.classList.remove('form-control');
        this.checkColorPicker()
    },
    updated: function () {
        this.$el.classList.remove('form-control');
        this.checkColorPicker();
    },
    methods: {
        /**
         * Проверка/установка выбора цвета
         */
        checkColorPicker: function () {
            var self = this;
            if (!$(this.$refs.picker).attr('data-colorpicker-applied')) {
                $(this.$refs.picker).spectrum({
                    color: this.value,
                    showInput: true,
                    showInitial: true,
                    preferredFormat: 'hex',
                    replacerClassName: 'btn btn-outline-secondary raas-field-color__picker',
                }).on('change', function () {
                    self.pValue = $(this).val();
                    self.$emit('input', self.pValue);
                }).attr('data-colorpicker-applied', 'true');
            } else {
                $(this.$refs.picker).spectrum('set', self.pValue);
            }
        },
    },
    watch: {
        pValue: function () {
            window.setTimeout(() => {
                $(this.$refs.picker).spectrum('set', this.pValue);
            }, 0)
        },
    }
};