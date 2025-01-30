import RAASFieldDatetimeLocal from './raas-field-datetime-local.vue.js';

/**
 * Поле времени
 */
export default {
    mixins: [RAASFieldDatetimeLocal],
    data() {
        let result = {
            canonicalMomentFormat: 'HH:mm',
            pattern: '^[0-2][0-9]:[0-5][0-9]$',
            momentFormat: 'HH:mm',
        };
        return result;
    },
    methods: {
        checkDatePicker() {
            var self = this;
            if (!$(this.$refs.field).attr('data-datepicker-applied')) {
                $(this.$refs.field).timepicker(this.timePickerParams)
                    .attr('data-datepicker-applied', 'true');
            } else {
                $(this.$refs.field).timepicker('refresh');
            }
        },
    },
    computed: {
        timePickerParams() {
            let result = this.datePickerParams;
            result.altField = null;
            result.altFormat = null;
            result.timeOnly = true;
            return result;
        },
    },
};