<?php
/**
 * Файл теста рендерера поля e-mail уведомления
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * Класс теста рендерера поля e-mail уведомления
 */
#[CoversClass(EmailNotificationFieldRenderer::class)]
class EmailNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = EmailNotificationFieldRenderer::class;

    const DATATYPE = 'email';

    public static function getValueHTMLDataProvider()
    {
        return [
            ['test@test.org', false, false, '<a href="mailto:test@test.org">test@test.org</a>'],
            ['test@test.org', false, true, 'test@test.org'],
        ];
    }
}
