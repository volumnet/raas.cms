export default function (params) {
    var $self;
    var options = {};
    switch (params.carousel) {
    case 'jcarousel':
        var autoscrollOptions = {};
        if (params.duration) {
            options.animation = { 
                duration: parseInt(params.duration),
                easing: 'linear'
            }
        }
        if (params.wrap) {
            options.wrap = params.wrap;
        }
        if (params.vertical && (params.vertical != 'false')) {
            options.vertical = true;
        } else if (params.vertical == 'false') {
            options.vertical = false;
        }
        if (params.autoscroll) {
            autoscrollOptions.target = '+=1';
            autoscrollOptions.autostart = true;
            if (params.interval) {
                autoscrollOptions.interval = parseInt(params.interval);
            }
        }
        options.nextTarget = params.nextTarget || '+=1';
        options.prevTarget = params.prevTarget || '-=1';
        options.arrowInactiveClass = params.arrowInactiveClass || 'inactive';
        options.paginationActiveClass = params.paginationActiveClass || 'active';
        options.paginationSelector = params.paginationSelector || 'a';
        options.paginationFunction = params.paginationFunction || function(page) { return '<a href="#' + page + '"></a>'; };
        // console.log(options)
        $self = $(this).jcarousel(options).on('movestart', (function (options) { 
            return function (e) { 
                var $self = $(this);
                if (options.vertical) {
                    var dim = e.distY;
                } else {
                    var dim = e.distX;
                }
                if (dim <= -6) {
                    $self.jcarousel('scroll', options.nextTarget);
                    return true;
                } else if (dim >= 6) {
                    $self.jcarousel('scroll', options.prevTarget);
                    return true;
                }
                e.preventDefault()
                return false;
            }; 
        })(options));
        if (params.$prev) {
            params.$prev
                .on('jcarouselcontrol:active', function() { $(this).removeClass(options.arrowInactiveClass); })
                .on('jcarouselcontrol:inactive', function() { $(this).addClass(options.arrowInactiveClass); })
                .jcarouselControl({ target: options.prevTarget, carousel: $self });
        }
        if (params.$next) {
            params.$next
                .on('jcarouselcontrol:active', function() { $(this).removeClass(options.arrowInactiveClass); })
                .on('jcarouselcontrol:inactive', function() { $(this).addClass(options.arrowInactiveClass); })
                .jcarouselControl({ target: options.nextTarget, carousel: $self });
        }
        if (params.$pagination) {
            params.$pagination
                .on('jcarouselpagination:active', options.paginationSelector, function() { $(this).addClass(options.paginationActiveClass); })
                .on('jcarouselpagination:inactive', options.paginationSelector, function() { $(this).removeClass(options.paginationActiveClass); })
                .jcarouselPagination({ carousel: $self, item: options.paginationFunction });
        }
        if (params.autoscroll) {
            $self.jcarouselAutoscroll(autoscrollOptions);
        }
        return $self;
        break;
    case 'bootstrap':
        if (params.autoscroll) {
            if (params.interval) {
                options.interval = parseInt(params.interval);
            }
        } else {
            options.interval = false;
        }
        $self = $(this);
        $self.on('movestart', function(e) { 
            var $self = $(this);
            console.log(e);
            if (e.distX <= -6) {
                $self.carousel('next');
                return true;
            } else if (e.distX >= 6) {
                $self.carousel('prev');
                return true;
            }
            e.preventDefault()
            return false;
        });
        $self.carousel(options);
        return $self;
        break;
    }
}