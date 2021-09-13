/**
 * Выбирает стандартный источник из select'а
 * @param  {jQuery} $obj jQuery-select для выбора
 * @return {Array} <pre><code>array<{
 *     value: String значение,
 *     name: String подпись,
 *     children?: <рекурсивно>
 * }></code></pre>
 */
export default function ($obj) {
    let level = 0;
    let result = { level: -1, children: [] };
    let flatEntries = [result];
    let currentLevel = -1;
    let current = result;
    $('option', $obj).each(function () {
        let value = $(this).attr('value') || '';
        let html = $(this).html();
        let name = $.trim($(this).text());
        let level = 0;
        let matches = html.match(/&nbsp;/gi);
        if (matches) {
            level = matches.length;
        }
        let entry = {
            value,
            name,
            level,
        };
        if (level > currentLevel) {
            current = flatEntries[flatEntries.length - 1];
        } else if (level < currentLevel) {
            current = flatEntries.filter(x => x.level < level).pop();
        }
        if (!current.children) {
            current.children = [];
        }
        flatEntries.push(entry);
        current.children.push(entry);
        if (level != currentLevel) {
            currentLevel = level;
        }
    });

    let clearLevels = function (x) {
        let y = JSON.parse(JSON.stringify(x));
        delete y.level;
        if (y.children) {
            y.children = y.children.map(clearLevels);
        }
        return y;
    }
    result = result.children.map(clearLevels);
    return result;
};