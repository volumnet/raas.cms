/**
 * Фиксированное меню
 */
export default {
    data() {
        return {
            fixedHeaderActive: false,
        };
    },
    computed: {
        /**
         * Фиксированная ли шапка
         * @return {Boolean}
         */
        fixedHeader() {
            return (this.scrollTop > Math.max($('.body__header-outer').outerHeight(), $('.body__header').outerHeight()));
        },
    },
    watch: {
        scrollTop() {
            if (this.fixedHeader) {
                if (this.scrollDelta > 100) {
                    this.fixedHeaderActive = false;
                } else if (this.scrollDelta < -60) {
                    this.fixedHeaderActive = true;
                }
            } else {
                this.fixedHeaderActive = false;
            }
        },
    }
}