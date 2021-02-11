import RAASFieldDate from './raas-field-date.vue.js';

/**
 * Поле даты/времени
 */
export default {
    mixins: [RAASFieldDate],
    data: function () {
        let lang = $('html').attr('lang') || 'ru';
        let result = {
            canonicalMomentFormat: 'YYYY-MM-DD HH:mm',
        };
        switch (lang) {
            case 'en':
                result.pattern = '^[0-1][0-9]\\/[0-3][0-9]\\/[1-2][0-9]{3} [0-2][0-9]:[0-5][0-9]$';
                result.momentFormat = 'MM/DD/YYYY HH:mm';
                break;
            default: // ru
                result.pattern = '^[0-3][0-9]\\.[0-1][0-9]\\.[1-2][0-9]{3} [0-2][0-9]:[0-5][0-9]$';
                result.momentFormat = 'DD.MM.YYYY HH:mm';
                break;
        }
        return result;
    },
    methods: {
        checkDatePicker: function () {
            var self = this;
            if (!$(this.$refs.field).attr('data-datepicker-applied')) {
                $(this.$refs.field).datetimepicker(this.timePickerParams)
                    .attr('data-datepicker-applied', 'true');
            }
        },
    },
    computed: {
        timePickerParams: function () {
            let result = this.datePickerParams;
            result.timeFormat = result.altTimeFormat = 'HH:mm';
            result.altFieldTimeOnly = false;
            result.altSeparator = ' ';
            result.showHour = true;
            result.showMinute = true;
            result.showSecond = false;
            result.showMillisec = false;
            result.showMicrosec = false;
            result.showTimezone = false;
            return result;
        },
    },
};