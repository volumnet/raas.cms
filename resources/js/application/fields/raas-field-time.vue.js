import RAASFieldDatetimeLocal from './raas-field-datetime-local.vue.js';

/**
 * Поле времени
 */
export default {
    mixins: [RAASFieldDatetimeLocal],
    data: function () {
        let result = {
            canonicalMomentFormat: 'HH:mm',
            pattern: '^[0-2][0-9]:[0-5][0-9]$',
            momentFormat: 'HH:mm',
        };
        return result;
    },
    methods: {
        checkDatePicker: function () {
            var self = this;
            if (!$(this.$refs.field).attr('data-datepicker-applied')) {
                $(this.$refs.field).timepicker(this.timePickerParams)
                    .attr('data-datepicker-applied', 'true');
            }
        },
    },
    computed: {
        timePickerParams: function () {
            let result = this.datePickerParams;
            result.altField = null;
            result.altFormat = null;
            result.timeOnly = true;
            return result;
        },
    },
};