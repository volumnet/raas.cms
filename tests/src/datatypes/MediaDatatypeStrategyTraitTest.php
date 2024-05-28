<?php
/**
 * Тест для класса MediaDatatypeStrategyTrait
 *
 * <pre><code>
 * Предустановленные типы данных:
 * <ФАЙЛ> => [
 *     'tmp_name' => string Путь к файлу,
 *     'name' => string Названия файлов,
 *     'type' => string MIME-типы файлов,
 * ]
 * </code></pre>
 */
namespace RAAS\CMS;

use stdClass;
use Exception;
use InvalidArgumentException;
use SOME\BaseTest;
use RAAS\Application;
use RAAS\Attachment;
use RAAS\DatatypeStrategy;

/**
 * Класс теста класса MediaDatatypeStrategyTrait
 * @covers \RAAS\CMS\MediaDatatypeStrategyTrait
 */
class MediaDatatypeStrategyTraitTest extends BaseTest
{
    public static $tables = [
        'attachments',
        'cms_templates',
    ];

    /**
     * Провайдер данных для метода testGetFilesData
     * @return array <pre><code>[
     *     array Данные поля,
     *     [
     *         'tmp_name' => string|array<string|рекурсивно> Пути к файлам,
     *         'name' => string|array<string|рекурсивно> Названия файлов,
     *         'type' => string|array<string|рекурсивно> MIME-типы файлов,
     *     ] Данные файла(ов),
     *     array $_POST-данные
     *     bool Использовать мета-данные
     *     bool Приводить результат к массиву
     *     <ФАЙЛ>|array<string[]|int[] Ключ массива => <ФАЙЛ>|рекурсивно> Ожидаемое значение
     * ]</code></pre>
     */
    public function getFilesDataDataProvider(): array
    {
        return [
            [
                ['urn' => 'test', 'multiple' => false],
                ['test' => [
                    'tmp_name' => 'tmpname',
                    'name' => 'filename',
                    'type' => 'filetype',
                ]],
                [
                    'test@vis' => '1',
                    'test@name' => 'Test',
                    'test@description' => 'Description',
                    'test@attachment' => '123',
                ],
                true,
                true,
                [[
                    'tmp_name' => 'tmpname',
                    'name' => 'filename',
                    'type' => 'filetype',
                    'meta' => [
                        'vis' => true,
                        'attachment' => '123',
                        'name' => 'Test',
                        'description' => 'Description',
                    ],
                ]],
            ],
            [
                ['urn' => 'test', 'multiple' => false],
                ['test' => [
                    'tmp_name' => 'tmpname',
                    'name' => 'filename',
                    'type' => 'filetype',
                ]],
                [],
                false,
                false,
                [
                    'tmp_name' => 'tmpname',
                    'name' => 'filename',
                    'type' => 'filetype',
                ],
            ],
            [
                ['urn' => 'test', 'multiple' => false],
                ['test' => [
                    'tmp_name' => ['tmpname1', 'tmpname2', 'tmpname3'],
                    'name' => ['filename1', 'filename2', 'filename3'],
                    'type' => ['filetype1', 'filetype2', 'filetype3'],
                ]],
                [],
                false,
                false,
                [
                    [
                        'tmp_name' => 'tmpname1',
                        'name' => 'filename1',
                        'type' => 'filetype1',
                    ],
                    [
                        'tmp_name' => 'tmpname2',
                        'name' => 'filename2',
                        'type' => 'filetype2',
                    ],
                    [
                        'tmp_name' => 'tmpname3',
                        'name' => 'filename3',
                        'type' => 'filetype3',
                    ],
                ],
            ],
            [
                ['urn' => 'test', 'multiple' => false],
                ['test' => [
                    'tmp_name' => ['aaa' => ['bbb' => ['ccc' => 'tmpname']]],
                    'name' => ['aaa' => ['bbb' => ['ccc' => 'filename']]],
                    'type' => ['aaa' => ['bbb' => ['ccc' => 'filetype']]],
                ]],
                [],
                false,
                false,
                [
                    'aaa' => [
                        'bbb' => [
                            'ccc' => [
                                'tmp_name' => 'tmpname',
                                'name' => 'filename',
                                'type' => 'filetype',
                            ],
                        ],
                    ],
                ],
            ],

            [
                ['urn' => 'test', 'multiple' => false],
                ['test' => [
                    'tmp_name' => 'tmpname',
                    'name' => 'filename',
                    'type' => 'filetype',
                ]],
                [],
                false,
                true,
                [[
                    'tmp_name' => 'tmpname',
                    'name' => 'filename',
                    'type' => 'filetype',
                ]],
            ],
            [
                ['urn' => 'test', 'multiple' => false],
                ['test' => [
                    'tmp_name' => ['tmpname1', 'tmpname2', 'tmpname3'],
                    'name' => ['filename1', 'filename2', 'filename3'],
                    'type' => ['filetype1', 'filetype2', 'filetype3'],
                ]],
                [],
                false,
                true,
                [
                    [
                        'tmp_name' => 'tmpname1',
                        'name' => 'filename1',
                        'type' => 'filetype1',
                    ],
                    [
                        'tmp_name' => 'tmpname2',
                        'name' => 'filename2',
                        'type' => 'filetype2',
                    ],
                    [
                        'tmp_name' => 'tmpname3',
                        'name' => 'filename3',
                        'type' => 'filetype3',
                    ],
                ],
            ],
            [
                ['urn' => 'test', 'multiple' => false],
                ['test' => [
                    'tmp_name' => ['aaa' => ['bbb' => ['ccc' => 'tmpname']]],
                    'name' => ['aaa' => ['bbb' => ['ccc' => 'filename']]],
                    'type' => ['aaa' => ['bbb' => ['ccc' => 'filetype']]],
                ]],
                ['test' => ['aaa' => ['bbb' => ['ccc' => '123']]]],
                false,
                true,
                [
                    'aaa' => [
                        'bbb' => [
                            'ccc' => [
                                'tmp_name' => 'tmpname',
                                'name' => 'filename',
                                'type' => 'filetype',
                            ],
                        ],
                    ],
                ],
            ],
            [
                ['urn' => 'test', 'multiple' => false],
                ['test' => [
                    'tmp_name' => ['aaa' => ['bbb' => ['ccc' => 'tmpname']]],
                    'name' => ['aaa' => ['bbb' => ['ccc' => 'filename']]],
                    'type' => ['aaa' => ['bbb' => ['ccc' => 'filetype']]],
                ]],
                [
                    'test@vis' => ['aaa' => ['bbb' => ['ccc' => '1']]],
                    'test@name' => ['aaa' => ['bbb' => ['ccc' => 'Test file']]],
                    'test@description' => ['aaa' => ['bbb' => ['ccc' => 'Test file description']]],
                    'test@attachment' => ['aaa' => ['bbb' => ['ccc' => '123']]],
                ],
                true,
                true,
                [
                    'aaa' => [
                        'bbb' => [
                            'ccc' => [
                                'tmp_name' => 'tmpname',
                                'name' => 'filename',
                                'type' => 'filetype',
                                'meta' => [
                                    'vis' => true,
                                    'name' => 'Test file',
                                    'description' => 'Test file description',
                                    'attachment' => 123,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                ['urn' => 'test', 'multiple' => true],
                ['test' => [
                    'tmp_name' => 'tmpname',
                    'name' => 'filename',
                    'type' => 'filetype',
                ]],
                [],
                false,
                false,
                [
                    'tmp_name' => 'tmpname',
                    'name' => 'filename',
                    'type' => 'filetype',
                ],
            ],
            [
                ['urn' => 'test', 'multiple' => true],
                ['test' => [
                    'tmp_name' => ['tmpname1', 'tmpname2', 'tmpname3'],
                    'name' => ['filename1', 'filename2', 'filename3'],
                    'type' => ['filetype1', 'filetype2', 'filetype3'],
                ]],
                [],
                false,
                false,
                [
                    [
                        'tmp_name' => 'tmpname1',
                        'name' => 'filename1',
                        'type' => 'filetype1',
                    ],
                    [
                        'tmp_name' => 'tmpname2',
                        'name' => 'filename2',
                        'type' => 'filetype2',
                    ],
                    [
                        'tmp_name' => 'tmpname3',
                        'name' => 'filename3',
                        'type' => 'filetype3',
                    ],
                ],
            ],
            [
                ['urn' => 'test', 'multiple' => true],
                ['test' => [
                    'tmp_name' => ['aaa' => ['bbb' => ['ccc' => 'tmpname']]],
                    'name' => ['aaa' => ['bbb' => ['ccc' => 'filename']]],
                    'type' => ['aaa' => ['bbb' => ['ccc' => 'filetype']]],
                ]],
                [],
                false,
                false,
                [
                    'aaa' => [
                        'bbb' => [
                            'ccc' => [
                                'tmp_name' => 'tmpname',
                                'name' => 'filename',
                                'type' => 'filetype',
                            ],
                        ],
                    ],
                ],
            ],

            [
                ['urn' => 'test', 'multiple' => true],
                ['test' => [
                    'tmp_name' => 'tmpname',
                    'name' => 'filename',
                    'type' => 'filetype',
                ]],
                [],
                false,
                true,
                [[
                    'tmp_name' => 'tmpname',
                    'name' => 'filename',
                    'type' => 'filetype',
                ]],
            ],
            [
                ['urn' => 'test', 'multiple' => true],
                ['test' => [
                    'tmp_name' => ['tmpname1', 'tmpname2', 'tmpname3'],
                    'name' => ['filename1', 'filename2', 'filename3'],
                    'type' => ['filetype1', 'filetype2', 'filetype3'],
                ]],
                [],
                false,
                true,
                [
                    [
                        'tmp_name' => 'tmpname1',
                        'name' => 'filename1',
                        'type' => 'filetype1',
                    ],
                    [
                        'tmp_name' => 'tmpname2',
                        'name' => 'filename2',
                        'type' => 'filetype2',
                    ],
                    [
                        'tmp_name' => 'tmpname3',
                        'name' => 'filename3',
                        'type' => 'filetype3',
                    ],
                ],
            ],
            [
                ['urn' => 'test', 'multiple' => true],
                ['test' => [
                    'tmp_name' => ['aaa' => ['bbb' => ['ccc' => 'tmpname']]],
                    'name' => ['aaa' => ['bbb' => ['ccc' => 'filename']]],
                    'type' => ['aaa' => ['bbb' => ['ccc' => 'filetype']]],
                ]],
                [],
                false,
                true,
                [
                    'aaa' => [
                        'bbb' => [
                            'ccc' => [
                                'tmp_name' => 'tmpname',
                                'name' => 'filename',
                                'type' => 'filetype',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }


    /**
     * Проверка метода getFilesData()
     * @dataProvider getFilesDataDataProvider
     * @param array $fieldData Данные поля,
     * @param bool $forceArray Приводить результат к массиву
     * @param array $filesData <pre><code>[
     *     'tmp_name' => string|array<string|рекурсивно> Пути к файлам,
     *     'name' => string|array<string|рекурсивно> Названия файлов,
     *     'type' => string|array<string|рекурсивно> MIME-типы файлов,
     * ]</code></pre> Данные файла(ов),
     * @param array $expected <pre><code>
     *     <ФАЙЛ>|array<string[]|int[] Ключ массива => <ФАЙЛ>|рекурсивно>
     * </code></pre> Ожидаемое значение
     */
    public function testGetFilesData(
        array $fieldData,
        array $filesData,
        array $postData,
        bool $useMetaData,
        bool $forceArray,
        array $expected
    ) {
        $field = new Field(array_merge(['urn' => 'test', 'type' => 'file'], $fieldData));
        $strategy = DatatypeStrategy::spawn('cms.file');
        $oldFiles = $_FILES;
        $oldPost = $_POST;
        $_FILES = $filesData;
        $_POST = $postData;

        $result = $strategy->getFilesData($field, $forceArray, $useMetaData);

        $this->assertEquals($expected, $result);

        $_FILES = $oldFiles;
        $_POST = $oldPost;
    }

    /**
     * Провайдер данных для метода testExport
     * @return array <pre><code>array<[
     *     mixed Входное значение,
     *     mixed Ожидаемое значение
     *     string? Ожидается исключение класса
     * ]></code></pre>
     */
    public function exportDataProvider(): array
    {
        static::installTables();
        return [
            [
                new Attachment(['id' => 1, 'vis' => true, 'name' => 'aaa', 'description' => 'AAA description']),
                ['vis' => true, 'name' => 'aaa', 'description' => 'AAA description', 'attachment' => 1],
            ],
            ['abc', null, InvalidArgumentException::class],
        ];
    }


    /**
     * Проверка метода export()
     * @dataProvider exportDataProvider
     * @param mixed $inputValue Входное значение
     * @param mixed $expected Ожидаемое значение
     * @param string $expectedException Ожидается исключение класса
     */
    public function testExport($inputValue, $expected, $expectedException = null)
    {
        $strategy = DatatypeStrategy::spawn('cms.file');

        $this->assertInstanceOf(FileDatatypeStrategy::class, $strategy);

        if ($expectedException) {
            $this->expectException($expectedException);
        }

        $result = $strategy->export($inputValue);

        if (!$expectedException) {
            $this->assertEquals(json_encode($expected), $result);
        }
    }


    /**
     * Проверка метода import()
     */
    public function testImport()
    {
        $attachment = new Attachment();
        $attachment->filename = 'aaa.txt';
        $attachment->touchFile = true;
        $attachment->commit();
        $attachmentId = (int)$attachment->id;
        $json = json_encode([
            'vis' => true,
            'name' => 'Test',
            'description' => 'Test description',
            'attachment' => $attachmentId,
        ]);

        $strategy = DatatypeStrategy::spawn('cms.file');

        $result = $strategy->import($json);

        $this->assertInstanceOf(Attachment::class, $result);
        $this->assertEquals($attachmentId, $result->id);
        $this->assertTrue($result->vis);
        $this->assertEquals('Test', $result->name);
        $this->assertEquals('Test description', $result->description);

        Attachment::delete($attachment);
    }



    /**
     * Проверка метода import()
     */
    public function testBatchImportAttachmentsIds()
    {
        $strategy = DatatypeStrategy::spawn('cms.file');

        $this->assertInstanceOf(FileDatatypeStrategy::class, $strategy);

        $result = $strategy->batchImportAttachmentsIds([
            json_encode(['vis' => true, 'name' => 'Test', 'description' => 'Test description', 'attachment' => 1]),
            json_encode(['vis' => true, 'name' => 'Test2', 'description' => 'Test description2', 'attachment' => 2]),
            'aaa',
            json_encode(['vis' => true, 'name' => 'Test3', 'description' => 'Test description3', 'attachment' => 3]),
            json_encode(['vis' => true, 'name' => 'Test4', 'description' => 'Test description4', 'attachment' => 2]),
            json_encode(['vis' => true, 'name' => 'Test5', 'description' => 'Test description5', 'attachment' => 1]),
            'bbb' => json_encode(['vis' => true, 'name' => 'Test6', 'description' => 'Test description6', 'attachment' => 4]),
            json_encode(['vis' => true, 'name' => 'Test7', 'description' => 'Test description7', 'attachment' => 4]),
        ]);

        $this->assertEquals([1, 2, 3, 4], $result);
    }

    /**
     * Проверка метода import()
     */
    public function testBatchImport()
    {
        $attachment1 = new Attachment();
        $attachment1->filename = 'aaa.txt';
        $attachment1->touchFile = true;
        $attachment1->commit();
        $attachment1Id = (int)$attachment1->id;
        $attachment2 = new Attachment();
        $attachment2->filename = 'bbb.txt';
        $attachment2->touchFile = true;
        $attachment2->commit();
        $attachment2Id = (int)$attachment2->id;
        $attachment3 = new Attachment();
        $attachment3->filename = 'ccc.txt';
        $attachment3->touchFile = true;
        $attachment3->commit();
        $attachment3Id = (int)$attachment3->id;
        $values = [
            json_encode(['vis' => true, 'name' => 'Test1', 'description' => 'Description1', 'attachment' => $attachment1Id]),
            json_encode(['vis' => true, 'name' => 'Test2', 'description' => 'Description2', 'attachment' => $attachment2Id]),
            json_encode(['vis' => true, 'name' => 'Test3', 'description' => 'Description3', 'attachment' => $attachment3Id]),
        ];

        $strategy = DatatypeStrategy::spawn('cms.file');

        $result = $strategy->batchImport($values);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertInstanceOf(Attachment::class, $result[0]);
        $this->assertTrue($result[0]->vis);
        $this->assertEquals('Test1', $result[0]->name);
        $this->assertEquals('Description1', $result[0]->description);
        $this->assertEquals($attachment1Id, $result[0]->id);
        $this->assertInstanceOf(Attachment::class, $result[1]);
        $this->assertTrue($result[1]->vis);
        $this->assertEquals('Test2', $result[1]->name);
        $this->assertEquals('Description2', $result[1]->description);
        $this->assertEquals($attachment2Id, $result[1]->id);
        $this->assertInstanceOf(Attachment::class, $result[2]);
        $this->assertTrue($result[2]->vis);
        $this->assertEquals('Test3', $result[2]->name);
        $this->assertEquals('Description3', $result[2]->description);
        $this->assertEquals($attachment3Id, $result[2]->id);

        Attachment::delete($attachment1);
        Attachment::delete($attachment2);
        Attachment::delete($attachment3);
    }


    /**
     * Проверка метода import() с пустым значением
     */
    public function testImportWithEmpty()
    {
        $strategy = DatatypeStrategy::spawn('cms.file');

        $result = $strategy->import('');

        $this->assertNull($result);
    }
}
