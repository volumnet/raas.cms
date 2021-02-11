/**
 * Получает значение(я) из select'а
 * @param  {jQuery} $obj jQuery-select для выбора
 * @return {Array|String}
 */
export default function ($obj) {
    let values = [];
    $('option:selected', $obj).each(function () {
        values.push($(this).attr('value'));
    });
    if ($obj.attr('multiple') || $obj.attr('data-multiple')) {
        return values;
    } else {
        return values[0] || '';
    }
};