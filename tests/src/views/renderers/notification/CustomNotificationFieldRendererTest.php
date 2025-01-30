<?php
/**
 * Файл теста рендерера произвольного поля уведомления
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Класс теста рендерера произвольного поля уведомления
 */
#[CoversClass(CustomNotificationFieldRenderer::class)]
abstract class CustomNotificationFieldRendererTest extends BaseTest
{
    /**
     * Класс рендерера поля
     */
    const CLASSNAME = NotificationFieldRenderer::class;

    /**
     * Тип данных поля
     */
    const DATATYPE = 'text';

    /**
     * Генератор данных для метода testGetValueHTML
     * @return array <pre><code>array<[
     *     mixed Значение
     *     bool Рендеринг для администратора
     *     bool Рендеринг для SMS
     *     string Искомый результат
     * ]></code></pre>
     */
    abstract public static function getValueHTMLDataProvider();

    /**
     * Тест получения HTML для значения
     * @param mixed $value Значение
     * @param bool $admin Рендеринг для администратора
     * @param bool $sms Рендеринг для SMS
     * @param string $search Искомый результат
     */
    #[DataProvider('getValueHTMLDataProvider')]
    public function testGetValueHTML($value, $admin, $sms, $search)
    {
        $classname = static::CLASSNAME;
        $renderer = new $classname(new Form_Field([
            'datatype' => static::DATATYPE,
        ]));

        $result = $renderer->getValueHTML($value, $admin, $sms);

        $this->assertEquals($search, $result);
    }
}
