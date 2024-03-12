<?php
/**
 * Файл теста рендерера URL-поля уведомления
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера URL-поля уведомления
 * @covers RAAS\CMS\URLNotificationFieldRenderer
 */
class URLNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = URLNotificationFieldRenderer::class;

    const DATATYPE = 'url';

    public function getValueHTMLDataProvider()
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
