
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

    $('#material_type').change(function() {
        var url = 'ajax.php?p=cms&action=material_fields&id=' + $(this).val();
        $('.jsMaterialTypeField').RAAS_getSelect(
            url, 
            { 
                before: function (data) { 
                    return data.Set; 
                } 
            }
        );
    })

    window.setTimeout(() => {
        $('#cache_type').on('change', checkCaching);
        checkCaching();
    }, 0); // Чтобы отработал Vue
});