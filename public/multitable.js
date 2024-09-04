jQuery(document).ready(function($) {
    $.fn.RAAS_MultiTable = function() {
        const thisObj = this;
        const $thisObj = $(this);
        const $all = $('[data-role="checkbox-all"]', thisObj);
        const $checkbox = $('[data-role="checkbox-row"]', thisObj);
        const $menu = $('tfoot .btn-group, tfoot .all-context-menu');
        const $menuItems = $('.dropdown-menu li a, .menu-dropdown__link', $menu);
        const idN = $thisObj.attr('data-idn') || 'id';

        const check = function () {
            if ($all.is(':checked')) {
                $checkbox.each(function() {
                    if (!$(this).is(':checked')) {
                        $all.removeAttr('checked');
                        return false;
                    }
                })
            }
            let ids = '';
            if ($all.is(':checked') && $all.val() && ($all.val() != 'ids')) {
                ids += '&' + idN + '=' + $all.val();
            } else {
                $checkbox.filter(':checked').each(function() {
                    ids += '&' + idN + '[]=' + $(this).val();
                });
            }
            $menuItems.each(function() {
                $(this).attr('href', $(this).attr('data-href') + ids);
            })
            if (ids) {
                $menu.show();
            } else {
                $menu.hide();
            }
        };

        const init = function() {
            $menuItems.each(function() {
                $(this).attr('data-href', $(this).attr('href'));
            })
        };

        const checkAccurate = function(e) {
            if ($(this).is(':checked')) {
                $(this).prop('checked', false);
            } else {
                $(this).prop('checked', true);
            }
            e.stopPropagation();
            e.preventDefault();
            check();
            return false;
        };

        $all.on('click', function() {
            if ($(this).is(':checked')) {
                $checkbox.prop('checked', true);
            } else {
                $checkbox.prop('checked', false);
            }
            check();
        });
        $checkbox.on('click', check);
        $all.on('contextmenu', checkAccurate);
        $checkbox.on('contextmenu', checkAccurate);
        init();
        check();
    };

    window.setTimeout(() => {
        $('[data-role="multitable"]').each(function() {
            $(this).RAAS_MultiTable();
        });
    }, 0); // Чтобы успел отработать Vue
});