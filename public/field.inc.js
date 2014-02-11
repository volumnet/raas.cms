jQuery(function($) {
    $('.well:has(input:file) a.close:not([data-role="raas-repo-del"])').click(function() {
        var $w = $(this).closest('.well');
        $('[data-role="file-link"]', $w).remove();
        $('input:text, input:hidden, textarea', $w).val('');
        $('input:checkbox', $w).attr('checked', 'checked');
        $(this).remove();
        return false;
    });
    $('.well:has(input:file) input:checkbox:visible').click(function() {
        var checked = $(this).attr('checked');
        var $w = $(this).closest('.well');
        if (checked) {
            $('input:checkbox[data-role="checkbox-shadow"]', $w).removeAttr('checked');
        } else {
            $('input:checkbox[data-role="checkbox-shadow"]', $w).attr('checked', 'checked');
        }
    })
});