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
                    self.$emit('input', $(this).val());
                }).attr('data-colorpicker-applied', 'true');
            } else {
                $(this.$refs.picker).spectrum('set', self.value);
            }
        },
    },
};