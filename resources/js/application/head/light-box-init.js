export default function (processAllImageLinks) {
    var rx = /\.(jpg|jpeg|pjpeg|png|gif)$/i;
    $('a:not([data-rel^=lightcase])').each(function () {
        if (processAllImageLinks) {
            if (rx.test($(this).attr('href'))) {
                $(this).attr('data-lightbox', 'true');
            }
        }
        var g = $(this).attr('data-lightbox-gallery');
        if (g || $(this).attr('data-lightbox')) {
            $(this).attr('data-rel', 'lightcase' + (g ? ':' + g : ''));
            $(this).removeAttr('data-lightbox-gallery');
            $(this).removeAttr('data-lightbox');
        }
    });
    $('a[data-rel^=lightcase]').lightcase({ swipe: true, transition: 'scrollHorizontal' });
};