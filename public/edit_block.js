
jQuery(function($) {
    var checkCaching = function()
    {
        var cacheType = parseInt($('#cache_type').val());
        if (!isNaN(cacheType) && (cacheType > 0)) {
            $('#cache_single_page, #cache_interface_id')
                .removeAttr('disabled')
                .closest('.control-group')
                .show();
        } else {
            $('#cache_single_page, #cache_interface_id')
                .attr('disabled', 'disabled')
                .closest('.control-group')
                .hide();
        }
    }


    window.setTimeout(() => {
        $('#material_type').on('change', function() {
            var url = 'ajax.php?p=cms&action=material_fields&id=' + $(this).val();
            $('[data-role="material-type-field"]').RAAS_getSelect(url, { before: (data) => data.Set });
        })
        $('#cache_type').on('change', checkCaching);
        checkCaching();
    }); // Чтобы отработал Vue
});