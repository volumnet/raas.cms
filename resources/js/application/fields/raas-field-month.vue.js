import RAASFieldDate from './raas-field-date.vue.js';

/**
 * Поле месяца
 */
export default {
    mixins: [RAASFieldDate],
    data: function () {
        let lang = $('html').attr('lang') || 'ru';
        let result = {
            canonicalMomentFormat: 'YYYY-MM',
            canonicalDatePickerFormat: 'yy-mm',
        };
        switch (lang) {
            case 'en':
                result.pattern = '^[0-1][0-9]\\/[1-2][0-9]{3}$';
                result.datePickerFormat = 'mm/yy';
                result.momentFormat = 'MM/YYYY';
                break;
            default: // ru
                result.pattern = '^[0-1][0-9]\\.[1-2][0-9]{3}$';
                result.datePickerFormat = 'mm.yy';
                result.momentFormat = 'MM.YYYY';
                break;
        }
        return result;
    },
    methods: {
        checkDatePicker: function () {
            var self = this;
            if (!$(this.$refs.field).attr('data-datepicker-applied')) {
                $(this.$refs.field).datepicker(this.monthPickerParams)
                    .attr('data-datepicker-applied', 'true');
            } else {
                $(this.$refs.field).datepicker('refresh');
            }
        },
        togglePicker: function () {
            if (this.pickerIsShown) {
                $(this.$refs.field).datepicker('hide');
                this.pickerIsShown = false;
            } else if (!this.$attrs.disabled) {
                this.pickerIsShown = true;
                let m = window.moment(this.value, this.canonicalMomentFormat);
                let year = m.year();
                let month = m.month();
                $(".ui-datepicker").addClass('raas-field-month__datepicker');
                $(this.$refs.field)
                    .datepicker('show')
                    .datepicker('setDate', new Date(year, month, 1));
            }
        },
    },
    computed: {
        monthPickerParams: function () {
            let result = this.datePickerParams;
            result.onClose = () => {
                this.pickerIsShown = false;
                window.setTimeout(() => {
                    $(".ui-datepicker").removeClass('raas-field-month__datepicker'); 
                }, 250);
            };
            result.onChangeMonthYear = (year, month) => {
                let m = window.moment([year, month - 1, 1]);
                let value = m.format(this.canonicalMomentFormat);
                this.$emit('input', value)
            }
            return result;
        },
    },
};