jQuery(document).ready(function($) {
    window.setTimeout(() => {
        $('[data-role="raas-repo-block"] input:checkbox').each(function () {
            var $clone = $(this).clone(true);
            $clone.val(0);
            $clone.attr('data-clone', 'true');
            $clone.css('display', 'none');
            // $clone.css('opacity', '.5');
            $clone.prop('checked', !$(this).prop('checked'));
            $clone.insertBefore(this);
            
        });
        $('[data-role="raas-repo-block"]').on('click', 'input:checkbox', function () {
            var $clone = $(this).prev('[data-clone]');
            $clone.prop('checked', !$(this).prop('checked'));
        });
    }, 0); // Чтобы успел отработать Vue
});