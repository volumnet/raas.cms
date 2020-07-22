<script>
export default {
    data: function () {
        return {
            active: false,
        };
    },
    mounted: function () {
        $('.logo:eq(0)').clone().appendTo('.menu-mobile__logo');
        $('.contacts-top-phones-list__item:eq(0) a')
            .clone()
            .addClass('menu-mobile__link menu-mobile__link_main menu-mobile__link_phone')
            .appendTo('.menu-mobile__item_phone');
                
        $('.triggers-item_menu, .menu-trigger').on('click', function () {
            $('.menu-mobile__list_main').toggleClass('menu-mobile__list_active');
            return false;
        });
        $('.menu-mobile__item:has(> .menu-mobile__list) > .menu-mobile__link').on('click', function () {
            $(this).closest('.menu-mobile__item').find('> .menu-mobile__list').addClass('menu-mobile__list_active');
            return false;
        })
        $('.menu-mobile__close-link').on('click', function(e) { 
            $('.menu-mobile__list').removeClass('menu-mobile__list_active');
            return false;
        });
        $('.menu-mobile__back-link').on('click', function(e) { 
            $(this).closest('.menu-mobile__list').removeClass('menu-mobile__list_active');
            return false;
        });
        $('.body').on('click', function(e) { 
            $('.menu-mobile__list').removeClass('menu-mobile__list_active');
        });
        $('.menu-mobile').on('click', function(e) { 
            e.stopPropagation();
        });
        if ($(window).outerWidth() < 992) {
            $('.menu-mobile__list').on('movestart', function(e) { 
                if (e.distX <= -6) {
                    $(this).removeClass('menu-mobile__list_active');
                    return false;
                }
                e.preventDefault()
                return false;
            });
        }
    },
}
</script>