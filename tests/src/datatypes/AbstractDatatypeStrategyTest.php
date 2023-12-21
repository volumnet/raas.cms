<?php
/**
 * Абстрактный класс проверки класса DatatypeStrategy
 */
namespace RAAS\CMS;

abstract class AbstractDatatypeStrategyTest extends BaseTest
{
    /**
     * Провайдер данных для метода testValidate
     * @return array <pre><code>array<[
     *     array Данные поля
     *     mixed Проверяемое значение
     *     bool|string Ожидаемый результат (true или класс исключения)
     * ]></code></pre>
     */
    public function validateDataProvider(): array
    {
        return [];
    }

    /**
     * Проверка метода validate()
     * @dataProvider validateDataProvider
     * @param mixed $value Проверяемое значение
     * @param mixed $expected Ожидаемое значение
     */
    public function testValidate(array $fieldData, $value, $expected)
    {
        $field = new Field($fieldData);
        if ($expected !== true) {
            $this->expectException($expected);
        }

        $result = $field->datatypeStrategy->validate($value, $field);

        if ($expected == true) {
            $this->assertEquals($expected, $result);
        }
    }
}
