<?php
/**
 * Файл теста рендерера URL-поля уведомления
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * Класс теста рендерера URL-поля уведомления
 */
#[CoversClass(URLNotificationFieldRenderer::class)]
class URLNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = URLNotificationFieldRenderer::class;

    const DATATYPE = 'url';

    public static function getValueHTMLDataProvider()
    {
        return [
            [
                '/aaa/bbb/ccc/',
                false,
                false,
                '<a href="http://localhost/aaa/bbb/ccc/">/aaa/bbb/ccc/</a>'
            ],
            [
                'https://localhost/aaa/bbb/ccc/',
                false,
                false,
                '<a href="https://localhost/aaa/bbb/ccc/">https://localhost/aaa/bbb/ccc/</a>'
            ],
            [
                '/aaa/bbb/ccc/',
                false,
                true,
                '/aaa/bbb/ccc/'
            ],
            [
                'https://localhost/aaa/bbb/ccc/',
                false,
                true,
                'https://localhost/aaa/bbb/ccc/'
            ],

        ];
    }
}
