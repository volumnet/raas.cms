/**
 * Компонент неполностью раскрытого блока
 */
export default {
    mounted: function () {
        $(window).on('load', this.checkOverflow.bind(this)) // onload для того чтобы CSS-стили успели примениться
            .on('resize', this.checkOverflow.bind(this));
    },
    data() {
        return {
            innerHeight: 0,
            innerWidth: 0,
            outerHeight: 0,
            outerWidth: 0,
            active: false,
        };
    },
    methods: {
        /**
         * Определяет, превышает ли внутренний размер размер блока
         * и выдает сообщение
         */
        checkOverflow() {
            let selfHeight = $(this.$el).outerHeight();
            this.outerHeight = selfHeight;
            let innerHeight = $(this.$refs.inner).outerHeight();
            this.innerHeight = innerHeight;
            let selfWidth = $(this.$el).outerWidth();
            this.outerWidth = selfWidth;
            let innerWidth = $(this.$refs.inner).outerWidth();
            this.innerWidth = innerWidth;
            let result = {
                x: (innerWidth > selfWidth),
                y: (innerHeight > selfHeight),
            };
            this.$emit('overflows', result);
        },
    },
    computed: {
        /**
         * Превышает по X
         * @return {Boolean}
         */
        overflowsX() {
            return this.innerWidth > this.outerWidth;
        },
        /**
         * Превышает по Y
         * @return {Boolean}
         */
        overflowsY() {
            return this.innerHeight > this.outerHeight;
        },
        /**
         * Распаковка текущего экземпляра для слота
         * @return {Object}
         */
        self() { 
            return { ...this };
        },
    },
};