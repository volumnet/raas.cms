export default function () {
    var h = 0;
    $(this).css('height', '');
    if ($(this).length) {
        $(this).each(function () {
            h = Math.max(h, $(this).outerHeight());
        });
        $(this).css('height', h + 'px');
    }
};