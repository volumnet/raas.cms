/**
 * Оборачивает элементы в компонент raas-repo
 */
export default function (horizontal) {
    $(this).wrap('<raas-repo></raas-repo>')
    let $repo = $(this).closest('raas-repo');
    let $selectedOptions;
    if ($(this).attr('required')) {
        $repo.attr('required', 'required');
    }
    if (horizontal) {
        $repo.attr(':horizontal', 'true');
    }
    if ($(this).attr('data-value')) {
        $repo.attr(':value', $(this).attr('data-value'));
    } else if (($selectedOptions = $('option:selected', this)).length) {
        let values = [];
        $selectedOptions.each(function () {
            values.push($(this).attr('value') || '');
        });
        $repo.attr(':value', JSON.stringify(values));
    } else if ($(this).attr('value')) {
        $repo.attr(':value', JSON.stringify([$(this).attr('value')]));
    } else {
        $repo.attr(':value', '[]');
    }
    $(this)
        .removeAttr('data-value')
        .removeAttr('multiple')
        .removeAttr('data-multiple')
        .val('')
        .attr(':value', 'slotProps.value')
        .replaceWith('<template v-slot="slotProps">' + this[0].outerHTML + '</template>');
    $('option', this).removeAttr('selected');
};