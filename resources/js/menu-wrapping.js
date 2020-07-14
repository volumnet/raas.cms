export default function (slide) {
    $('a', this).on('click', function () {
        var $li = $(this).closest('li');
        var $otherLis = $li.siblings();
        var $ul = $li.find('> ul');
        if (($ul.length == 0) || $ul.is(':visible')) {
            return true;
        } else {
            $otherLis.removeClass('focused').find('> ul').hide();
            $li.addClass('focused');
            if (slide) {
                $ul.slideDown();
            } else {
                $ul.show();
            }
            return false;
        }
    });
    return this;
};