<?php
/**
 * Файл теста рендерера флажка уведомления
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * Класс теста рендерера флажка уведомления
 */
#[CoversClass(CheckboxNotificationFieldRenderer::class)]
class CheckboxNotificationFieldRendererTest extends CustomNotificationFieldRendererTest
{
    const CLASSNAME = CheckboxNotificationFieldRenderer::class;

    const DATATYPE = 'date';

    public static function getValueHTMLDataProvider()
    {
        return [
            ['1', false, false, 'Да'],
            ['0', false, false, 'Нет'],
        ];
    }

    /**
     * Тест получения HTML-значения - случай с множественным полем
     */
    public function testGetValueHTMLWithMultiple()
    {
        $classname = static::CLASSNAME;
        $renderer = new $classname(new Form_Field([
            'type' => static::DATATYPE,
            'multiple' => 1,
        ]));

        $result = $renderer->getValueHTML('"aaa', false, false);

        $this->assertEquals('&quot;aaa', $result);
    }

    /**
     * Тест получения HTML-значения - случай с множественным полем и SMS
     */
    public function testGetValueHTMLWithMultipleAndSMS()
    {
        $classname = static::CLASSNAME;
        $renderer = new $classname(new Form_Field([
            'type' => static::DATATYPE,
            'multiple' => 1,
        ]));

        $result = $renderer->getValueHTML('"aaa', false, true);

        $this->assertEquals('"aaa', $result);
    }
}
