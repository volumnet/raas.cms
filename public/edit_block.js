jQuery(function($) {
    $('#material_type').change(function() {
        $('.jsMaterialTypeField').RAAS_getSelect('ajax.php?p=cms&action=material_fields&id=' + $(this).val(), {before: function(data) { return data.Set; }});
    })
});