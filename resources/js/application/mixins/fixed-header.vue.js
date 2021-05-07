/**
 * Фиксированное меню
 */
export default {
    data: function () {
        return {
            fixedHeaderActive: false,
            lastScrollTop: 0,
        };
    },
    computed: {
        /**
         * Фиксированная ли шапка
         * @return {Boolean}
         */
        fixedHeader: function () {
            return (this.scrollTop > $('.body__header').outerHeight());
        }
    },
    watch: {
        scrollTop: function () {
            if (this.fixedHeader) {
                let delta = this.scrollTop - this.lastScrollTop;
                if (delta > 100) {
                    this.fixedHeaderActive = false;
                    this.lastScrollTop = this.scrollTop; // Перенести в общее нельзя, т.к. выполняется по условию
                } else if (delta < -60) {
                    this.fixedHeaderActive = true;
                    this.lastScrollTop = this.scrollTop; // Перенести в общее нельзя, т.к. выполняется по условию
                }
            } else {
                this.fixedHeaderActive = false;
                this.lastScrollTop = this.scrollTop; // Перенести в общее нельзя, т.к. выполняется по условию
            }
        },
    }
}