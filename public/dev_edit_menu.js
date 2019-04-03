jQuery(function($) {
    var pid = parseInt($('#pid').val());
    // Если не установлена страница, то скрываем поле уровня наследования
    if (!parseInt($('#page_id').val())) {
        $('#inherit').attr('disabled', 'disabled')
            .closest('div.control-group')
            .hide();
    }
    
    // Если установлена страница или корневое меню, скрываем поле адреса ссылки
    if ((parseInt($('#page_id').val()) > 0) || !pid) {
        $('#url').attr('disabled', 'disabled')
            .closest('div.control-group')
            .hide();
    }

    // При изменении страницы
    $('body').on('change', '#page_id', function() {
        // Установим адрес
        $('#url').val($(this).find('option:selected').attr('data-src'));
        
        // Отображаем поле уровня наследования, только если выбрана страница
        if (parseInt($(this).val()) > 0) {
            $('#inherit').removeAttr('disabled')
                .closest('div.control-group')
                .fadeIn();
        } else {
            $('#inherit').attr('disabled', 'disabled')
                .closest('div.control-group')
                .fadeOut();
        }

        if (pid) {
            // У дочерних установим название ссылки по названию страницы
            var name = $(this).find('option:selected').text().replace(/(^\s+)|(\s+$)/gi, '');
            $('#name').val(name);
            
            // Если установлена страница, скроем адрес ссылки
            if (parseInt($(this).val()) > 0) {
                $('#url').attr('disabled', 'disabled')
                    .closest('div.control-group')
                    .fadeOut();
            } else {
                $('#url').removeAttr('disabled')
                    .closest('div.control-group')
                    .fadeIn();
            }
        }
    });


    // Меняем список страниц по домену
    $('#domain_id').on('change', function () {
        var domainId = $(this).val();
        var url = 'ajax.php?p=cms&action=get_menu_domain_pages' 
                + (domainId ? '&domain_id=' + domainId : '');
        $('#page_id').RAAS_getSelect(
            url, 
            { 
                before: function (data) { 
                    console.log(data);
                    return data.Set; 
                },
                after: function () {
                    $(this).trigger('change');
                }
            },
        )
    })
})