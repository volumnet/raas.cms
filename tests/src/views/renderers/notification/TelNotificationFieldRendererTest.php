<?php
/**
 * Файл теста рендерера поля телефона уведомления
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * Класс теста рендерера поля телефона уведомления
 */
#[CoversClass(TelNotificationFieldRenderer::class)]
class TelNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = TelNotificationFieldRenderer::class;

    const DATATYPE = 'tel';

    public static function getValueHTMLDataProvider()
    {
        return [
            ['+7 999 000-00-00', false, false, '<a href="tel:%2B79990000000">+7 999 000-00-00</a>'],
            ['+7 999 000-00-00', false, true, '+7 999 000-00-00'],
        ];
    }
}
