jQuery(document).ready(function($) {
    window.setTimeout(() => {
        var rebuildCacheLocked = false;
        $('.cms-cache [data-role="rebuild-cache"]').on('click', function() {
            if (rebuildCacheLocked) {
                return false;
            }
            rebuildCacheLocked = true;
            var $button = $(this);
            $button.attr('disabled', 'disabled').addClass('disabled');
            var $obj = $('.cms-cache');
            var cacheMap = [];
            $('.cms-rebuild-cache', $obj).show();
            $('.alert', $obj).removeClass('alert-success').addClass('alert-info');
            $('[data-role="status-text"]', $obj).text($('[data-role="status-text-get-map"]', $obj).text());
            
            var dO = $.get('ajax.php?p=cms&action=get_cache_map');
            dO = dO.then(function(result) {
                $('[data-role="status-text"]', $obj).text($('[data-role="status-text-clear-cache"]', $obj).text());
                cacheMap = result.Set;
                return $.getJSON('ajax.php?p=cms&action=clear_cache');
            })
            dO = dO.then(function() {
                $('[data-role="progress"]', $obj).text('0 / ' + cacheMap.length);
                $('.progress .bar', $obj).css('width', '0');
                var a = new $.Deferred();
                a.resolve(true);
                for (var i = 0; i < cacheMap.length; i++) {
                    a = a.then((function(i) {
                        return function() {
                            var row = cacheMap[i];
                            $('[data-role="status-text"]', $obj).text($('[data-role="status-text-rebuild-page"]', $obj).text() + ' "' + row.name + '"');
                            var url = 'ajax.php?p=cms&action=rebuild_page_cache&id=' + row.id;
                            if (row.mid) {
                                url += '&mid=' + row.mid;
                            }
                            console.log(url);
                            var b = $.get(url).then(function() {
                                $('[data-role="progress"]', $obj).text((i + 1) + ' / ' + cacheMap.length);
                                $('.progress .bar', $obj).css('width', parseFloat(((i + 1) * 100) / cacheMap.length) + '%');
                            })
                            return b;
                        }
                    })(i))
                }
                return a;
            })
            dO = dO.then(function() { 
                $('.alert', $obj).removeClass('alert-info').addClass('alert-success');
                $('[data-role="status-text"]', $obj).text($('[data-role="status-text-success"]', $obj).text());
                $button.removeAttr('disabled').removeClass('disabled');
                rebuildCacheLocked = false;
            })
        })
    }, 0); // Чтобы успел отработать Vue
});