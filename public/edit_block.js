jQuery(function($) {
    var checkCaching = function()
    {
        var cacheType = parseInt($('#cache_type').val());
        if (!isNaN(cacheType) && (cacheType > 0)) {
            $('#cache_single_page, #cache_interface_id')
                .removeAttr('disabled')
                .closest('.control-group')
                .show();
        } else {
            $('#cache_single_page, #cache_interface_id')
                .attr('disabled', 'disabled')
                .closest('.control-group')
                .hide();
        }
    }

    $('#material_type').change(function() {
        var url = 'ajax.php?p=cms&action=material_fields&id=' + $(this).val();
        $('.jsMaterialTypeField').RAAS_getSelect(
            url, 
            { 
                before: function (data) { 
                    return data.Set; 
                } 
            }
        );
    })

    $('#wysiwyg').click(function() {
        var $originalDescription = $('#description');
        var mime = $('#description').attr('data-mime');
        var text = $originalDescription.val();
        var $container = $originalDescription.closest('.control-group');
        $container.empty();
        var $description = $('<textarea>');
        $description.attr({ 
            'id': 'description', 
            'name': 'description',
            'data-mime': mime, 
        }).val(text);
        $container.append($description);
        if ($(this).is(':checked')) {
            $description.ckeditor(ckEditorConfig);
        } else {
            CodeMirror.fromTextArea(
                $description[0], 
                { 
                    lineNumbers: true, 
                    mode: mime || 'text/html', 
                    indentUnit: 2, 
                    indentWithTabs: false, 
                    enterMode: "keep", 
                    tabMode: "shift", 
                    tabSize: 2 
                }
            );
        }
    });

    $('#cache_type').on('change', checkCaching);
    checkCaching();
});