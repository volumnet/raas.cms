jQuery(function($) {
    var checkCaching = function()
    {
        var cache_type = parseInt($('#cache_type').val());
        if (!isNaN(cache_type) && (cache_type > 0)) {
            $('#cache_single_page, #cache_interface_id').removeAttr('disabled').closest('.control-group').show();
        } else {
            $('#cache_single_page, #cache_interface_id').attr('disabled', 'disabled').closest('.control-group').hide();
        }
    }

    $('#material_type').change(function() {
        $('.jsMaterialTypeField').RAAS_getSelect('ajax.php?p=cms&action=material_fields&id=' + $(this).val(), {before: function(data) { return data.Set; }});
    })

    $('#wysiwyg').click(function() {
        var text = $('#description').val();
        var $container = $('#description').closest('.control-group');
        $container.empty();
        var $description = $('<textarea>');
        $description.attr({ 'id': 'description', 'name': 'description' }).val(text);
        $container.append($description);
        if ($(this).is(':checked')) {
            $description.ckeditor();
        } else {
            CodeMirror.fromTextArea(
                $description[0], 
                { lineNumbers: true, mode: "text/html", indentUnit: 2, indentWithTabs: false, enterMode: "keep", tabMode: "shift", tabSize: 2 }
            );
        }
    });

    $('#cache_type').on('change', checkCaching);
    checkCaching();
});