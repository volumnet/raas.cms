jQuery(document).ready(function($) {
    var rx = /\S+/gi;
    var checkLength = function($obj, recommendedLength, strictLength, wordsLength) {
        var text = $.trim($obj.val());
        var temp = text.match(rx);
        var words = 0;
        if (temp) {
            words = temp.length;
        }
        if ((strictLength && (text.length > strictLength)) || (wordsLength && (words > wordsLength))) {
            $obj.closest('.control-group').removeClass('warning').addClass('error');
        } else if (recommendedLength && (text.length > recommendedLength)) {
            $obj.closest('.control-group').removeClass('error').addClass('warning');
        } else {
            $obj.closest('.control-group').removeClass('warning').removeClass('error');
        }
    };
    $('[data-recommended-limit], [data-strict-limit]').each(function() {
        var $obj = $(this);
        var strictLength = parseInt($obj.attr('data-strict-limit'));
        if (isNaN(strictLength)) {
            strictLength = 0;
        }
        var recommendedLength = parseInt($obj.attr('data-recommended-limit'));
        if (isNaN(recommendedLength)) {
            recommendedLength = 0;
        }
        var wordsLength = parseInt($obj.attr('data-words-limit'));
        if (isNaN(wordsLength)) {
            wordsLength = 0;
        }
        var f = function() { 
            checkLength($obj, recommendedLength, strictLength, wordsLength); 
        };

        $(this).on('keydown', f);
        $(this).on('keyup', f);
        $(this).on('blur', f);
        f();
    });
});