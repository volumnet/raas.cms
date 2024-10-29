/**
 * Миксин флажков (собственно флажок и дерево флажков)
 */
export default {
    computed: {
        /**
         * Отмеченные значения
         * @return {Object} Ассоциативный массив
         */
        checkedValues() {
            const result = {};
            for (let item of this.arrayValue) {
                result[item.toString()] = item;
            }
            return result;
        },
        /**
         * Значение, принудительно приведенное к массиву
         * @return {Array}
         */
        arrayValue: function () {
            let value = this.pValue;
            if (!(value instanceof Array)) {
                value = ((value !== null) && (value !== undefined)) ? [value] : [];
            }
            return value;
        },
    },
};