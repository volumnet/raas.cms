import RAASField from './raas-field.vue.js';

/**
 * Поле выпадающего меню
 */
export default {
    mixins: [RAASField],
    props: [
        /**
         * Множественное поле
         */
        'multiple',
    ],
    data: function () {
        return {
            /**
             * Блокировка (костыль для совместимости с Bootstrap 5, 
             * иначе просто сразу же закрывает меню)
             * @type {Boolean}
             */
            locked: false
        }
    },
    mounted: function () {
        this.checkMultiselect();
    },
    updated: function () {
        this.checkMultiselect();
    },
    methods: {
        /**
         * Проверка/установка multiselect'а
         */
        checkMultiselect: function () {
            let self = this;
            if (!$(this.$el).attr('data-multiselect-applied')) {
                $(this.$el).multiselect(this.multiselectConfig)
                    .attr('data-multiselect-applied', 'true')
                    .on('change', function () {
                        // console.log($('option:selected', this))
                        self.$emit('input', $(this).val())
                    });
            }
        },
        /**
         * Блокировка (костыль для совместимости с Bootstrap 5, 
         * иначе просто сразу же закрывает меню)
         * @param {Boolean} isShown Условие для блокировки по видимости меню
         */
        doLock: function (isShown) {
            let lockCondition = $('[data-bs-toggle="dropdown"]').is('.show');
            if (!isShown) {
                lockCondition = !lockCondition;
            }
            if (lockCondition) {
                this.locked = true;
                window.setTimeout(() => {
                    this.locked = false;
                }, 100);
            }
        },
    },
    computed: {
        /**
         * Конфигурация multiselect'а
         * @return {Object}
         */
        multiselectConfig: function () {
            return {
                buttonText: function (options, select) {
                    if (options.length == 0) {
                        return '--';
                    }
                    else {
                      var selected = '';
                      var i = 0;
                      options.each(function () {
                          if (i < 3) {
                              selected += $.trim($(this).text()) + ', ';
                          }
                          i++;
                      });
                      selected = selected.substr(0, selected.length - 2);
                      return selected + (options.length > 3 ? '...' : '');
                    }
                },
                buttonClass: 'form-control form-select',
                maxHeight: 200,
                templates: {
                    button: '<button type="button" class="multiselect dropdown-toggle" data-bs-toggle="dropdown"><span class="multiselect-selected-text"></span></button>',
                    // popupContainer: '<div class="multiselect-container dropdown-menu"></div>',
                    // filter: '<div class="multiselect-filter d-flex align-items-center"><i class="fas fa-sm fa-search text-muted"></i><input type="search" class="multiselect-search form-control" /></div>',
                    // option: '<a class="multiselect-option dropdown-item"></a>',
                    // divider: '<div class="multiselect-item dropdown-divider"></div>',
                    // optionGroup: '<a class="multiselect-item multiselect-group"></a>',
                    // resetButton: '<div class="multiselect-reset text-center p-2"><a class="btn btn-sm btn-block btn-outline-secondary"></a></div>'
                },
                onDropdownShow: (event) => {
                    if (this.locked) {
                        return false;
                    }
                    this.doLock(false);
                },
                onDropdownHide: (event) => {
                    if (this.locked) {
                        return false;
                    }
                    this.doLock(true);
                },
            };
        },
    },
};