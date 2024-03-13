<?php
/**
 * Файл теста рендерера поля e-mail уведомления
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера поля e-mail уведомления
 * @covers \RAAS\CMS\EmailNotificationFieldRenderer
 */
class EmailNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = EmailNotificationFieldRenderer::class;

    const DATATYPE = 'email';

    public function getValueHTMLDataProvider()
    {
        return [
            ['test@test.org', false, false, '<a href="mailto:test@test.org">test@test.org</a>'],
            ['test@test.org', false, true, 'test@test.org'],
        ];
    }
}
