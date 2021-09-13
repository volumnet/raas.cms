/**
 * Mixin шаблонизатора полей (inputmask)
 */
export default {
    methods: {
        inputMask: function (options = {}) {
            let $objects = $(this.$el).add($('input', this.$el));
            $objects.filter('[pattern]:not([data-inputmask-pattern])')
                .each(function () {
                    var pattern = $(this).attr('pattern');
                    var el = this;
                    $(this)
                        .attr('data-inputmask-pattern', pattern)
                        .inputmask({regex: pattern}, { showMaskOnHover: false });
                });
            $objects
                .filter('[type="tel"]:not([pattern]):not([data-inputmask-pattern])')
                .attr('data-inputmask-pattern', '+9 (999) 999-99-99')
                .inputmask('+9 (999) 999-99-99', { showMaskOnHover: false });
            $objects
                .filter('[data-type="email"]:not([pattern]):not([data-inputmask-pattern])')
                .attr('data-inputmask-pattern', '*{+}@*{+}.*{+}')
                .inputmask('*{+}@*{+}.*{+}', { showMaskOnHover: false });
        },
        applyInputMaskListeners: function () {
            let self = this;
            let $objects = $(this.$el).add($('input', this.$el));
            $objects
                .filter('[data-inputmask-pattern]:not([data-inputmask-events])')
                .on('input', function (e) {
                    self.$emit('input', e.target.value);
                }).on('change', function (e) {
                    self.$emit('change', e.target.value);
                }).on('keydown', function (e) {
                    self.$emit('input', e.target.value);
                })
                .attr('data-inputmask-events', 'true');
        },
    },
};
