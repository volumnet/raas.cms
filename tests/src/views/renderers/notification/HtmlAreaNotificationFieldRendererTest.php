<?php
/**
 * Файл теста рендерера HTML-поля уведомления
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * Класс теста рендерера HTML-поля уведомления
 */
#[CoversClass(HtmlAreaNotificationFieldRenderer::class)]
class HtmlAreaNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = HtmlAreaNotificationFieldRenderer::class;

    const DATATYPE = 'htmlarea';

    public static function getValueHTMLDataProvider()
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
