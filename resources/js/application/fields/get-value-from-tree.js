/**
 * Получает значение(я) из select'а
 * @param  {jQuery} $obj jQuery-select для выбора
 * @return {Array|String}
 */
export default function ($obj) {
    let values = [];
    $('input:checked', $obj).each(function () {
        values.push($(this).attr('value'));
    });
    if ($obj.attr('data-type') == 'checkbox') {
        return values;
    } else if ($obj.attr('data-type') == 'radio') {
        return values.length ? values[0] : '';
    }
};