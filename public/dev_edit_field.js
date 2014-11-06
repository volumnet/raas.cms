jQuery(function($) {
    var checkDataTypedRows = function() {
        // Если выбрали select, radio или множественный checkbox, появляется выбор источника, для всех остальных исчезает
        if (($.inArray($('#datatype').val(), ['select', 'radio']) != -1) || (($('#datatype').val() == 'checkbox') && ($('#multiple').attr('checked')))) {
            $('#source_type').removeAttr('disabled').attr('required', 'required');
            $('.control-group:has(#source_type)').fadeIn();
        } else {
            $('#source_type').attr('disabled', 'disabled').removeAttr('required');
            $('.control-group:has(#source_type)').fadeOut();
        }
        
        if (($.inArray($('#datatype').val(), ['select', 'radio', 'material']) != -1) || (($('#datatype').val() == 'checkbox') && ($('#multiple').attr('checked')))) {
            $('.control-group:has(#source_textarea)').fadeIn();
        } else {
            $('.control-group:has(#source_textarea)').fadeOut();
        }
        

        // Если выбрали number или range, появляются минимальное и максимальное значения, для всех остальных исчезают
        if ($.inArray($('#datatype').val(), ['number', 'range']) != -1) {
            $('#min_val, #max_val').removeAttr('disabled').closest('.control-group').fadeIn();
        } else {
            $('#min_val, #max_val').attr('disabled', 'disabled').closest('.control-group').fadeOut();
        }
        
        // Если выбрали radio, блокируется выбор multiple
        if (($.inArray($('#datatype').val(), ['radio']) != -1)) {
            $('#multiple').removeAttr('checked').attr('disabled', 'disabled').closest('.control-group').fadeOut();
        } else {
            $('#multiple').removeAttr('disabled').closest('.control-group').fadeIn();
        }
    
        // Показываем выбор источника в зависимости от типа
        if ($('#datatype').val() == 'material') {
            $('#source_textarea').hide().attr('disabled', 'disabled').removeAttr('required');
            $('#source_dictionary').hide().attr('disabled', 'disabled').removeAttr('required');
            $('#source_materials').fadeIn().removeAttr('disabled');
        } else if ($('#source_type').val() == 'dictionary') {
            $('#source_textarea').hide().attr('disabled', 'disabled').removeAttr('required');
            $('#source_dictionary').fadeIn().removeAttr('disabled').attr('required', 'required');
            $('#source_materials').hide().attr('disabled', 'disabled');
        } else {
            $('#source_textarea').fadeIn().removeAttr('disabled').attr('required', 'required');
            $('#source_dictionary').hide().attr('disabled', 'disabled').removeAttr('required');
            $('#source_materials').hide().attr('disabled', 'disabled');
            
        }
    };
    
    var checkSourceTypeHint = function() {
        $('.controls:has(#source_type) a[rel="popover"]').attr('data-content', $('#source_type option:selected').attr('data-hint')).popover('hide');
    };
    
    $('#datatype').on('change', checkDataTypedRows);
    
    $('#multiple').on('click', checkDataTypedRows);
    
    $('#source_type').on('change', function() {
        checkSourceTypeHint();
        checkDataTypedRows();
    });
    
    checkDataTypedRows();
    checkSourceTypeHint();
});