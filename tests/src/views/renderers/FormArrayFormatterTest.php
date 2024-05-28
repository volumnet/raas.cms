<?php
/**
 * Тест класса FormArrayFormatter
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Тест класса FormArrayFormatter
 * @covers RAAS\CMS\FormArrayFormatter
 */
class FormArrayFormatterTest extends BaseTest
{
    public static $tables = [
        'cms_fields',
        'cms_forms',
    ];

    /**
     * Провайдер данных для метода format()
     * @return array <pre><code>array<
     *     array Данные поля для форматирования
     *     bool Получать поля для администратора
     *     array <pre>array<(
     *         string[] URN атрибута => function (Form $form Поле): mixed Обработчик данных
     *     )|(
     *         int[] Индекс атрибута => string URN поля
     *     )> Массив дополнительных полей для отображения
     *     array Ожидаемое значение
     * ></code></pre>
     */
    public function formatDataProvider(): array
    {
        return [
            [
                [
                    'id' => 123,
                    'material_type' => 1,
                    'interface_id' => 111,
                    'create_feedback' => 1,
                    'signature' => 1,
                    'email' => 'info@volumnet.ru',
                    'someprop' => 1111,
                ],
                true,
                [
                    'aaa' => 123,
                    'bbb' => function ($field) {
                        return $field->interface_id;
                    },
                    'someprop',
                ],
                [
                    'id' => 123,
                    'material_type' => 1,
                    'interface_id' => 111,
                    'create_feedback' => true,
                    'signature' => true,
                    'email' => 'info@volumnet.ru',
                    'someprop' => 1111,
                    'aaa' => 123,
                    'bbb' => 111,
                    'fields' => [],
                ],
            ],
            [
                [
                    'id' => 123,
                    'material_type' => 1,
                    'interface_id' => 111,
                    'create_feedback' => 1,
                    'signature' => 1,
                    'email' => 'info@volumnet.ru',
                    'someprop' => 1111,
                ],
                false,
                [
                    'aaa' => 123,
                    'bbb' => function ($field) {
                        return $field->interface_id;
                    },
                    'someprop',
                ],
                [
                    'id' => 123,
                    'signature' => true,
                    'someprop' => 1111,
                    'aaa' => 123,
                    'bbb' => 111,
                    'fields' => [],
                ],
            ],
        ];
    }

    /**
     * Тест метода format()
     * @param array $formData Данные формы для форматирования
     * @param bool $getAdminFields Получать поля для администратора
     * @param array $with <pre>array<(
     *     string[] URN атрибута => function (Form $form Поле): mixed Обработчик данных
     * )|(
     *     int[] Индекс атрибута => string URN поля
     * )> Массив дополнительных полей для отображения
     * @param array $expected Ожидаемое значение
     * @dataProvider formatDataProvider
     */
    public function testFormat(array $formData, bool $getAdminFields, array $with, array $expected)
    {
        $form = new Form($formData);
        $formatter = new FormArrayFormatter($form, $getAdminFields);

        $result = $formatter->format($with);

        foreach ($expected as $key => $val) {
            $this->assertEquals($val, $result[$key] ?? null, 'Не совпадает ключ ' . $key);
            $expType = gettype($val);
            $resType = gettype($result[$key]);
            if (!(in_array($expType, ['integer', 'double']) && in_array($resType, ['integer', 'double']))) {
                $this->assertEquals($expType, $resType, 'Не совпадает тип ' . $key);
            }
        }
        $diff = array_diff_key($result, $expected);
        $this->assertEmpty($diff, 'Остались значения: ' . var_export($diff, true));
    }


    /**
     * Тест метода format - случай с полями
     */
    public function testFormatWithFields()
    {
        $form = new Form(1);
        $formatter = new FormArrayFormatter($form, true);
        $field = $form->fields['_description_'];
        $field->vis = 0;
        $field->commit();

        $result = $formatter->format([], ['aaa' => 'bbb']);

        $this->assertIsArray($result['fields']);
        $this->assertEquals('Ваше имя', $result['fields']['full_name']['name']);
        $this->assertEquals('bbb', $result['fields']['_description_']['aaa']);
        $this->assertEquals('Текст вопроса', $result['fields']['_description_']['name']);
        $this->assertEquals('bbb', $result['fields']['_description_']['aaa']);

        $field = $form->fields['_description_'];
        $field->vis = 1;
        $field->commit();
    }


    /**
     * Тест метода format - случай с полями без администраторских полей
     */
    public function testFormatWithFieldsNoAdmin()
    {
        $form = new Form(1);
        $formatter = new FormArrayFormatter($form, false);
        $field = $form->fields['_description_'];
        $field->vis = 0;
        $field->commit();

        $result = $formatter->format();

        $this->assertIsArray($result['fields']);
        $this->assertEquals('Ваше имя', $result['fields']['full_name']['name']);
        $this->assertNull($result['fields']['_description_'] ?? null);

        $field = $form->fields['_description_'];
        $field->vis = 1;
        $field->commit();
    }
}
