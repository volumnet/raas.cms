export default function () {
    var $thisObj = $(this);
    var fSuccess = function (data) {
        $('button[type="submit"]', $thisObj).removeAttr('disabled');
        if (data.success) {
            $('[data-role="notifications"], [data-role="notifications"] .alert-success', $thisObj).show();
            $('[data-role="feedback-form"], [data-role="notifications"] .alert-danger', $thisObj).hide();
            $thisObj.trigger('RAAS.AJAXForm.success', data);
        } else if (data.localError) {
            $('[data-role="notifications"] .alert-danger ul', $thisObj).empty();
            for (key in data.localError) {
                var val = data.localError[key];
                $('[data-role="notifications"] .alert-danger ul', $thisObj).append('<li>' + val + '</li>');
                $('[name="' + key + '"]', $thisObj).closest('.form-group').addClass('has-error');
            }
            $('[data-role="notifications"] .alert-success', $thisObj).hide();
            $('[data-role="notifications"], [data-role="feedback-form"], [data-role="notifications"] .alert-danger', $thisObj).show();
            $thisObj.trigger('RAAS.AJAXForm.error', data);
        }
    };

    $(this).submit(function () {
        $(this).ajaxForm();
        $thisObj.trigger('RAAS.AJAXForm.submit');
        $('button[type="submit"]', $thisObj).attr('disabled', 'disabled');
        $(this).ajaxSubmit({ dataType: 'json', 'url': $(this).attr('action'), success: fSuccess, data: { AJAX: 1 } });
        return false;
    })
    
    $('input, select, textarea').change(function () {
        $(this).closest('.form-group').removeClass('has-error');
    })
    return $thisObj;
};