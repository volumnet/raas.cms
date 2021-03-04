/**
 * Однократное выравнивание элементов по высоте
 */
let adjustOnce = function () {
    let h = 0;
    $(this).css('height', '');
    if ($(this).length) {
        $(this).each(function () {
            h = Math.max(h, $(this).outerHeight());
        });
        $(this).css('height', h + 'px');
    }
};

/**
 * Выравнивание элементов по высоте
 * @param {Boolean} watch Следить за высотой при ресайзе
 */
export default function (watch = false) {
    $(this).adjustOnce();
    if (watch) {
        $(window).on('resize', () => {
            $(this).adjustOnce();
        });
    }
};