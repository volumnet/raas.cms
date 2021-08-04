jQuery(function($) {
    if ($('.code#description').length) {
        if (parseInt($('select#interface_id').val()) > 0) {
            $('.control-group:has(.code#description)').hide();
        }
        $('#interface_id').change(function() {
            if (parseInt($(this).val()) > 0) {
                $('.control-group:has(.code#description)').fadeOut();
            } else {
                $('.control-group:has(.code#description)').fadeIn();
            }
        })
    }

    $('#antispam').change(function() {
        if (['captcha', 'hidden'].indexOf($(this).val()) != -1) {
            $('#antispam_field_name').removeAttr('disabled');
            if ($('#antispam').val() == 'captcha') {
                $('#antispam_field_name').val('captcha');
            } else if ($('#antispam').val() == 'hidden') {
                $('#antispam_field_name').val('_name');
            }
        } else {
            $('#antispam_field_name').attr('disabled', 'disabled');
        }
    });
});