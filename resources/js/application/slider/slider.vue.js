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
            prevAvailable: this.wrap,
            /**
             * Следующий кадр доступен
             * @type {Boolean}
             */
            nextAvailable: true,
            /**
             * ID# window.setInterval автоскроллинга при fade-типе
             * @type {Number}
             */
            autoscrollInterval: null,
        };
    },
    mounted: function () {
        if (this.type == 'fade') {
            this.refreshFadeInterval();
            $(this.$el).on('mouseover', () => {
                this.clearFadeInterval();
            }).on('mouseout', () => {
                this.refreshFadeInterval();
            })
        } else {
            this.initJCarousel();
        }
        this.counter = $('[data-role="slider-item"]', this.$el).length;
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
                this.refreshFadeInterval();
            } else {
                $('[data-role="slider-list"]', this.$el).jcarousel('scroll', index);
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
            
            let $self = $('[data-role="slider-list"]', this.$el).jcarousel(options);
                
            let $items = $self.jcarousel('items');
            $items.on('jcarousel:firstin', function () {
                let i = $items.index(this);
                self.activeFrame = i;
            });
            
            if (this.autoscroll) {
                $self.jcarouselAutoscroll({
                    target: '+=1',
                    autostart: true,
                    interval: this.interval,
                });
            }
            if (!this.wrap) {
                let $first = $items.eq(0);
                let $last = $items.eq(-1);
                $first.on('jcarousel:fullyvisiblein', () => {
                    this.prevAvailable = false;
                }).on('jcarousel:fullyvisibleout', () => {
                    this.prevAvailable = true;
                });
                if ($last.filter($self.jcarousel('fullyvisible')).length) {
                    this.nextAvailable = false;
                } else {
                    this.nextAvailable = true;
                }
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
    },
    computed: {
        self: function () { 
            return { ...this };
        },
    },
}