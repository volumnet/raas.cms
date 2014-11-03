jQuery(function($) {
    $('.tree input:checkbox').on('click', function() {
        var arr = [];
        $('.tree input:checkbox:checked').each(function() {
            var val = parseInt($(this).val());
            if (!isNaN(val)) {
                arr.push(val);
            }
        });
        $('#page_id option').each(function() {
            var val = parseInt($(this).val());
            if (!isNaN(val) && (val > 0)) {
                if ($.inArray(val, arr) == -1) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            }
        });
        var val1 = parseInt($('#page_id').val());
        if (!isNaN(val1) && (val1 > 0)) {
            if ($.inArray(val1, arr) == -1) {
                $('#page_id').val('');
            }
        }
    })
});