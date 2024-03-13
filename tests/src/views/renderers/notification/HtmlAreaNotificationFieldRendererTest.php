<?php
/**
 * Файл теста рендерера HTML-поля уведомления
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера HTML-поля уведомления
 * @covers \RAAS\CMS\HtmlAreaNotificationFieldRenderer
 */
class HtmlAreaNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = HtmlAreaNotificationFieldRenderer::class;

    const DATATYPE = 'htmlarea';

    public function getValueHTMLDataProvider()
    {
        return [
            [
                "<div>aaa,<br>\nbbb</div>",
                false,
                false,
                "<div><div>aaa,<br>\nbbb</div></div>"
            ],
            [
                "<div>aaa,\nbbb</div>",
                false,
                true,
                "aaa,\nbbb"
            ],
        ];
    }
}
