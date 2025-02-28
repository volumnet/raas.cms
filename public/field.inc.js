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
                // var rx = /p=(\w+)/.exec(document.location.href);
                // var p = rx ? rx[1] : 'cms';
                // rx = /sub=(\w+)/.exec(document.location.href);
                // var sub = rx ? rx[1] : 'main';
                // var url = '?p=' + p + '&sub=' + sub;
                var url = '?p=cms&sub=main';
                return url; 
            },
            materialSelect : function(id, pid, name) {
                $thisObj.val(id).attr({ 
                    'data-material-id': id, 
                    'data-material-pid': pid, 
                    'data-material-name': name 
                });
                methods.checkIfExists();
                $thisObj.trigger('RAAS.material-field.selected');
            },
            materialDelete : function() {
                methods.materialSelect('', '', '');
                $('[data-role="material-field-without"] input:text', $container)
                    .val('');
                methods.checkIfExists();
                $thisObj.trigger('RAAS.material-field.deleted');
            },
            clearMaterialClick : function() {
                $container = $(this).closest('[data-role="raas-autotext-container"]');
                $thisObj = $container.find('input[type="hidden"]');
                methods.materialDelete();
                return false;
            },
            checkIfExists: function()
            {
                var id = parseInt($thisObj.attr('data-material-id'));
                var pid = parseInt($thisObj.attr('data-material-pid'));
                id = isNaN(id) ? 0 : id;
                pid = isNaN(pid) ? 0 : pid;
                if (id > 0) {
                    var url = methods.getBaseURL() + '&action=edit_material&id=' 
                            + id + ((pid > 0) ? '&pid=' + pid : '');
                    $('[data-role="raas-autotext-link"]', $container)
                        .attr('href', url)
                        .text($thisObj.attr('data-material-name'));
                    $('[data-role="material-field-with"]', $container).show();
                    $('[data-role="material-field-without"]', $container).hide();
                } else {
                    $('[data-role="raas-autotext-link"]', $container).attr('href', '#').text('');
                    $('[data-role="material-field-with"]', $container).hide();
                    $('[data-role="material-field-without"]', $container).show();
                }
            },
            wrap: function()
            {
                $thisObj.wrap('<div data-role="raas-autotext-container" class="raas-autotext-container"></div>');
                var textFieldName = $thisObj.attr('name');
                var textFieldRx = /^(.*?)(\[.*?\])$/gi;
                if (textFieldRx.test(textFieldName)) {
                    textFieldName = textFieldName.replace(textFieldRx, '$1@name$2');
                } else {
                    textFieldName += '@name';
                }
                $container = $thisObj.closest('[data-role="raas-autotext-container"]');
                
                var text = '  <div data-role="material-field-with" style="display: none">';
                text    += '    <a href="#" data-role="raas-autotext-link" target="_blank"></a>';
                if ($thisObj.closest('[data-role="raas-repo-block"]').length == 0) {
                    text += ' &nbsp; <a href="#" class="close" data-role="raas-autotext-clear">&times;</a>';
                }
                text    += '</div>';
                text    += '<div data-role="material-field-without">';
                text    += '  <input type="text" name="' + textFieldName + '">';
                text    += '</div>';
                $container.append(text);
            },
            init: function(options) { 
                params = $.extend(defaultParams, options);
                $thisObj = $(this);
                methods.wrap();
                methods.checkIfExists();
                $container.on('click', '[data-role="raas-autotext-clear"]', methods.clearMaterialClick);
                var url = 'ajax.php' + methods.getBaseURL() 
                        + '&action=get_materials_by_field'; 
                var fid;
                if (fid = parseInt($thisObj.attr('data-field-id'))) {
                    url += '&id=' + fid;
                } else if (fid = parseInt($thisObj.attr('data-material-type-id'))) {
                    url += '&mtype=' + fid;
                }
                if ($thisObj.attr('data-only-by-name')) {
                    url += '&only_by_name=1';
                }
                url += '&search_string=';
                $('[data-role="material-field-without"] input:text', $container).RAAS_autocompleter({
                    'url': url,
                    callback: function() {
                        var id = $(this).attr('data-id');
                        var pid = $(this).attr('data-pid');
                        var name = $('.raas-autotext__name', this).text();
                        methods.materialSelect(id, pid, name);
                        $thisObj.trigger('RAAS.material-field.init');
                        return false;
                    }
                })
            },
        };
    
        // логика вызова метода
        if ( methods[method] ) {
            return methods[ method ].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        }
    };

    window.setTimeout(() => {
        $('[datatype="material"]:not([disabled])').each(function() { $(this).RAAS_CMS_materialField(); });
        // 2015-05-04, AVS: заменили input:hidden на [datatype="material"], чтобы вызывалось только у соответствующих репозиториев;
        // добавили each(), чтобы не вызывались на чужие типы полей
        $('body').on('RAAS_repo.add', '[data-role="raas-repo-element"]', function() { 
            $('[datatype="material"]', this).each(function() { $(this).RAAS_CMS_materialField() }); }
        );
    }, 0); // Чтобы успел отработать Vue
});