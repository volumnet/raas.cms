/**
 * Форматирует цену
 * @param  {Number} price Цена
 * @param {Boolean} forceRemainder Всегда выводить остаток
 * @return {String} 
 */
export default function (price, forceRemainder = false) {
    price = (Math.round(price * 100) / 100);
    var pR = Math.round((parseFloat(price) - parseInt(price)) * 100);
    var pS = parseInt(price).toString();
    var pT = '';
    var i;

    for (i = 0; i < pS.length; i++) {
        var j = pS.length - i - 1;
        pT = ((i % 3 == 2) && (j > 0) ? ' ' : '') + pS.substr(j, 1) + pT;
    }
    if ((pR > 0) || forceRemainder) {
        pR = pR.toString();
        if (pR.length < 2) {
            pR = '0' + pR;
        }
        pT += ',' + pR;
    }
    return pT;
}