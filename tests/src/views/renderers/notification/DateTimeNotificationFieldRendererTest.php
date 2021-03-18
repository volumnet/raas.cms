<?php
/**
 * Файл теста рендерера поля даты/времени уведомления
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера поля даты/времени уведомления
 * @covers RAAS\CMS\DateTimeNotificationFieldRenderer
 */
class DateTimeNotificationFieldRendererTest extends CustomFormFieldRendererTest
{
    const CLASSNAME = DateTimeNotificationFieldRenderer::class;

    const DATATYPE = 'datetime-local';

    public function getValueHTMLDataProvider()
    {
        return [
            ['2021-03-17 13:34:00', false, false, '17.03.2021 13:34:00'],
            ['0000-00-00 13:34:00', false, false, ''],
        ];
    }
}
