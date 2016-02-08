jQuery(document).ready(function($) {
    $.fn.RAAS_MultiTable = function() {
        var thisObj = this;
        var $thisObj = $(this);
        var $all = $('[data-role="checkbox-all"]', thisObj);
        var $checkbox = $('[data-role="checkbox-row"]', thisObj);
        var $menu = $('tfoot .btn-group');
        var $menuItems = $('.dropdown-menu li a', $menu);

        var check = function () {
            var ids = '';
            if ($all.is(':checked')) {
                ids += '&id=' + $all.val();
            } else {
                $checkbox.filter(':checked').each(function() {
                    ids += '&id[]=' + $(this).val();
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

        var init = function() {
            $menuItems.each(function() {
                $(this).attr('data-href', $(this).attr('href'));
            })
        };

        var checkAccurate = function(e) {
            if ($(this).is(':checked')) {
                $(this).removeAttr('checked');
            } else {
                $(this).attr('checked', 'checked');
            }
            e.stopPropagation();
            e.preventDefault();
            check();
            return false;
        };

        $all.on('click', function() {
            if ($(this).is(':checked')) {
                $checkbox.attr('checked', 'checked');
            } else {
                $checkbox.removeAttr('checked');
            }
            check();
        });
        $checkbox.on('click', check);
        $all.on('contextmenu', checkAccurate);
        $checkbox.on('contextmenu', checkAccurate);
        init();
        check();
    };

    $('[data-role="multitable"]').each(function() {
        $(this).RAAS_MultiTable();
    })
});