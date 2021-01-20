/**
 * Форматирует цену
 * @param  {Number} price Цена
 * @return {String} 
 */
export default function (price) {
    var pR = Math.round((parseFloat(price) - parseInt(price)) * 100);
    var pS = parseInt(price).toString();
    var pT = '';
    var i;

    for (i = 0; i < pS.length; i++) {
        var j = pS.length - i - 1;
        pT = ((i % 3 == 2) && (j > 0) ? ' ' : '') + pS.substr(j, 1) + pT;
    }
    if (pR > 0) {
        pR = pR.toString();
        if (pR.length < 2) {
            pR = '0' + pR;
        }
        pT += ',' + pR;
    }
    return pT;
}