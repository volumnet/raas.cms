jQuery(function($) {
    window.setTimeout(() => {
        $('#antispam').change(function() {
            if (['captcha', 'hidden', 'smart'].indexOf($(this).val()) != -1) {
                $('#antispam_field_name').removeAttr('disabled');
                if ($('#antispam').val() == 'captcha') {
                    $('#antispam_field_name').val('captcha');
                } else if (($('#antispam').val() == 'hidden') || ($('#antispam').val() == 'smart')) {
                    $('#antispam_field_name').val('_question');
                }
            } else {
                $('#antispam_field_name').attr('disabled', 'disabled');
            }
        });
    }, 0); // Чтобы успел отработать Vue
});