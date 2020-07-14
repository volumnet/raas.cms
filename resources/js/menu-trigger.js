export default function (slide) {
    var $thisObj = $(this);
    var $trigger = $('<a class="menu-trigger"></a>');
    $thisObj.prepend($trigger);
    $trigger.on('click', function () {
        if (slide) {
            $('> ul', $thisObj).slideToggle();
        } else {
            $('> ul', $thisObj).toggle();
        }
    });
    return this;
};