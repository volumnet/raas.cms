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
                    $(this)
                        .attr('data-inputmask-pattern', pattern)
                        // @todo Пока отключаем placeholder, т.к. глючит с InputMask
                        .inputmask({regex: pattern, showMaskOnFocus: false, showMaskOnHover: true/*, placeholder: ''*/ }/*, { showMaskOnHover: true }*/);
                });
            $objects
                .filter('[type="tel"]:not([pattern]):not([data-inputmask-pattern])')
                .attr('data-inputmask-pattern', '+9 (999) 999-99-99')
                // @todo Пока отключаем placeholder, т.к. глючит с InputMask
                .inputmask('+9 (999) 999-99-99', { showMaskOnFocus: false, showMaskOnHover: true/*, placeholder: ''*/ });
            $objects
                .filter('[data-type="email"]:not([pattern]):not([data-inputmask-pattern])')
                .attr('data-inputmask-pattern', '*{+}@*{+}.*{+}')
                // @todo Пока отключаем placeholder, т.к. глючит с InputMask
                .inputmask('*{+}@*{+}.*{+}', { showMaskOnFocus: false, showMaskOnHover: true/*, placeholder: ''*/ });
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
