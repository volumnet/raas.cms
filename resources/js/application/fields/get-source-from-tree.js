/**
 * Выбирает стандартный источник из дерева флажков или переключателей
 * @param  {jQuery} $obj jQuery-ul для выбора
 * @return {Array} <pre><code>array<{
 *     value: String значение,
 *     name: String подпись,
 *     children?: <рекурсивно>
 * }></code></pre>
 */
let getSourceFromTree = function ($obj) {
    let result = [];
    $('> li', $obj).each(function () {
        let value = $('> label input', this).attr('value');
        let name = $.trim($('> label', this).text());
        let entry = {
            value,
            name
        };
        if ($('> ul', this).length) {
            let children = getSourceFromTree($('> ul', this));
            if (children.length) {
                entry.children = children;
            }
        }
        result.push(entry);
    });
    return result;
};

export default getSourceFromTree;