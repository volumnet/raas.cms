<?php
/**
 * Тест класса FieldArrayFormatter
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Тест класса FieldArrayFormatter
 * @covers RAAS\CMS\FieldArrayFormatter
 */
class FieldArrayFormatterTest extends BaseTest
{
    public static $tables = [];

    /**
     * Провайдер данных для метода format()
     * @return array <pre><code>array<
     *     array Данные поля для форматирования
     *     bool Получать поля для администратора
     *     array <pre>array<(
     *         string[] URN атрибута => function (Field $field Поле): mixed Обработчик данных
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
                    'id' => 111,
                    'classname' => Form::class,
                    'pid' => 2,
                    'vis' => 1,
                    'required' => 1,
                    'multiple' => 0,
                    'show_in_table' => 0,
                    'pid' => 222,
                    'maxlength' => 5,
                    'datatype' => 'number',
                    'preprocessor_classname' => PreprocessorMock::class,
                    'preprocessor_id' => 333,
                    'postprocessor_classname' => PostprocessorMock::class,
                    'postprocessor_id' => 444,
                    'priority' => 10,
                    'min_val' => 50,
                    'max_val' => 10000,
                    'step' => 10,
                    'source' => '123',
                    'someprop' => 1111,
                ],
                true,
                [
                    'aaa' => 123,
                    'bbb' => function ($field) {
                        return $field->pid;
                    },
                    'someprop',
                ],
                [
                    'id' => 111,
                    'classname' => Form::class,
                    'vis' => true,
                    'required' => true,
                    'multiple' => false,
                    'show_in_table' => false,
                    'pid' => 222,
                    'maxlength' => 5,
                    'datatype' => 'number',
                    'preprocessor_classname' => PreprocessorMock::class,
                    'preprocessor_id' => 333,
                    'postprocessor_classname' => PostprocessorMock::class,
                    'postprocessor_id' => 444,
                    'priority' => 10,
                    'min_val' => 50,
                    'max_val' => 10000,
                    'step' => 10,
                    'aaa' => 123,
                    'source' => 123,
                    'someprop' => 1111,
                    'bbb' => 222,
                ],
            ],
            [
                [
                    'id' => 111,
                    'classname' => Form::class,
                    'pid' => 2,
                    'vis' => 1,
                    'required' => 1,
                    'multiple' => 0,
                    'show_in_table' => 0,
                    'pid' => 222,
                    'maxlength' => 5,
                    'datatype' => 'number',
                    'preprocessor_classname' => PreprocessorMock::class,
                    'preprocessor_id' => 333,
                    'postprocessor_classname' => PostprocessorMock::class,
                    'postprocessor_id' => 444,
                    'priority' => 10,
                    'min_val' => 50,
                    'max_val' => 10000,
                    'step' => 10,
                    'source' => '123',
                    'someprop' => 1111,
                    'source_type' => 'ini',
                ],
                false,
                [
                    'aaa' => 123,
                    'someprop',
                ],
                [
                    'id' => 111,
                    'vis' => true,
                    'required' => true,
                    'multiple' => false,
                    'pid' => 222,
                    'maxlength' => 5,
                    'datatype' => 'number',
                    'priority' => 10,
                    'min_val' => 50,
                    'max_val' => 10000,
                    'step' => 10,
                    'aaa' => 123,
                    'someprop' => 1111,
                ],
            ],
            [
                [
                    'datatype' => 'image',
                    'source' => 'JPG,GIF,PNG',
                ],
                true,
                [],
                [
                    'id' => null,
                    'datatype' => 'image',
                    'source' => 'JPG,GIF,PNG',
                    'accept' => '.jpg,.gif,.png',
                ],
            ],
            [
                [
                    'datatype' => 'select',
                    'source_type' => 'ini',
                    'source' => (
                        '1="aaa"' . "\n" .
                        '2="bbb"' . "\n" .
                        '3="ccc"' . "\n"
                    ),
                ],
                false,
                [],
                [
                    'id' => null,
                    'datatype' => 'select',
                    'stdSource' => [
                        ['value' => 1, 'name' => 'aaa'],
                        ['value' => 2, 'name' => 'bbb'],
                        ['value' => 3, 'name' => 'ccc'],
                    ],
                ],
            ],
        ];
    }


    /**
     * Тест метода format()
     * @param array $fieldData Данные поля для форматирования
     * @param bool $getAdminFields Получать поля для администратора
     * @param array $with <pre>array<(
     *     string[] URN атрибута => function (Field $field Поле): mixed Обработчик данных
     * )|(
     *     int[] Индекс атрибута => string URN поля
     * )> Массив дополнительных полей для отображения
     * @param array $expected Ожидаемое значение
     * @dataProvider formatDataProvider
     */
    public function testFormat(array $fieldData, bool $getAdminFields, array $with, array $expected)
    {
        $field = new Field($fieldData);
        $formatter = new FieldArrayFormatter($field, $getAdminFields);

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
     * Тест метода formatStdSource()
     */
    public function testFormatStdSource()
    {
        $source = [
            '1' => ['name' => 'aaa'],
            '2' => [
                'name' => 'bbb',
                'children' => [
                    '3' => ['name' => 'ccc'],
                ],
            ],
        ];
        $formatter = new FieldArrayFormatter(new Field(), false);

        $result = $formatter->formatStdSource($source);
        $expected = [
            ['value' => 1, 'name' => 'aaa'],
            ['value' => 2, 'name' => 'bbb', 'children' => [['value' => 3, 'name' => 'ccc']]],
        ];

        $this->assertEquals($expected, $result);
    }
}
