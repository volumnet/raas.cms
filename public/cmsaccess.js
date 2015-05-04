jQuery(document).ready(function($) {
    $('[data-role="raas-cms-access"]').on('change', '[name="access_to_type[]"]', function() {
        var $opt = $('option:selected', this);
        var $tr = $(this).closest('tr');
        $('[data-role="access-uid"], [data-role="access-gid"]', $tr).hide();
        if ($opt.attr('data-show')) {
            $('[data-role=access-' + $opt.attr('data-show') + ']', $tr).show();
        }
    });

    $.fn.RAAS_CMS_accessUserField = function(method) {
        var $thisObj;
        var $container;
        var defaultParams = {
            showInterval: 1000
        };
        var params;
        var timeout_id = 0;
        
        var methods = {
            getBaseURL: function() {
                var url = '?p=cms&m=users';
                return url; 
            },
            userSelect : function(id, name) {
                $thisObj.val(id).attr({ 'data-user-id': id, 'data-user-name': name });
                methods.checkIfExists();
            },
            userDelete : function() {
                methods.userSelect('', '', '');
                $('[data-role="access-user-field-without"] input:text', $container).val('');
                methods.checkIfExists();
            },
            clearUserClick : function() {
                $container = $(this).closest('[data-role="raas-autotext-container"]');
                $thisObj = $container.find('input[type="hidden"]');
                methods.userDelete();
                methods.hideUsersList();
                return false;
            },
            checkIfExists: function()
            {
                var id = parseInt($thisObj.attr('data-user-id'));
                id = isNaN(id) ? 0 : id;
                if (id > 0) {
                    var url = methods.getBaseURL() + '&action=edit&id=' + id;
                    $('[data-role="raas-autotext-link"]', $container).attr('href', url).text($thisObj.attr('data-user-name'));
                    $('[data-role="access-user-field-with"]', $container).show();
                    $('[data-role="access-user-field-without"]', $container).hide();
                } else {
                    $('[data-role="raas-autotext-link"]', $container).attr('href', '#').text('');
                    $('[data-role="access-user-field-with"]', $container).hide();
                    $('[data-role="access-user-field-without"]', $container).show();
                }
            },
            wrap: function()
            {
                $thisObj.wrap('<div data-role="raas-autotext-container" class="raas-autotext-container"></div>');
                $container = $thisObj.closest('[data-role="raas-autotext-container"]');
                
                var text = '  <div data-role="access-user-field-with" style="display: none">';
                text    += '    <a href="#" data-role="raas-autotext-link" target="_blank"></a>';
                if ($thisObj.closest('[data-role="raas-repo-block"]').length == 0) {
                    text += ' &nbsp; <a href="#" class="close" data-role="raas-autotext-clear">&times;</a>';
                }
                text    += '</div>';
                text    += '<div data-role="access-user-field-without">';
                text    += '  <input type="text">';
                text    += '</div>';
                $container.append(text);
            },
            init: function(options) { 
                params = $.extend(defaultParams, options);
                $thisObj = $(this);
                methods.wrap();
                methods.checkIfExists();
                $container.on('click', '[data-role="raas-autotext-clear"]', methods.clearUserClick);
                $('[data-role="access-user-field-without"] input:text', $container).RAAS_autocompleter({
                    url: 'ajax.php' + methods.getBaseURL() + '&action=get_users&search_string=',
                    callback: function() {
                        var id = $(this).attr('data-id');
                        var name = $('.raas-autotext__name', this).text();
                        methods.userSelect(id, name);
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
    $('[data-role="raas-cms-access"] input[name="access_uid[]"]:not([disabled])').each(function() { $(this).RAAS_CMS_accessUserField(); });
    $('[data-role="raas-cms-access"]').on('RAAS_repo.add', '[data-role="raas-repo-element"]', function() { 
        $('input[name="access_uid[]"]', this).each(function() { $(this).RAAS_CMS_accessUserField() }); }
    );


});
