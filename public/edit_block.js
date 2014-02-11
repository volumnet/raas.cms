jQuery(function($) {
    if (($('#widget_id').length > 0) && ($('.control-group:has(#widget)').length > 0)) {
        if (parseInt($('#widget_id').val()) > 0) {
            $('#widget').attr('disabled', 'disabled');
            $('.control-group:has(#widget)').hide();
        }
        $('#widget_id').change(function() {
            if (parseInt($(this).val()) > 0) {
                $('#widget').attr('disabled', 'disabled');
                $('.control-group:has(#widget)').fadeOut();
            } else {
                $('#widget').removeAttr('disabled', 'disabled');
                $('.control-group:has(#widget)').fadeIn();
            }
        });
    }
    
    var checkInterface = function(first) {
        $obj = $('.control-group:has(#interface)');
        $txt = $('#interface');
        if (parseInt($('select#interface_id').val()) > 0) {
            first === true ? $obj.hide() : $obj.fadeOut();
            $txt.attr('disabled', 'disabled');
        } else {
            $txt.removeAttr('disabled', 'disabled');
            first === true ? $obj.show() : $obj.fadeIn();
        }
    }
    
    checkInterface(true);
    // if (!$('form[data-block-type="Block_PHP"]').length && !$('form[data-block-type="Block_HTML"]').length && !/&id=/i.test(document.location.href)) {
    //     $('.control-group:has(#widget)').hide();
    // }
    $('select#interface_id').change(checkInterface);
    $('input#interface_id:checkbox').click(checkInterface);
    $('#material_type').change(function() {
        $('.jsMaterialTypeField').RAAS_getSelect('ajax.php?p=cms&action=material_fields&id=' + $(this).val(), {before: function(data) { return data.Set; }});
    })
});