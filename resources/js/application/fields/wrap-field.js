import getSourceFromSelect from './get-source-from-select.js';
import getSourceFromTree from './get-source-from-tree.js';
import getValueFromSelect from './get-value-from-select.js';
import getValueFromTree from './get-value-from-tree.js';

/**
 * Оборачивает RAAS-поля в компонент raas-field
 * @return {[type]} [description]
 */
export default function () {
    let source, value;
    if (/video(s)?/gi.test($(this).attr('name'))) {
        $(this).attr('data-type', 'video');
    }
    if (($(this).attr('data-type') == 'select') || $(this).is('select')) {
        source = getSourceFromSelect($(this));
    } else if ($(this).attr('data-role') == 'checkbox-tree') {
        source = getSourceFromTree($(this));
        if ($(this).attr('data-type') == 'checkbox') {
            $(this).attr('multiple', 'multiple');
        } else if ($(this).attr('data-type') == 'radio') {
            $(this).removeAttr('multiple');
        }
        $(this).removeAttr('data-multiple');
        $(this).attr('name', $('input[name]:eq(0)', this).attr('name'));
    } else if (($(this).attr('data-type') == 'checkbox')) {
        $(this).attr('defval', $(this).attr('value'));
    }
    if ($(this).attr('data-multiple')) {
        $(this).attr('multiple', 'multiple');
        $(this).removeAttr('data-multiple');
    }
    if ($(this).attr('data-placeholder')) {
        $(this).attr('placeholder', $(this).attr('data-placeholder'));
        $(this).removeAttr('data-placeholder');
    }
    if (!$(this).attr('data-value') && !$(this).attr('value')) {
        if (($(this).attr('data-type') == 'select') || $(this).is('select')) {
            value = getValueFromSelect($(this));
        } else if ($(this).attr('data-role') == 'checkbox-tree') {
            value = getValueFromTree($(this));
        } else if ($(this).is('textarea')) {
            value = $(this).val();
        } else if ($(this).attr('value')) {
            value = $(this).attr('value');
        }
        if (value) {
            if (value instanceof Array) {
                $(this).attr('data-value', JSON.stringify(value));
                $(this).attr(':value', JSON.stringify(value));
            } else {
                $(this).attr('value', value);
            }
        }
    }
    if (source) {
        $(this).attr(':source', JSON.stringify(source))
    }
    if ($(this).attr('data-type')) {
        $(this).attr('type', $(this).attr('data-type'))
        $(this).removeAttr('data-type');
    }
    if ($(this).attr('data-role') == 'checkbox-tree') {
        $(this).removeAttr('data-role').removeClass('checkbox-tree');
    }
    $(this).html('');
    let html = $(this)[0].outerHTML;
    let tagName = $(this)[0].tagName.toLowerCase();
    html = html.replace(new RegExp('\\<' + tagName, 'gi'), '<raas-field');
    html = html.replace(new RegExp('\\<\\/' + tagName, 'gi'), '</raas-field');
    $(this).replaceWith($(html));
};