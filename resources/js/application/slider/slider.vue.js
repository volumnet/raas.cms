/**
 * Слайдер
 */
export default {
    props: {
        /**
         * Тип слайдера:
         * horizontal - горизонтальный (по умолчанию),
         * vertical - вертикальный
         * fade - фейдинг
         */
        type: {
            type: String,
            default: 'horizontal'
        },
        /**
         * Время пролистывания кадра (мс) - только для типов horizontal и vertical
         */
        duration: {
            type: Number,
            default: 500,
        },
        /**
         * Задержка кадра (мс)
         */
        interval: {
            type: Number,
            default: 5000,
        },
        /**
         * Зацикливание кадров
         */
        wrap: {
            type: Boolean,
            default: true,
        },
        /**
         * Автоскролл
         */
        autoscroll: {
            type: Boolean,
            default: false,
        },
    },
    data: function () {
        return {
            /**
             * Номер активного кадра
             * @type {Number}
             */
            activeFrame: 0,
            /**
             * Количество кадров
             * @type {Number}
             */
            counter: 0,
            /**
             * Предыдущий кадр доступен
             * @type {Boolean}
             */
            prevAvailable: false,
            /**
             * Следующий кадр доступен
             * @type {Boolean}
             */
            nextAvailable: false,
            /**
             * ID# window.setInterval автоскроллинга при fade-типе
             * @type {Number}
             */
            autoscrollInterval: null,
        };
    },
    mounted: function () {
        this.counter = $('[data-role="slider-item"]', this.$el).length;
        if (this.type == 'fade') {
            if (this.wrap) {
                this.prevAvailable = (this.counter > 1);
            }
            this.nextAvailable = (this.counter > 1);
            this.refreshFadeInterval();
            $(this.$el).on('mouseover', () => {
                this.clearFadeInterval();
            }).on('mouseout', () => {
                this.refreshFadeInterval();
            })
        } else {
            window.setTimeout(() => {
                this.initJCarousel();
            }, 0); // Чтобы успел инициализироваться App (в частности ширина экрана)
        }
        $('[data-role="slider-list"]', this.$el).on('movestart', (e) => { 
            // console.log(e)
            let dim = (this.type == 'vertical') ? e.distY : e.distX;
            if (dim <= -6) {
                this.next();
                return true;
            } else if (dim >= 6) {
                this.prev();
                return true;
            }
            e.preventDefault()
            return false;
        });
        $(window).on('resize', this.refresh.bind(this, false));
    },
    methods: {
        /**
         * Перемещает к определенному кадру
         * @param {Number} index Номер кадра
         */
        slideTo: function (index) {
            if (this.type == 'fade') {
                let newActiveFrame = index;
                if (this.wrap) {
                    newActiveFrame = (newActiveFrame + this.counter) % this.counter;
                } else {
                    newActiveFrame = Math.min(this.counter - 1, newActiveFrame);
                    newActiveFrame = Math.max(0, newActiveFrame);
                    this.prevAvailable = (newActiveFrame > 0);
                    this.nextAvailable = (newActiveFrame < (this.counter - 1));
                }
                this.activeFrame = newActiveFrame;
                this.$emit('slid', index);
                this.refreshFadeInterval();
            } else {
                // 2021-08-22, AVS: сделал явные атрибуты индекса, 
                // т.к. при перестановке слайдов с wrap'ом индексация слетает
                let $list = $('[data-role="slider-list"]', this.$el);
                let slideToScroll = $list
                    .jcarousel('items')
                    .filter('[data-slider-index="' + index + '"]')[0];
                $list.jcarousel('scroll', slideToScroll);
            }
        },
        /**
         * Перемещает к предыдущему кадру
         */
        prev: function () {
            if (this.type == 'fade') {
                this.slideTo(this.activeFrame - 1);
            } else {
                $('[data-role="slider-list"]', this.$el).jcarousel('scroll', '-=1');
            }
        },
        /**
         * Перемещает к следующему кадру
         */
        next: function () {
            if (this.type == 'fade') {
                this.slideTo(this.activeFrame + 1);
            } else {
                $('[data-role="slider-list"]', this.$el).jcarousel('scroll', '+=1');
            }
        },
        /**
         * Инициализирует JCarousel
         */
        initJCarousel: function () {
            let self = this;
            let options = {
                nextTarget: '+=1',
                prevTarget: '-=1',
            };
            if (this.duration) {
                options.animation = { 
                    duration: parseInt(this.duration),
                    easing: 'linear'
                }
            }
            if (this.wrap) {
                options.wrap = 'circular';
            }
            options.vertical = (this.type == 'vertical');
            // console.log(options)
            let $self = $('[data-role="slider-list"]', this.$el).jcarousel(options);
                
            let $items = $self.jcarousel('items');
            // 2021-08-22, AVS: сделал явные атрибуты индекса, 
            // т.к. при перестановке слайдов с wrap'ом индексация слетает
            $items.each((index, el) => {
                $(el).attr('data-slider-index', index);
            }).on('jcarousel:firstin', function () {
                // let i = $items.index(this);
                let i = $(this).attr('data-slider-index');
                self.activeFrame = i;
                self.$emit('slid', i);
            });
            
            if (this.autoscroll) {
                $self.jcarouselAutoscroll({
                    target: '+=1',
                    autostart: true,
                    interval: this.interval,
                });
            }
            let $first = $items.eq(0);
            let $last = $items.eq(-1);
            this.nextAvailable = !$last.filter($self.jcarousel('fullyvisible')).length;
            if (this.wrap) {
                this.prevAvailable = !$last.filter($self.jcarousel('fullyvisible')).length;
            } else {
                $first.on('jcarousel:fullyvisiblein', () => {
                    this.prevAvailable = false;
                }).on('jcarousel:fullyvisibleout', () => {
                    this.prevAvailable = true;
                });
                $last.on('jcarousel:fullyvisiblein', () => {
                    this.nextAvailable = false;
                }).on('jcarousel:fullyvisibleout', () => {
                    this.nextAvailable = true;
                });
            }
            return $self; 
        },
        /**
         * Сбрасывает интервал автоскролла для fade-типа
         */
        clearFadeInterval: function () {
            if (this.autoscrollInterval) {
                window.clearInterval(this.autoscrollInterval);
            }
        },
        /**
         * Переустанавливает интервал автоскролла для fade-типа
         */
        refreshFadeInterval: function () {
            this.clearFadeInterval();
            if (this.autoscroll) {
                this.autoscrollInterval = window.setInterval(() => {
                    this.next();
                }, this.interval + this.duration);
            }
        },
        /**
         * Обновляет данные слайдера
         * @param  {Boolean} slideToFirst Переместиться к первому слайду
         */
        refresh: function (slideToFirst = false) {
            if (this.type == 'fade') {
                if (slideToFirst) {
                    this.slideTo(0);
                }
            } else {
                window.setTimeout(() => {
                    let $self = $('[data-role="slider-list"]', this.$el);
                
                    let $items = $self.jcarousel('items');

                    $self.jcarousel('reload');
                    if (slideToFirst) {
                        this.slideTo(0);
                    }
                    let $first = $items.eq(0);
                    let $last = $items.eq(-1);
                    this.nextAvailable = !$last.filter($self.jcarousel('fullyvisible')).length;
                    if (this.wrap) {
                        this.prevAvailable = !$last.filter($self.jcarousel('fullyvisible')).length;
                    }
                }, 0);
            }
        },
    },
    computed: {
        self: function () { 
            return { ...this };
        },
    },
}