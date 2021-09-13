/**
 * Компонент неполностью раскрытого блока
 */
export default {
    mounted: function () {
        $(window).on('load', this.checkOverflow.bind(this)) // onload для того чтобы CSS-стили успели примениться
            .on('resize', this.checkOverflow.bind(this));
    },
    methods: {
        /**
         * Определяет, превышает ли внутренний размер размер блока
         * и выдает сообщение
         */
        checkOverflow: function () {
            let selfHeight = $(this.$el).outerHeight();
            let innerHeight = $(this.$refs.inner).outerHeight();
            let selfWidth = $(this.$el).outerWidth();
            let innerWidth = $(this.$refs.inner).outerWidth();
            let result = {
                x: (innerWidth > selfWidth),
                y: (innerHeight > selfHeight),
            };
            this.$emit('overflows', result);
        },
    },
    computed: {
        /**
         * Распаковка текущего экземпляра для слота
         * @return {Object}
         */
        self: function () { 
            return { ...this };
        },
    },
};