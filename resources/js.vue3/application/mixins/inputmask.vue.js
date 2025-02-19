/**
 * Mixin шаблонизатора полей (inputmask)
 */
export default {
    methods: {
        inputMask(options = {}) {
            let config = Object.assign({
                showMaskOnFocus: false, 
                showMaskOnHover: true,
            }, options);
            let $objects = $(this.$el).add($('input', this.$el));
            $objects.filter('[pattern]:not([data-inputmask-pattern]):not([data-no-inputmask])')
                .each(function () {
                    var pattern = $(this).attr('pattern');
                    $(this)
                        .attr('data-inputmask-pattern', pattern)
                        .attr('autocomplete', 'off')
                        // @todo Пока отключаем placeholder, т.к. глючит с InputMask
                        .inputmask(Object.assign({regex: pattern}, config));
                });
            $objects
                .filter('[type="tel"]:not([pattern]):not([data-inputmask-pattern]):not([data-no-inputmask])')
                .attr('data-inputmask-pattern', '+9 (999) 999-99-99')
                .attr('autocomplete', 'off')
                // @todo Пока отключаем placeholder, т.к. глючит с InputMask
                .inputmask('+9 (999) 999-99-99', config);
            $objects
                .filter('[data-type="email"]:not([pattern]):not([data-inputmask-pattern]):not([data-no-inputmask])')
                .attr('data-inputmask-pattern', '*{+}@*{+}.*{+}')
                .attr('autocomplete', 'off')
                // @todo Пока отключаем placeholder, т.к. глючит с InputMask
                .inputmask('*{+}@*{+}.*{+}', config);
        },
        applyInputMaskListeners() {
            let $objects = $(this.$el).add($('input', this.$el));
            // 2025-02-03, AVS: в десктопе inputmask заглушает стандартную прослушку input
            $objects
                .filter('[data-inputmask-pattern]:not([data-inputmask-events])')
                .on('input', (e) => {
                    this.$emit('update:modelValue', this.pValue = e.target.value);
                }).on('change', (e) => {
                    this.$emit('update:modelValue', this.pValue = e.target.value);
                }).on('keydown', (e) => {
                    this.$emit('update:modelValue', this.pValue = e.target.value);
                })
                .attr('data-inputmask-events', 'true');
        },
    },
};
