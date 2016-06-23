jQuery(function($) {
    config = {
        resizeGrid: 10,
        dragGrid: 10,
        delay: 125,
        minTemplateWidth: 200,
        minTemplateHeight: 200,
        minLocationWidth: 140,
        minLocationHeight: 60
    };
            
            
    var eventX = 0;
    var eventY = 0;
    var locationDragOrResize = function(obj) {
        $('.jsLocationWidth', obj).val(parseInt($(obj).css('width')));
        $('.jsLocationHeight', obj).val(parseInt($(obj).css('height')));
        $('.jsLocationLeft', obj).val(parseInt($(obj).css('left')));
        $('.jsLocationTop', obj).val(parseInt($(obj).css('top')));
    }
    
    var adjustLocations = function () {
        var W = $('.cms-template').innerWidth();
        var H = $('.cms-template').innerHeight();
        $('.cms-location__outer').each(function() {
            var w = $(this).outerWidth();
            var h = $(this).outerHeight();
            var x = parseInt($(this).css('left'));
            var y = parseInt($(this).css('top'));
            var resize = false;
            if (x + w > W) {
                w = Math.max(100, W - x);
                resize = true;
            }
            if (x + w > W) {
                x = W - w;
            }
            if (y + h > H) {
                h = Math.max(32, H - y);
                resize = true;
            }
            if (y + h > H) {
                y = H - h;
            }
            if (resize) {
                $(this).css({ left: x + 'px', top: y + 'px', width: w + 'px', height: h + 'px'});
                locationDragOrResize($(this));
            }
        })
    }
    var templateResizeConfig = {
        autoHide: true,
        delay: config.delay,
        grid: [config.resizeGrid, config.resizeGrid],
        minHeight: config.minTemplateHeight,
        minWidth: config.minTemplateWidth,
        resize: function(event, ui) {
            $('input#width').val(parseInt($(this).css('width')));
            $('input#height').val(parseInt($(this).css('height')));
            // adjustLocations();
        }
    };
    var locationResizeConfig = {
        autoHide: true,
        delay: config.delay,
        grid: [config.resizeGrid, config.resizeGrid],
        containment: 'parent',
        minHeight: config.minLocationHeight,
        minWidth: config.minLocationWidth,
        resize: function() { locationDragOrResize(this); },
        handles: 'all'
    };
    var locationDragConfig = {
        delay: config.delay,
        grid: [config.dragGrid, config.dragGrid],
        containment: 'parent',
        scroll: false,
        drag: function() { locationDragOrResize(this); }
    };
    
    $('.cms-template').resizable(templateResizeConfig);
    $('.cms-location__outer').resizable(locationResizeConfig).draggable(locationDragConfig);    
});