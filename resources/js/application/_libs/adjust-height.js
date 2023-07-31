/**
 * Однократное выравнивание элементов по высоте
 * @param {jQuery} $obj Объект jQuery для выравнивания
 */
let adjustOnce = function ($obj) {
    let h = 0;
    $obj.css('height', '');
    if ($obj.length) {
        $obj.each(function () {
            h = Math.max(h, $(this).outerHeight());
        });
        $obj.css('height', h + 'px');
    }
};

/**
 * Выравнивание элементов по высоте
 * @param {Boolean} watch Следить за высотой при ресайзе
 */
export default function (watch = false) {
    adjustOnce($(this));
    if (watch) {
        $(window).on('resize', () => {
            adjustOnce($(this));
        });
    }
};