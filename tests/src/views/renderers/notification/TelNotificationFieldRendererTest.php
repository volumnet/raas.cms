<?php
/**
 * Файл теста рендерера поля телефона уведомления
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера поля телефона уведомления
 * @covers \RAAS\CMS\TelNotificationFieldRenderer
 */
class TelNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = TelNotificationFieldRenderer::class;

    const DATATYPE = 'tel';

    public function getValueHTMLDataProvider()
    {
        return [
            ['+7 999 000-00-00', false, false, '<a href="tel:%2B79990000000">+7 999 000-00-00</a>'],
            ['+7 999 000-00-00', false, true, '+7 999 000-00-00'],
        ];
    }
}
