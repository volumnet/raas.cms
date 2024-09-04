jQuery(function($) {
    window.setTimeout(() => {
        var checkDataTypedRows = function() {
            // Если выбрали select, radio или множественный checkbox, появляется выбор источника, для всех остальных исчезает
            if (($.inArray($('#datatype').val(), ['select', 'radio']) != -1) || 
                (
                    ($('#datatype').val() == 'checkbox') && 
                    ($('#multiple').prop('checked'))
                )
            ) {
                $('#source_type').removeAttr('disabled').attr('required', 'required');
                $('.control-group:has(#source_type)').fadeIn();
            } else {
                $('#source_type').attr('disabled', 'disabled').removeAttr('required');
                $('.control-group:has(#source_type)').fadeOut();
            }
            
            if (($.inArray($('#datatype').val(), ['select', 'radio', 'material']) != -1) || 
                (($('#datatype').val() == 'checkbox') && ($('#multiple').prop('checked')))
            ) {
                $('.control-group:has(#source_textarea)').fadeIn();
            } else {
                $('.control-group:has(#source_textarea)').fadeOut();
            }

            // Значение по умолчанию
            if ($.inArray($('#datatype').val(), ['file', 'image', 'material']) == -1) {
                $('#defval').removeAttr('disabled');
                $('.control-group:has(#defval)').fadeIn();
            } else {
                $('#defval').attr('disabled', 'disabled');
                $('.control-group:has(#defval)').fadeOut();
            }
            
            // Обработчики
            if ($.inArray($('#datatype').val(), ['file', 'image']) != -1) {
                $('#preprocessor_id, #postprocessor_id').removeAttr('disabled');
                $('.control-group:has(#preprocessor_id, #postprocessor_id)').fadeIn();
            } else {
                $('#preprocessor_id, #postprocessor_id').attr('disabled', 'disabled');
                $('.control-group:has(#preprocessor_id, #postprocessor_id)').fadeOut();
            }
            

            // Если выбрали number или range, появляются минимальное и максимальное значения, для всех остальных исчезают
            if ($.inArray($('#datatype').val(), ['number', 'range']) != -1) {
                $('#min_val, #max_val, #step').removeAttr('disabled').closest('.control-group').fadeIn();
            } else {
                $('#min_val, #max_val, #step').attr('disabled', 'disabled').closest('.control-group').fadeOut();
            }
            
            // Если выбрали radio, блокируется выбор multiple
            if (($.inArray($('#datatype').val(), ['radio']) != -1)) {
                $('#multiple').removeAttr('checked').attr('disabled', 'disabled').closest('.control-group').fadeOut();
            } else {
                $('#multiple').removeAttr('disabled').closest('.control-group').fadeIn();
            }
        
            // Показываем выбор источника в зависимости от типа
            $('#source_textarea, #source_dictionary').hide().attr('disabled', 'disabled').removeAttr('required');
            $('#source_file, #source_unit').attr('disabled', 'disabled').closest('.control-group').hide();
            $('#source_materials').hide().attr('disabled', 'disabled');

            switch ($('#datatype').val()) {
                case 'material':
                    $('#source_materials').fadeIn().removeAttr('disabled');
                    break;
                case 'number':
                case 'range':
                case 'text':
                    $('#source_unit').removeAttr('disabled', 'disabled').closest('.control-group').fadeIn();
                    break;
                case 'radio':
                case 'select':
                case 'checkbox':
                    if ($('#source_type').val() == 'dictionary') {
                        $('#source_dictionary').fadeIn().removeAttr('disabled').attr('required', 'required');
                    } else if ($('#multiple').is(':checked') || ($('#datatype').val() == 'select') || ($('#datatype').val() == 'radio')) {
                        $('#source_textarea').fadeIn().removeAttr('disabled').attr('required', 'required');
                    }
                    break;
                case 'file':
                    $('#source_file').removeAttr('disabled', 'disabled').closest('.control-group').fadeIn();
                    break;

            }

            // Шаблон ввода
            if ($.inArray($('#datatype').val(), ['text', 'tel', 'email', 'textarea', 'url']) != -1) {
                $('#pattern').removeAttr('disabled').closest('.control-group').fadeIn();
            } else {
                $('#pattern').attr('disabled', 'disabled').closest('.control-group').fadeOut();
            }
        };
        
        var checkSourceTypeHint = function() {
            $('.controls:has(#source_type) a[rel="popover"]').attr('data-content', $('#source_type option:selected').attr('data-hint')).popover('hide');
        };

        var checkStep = function() {
            var val = $('#step').val();
            val = val.replace(/,/, '.');
            val = val.replace(/[^\d\.]+/, '');
            val = parseFloat(val);
            if (isNaN(val) || !val) {
                val = 1;
            }
            $('#step').val(val);
            $('#min_val, #max_val').attr('step', val);
        }
        
        $('#datatype').on('change', checkDataTypedRows);
        
        $('#multiple').on('click', checkDataTypedRows);
        
        $('#source_type').on('change', function() {
            checkSourceTypeHint();
            checkDataTypedRows();
        });

        $('#step').on('change', checkStep);
        
        checkDataTypedRows();
        checkSourceTypeHint();
        checkStep();
    }, 0); // Чтобы успел отработать Vue
})