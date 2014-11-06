jQuery(function($) {
    $.fn.RAAS_CMS_materialField = function(method) {
        var $thisObj;
        var $container;
        var defaultParams = {
            showInterval: 1000
        };
        var params;
        var timeout_id = 0;
        
        var methods = {
            getBaseURL: function() {
                var rx = /p=(\w+)/.exec(document.location.href);
                var p = rx ? rx[1] : 'cms';
                rx = /sub=(\w+)/.exec(document.location.href);
                var sub = rx ? rx[1] : 'main';
                var url = '?p=' + p + '&sub=' + sub;
                return url; 
            },
            getCompletion: function(data) {
                var Set = data.Set;
                var i;
                $('[data-role="material-field-autotext"]', $container).empty();
                if (Set.length > 0) {
                    for (i = 0; i < Set.length; i++) {
                        var text = '<li>';
                        text    += '  <a href="#" data-id="' + Set[i].id + '" data-pid="' + Set[i].pid + '">';
                        if (Set[i].img) {
                            text += '   <img src="' + Set[i].img + '" />';
                        }
                        text    += '    <span class="cms-material-field-autotext__name">' + Set[i].name + '</span>';
                        text    += '    <span class="cms-material-field-autotext__description">' + Set[i].description + '</span>';
                        text    += '  </a>';
                        text    += '</li>';
                        $('[data-role="material-field-autotext"]', $container).append(text);
                    }
                    $('[data-role="material-field-autotext"]', $container).show();
                } else {
                    methods.hideMaterialsList();
                }
            },
            textOnChange: function() {
                $container = $(this).closest('[data-role="material-field-container"]');
                $thisObj = $container.find('input[type="hidden"]');
                var text = $(this).val();
                var id = parseInt($thisObj.attr('data-field-id'));
                id = isNaN(id) ? 0 : id;
                var url = 'ajax.php' + methods.getBaseURL() + '&action=get_materials_by_field&id=' + id + '&search_string=' + text;
                window.clearTimeout(timeout_id);
                timeout_id = window.setTimeout(function() { $.getJSON(url, methods.getCompletion) }, params.showInterval);
            },
            hideMaterialsList : function() {
                $('[data-role="material-field-autotext"]', $container).hide();
            },
            materialSelect : function(id, pid, name) {
                $thisObj.val(id).attr({ 'data-material-id': id, 'data-material-pid': pid, 'data-material-name': name });
                methods.checkIfExists();
            },
            materialOnClick: function() {
                $container = $(this).closest('[data-role="material-field-container"]');
                $thisObj = $container.find('input[type="hidden"]');
                var id = $(this).attr('data-id');
                var pid = $(this).attr('data-pid');
                var name = $('.cms-material-field-autotext__name', this).text();
                methods.materialSelect(id, pid, name);
                methods.hideMaterialsList();
                return false;
            },
            materialDelete : function() {
                methods.materialSelect('', '', '');
                $('[data-role="material-field-without"] input:text', $container).val('');
                methods.checkIfExists();
            },
            clearMaterialClick : function() {
                $container = $(this).closest('[data-role="material-field-container"]');
                $thisObj = $container.find('input[type="hidden"]');
                methods.materialDelete();
                methods.hideMaterialsList();
                return false;
            },
            checkIfExists: function()
            {
                var id = parseInt($thisObj.attr('data-material-id'));
                var pid = parseInt($thisObj.attr('data-material-pid'));
                id = isNaN(id) ? 0 : id;
                pid = isNaN(pid) ? 0 : pid;
                if (id > 0) {
                    var url = methods.getBaseURL() + '&action=edit_material&id=' + id + ((pid > 0) ? '&pid=' + pid : '');
                    $('[data-role="material-field-link"]', $container).attr('href', url).text($thisObj.attr('data-material-name'));
                    $('[data-role="material-field-with"]', $container).show();
                    $('[data-role="material-field-without"]', $container).hide();
                } else {
                    $('[data-role="material-field-link"]', $container).attr('href', '#').text('');
                    $('[data-role="material-field-with"]', $container).hide();
                    $('[data-role="material-field-without"]', $container).show();
                }
            },
            wrap: function()
            {
                $thisObj.wrap('<div data-role="material-field-container" class="cms-material-field-container"></div>');
                $container = $thisObj.closest('[data-role="material-field-container"]');
                
                var text = '  <div data-role="material-field-with" style="display: none">';
                text    += '    <a href="#" data-role="material-field-link" target="_blank"></a>';
                if ($thisObj.closest('[data-role="raas-repo-block"]').length == 0) {
                    text += ' &nbsp; <a href="#" class="close" data-role="material-field-clear">&times;</a>';
                }
                text    += '</div>';
                text    += '<div data-role="material-field-without">';
                text    += '  <input type="text"> <ul class="cms-material-field-autotext" style="display: none" data-role="material-field-autotext"></ul>';
                text    += '</div>';
                $container.append(text);
            },
            init: function(options) { 
                params = $.extend(defaultParams, options);
                $thisObj = $(this);
                methods.wrap();
                methods.checkIfExists();
                $container.on('keyup', '[data-role="material-field-without"] input:text', methods.textOnChange);
                $('body').on('click', methods.hideMaterialsList);
                $container.on('click', '[data-role="material-field-autotext"] a', methods.materialOnClick);
                $container.on('click', '[data-role="material-field-clear"]', methods.clearMaterialClick)
            },
        };
    
        // логика вызова метода
        if ( methods[method] ) {
            return methods[ method ].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        }
    };


    $('.well:has(input:file)').on('click', 'a.close:not([data-role="raas-repo-del"])', function() {
        var $w = $(this).closest('.well');
        $('[data-role="file-link"]', $w).remove();
        $('input:text, input:hidden, textarea', $w).val('');
        $('input:checkbox', $w).attr('checked', 'checked');
        $(this).remove();
        return false;
    });
    $('.well:has(input:file) input:checkbox:visible').click(function() {
        var checked = $(this).attr('checked');
        var $w = $(this).closest('.well');
        if (checked) {
            $('input:checkbox[data-role="checkbox-shadow"]', $w).removeAttr('checked');
        } else {
            $('input:checkbox[data-role="checkbox-shadow"]', $w).attr('checked', 'checked');
        }
    });
    $('[datatype="material"]').each(function() { $(this).RAAS_CMS_materialField(); });
});