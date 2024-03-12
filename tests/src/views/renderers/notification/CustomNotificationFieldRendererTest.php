<?php
/**
 * Файл теста рендерера произвольного поля уведомления
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера произвольного поля уведомления
 */
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
    abstract public function getValueHTMLDataProvider();

    /**
     * Тест получения HTML для значения
     * @dataProvider getValueHTMLDataProvider
     * @param mixed $value Значение
     * @param bool $admin Рендеринг для администратора
     * @param bool $sms Рендеринг для SMS
     * @param string $search Искомый результат
     */
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
