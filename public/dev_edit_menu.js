jQuery(function($) {
    var pid = parseInt($('#pid').val());
    if (!parseInt($('#page_id').val())) {
        $('#inherit').attr('disabled', 'disabled').closest('div.control-group').hide();
    }
    if ((parseInt($('#page_id').val()) > 0) || !pid) {
        $('#url').attr('disabled', 'disabled').closest('div.control-group').hide();
    }
    $('#page_id').live('change', function() {
        $('#url').val($(this).find('option:selected').attr('data-src'));
        if (parseInt($(this).val()) > 0) {
            $('#inherit').removeAttr('disabled').closest('div.control-group').fadeIn();
        } else {
            $('#inherit').attr('disabled', 'disabled').closest('div.control-group').fadeOut();
        }
        if (pid) {
            $('#name').val($(this).find('option:selected').text().replace(/(^\s+)|(\s+$)/gi, ''));
            if (parseInt($(this).val()) > 0) {
                $('#url').attr('disabled', 'disabled').closest('div.control-group').fadeOut();
            } else {
                $('#url').removeAttr('disabled').closest('div.control-group').fadeIn();
            }
        }
    });
})