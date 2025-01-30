<?php
/**
 * Файл теста рендерера поля даты уведомления
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * Класс теста рендерера поля даты уведомления
 */
#[CoversClass(DateNotificationFieldRenderer::class)]
class DateNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = DateNotificationFieldRenderer::class;

    const DATATYPE = 'date';

    public static function getValueHTMLDataProvider()
    {
        return [
            ['2021-03-17', false, false, '17.03.2021'],
            ['0000-00-00', false, false, ''],
        ];
    }
}
