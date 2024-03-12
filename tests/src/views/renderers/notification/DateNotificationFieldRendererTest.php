<?php
/**
 * Файл теста рендерера поля даты уведомления
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера поля даты уведомления
 * @covers RAAS\CMS\DateNotificationFieldRenderer
 */
class DateNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = DateNotificationFieldRenderer::class;

    const DATATYPE = 'date';

    public function getValueHTMLDataProvider()
    {
        return [
            ['2021-03-17', false, false, '17.03.2021'],
            ['0000-00-00', false, false, ''],
        ];
    }
}
