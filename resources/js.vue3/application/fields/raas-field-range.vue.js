import RAASFieldNumber from './raas-field-number.vue.js';

/**
 * Поле слайдера
 */
export default {
    mixins: [RAASFieldNumber],
    mounted() {
        this.$el.classList.remove('form-control');
        this.checkSlider()
    },
    updated() {
        this.$el.classList.remove('form-control');
        this.checkSlider();
    },
    methods: {
        checkSlider() {
            let self = this;
            if (!$(this.$refs.slider).attr('data-slider-applied')) {
                $(this.$refs.slider).slider({
                    min: parseFloat(this.min) || 0,
                    max: parseFloat(this.max) || 10,
                    step: parseFloat(this.step) || 1,
                    value: this.pValue,
                    slide: (event, ui) => {
                        self.pValue = ui.value;
                        self.$emit('update:modelValue', ui.value);
                    },
                }).attr('data-slider-applied', 'true');
            } else {
                $(this.$refs.slider).slider('value', parseFloat(this.pValue) || 0);
            }
        },
    },
    watch: {
        pValue() {
            window.setTimeout(() => {
                $(this.$refs.slider).slider('value', this.pValue);
            }, 0)
        },
    }
};