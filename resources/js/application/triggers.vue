<script>
export default {
    data: function () {
        return {
            scrollTop: 0,
            filterActive: false,
        };
    },
    mounted: function () {
        var self = this;
        this.doScroll();
        $(window).on('scroll', this.doScroll.bind(this));
        $(document).on('raas.shop.displayfiltertrigger', function () {
            self.filterActive = true;
        });
    },
    methods: {
        doScroll: function () {
            this.scrollTop = $(window).scrollTop();
        },
        openFilter: function ($event) {
            $(document).trigger('raas.shop.openfilter');
            $event.stopPropagation();
            $event.preventDefault();
        }
    },
    computed: {
        toTopClass: function () {
            let result = {
                'triggers-list__item': true,
                'triggers-list__item_totop': true,
                'triggers-list__item_active': (this.scrollTop > 500),
            };
            return result;
        },
        filterClass: function () {
            let result = {
                'triggers-list__item': true,
                'triggers-list__item_filter': true,
                'triggers-list__item_active': this.filterActive,
            };
            return result;
        },
    }
};
</script>