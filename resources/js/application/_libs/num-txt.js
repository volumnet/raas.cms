/**
 * Возвращает корректное числительное для числа
 * @param  {Number} number    Число
 * @param  {Array} wordForms Словоформы числа в виде <pre><code>[
 *     'много объектов', 
 *     'один объект', 
 *     'два или три объекта'
 * ]</code></pre>
 * @return {String}
 */
export default function (number, wordForms) {
    var num = number.toString();
    if (num.length > 3) {
        num = num.substr(num, -3);
    }
    var e = parseInt(num) % 10;
    var d = Math.floor(parseInt(num) / 10) % 10;
    if ((e == 1) && (d != 1)) {
        return wordForms[1];
    } else if ((e >= 2) && (e <= 4) && (d != 1)) {
        return wordForms[2];
    } else {
        return wordForms[0];
    }
};