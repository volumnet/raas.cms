<?php
/**
 * Файл теста рендерера поля даты/времени уведомления
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * Класс теста рендерера поля даты/времени уведомления
 */
#[CoversClass(DateTimeNotificationFieldRenderer::class)]
class DateTimeNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = DateTimeNotificationFieldRenderer::class;

    const DATATYPE = 'datetime-local';

    public static function getValueHTMLDataProvider()
    {
        return [
            ['2021-03-17 13:34:00', false, false, '17.03.2021 13:34:00'],
            ['0000-00-00 13:34:00', false, false, ''],
        ];
    }
}
