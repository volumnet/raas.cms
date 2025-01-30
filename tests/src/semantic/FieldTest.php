<?php
/**
 * Тест класса Field
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;
use SOME\SOME;
use RAAS\Application;
use RAAS\Attachment;
use RAAS\SelectDatatypeStrategy;
use RAAS\TextDatatypeStrategy;

/**
 * Тест класса Field
 */
#[CoversClass(Field::class)]
class FieldTest extends BaseTest
{
    use WithTempTablesTrait;

    /**
     * Проблема от 2023-12-03 00:52 - логотип компании при установке обязательным полем не видит уже сохраненный логотип
     */
    public function testBehaviour20231204T0052Logo()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'image', 'required' => true]);
        $field->Owner = $item;
        $attachment = new Attachment();
        $attachment->upload = static::getResourcesDir() . '/nophoto.jpg';
        $attachment->copy = true;
        $attachment->parent = $field;
        $attachment->image = true;
        $attachment->commit();
        $attachmentId = $attachment->id;

        $sqlArr = ['pid' => 10, 'fid' => 1, 'fii' => 0, 'value' => '{"vis":true,"name":"","description":"","attachment":' . $attachmentId . '}'];
        Application::i()->SQL->add('tmp_data', $sqlArr);

        $oldPost = $_POST;
        $oldFiles = $_FILES;
        $_POST = ['test@vis' => '1', 'test@name' => '', 'test@description' => '', 'test@attachment' => $attachmentId];
        $_FILES = [];

        $isFilled = $field->Field->isFilled;

        $this->assertTrue($isFilled);

        $result = $field->Field->getErrors($field->Field);

        $this->assertEmpty($result);

        $_POST = $oldPost;
        $_FILES = $oldFiles;
        Attachment::delete($attachment);
    }


    /**
     * Проверка получения владельца
     */
    public function testOwner()
    {
        $item = new CustomEntity(['id' => 123, 'name' => 'Custom entity 123']);

        $field = new TestField();
        $field->Owner = $item;

        $this->assertInstanceOf(CustomEntity::class, $field->Owner);
        $this->assertEquals(123, $field->Owner->id);
        $this->assertEquals('Custom entity 123', $field->Owner->name);
    }


    /**
     * Проверка получения URN стратегии типа данных
     * @param string $datatype Тип данных
     * @param string $expected Ожидаемое значение
     */
    #[TestWith(['select', 'select'])]
    #[TestWith(['file', 'cms.file'])]
    #[TestWith(['material', 'cms.material'])]
    #[TestWith(['text', 'text'])]
    public function testDatatypeStrategyURN($datatype, $expected)
    {
        $field = new TestField(['datatype' => $datatype]);

        $result = $field->datatypeStrategyURN;

        $this->assertEquals($expected, $result);
    }


    /**
     * Проверка получения URN стратегии типа данных
     * @param string $datatype Тип данных
     * @param string $expected Ожидаемое значение
     */
    #[TestWith(['select', SelectDatatypeStrategy::class])]
    #[TestWith(['file', FileDatatypeStrategy::class])]
    #[TestWith(['material', MaterialDatatypeStrategy::class])]
    #[TestWith(['text', TextDatatypeStrategy::class])]
    public function testDatatypeStrategy($datatype, $expected)
    {
        $field = new TestField(['datatype' => $datatype]);

        $result = $field->datatypeStrategy;

        $this->assertInstanceOf($expected, $result);
    }


    /**
     * Проверка получения RAAS-поля
     * @param array $fieldData <pre><code>array<string[] => mixed></code></pre> Набор полей для установки CustomField,
     * @param array $expected <pre><code>array<string[] => mixed></code></pre> Набор полей для проверки RAAS-поля
     */
    #[TestWith([
        [
            'datatype' => 'number',
            'urn' => 'test',
            'name' => 'Test',
            'required' => '1',
            'maxlength' => '4',
            'defval' => '2',
            'min_val' => '1',
            'max_val' => '9999',
            'step' => '2',
            'placeholder' => 'Test field',
            'pattern' => '\d+',
        ],
        [
            'type' => 'number',
            'name' => 'test',
            'caption' => 'Test',
            'required' => true,
            'maxlength' => 4,
            'default' => '2',
            'min' => 1,
            'max' => 9999,
            'step' => 2,
            'placeholder' => 'Test field',
            'pattern' => '\d+',
            'export' => 'is_null',
        ],
    ])]
    #[TestWith([
        [
            'datatype' => 'file',
            'source' => 'image/jpeg, doc, docx, pdf, xls, xlsx',
        ],
        [
            'type' => 'file',
            'datatypeStrategyURN' => 'cms.file',
            'template' => 'cms/field.inc.php',
            'accept' => 'image/jpeg,.doc,.docx,.pdf,.xls,.xlsx',
        ],
    ])]
    public function testField(array $fieldData, array $expected)
    {
        $field = new TestField($fieldData);

        $result = $field->Field;

        foreach ($expected as $key => $val) {
            $this->assertEquals($val, $result->$key);
        }
        $this->assertEquals($field, $result->meta['CustomField']);
    }


    /**
     * Проверяет поле на ошибки (с использованием метода $this->Field->check)
     * @param array $fieldData Данные поля
     * @param array $postData POST-данные
     * @param bool $expected Ожидаемое значение
     */
    #[TestWith([
        ['urn' => 'test', 'name' => 'Тест', 'datatype' => 'text', 'required' => true],
        ['test' => 123],
        false,
    ])]
    #[TestWith([
        ['urn' => 'test', 'name' => 'Тест', 'datatype' => 'image', 'required' => true],
        [],
        false,
    ])]
    #[TestWith([
        ['urn' => 'test', 'name' => 'Тест', 'datatype' => 'image', 'required' => true],
        [
            'test@vis' => '1',
            'test@name' => '',
            'test@description' => '',
            'test@attachment' => '123',
        ],
        true,
    ])]
    #[TestWith([
        ['urn' => 'test', 'name' => 'Тест', 'datatype' => 'image', 'multiple' => true, 'pattern' => 'favicon'],
        [
            'test@vis' => ['1', '1'],
            'test@name' => ['', ''],
            'test@description' => ['', ''],
            'test@attachment' => ['123', '456'],
        ],
        true,
    ])]
    #[TestWith([
        ['urn' => 'test', 'name' => 'Тест', 'datatype' => 'image', 'multiple' => true, 'pattern' => 'favicon'],
        ['test' => ['aaa', 'bbb']],
        false,
    ])]
    public function testIsMediaFilled(array $fieldData, array $postData, bool $expected)
    {
        $field = new TestField($fieldData);
        $form = new Form(['Item' => new CustomEntity()]);
        $raasField = $field->Field;
        $checkFunction = $raasField->isMediaFilled;
        $oldPost = $_POST;
        $_POST = $postData;

        $result = $checkFunction($raasField);

        $this->assertEquals($expected, $result);

        $_POST = $oldPost;
    }


    /**
     * Тест коммита (случай со скалярными значениями)
     * @param array $fieldData Установочные данные для поля
     * @param mixed $value Проверяемое значение
     * @param array $expected Список ожидаемых значение
     */
    #[TestWith([
        ['datatype' => 'date', 'multiple' => true],
        ['2023-11-12', '1900-01-01', 'aaa', '', '0000-00-00', '0001-01-01'],
        ['2023-11-12', '1900-01-01', '0000-00-00', '0000-00-00', '0000-00-00', '0001-01-01']
    ])]
    #[TestWith([
        ['datatype' => 'datetime', 'multiple' => true],
        ['2023-11-12T16:05', '1900-01-01T10:00', 'aaa', '', '0001-01-01 12:30'],
        ['2023-11-12 16:05:00', '1900-01-01 10:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0001-01-01 12:30:00']
    ])]
    #[TestWith([['datatype' => 'year'], '2023-01-01', ['2023']])]
    #[TestWith([['datatype' => 'number'], '123,5', [123.5]])]
    #[TestWith([['datatype' => 'time'], '12:05', ['12:05:00']])]
    #[TestWith([['datatype' => 'time'], 'aaa', ['00:00:00']])]
    #[TestWith([['datatype' => 'time'], '', ['00:00:00']])]
    #[TestWith([['datatype' => 'month'], '2023-11', ['2023-11-01']])]
    #[TestWith([['datatype' => 'month'], 'aaa', ['0000-00-00']])]
    #[TestWith([['datatype' => 'month'], '0000-00', ['0000-00-00']])]
    #[TestWith([['datatype' => 'month'], '0001-01', ['0001-01-01']])]
    #[TestWith([['datatype' => 'week'], '2023-W01', ['2023-01-02']])] // Хз почему так, но вроде так
    #[TestWith([['datatype' => 'week'], '0000-W01', ['0000-01-03']])] // Хз почему так, но вроде так
    #[TestWith([['datatype' => 'week'], 'aaa', ['0000-00-00']])]
    #[TestWith([['datatype' => 'checkbox'], 'aaa ', ['aaa']])]
    #[TestWith([['datatype' => 'checkbox', 'multiple' => true], ['aaa', 'bbb '], ['aaa', 'bbb']])]
    public function testOnCommitWithScalar(array $fieldData, $value, $expected)
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(array_merge(['id' => 1, 'urn' => 'test'], $fieldData));
        $field->Owner = $item;
        $form = new Form(['Item' => $item]);
        $raasField = $field->Field;
        $onCommit = $raasField->oncommit;
        $oldPost = $_POST;
        $_POST = ['test' => $value];

        $onCommit($raasField);

        $sqlQuery = "SELECT value FROM tmp_data WHERE fid = 1 AND pid = 10 ORDER BY fii";
        $sqlResult = Application::i()->SQL->getcol($sqlQuery);

        $this->assertEquals($expected, $sqlResult);

        $_POST = $oldPost;
    }


    /**
     * Тест коммита (случай со медиа-полем)
     */
    public function testOnCommitWithMedia()
    {
        $preprocessor = new Snippet(['urn' => 'testpreprocessor', 'description' => '<' . '?php' . ' $GLOBALS["preprocessorData"] = $files; ']);
        $preprocessor->commit();
        $postprocessor = new Snippet(['urn' => 'testpostprocessor', 'description' => '<' . '?php' . ' $GLOBALS["postprocessorData"] = $files; ']);
        $postprocessor->commit();

        $item = new CustomEntity(['id' => 10]);
        $field = new TestField([
            'id' => 1,
            'urn' => 'test',
            'datatype' => 'image',
            'multiple' => true,
            'preprocessor_id' => $preprocessor->id,
            'postprocessor_id' => $postprocessor->id,
        ]);
        $field->Owner = $item;
        $form = new Form(['Item' => $item]);
        $raasField = $field->Field;
        $onCommit = $raasField->oncommit;
        $attachment = new Attachment();
        $attachment->filename = 'aaa.txt';
        $attachment->mime = 'text/plain';
        $attachment->touchFile = true;
        $attachment->parent = $field;
        $attachment->commit();
        $attachmentId = (int)$attachment->id;

        $oldPost = $_POST;
        $oldFiles = $_FILES;
        $_POST = [
            'test@vis' => ['1', '', '1', ''],
            'test@name' => ['Test 1', 'Test 2', 'Test 3', 'Test 4'],
            'test@description' => ['Description 1', 'Description 2', 'Description 3', 'Description 4'],
            'test@attachment' => [null, null, $attachmentId, null],
        ];
        $_FILES = ['test' => [
            'tmp_name' => [
                static::getResourcesDir() . '/nophoto.jpg',
                static::getResourcesDir() . '/notexist.jpg',
                null,
                static::getResourcesDir() . '/favicon.svg',
            ],
            'name' => [
                'nophoto.jpg',
                'notexist.jpg',
                null,
                'favicon.svg',
            ],
            'type' => [
                'image/jpeg',
                'image/jpeg',
                null,
                'application/xml+svg',
            ],

        ]];

        $onCommit($raasField);

        $sqlQuery = "SELECT value FROM tmp_data WHERE fid = 1 AND pid = 10 ORDER BY fii";
        $sqlResult = Application::i()->SQL->getcol($sqlQuery);

        $this->assertIsArray($sqlResult);
        $this->assertCount(3, $sqlResult);
        $this->assertNotEmpty($sqlResult[0]);
        $this->assertEquals(json_encode([
            'vis' => true,
            'name' => 'Test 3',
            'description' => 'Description 3',
            'attachment' => $attachmentId,
        ]), $sqlResult[1]);
        $this->assertNotEmpty($sqlResult[2]);

        $attachment1 = $raasField->datatypeStrategy->import($sqlResult[0]);
        $oldAttachment = $raasField->datatypeStrategy->import($sqlResult[1]);
        $attachment2 = $raasField->datatypeStrategy->import($sqlResult[2]);

        $this->assertEquals([
            static::getResourcesDir() . '/nophoto.jpg',
            static::getResourcesDir() . '/notexist.jpg',
            null,
            static::getResourcesDir() . '/favicon.svg',
        ], $GLOBALS['preprocessorData']);
        $this->assertEquals([
            $attachment1->file,
            $attachment2->file,
        ], $GLOBALS['postprocessorData']);

        $this->assertEquals('nophoto.jpg', $attachment1->filename);
        $this->assertEquals('image/jpeg', $attachment1->mime);
        $this->assertEquals(TestField::class, $attachment1->classname);
        $this->assertEquals(1, $attachment1->pid);
        $this->assertFileExists($attachment1->file);
        $this->assertTrue($attachment1->vis);
        $this->assertEquals('Test 1', $attachment1->name);
        $this->assertEquals('Description 1', $attachment1->description);
        $this->assertEquals(true, $attachment1->image); // Вообще строка "1" - берется из базы

        $this->assertEquals($attachmentId, $oldAttachment->id);
        $this->assertEquals('aaa.txt', $oldAttachment->filename);
        $this->assertEquals('text/plain', $oldAttachment->mime);
        $this->assertEquals(TestField::class, $oldAttachment->classname);
        $this->assertEquals(1, $oldAttachment->pid);
        $this->assertFileExists($oldAttachment->file);
        $this->assertTrue($oldAttachment->vis);
        $this->assertEquals('Test 3', $oldAttachment->name);
        $this->assertEquals('Description 3', $oldAttachment->description);
        $this->assertEquals(false, $oldAttachment->image); // Вообще строка "1" - берется из базы

        $this->assertFileExists($attachment1->file);
        $this->assertInstanceOf(Attachment::class, $attachment2);
        $this->assertEquals($attachment1->id + 1, $attachment2->id);
        $this->assertEquals('favicon.svg', $attachment2->filename);
        $this->assertEquals('application/xml+svg', $attachment2->mime);
        $this->assertEquals(TestField::class, $attachment2->classname);
        $this->assertEquals(1, $attachment2->pid);
        $this->assertFileExists($attachment2->file);
        $this->assertFalse($attachment2->vis);
        $this->assertEquals('Test 4', $attachment2->name);
        $this->assertEquals('Description 4', $attachment2->description);
        $this->assertEquals(true, $attachment2->image); // Вообще строка "1" - берется из базы

        $_POST = $oldPost;
        $_FILES = $oldFiles;

        Attachment::delete($attachment);
        Attachment::delete($attachment1);
        Attachment::delete($attachment2);
        Snippet::delete($preprocessor);
        Snippet::delete($postprocessor);
        unset($GLOBALS['preprocessorData'], $GLOBALS['postprocessorData']);
    }


    /**
     * Тест коммита (случай со медиа-полем и классами процессоров)
     */
    public function testOnCommitWithMediaWithProcessorsClassnames()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField([
            'id' => 1,
            'urn' => 'test',
            'datatype' => 'image',
            'multiple' => true,
            'preprocessor_classname' => PreprocessorMock::class,
            'postprocessor_classname' => PostprocessorMock::class,
        ]);
        $field->Owner = $item;
        $form = new Form(['Item' => $item]);
        $raasField = $field->Field;
        $onCommit = $raasField->oncommit;
        $attachment = new Attachment();
        $attachment->filename = 'aaa.txt';
        $attachment->mime = 'text/plain';
        $attachment->touchFile = true;
        $attachment->parent = $field;
        $attachment->commit();
        $attachmentId = (int)$attachment->id;

        $oldPost = $_POST;
        $oldFiles = $_FILES;
        $_POST = [
            'test@vis' => ['1', '', '1', ''],
            'test@name' => ['Test 1', 'Test 2', 'Test 3', 'Test 4'],
            'test@description' => ['Description 1', 'Description 2', 'Description 3', 'Description 4'],
            'test@attachment' => [null, null, $attachmentId, null],
        ];
        $_FILES = ['test' => [
            'tmp_name' => [
                static::getResourcesDir() . '/nophoto.jpg',
                static::getResourcesDir() . '/notexist.jpg',
                null,
                static::getResourcesDir() . '/favicon.svg',
            ],
            'name' => [
                'nophoto.jpg',
                'notexist.jpg',
                null,
                'favicon.svg',
            ],
            'type' => [
                'image/jpeg',
                'image/jpeg',
                null,
                'application/xml+svg',
            ],

        ]];

        $onCommit($raasField);

        $sqlQuery = "SELECT value FROM tmp_data WHERE fid = 1 AND pid = 10 ORDER BY fii";
        $sqlResult = Application::i()->SQL->getcol($sqlQuery);

        $this->assertIsArray($sqlResult);
        $this->assertCount(3, $sqlResult);
        $this->assertNotEmpty($sqlResult[0]);
        $this->assertEquals(json_encode([
            'vis' => true,
            'name' => 'Test 3',
            'description' => 'Description 3',
            'attachment' => $attachmentId,
        ]), $sqlResult[1]);
        $this->assertNotEmpty($sqlResult[2]);

        $attachment1 = $raasField->datatypeStrategy->import($sqlResult[0]);
        $oldAttachment = $raasField->datatypeStrategy->import($sqlResult[1]);
        $attachment2 = $raasField->datatypeStrategy->import($sqlResult[2]);

        $this->assertEquals([
            static::getResourcesDir() . '/nophoto.jpg',
            static::getResourcesDir() . '/notexist.jpg',
            null,
            static::getResourcesDir() . '/favicon.svg',
        ], $GLOBALS['preprocessorData']);
        $this->assertEquals([
            $attachment1->file,
            $attachment2->file,
        ], $GLOBALS['postprocessorData']);

        $_POST = $oldPost;
        $_FILES = $oldFiles;

        Attachment::delete($attachment);
        Attachment::delete($attachment1);
        Attachment::delete($attachment2);
        unset($GLOBALS['preprocessorData'], $GLOBALS['postprocessorData']);
    }


    /**
     * Записать данные для одного поля и одной сущности, подготовить кэш поля
     */
    public function seedOneField()
    {
        $sqlArr = [
            ['pid' => 10, 'fid' => 1, 'fii' => 0, 'value' => 'aaa'],
            ['pid' => 10, 'fid' => 1, 'fii' => 1, 'value' => 'bbb'],
            ['pid' => 10, 'fid' => 1, 'fii' => 2, 'value' => 'ccc'],
        ];
        Application::i()->SQL->add('tmp_data', $sqlArr);
        TestField::clearCache();
    }


    /**
     * Проверка метода getValue()
     */
    public function testGetValue()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text', 'multiple' => true]);
        $field->Owner = $item;
        $this->seedOneField();

        $result = $field->getValue(1);

        $this->assertEquals('bbb', $result);
        $this->assertEquals('bbb', TestField::$cache[10][1][1]);
    }


    /**
     * Проверка метода getValue() - случай с числовым значением
     */
    public function testGetValueWithNumber()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'number']);
        $field->Owner = $item;
        $sqlArr = [
            ['pid' => 10, 'fid' => 1, 'fii' => 0, 'value' => '1,42'],
        ];
        Application::i()->SQL->add('tmp_data', $sqlArr);
        TestField::clearCache();

        $result = $field->getValue();

        $this->assertEquals('1.42', $result);
        $this->assertEquals('1,42', TestField::$cache[10][1][0]);
    }


    /**
     * Проверка метода getValue()
     */
    public function testGetValueWithMedia()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'file', 'multiple' => true]);
        $field->Owner = $item;
        $attachment = new Attachment();
        $attachment->filename = 'aaa.txt';
        $attachment->touchFile = true;
        $attachment->commit();
        $attachmentId = (int)$attachment->id;
        $sqlArr = [
            [
                'pid' => 10,
                'fid' => 1,
                'fii' => 0,
                'value' => json_encode([
                    'vis' => 1,
                    'name' => 'Test',
                    'description' => 'Test description',
                    'attachment' => $attachmentId
                ]),
            ]
        ];
        Application::i()->SQL->add('tmp_data', $sqlArr);
        TestField::clearCache();

        $result = $field->getValue();

        $this->assertInstanceOf(Attachment::class, $result);
        $this->assertEquals($attachmentId, $result->id);
        $this->assertTrue($result->vis);
        $this->assertEquals('Test', $result->name);
        $this->assertEquals('Test description', $result->description);
        $this->assertEquals(json_encode([
            'vis' => 1,
            'name' => 'Test',
            'description' => 'Test description',
            'attachment' => $attachmentId
        ]), TestField::$cache[10][1][0]);
        Attachment::delete($attachment);
    }


    /**
     * Проверка метода getValue()
     */
    public function testGetValueWithMaterial()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'material', 'multiple' => true]);
        $field->Owner = $item;
        $material = new Material();
        $material->commit();
        $materialId = (int)$material->id;
        $sqlArr = [['pid' => 10, 'fid' => 1, 'fii' => 0, 'value' => $materialId]];
        Application::i()->SQL->add('tmp_data', $sqlArr);
        TestField::clearCache();

        $result = $field->getValue();

        $this->assertInstanceOf(Material::class, $result);
        $this->assertEquals($materialId, $result->id);
        $this->assertEquals($materialId, TestField::$cache[10][1][0]);
        Material::delete($material);
    }


    /**
     * Проверка метода getValue() (случай без владельца)
     */
    public function testGetValueWithoutOwner()
    {
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text', 'multiple' => true]);
        $this->seedOneField();

        $result = $field->getValue(1);

        $this->assertNull($result);
        $this->assertFalse(isset(TestField::$cache[10][1][1]));
    }


    /**
     * Проверка метода getValues()
     */
    public function testGetValues()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text']);
        $field->Owner = $item;
        $this->seedOneField();

        $result = $field->getValues();

        $this->assertEquals('aaa', $result);
        $this->assertEquals(['aaa', 'bbb', 'ccc'], TestField::$cache[10][1]);
    }


    /**
     * Проверка метода getValues() (отсутствие владельца)
     */
    public function testGetValuesWithoutOwner()
    {
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text']);
        $this->seedOneField();

        $result = $field->getValues();

        $this->assertNull($result);
        $this->assertFalse(isset(TestField::$cache[10][1]));
    }


    /**
     * Проверка метода getValues() (множественное поле)
     */
    public function testGetValuesWithMultiple()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text', 'multiple' => true]);
        $field->Owner = $item;
        $this->seedOneField();

        $result = $field->getValues();

        $this->assertEquals(['aaa', 'bbb', 'ccc'], $result);
        $this->assertEquals(['aaa', 'bbb', 'ccc'], TestField::$cache[10][1]);
    }


    /**
     * Проверка метода getValues() (принудительное приведение к массиву)
     */
    public function testGetValuesWithForceArray()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text']);
        $field->Owner = $item;
        $this->seedOneField();

        $result = $field->getValues(true);

        $this->assertEquals(['aaa', 'bbb', 'ccc'], $result);
        $this->assertEquals(['aaa', 'bbb', 'ccc'], TestField::$cache[10][1]);
    }


    /**
     * Проверка метода getValue() - случай с числовым значением
     */
    public function testGetValuesWithNumber()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'number']);
        $field->Owner = $item;
        $sqlArr = [
            ['pid' => 10, 'fid' => 1, 'fii' => 0, 'value' => '1,42'],
        ];
        Application::i()->SQL->add('tmp_data', $sqlArr);
        TestField::clearCache();

        $result = $field->getValues(true);

        $this->assertIsArray($result);
        $this->assertEquals('1.42', $result[0]);
        $this->assertEquals('1,42', TestField::$cache[10][1][0]);
    }


    /**
     * Проверка метода getValue()
     */
    public function testGetValuesWithMedia()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'file', 'multiple' => true]);
        $field->Owner = $item;
        $attachment = new Attachment();
        $attachment->filename = 'aaa.txt';
        $attachment->touchFile = true;
        $attachment->commit();
        $attachmentId = (int)$attachment->id;
        $sqlArr = [
            [
                'pid' => 10,
                'fid' => 1,
                'fii' => 0,
                'value' => json_encode([
                    'vis' => 1,
                    'name' => 'Test',
                    'description' => 'Test description',
                    'attachment' => $attachmentId
                ]),
            ]
        ];
        Application::i()->SQL->add('tmp_data', $sqlArr);
        TestField::clearCache();

        $result = $field->getValues();

        $this->assertIsArray($result);
        $this->assertInstanceOf(Attachment::class, $result[0]);
        $this->assertEquals($attachmentId, $result[0]->id);
        $this->assertTrue($result[0]->vis);
        $this->assertEquals('Test', $result[0]->name);
        $this->assertEquals('Test description', $result[0]->description);
        $this->assertEquals(json_encode([
            'vis' => 1,
            'name' => 'Test',
            'description' => 'Test description',
            'attachment' => $attachmentId
        ]), TestField::$cache[10][1][0]);
        Attachment::delete($attachment);
    }


    /**
     * Проверка метода getValue()
     */
    public function testGetValuesWithMaterial()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'material', 'multiple' => true]);
        $field->Owner = $item;
        $material = new Material();
        $material->commit();
        $materialId = (int)$material->id;
        $sqlArr = [['pid' => 10, 'fid' => 1, 'fii' => 0, 'value' => $materialId]];
        Application::i()->SQL->add('tmp_data', $sqlArr);
        TestField::clearCache();

        $result = $field->getValues();

        $this->assertIsArray($result);
        $this->assertInstanceOf(Material::class, $result[0]);
        $this->assertEquals($materialId, $result[0]->id);
        $this->assertEquals($materialId, TestField::$cache[10][1][0]);
        Material::delete($material);
    }


    /**
     * Проверка метода countValues()
     */
    public function testCountValues()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text']);
        $field->Owner = $item;
        $this->seedOneField();

        $result = $field->countValues();

        $this->assertEquals(3, $result);
    }


    /**
     * Проверка метода countValues() - случай без владельца
     */
    public function testCountValuesWithoutOwner()
    {
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text']);
        $this->seedOneField();

        $result = $field->countValues();

        $this->assertNull($result);
    }


    /**
     * Проверка метода setValue()
     */
    public function testSetValue()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text']);
        $field->Owner = $item;
        $this->seedOneField();

        $sqlQuery = "SELECT fii, value FROM tmp_data WHERE pid = 10 AND fid = 1 ORDER BY fii";
        $sqlResult = Application::i()->SQL->get($sqlQuery);

        $value = $field->getValue(1);

        $this->assertEquals('bbb', $value);
        $this->assertEquals([
            ['fii' => 0, 'value' => 'aaa'],
            ['fii' => 1, 'value' => 'bbb'],
            ['fii' => 2, 'value' => 'ccc'],
        ], $sqlResult);
        $this->assertEquals(['aaa', 'bbb', 'ccc'], TestField::$cache[10][1]);

        $result = $field->setValue('ddd', 1);

        $sqlQuery = "SELECT fii, value FROM tmp_data WHERE pid = 10 AND fid = 1 ORDER BY fii";
        $sqlResult = Application::i()->SQL->get($sqlQuery);

        $this->assertEquals('ddd', $result);
        $this->assertEquals([
            ['fii' => 0, 'value' => 'aaa'],
            ['fii' => 1, 'value' => 'ddd'],
            ['fii' => 2, 'value' => 'ccc'],
        ], $sqlResult);
        $this->assertEquals(['aaa', 'ddd', 'ccc'], TestField::$cache[10][1]);
    }


    /**
     * Проверка метода setValue() - случай без владельца
     */
    public function testSetValueWithoutOwner()
    {
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text']);
        $this->seedOneField();

        $result = $field->setValue(2);

        $this->assertNull($result);
    }


    /**
     * Проверка метода addValue()
     */
    public function testAddValue()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text']);
        $field->Owner = $item;
        $this->seedOneField();

        $sqlQuery = "SELECT fii, value FROM tmp_data WHERE pid = 10 AND fid = 1 ORDER BY fii";
        $sqlResult = Application::i()->SQL->get($sqlQuery);

        $value = $field->getValue(1);

        $this->assertEquals('bbb', $value);
        $this->assertEquals([
            ['fii' => 0, 'value' => 'aaa'],
            ['fii' => 1, 'value' => 'bbb'],
            ['fii' => 2, 'value' => 'ccc'],
        ], $sqlResult);
        $this->assertEquals(['aaa', 'bbb', 'ccc'], TestField::$cache[10][1]);

        $result = $field->addValue('ddd', 1);

        $sqlQuery = "SELECT fii, value FROM tmp_data WHERE pid = 10 AND fid = 1 ORDER BY fii";
        $sqlResult = Application::i()->SQL->get($sqlQuery);

        $this->assertEquals('ddd', $result);
        $this->assertEquals([
            ['fii' => 0, 'value' => 'aaa'],
            ['fii' => 1, 'value' => 'ddd'],
            ['fii' => 2, 'value' => 'bbb'],
            ['fii' => 3, 'value' => 'ccc'],
        ], $sqlResult);
        $this->assertEquals(['aaa', 'ddd', 'bbb', 'ccc'], TestField::$cache[10][1]);
    }


    /**
     * Проверка метода addValue() - случай без владельца
     */
    public function testAddValueWithoutOwner()
    {
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text']);
        $this->seedOneField();

        $result = $field->addValue(1);

        $this->assertNull($result);
    }


    /**
     * Проверка метода deleteValue()
     */
    public function testDeleteValue()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text']);
        $field->Owner = $item;
        $this->seedOneField();

        $sqlQuery = "SELECT fii, value FROM tmp_data WHERE pid = 10 AND fid = 1 ORDER BY fii";
        $sqlResult = Application::i()->SQL->get($sqlQuery);

        $field->getValues();

        $this->assertEquals([
            ['fii' => 0, 'value' => 'aaa'],
            ['fii' => 1, 'value' => 'bbb'],
            ['fii' => 2, 'value' => 'ccc'],
        ], $sqlResult);
        $this->assertEquals(['aaa', 'bbb', 'ccc'], TestField::$cache[10][1]);

        $field->deleteValue(1);

        $sqlQuery = "SELECT fii, value FROM tmp_data WHERE pid = 10 AND fid = 1 ORDER BY fii";
        $sqlResult = Application::i()->SQL->get($sqlQuery);

        $this->assertEquals([
            ['fii' => 0, 'value' => 'aaa'],
            ['fii' => 1, 'value' => 'ccc'],
        ], $sqlResult);
        $this->assertEquals(['aaa', 'ccc'], TestField::$cache[10][1]);
    }


    /**
     * Проверка метода deleteValue() - случай без владельца
     */
    public function testDeleteValueWithoutOwner()
    {
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text']);
        $this->seedOneField();

        $result = $field->deleteValue(1);

        $this->assertNull($result);
    }


    /**
     * Проверка метода deleteValues()
     */
    public function testDeleteValues()
    {
        $item = new CustomEntity(['id' => 10]);
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text']);
        $field->Owner = $item;
        $this->seedOneField();

        $sqlQuery = "SELECT fii, value FROM tmp_data WHERE pid = 10 AND fid = 1 ORDER BY fii";
        $sqlResult = Application::i()->SQL->get($sqlQuery);

        $field->getValues();

        $this->assertEquals([
            ['fii' => 0, 'value' => 'aaa'],
            ['fii' => 1, 'value' => 'bbb'],
            ['fii' => 2, 'value' => 'ccc'],
        ], $sqlResult);
        $this->assertEquals(['aaa', 'bbb', 'ccc'], TestField::$cache[10][1]);

        $field->deleteValues();

        $sqlQuery = "SELECT fii, value FROM tmp_data WHERE pid = 10 AND fid = 1 ORDER BY fii";
        $sqlResult = Application::i()->SQL->get($sqlQuery);

        $this->assertEmpty($sqlResult);
        $this->assertFalse(isset(TestField::$cache[10][1]));
    }


    public function testClearLostAttachments()
    {
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'image']);
        $attachment1 = new Attachment();
        $attachment1->filename = 'aaa.txt';
        $attachment1->touchFile = true;
        $attachment1->parent = $field;
        $attachment1->commit();
        $attachment1Id = (int)$attachment1->id;

        $attachment2 = new Attachment();
        $attachment2->filename = 'bbb.txt';
        $attachment2->touchFile = true;
        $attachment2->parent = $field;
        $attachment2->commit();
        $attachment2Id = (int)$attachment2->id;

        $attachment3 = new Attachment();
        $attachment3->filename = 'ccc.txt';
        $attachment3->touchFile = true;
        $attachment3->parent = $field;
        $attachment3->commit();
        $attachment3Id = (int)$attachment3->id;
        $sqlArr = [
            [
                'pid' => 10,
                'fid' => 1,
                'fii' => 0,
                'value' => json_encode([
                    'vis' => 1,
                    'name' => 'Test',
                    'description' => 'Test description',
                    'attachment' => $attachment3Id,
                ]),
            ],
        ];
        Application::i()->SQL->add('tmp_data', $sqlArr);
        TestField::clearCache();

        $ids = [$attachment1Id, $attachment2Id, $attachment3Id];
        $sqlQuery = "SELECT id FROM " . Attachment::_tablename() . " WHERE id IN (" . implode(", ", $ids) . ") ORDER BY id";
        $sqlResult = Application::i()->SQL->getcol($sqlQuery);

        $this->assertEquals($ids, $sqlResult);
        $this->assertFileExists($attachment1->file);
        $this->assertFileExists($attachment2->file);
        $this->assertFileExists($attachment3->file);

        $field->clearLostAttachments();

        $sqlResult = Application::i()->SQL->getcol($sqlQuery);

        $this->assertEquals([$attachment3Id], $sqlResult);
        $this->assertFileDoesNotExist($attachment1->file);
        $this->assertFileDoesNotExist($attachment2->file);
        $this->assertFileExists($attachment3->file);

        Attachment::delete($attachment3);
    }


    /**
     * Проверка метода inheritValues()
     */
    public function testInheritValues()
    {
        $sqlArr = [
            ['id' => 10, 'pid' => 0],
            ['id' => 20, 'pid' => 10],
            ['id' => 30, 'pid' => 20],
        ];
        Application::i()->SQL->add('tmp_entities', $sqlArr);
        $sqlArr = [
            ['pid' => 10, 'fid' => 1, 'fii' => 0, 'value' => 'aaa', 'inherited' => 0],
            ['pid' => 10, 'fid' => 1, 'fii' => 1, 'value' => 'bbb', 'inherited' => 0],
            ['pid' => 10, 'fid' => 1, 'fii' => 2, 'value' => 'ccc', 'inherited' => 0],
            ['pid' => 20, 'fid' => 1, 'fii' => 0, 'value' => 'ddd', 'inherited' => 0],
            ['pid' => 20, 'fid' => 1, 'fii' => 1, 'value' => 'eee', 'inherited' => 0],
            ['pid' => 20, 'fid' => 1, 'fii' => 2, 'value' => 'fff', 'inherited' => 0],
            ['pid' => 30, 'fid' => 1, 'fii' => 0, 'value' => 'ggg', 'inherited' => 0],
            ['pid' => 30, 'fid' => 1, 'fii' => 1, 'value' => 'hhh', 'inherited' => 0],
            ['pid' => 30, 'fid' => 1, 'fii' => 2, 'value' => 'jjj', 'inherited' => 0],
        ];
        Application::i()->SQL->add('tmp_data', $sqlArr);

        $field = new TestField(['id' => 1]);
        $entity = new CustomEntity(['id' => 10]);
        $field->Owner = $entity;

        $field->inheritValues();

        $sqlQuery = "SELECT pid, fid, fii, value, inherited FROM tmp_data ORDER BY pid, fii";
        $expected = [
            ['pid' => 10, 'fid' => 1, 'fii' => 0, 'value' => 'aaa', 'inherited' => 1],
            ['pid' => 10, 'fid' => 1, 'fii' => 1, 'value' => 'bbb', 'inherited' => 1],
            ['pid' => 10, 'fid' => 1, 'fii' => 2, 'value' => 'ccc', 'inherited' => 1],
            ['pid' => 20, 'fid' => 1, 'fii' => 0, 'value' => 'aaa', 'inherited' => 1],
            ['pid' => 20, 'fid' => 1, 'fii' => 1, 'value' => 'bbb', 'inherited' => 1],
            ['pid' => 20, 'fid' => 1, 'fii' => 2, 'value' => 'ccc', 'inherited' => 1],
            ['pid' => 30, 'fid' => 1, 'fii' => 0, 'value' => 'aaa', 'inherited' => 1],
            ['pid' => 30, 'fid' => 1, 'fii' => 1, 'value' => 'bbb', 'inherited' => 1],
            ['pid' => 30, 'fid' => 1, 'fii' => 2, 'value' => 'ccc', 'inherited' => 1],
        ];
        $sqlResult = Application::i()->SQL->get($sqlQuery);

        $this->assertEquals($expected, $sqlResult);
    }


    /**
     * Проверка метода deleteValues() - случай без владельца
     */
    public function testDeleteValuesWithoutOwner()
    {
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'text']);
        $this->seedOneField();

        $result = $field->deleteValues();

        $this->assertNull($result);
    }


    /**
     * Провайдер данных для метода testDoRich()
     * @return array <pre><code>array<[
     *     array Данные поля,
     *     mixed Ожидаемое значение,
     *     mixed? Проверяемое значение,
     *     array? Данные сущности
     * ]></code></pre>
     */
    public static function doRichDataProvider(): array
    {
        static::installTables();
        $attachment = new Attachment(['id' => 123]);
        return [
            [
                [
                    'id' => 1,
                    'urn' => 'test',
                    'datatype' => 'select',
                    'source_type' => 'ini',
                    'source' => 'aaa="Test AAA"' . "\n" .
                                'bbb="Test BBB"' . "\n" .
                                'ccc="Test CCC"',
                ],
                'Test AAA',
                null,
                ['id' => 10],
            ],
            [
                [
                    'id' => 1,
                    'urn' => 'test',
                    'datatype' => 'select',
                    'source_type' => 'ini',
                    'source' => 'aaa="Test AAA"' . "\n" .
                                'bbb="Test BBB"' . "\n" .
                                'ccc="Test CCC"',
                ],
                'Test BBB',
                'bbb',
            ],
            [
                [
                    'id' => 1,
                    'urn' => 'test',
                    'datatype' => 'file',
                    'source' => 'PNG, GIF',
                ],
                'bbb',
                'bbb',
            ],
            [
                [
                    'id' => 1,
                    'urn' => 'test',
                    'datatype' => 'checkbox',
                ],
                true,
                'bbb',
            ],
            [
                [
                    'id' => 1,
                    'urn' => 'test',
                    'datatype' => 'select',
                    'source_type' => 'ini',
                    'source' => 'aaa="Test AAA"' . "\n" .
                                'bbb="Test BBB"' . "\n" .
                                'ccc="Test CCC"',
                ],
                null,
                '',
            ],
            [
                [
                    'id' => 1,
                    'urn' => 'test',
                    'type' => 'image',
                ],
                $attachment,
                $attachment,
            ],
        ];
    }


    /**
     * Проверка метода doRich()
     * @param $fieldData  Данные поля,
     * @param $expected Ожидаемое значение,
     * @param $value  Проверяемое значение,
     * @param $itemData Данные сущности
     */
    #[DataProvider('doRichDataProvider')]
    public function testDoRich($fieldData, $expected, $value = null, $itemData = [])
    {
        $field = new TestField($fieldData);
        if ($itemData) {
            $item = new CustomEntity($itemData);
            $field->Owner = $item;
            $this->seedOneField();
        } else {
            TestField::clearCache();
        }

        $result = $field->doRich($value);

        $this->assertEquals($expected, $result);
    }


    /**
     * Проверка метода getRichValue()
     */
    public function testGetRichValue()
    {
        $field = new TestField([
            'id' => 1,
            'urn' => 'test',
            'datatype' => 'select',
            'source_type' => 'ini',
            'source' => 'aaa="Test AAA"' . "\n" .
                        'bbb="Test BBB"' . "\n" .
                        'ccc="Test CCC"',
        ]);
        $item = new CustomEntity(['id' => 10]);
        $field->Owner = $item;
        $this->seedOneField();

        $result = $field->getRichValue(1);

        $this->assertEquals('Test BBB', $result);
    }


    /**
     * Проверка метода getRichValues()
     * @param array $fieldData Данные поля
     * @param bool $forceArray Привести к массиву
     * @param mixed expected Ожидаемое значение
     */
    #[TestWith([
        [
            'id' => 1,
            'urn' => 'test',
            'datatype' => 'select',
            'source_type' => 'ini',
            'source' => 'aaa="Test AAA"' . "\n" .
                        'bbb="Test BBB"' . "\n" .
                        'ccc="Test CCC"',
        ],
        false,
        'Test AAA'
    ])]
    #[TestWith([
        [
            'id' => 1,
            'urn' => 'test',
            'datatype' => 'select',
            'source_type' => 'ini',
            'source' => 'aaa="Test AAA"' . "\n" .
                        'bbb="Test BBB"' . "\n" .
                        'ccc="Test CCC"',
        ],
        true,
        ['Test AAA', 'Test BBB', 'Test CCC']
    ])]
    #[TestWith([
        [
            'id' => 1,
            'multiple' => true,
            'urn' => 'test',
            'datatype' => 'select',
            'source_type' => 'ini',
            'source' => 'aaa="Test AAA"' . "\n" .
                        'bbb="Test BBB"' . "\n" .
                        'ccc="Test CCC"',
        ],
        false,
        ['Test AAA', 'Test BBB', 'Test CCC']
    ])]
    public function testGetRichValues(array $fieldData, bool $forceArray, $expected)
    {
        $field = new TestField($fieldData);
        $item = new CustomEntity(['id' => 10]);
        $field->Owner = $item;
        $this->seedOneField();

        $result = $field->getRichValues($forceArray);

        $this->assertEquals($expected, $result);
    }


    /**
     * Проверка метода getRichString()
     */
    public function testGetRichString()
    {
        $field = new TestField([
            'id' => 1,
            'multiple' => true,
            'urn' => 'test',
            'datatype' => 'select',
            'source_type' => 'ini',
            'source' => 'aaa="Test AAA"' . "\n" .
                        'bbb="Test BBB"' . "\n" .
                        'ccc="Test CCC"',
        ]);
        $item = new CustomEntity(['id' => 10]);
        $field->Owner = $item;
        $this->seedOneField();

        $result = $field->getRichString();

        $this->assertEquals('Test AAA, Test BBB, Test CCC', $result);
    }


    /**
     * Проверка метода getRichString() - случай с объектами
     */
    public function testGetRichStringWithObjects()
    {
        $field = new TestFieldMockObjectGetValues([
            'id' => 1,
            'multiple' => true,
            'urn' => 'test',
            'datatype' => 'image',
        ]);

        $result = $field->getRichString();

        $this->assertEquals('Entity 1, Entity 2, Entity 3', $result);
    }


    /**
     * Провайдер данных для метода testFromRich()
     * @return array <pre><code>array<[
     *     array Данные поля,
     *     mixed? Проверяемое значение,
     *     mixed Ожидаемое значение,
     * ]></code></pre>
     */
    public static function fromRichDataProvider(): array
    {
        static::installTables();
        $attachment = new Attachment(['id' => 123]);
        return [
            [
                [
                    'id' => 1,
                    'urn' => 'test',
                    'datatype' => 'select',
                    'source_type' => 'ini',
                    'source' => 'aaa="Test AAA"' . "\n" .
                                'bbb="Test BBB"' . "\n" .
                                'ccc="Test CCC"',
                ],
                'Test AAA',
                'aaa',
            ],
            [
                [
                    'id' => 1,
                    'urn' => 'test',
                    'datatype' => 'file',
                    'source' => 'PNG, GIF',
                ],
                'bbb',
                'bbb',
            ],
            [
                [
                    'id' => 1,
                    'urn' => 'test',
                    'datatype' => 'select',
                    'source_type' => 'ini',
                    'source' => 'aaa="Test AAA"' . "\n" .
                                'bbb="Test BBB"' . "\n" .
                                'ccc="Test CCC"',
                ],
                '',
                null,
            ],
            [
                [
                    'id' => 1,
                    'urn' => 'test',
                    'datatype' => 'checkbox',
                ],
                'Test BBB',
                true,
            ],
            [
                [
                    'id' => 1,
                    'urn' => 'test',
                    'type' => 'image',
                ],
                $attachment,
                $attachment,
            ],
        ];
    }


    /**
     * Проверка метода fromRich()
     * @param $fieldData  Данные поля,
     * @param $expected Ожидаемое значение,
     * @param $value  Проверяемое значение,
     * @param $itemData Данные сущности
     */
    #[DataProvider('fromRichDataProvider')]
    public function testFromRich($fieldData, $value, $expected)
    {
        $field = new TestField($fieldData);
        TestField::clearCache();

        $result = $field->fromRich($value);

        $this->assertEquals($expected, $result);
    }


    /**
     * Проверка метода parseCSV()
     */
    public function testParseCSV()
    {
        $source = "Category 1;cat1\n"
                . ";Category 11;cat11\n"
                . ";;Category 111;cat111\n"
                . ";;Category 112;cat112\n"
                . ";;Category 113;cat113\n"
                . ";Category 12;cat12\n"
                . ";Category 13;cat13\n"
                . "Category 2;cat2\n"
                . "Category 3;cat3\n";
        $field = new TestField(['name' => 'select', 'source_type' => 'csv', 'source' => $source]);

        $result = $field->stdSource;
        $expected = [
            'cat1' => [
                'name' => 'Category 1',
                'children' => [
                    'cat11' => [
                        'name' => 'Category 11',
                        'children' => [
                            'cat111' => ['name' => 'Category 111'],
                            'cat112' => ['name' => 'Category 112'],
                            'cat113' => ['name' => 'Category 113'],
                        ],
                    ],
                    'cat12' => ['name' => 'Category 12'],
                    'cat13' => ['name' => 'Category 13'],
                ],
            ],
            'cat2' => ['name' => 'Category 2'],
            'cat3' => ['name' => 'Category 3'],
        ];

        $this->assertEquals($expected, $result);
    }


    /**
     * Проверка метода parseXML()
     */
    public function testParseXML()
    {
        $source = ' <element title="Category1" id="cat1">
                      <Category11 value="cat11">
                        <cat111 />
                        <Category112 id="cat112" />
                        <element name="Category113" value="cat113" />
                      </Category11>
                      <Category12 id="cat12" />
                      <Category13 value="cat13" />
                    </element>
                    <Category2 id="cat2" />
                    <Category3 value="cat3" />';
                    // $source = '';
        $field = new TestField(['name' => 'select', 'source_type' => 'xml', 'source' => $source]);

        $result = $field->stdSource;
        $expected = [
            'cat1' => [
                'name' => 'Category1',
                'children' => [
                    'cat11' => [
                        'name' => 'Category11',
                        'children' => [
                            'cat111' => ['name' => 'cat111'],
                            'cat112' => ['name' => 'Category112'],
                            'cat113' => ['name' => 'Category113'],
                        ],
                    ],
                    'cat12' => ['name' => 'Category12'],
                    'cat13' => ['name' => 'Category13'],
                ],
            ],
            'cat2' => ['name' => 'Category2'],
            'cat3' => ['name' => 'Category3'],
        ];

        $this->assertEquals($expected, $result);
    }


    /**
     * Проверка метода parseXML() с ошибкой
     */
    public function testParseXMLWithError()
    {
        $source = ' <abc> ';
                    // $source = '';
        $field = new TestField(['name' => 'select', 'source_type' => 'xml', 'source' => $source]);

        $result = $field->stdSource;
        $expected = [];

        $this->assertEquals($expected, $result);
    }


    /**
     * Проверка метода parseSQL()
     */
    public function testParseSQL()
    {
        $sqlQuery = "CREATE TEMPORARY TABLE IF NOT EXISTS tmp_testparsesql (
                        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                        pid INT UNSIGNED NOT NULL DEFAULT 0,
                        name VARCHAR(255) NOT NULL DEFAULT '',

                        PRIMARY KEY (id),
                        KEY (pid)
                    );";
        Application::i()->SQL->query($sqlQuery);
        Application::i()->SQL->add('tmp_testparsesql', [
            ['id' => 1, 'pid' => 0, 'name' => 'Category1'],
            ['id' => 11, 'pid' => 1, 'name' => 'Category11'],
            ['id' => 111, 'pid' => 11, 'name' => 'Category111'],
            ['id' => 112, 'pid' => 11, 'name' => 'Category112'],
            ['id' => 113, 'pid' => 11, 'name' => 'Category113'],
            ['id' => 12, 'pid' => 1, 'name' => 'Category12'],
            ['id' => 13, 'pid' => 1, 'name' => 'Category13'],
            ['id' => 2, 'pid' => 0, 'name' => 'Category2'],
            ['id' => 3, 'pid' => 0, 'name' => 'Category3'],
        ]);

        $source = "SELECT id AS val, pid, name FROM tmp_testparsesql";
        $field = new TestField(['name' => 'select', 'source_type' => 'sql', 'source' => $source]);

        $result = $field->stdSource;
        $expected = [
            '1' => [
                'name' => 'Category1',
                'children' => [
                    '11' => [
                        'name' => 'Category11',
                        'children' => [
                            '111' => ['name' => 'Category111'],
                            '112' => ['name' => 'Category112'],
                            '113' => ['name' => 'Category113'],
                        ],
                    ],
                    '12' => ['name' => 'Category12'],
                    '13' => ['name' => 'Category13'],
                ],
            ],
            '2' => ['name' => 'Category2'],
            '3' => ['name' => 'Category3'],
        ];

        $this->assertEquals($expected, $result);

        $sqlQuery = "DROP TABLE IF EXISTS tmp_testparsesql";
        Application::i()->SQL->query($sqlQuery);
    }


    /**
     * Проверка метода parseSQL() с одной колонкой
     */
    public function testParseSQLWithOneColumn()
    {
        $sqlQuery = "CREATE TEMPORARY TABLE IF NOT EXISTS tmp_testparsesql (
                        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                        pid INT UNSIGNED NOT NULL DEFAULT 0,
                        name VARCHAR(255) NOT NULL DEFAULT '',

                        PRIMARY KEY (id),
                        KEY (pid)
                    );";
        Application::i()->SQL->query($sqlQuery);
        Application::i()->SQL->add('tmp_testparsesql', [
            ['id' => 1, 'pid' => 0, 'name' => 'Category1'],
            ['id' => 11, 'pid' => 1, 'name' => 'Category11'],
            ['id' => 111, 'pid' => 11, 'name' => 'Category111'],
            ['id' => 112, 'pid' => 11, 'name' => 'Category112'],
            ['id' => 113, 'pid' => 11, 'name' => 'Category113'],
            ['id' => 12, 'pid' => 1, 'name' => 'Category12'],
            ['id' => 13, 'pid' => 1, 'name' => 'Category13'],
            ['id' => 2, 'pid' => 0, 'name' => 'Category2'],
            ['id' => 3, 'pid' => 0, 'name' => 'Category3'],
        ]);


        $source = "SELECT id AS foo, pid FROM tmp_testparsesql";
        $field = new TestField(['name' => 'select', 'source_type' => 'sql', 'source' => $source]);

        $result = $field->stdSource;
        $expected = [
            '1' => [
                'name' => '1',
                'children' => [
                    '11' => [
                        'name' => '11',
                        'children' => [
                            '111' => ['name' => '111'],
                            '112' => ['name' => '112'],
                            '113' => ['name' => '113'],
                        ],
                    ],
                    '12' => ['name' => '12'],
                    '13' => ['name' => '13'],
                ],
            ],
            '2' => ['name' => '2'],
            '3' => ['name' => '3'],
        ];

        $this->assertEquals($expected, $result);

        $sqlQuery = "DROP TABLE IF EXISTS tmp_testparsesql";
        Application::i()->SQL->query($sqlQuery);
    }


    /**
     * Проверка метода parseSQL() с опасным запросом
     */
    public function testParseSQLWithDangerousQuery()
    {
        $source = "DROP TABLE IF NOT EXISTS tmp_testparsesql1;";
        $field = new TestField(['name' => 'select', 'source_type' => 'sql', 'source' => $source]);

        $result = $field->stdSource;
        $expected = [];

        $this->assertEquals($expected, $result);

        $sqlQuery = "DROP TABLE IF EXISTS tmp_testparsesql";
        Application::i()->SQL->query($sqlQuery);
    }


    /**
     * Проверка метода parsePHP() с одной колонкой
     */
    public function testParsePHP()
    {
        $source = "return [
            'cat1' => [
                'name' => 'Category1',
                'children' => [
                    'cat11' => [
                        'name' => 'Category11',
                        'children' => [
                            'cat111' => 'cat111',
                            'cat112' => 'Category112',
                            'cat113' => 'Category113',
                        ],
                    ],
                    'cat12' => ['name' => 'Category12'],
                    'cat13' => ['name' => 'Category13'],
                ],
            ],
            'cat2' => ['name' => 'Category2'],
            'cat3' => ['name' => 'Category3'],
        ];";
        $field = new TestField(['name' => 'select', 'source_type' => 'php', 'source' => $source]);

        $result = $field->stdSource;
        $expected = [
            'cat1' => [
                'name' => 'Category1',
                'children' => [
                    'cat11' => [
                        'name' => 'Category11',
                        'children' => [
                            'cat111' => ['name' => 'cat111'],
                            'cat112' => ['name' => 'Category112'],
                            'cat113' => ['name' => 'Category113'],
                        ],
                    ],
                    'cat12' => ['name' => 'Category12'],
                    'cat13' => ['name' => 'Category13'],
                ],
            ],
            'cat2' => ['name' => 'Category2'],
            'cat3' => ['name' => 'Category3'],
        ];
        $this->assertEquals($expected, $result);
    }


    /**
     * Проверка метода parseINI()
     */
    public function testParseINI()
    {
        $source = 'cat1="Category 1"' . "\n"
                . 'cat2="Category 2"' . "\n"
                . 'cat3="Category 3"' . "\n";
        $field = new TestField(['name' => 'select', 'source_type' => 'ini', 'source' => $source]);

        $result = $field->stdSource;
        $expected = [
            'cat1' => ['name' => 'Category 1'],
            'cat2' => ['name' => 'Category 2'],
            'cat3' => ['name' => 'Category 3'],
        ];

        $this->assertEquals($expected, $result);
    }


    /**
     * Проверка метода parseDictionary()
     */
    public function testParseDictionary()
    {
        $sqlQuery = "TRUNCATE TABLE cms_dictionaries";
        Application::i()->SQL->query($sqlQuery);

        Application::i()->SQL->add('tmp_dictionaries', [
            ['id' => 1, 'pid' => 0, 'name' => 'Dictionary', 'urn' => 'test', 'priority' => 0],
            ['id' => 11, 'pid' => 1, 'name' => 'Category1', 'urn' => 'cat1', 'priority' => 1],
            ['id' => 111, 'pid' => 11, 'name' => 'Category11', 'urn' => 'cat11', 'priority' => 2],
            ['id' => 1111, 'pid' => 111, 'name' => 'Category111', 'urn' => 'cat111', 'priority' => 3],
            ['id' => 1112, 'pid' => 111, 'name' => 'Category112', 'urn' => 'cat112', 'priority' => 4],
            ['id' => 1113, 'pid' => 111, 'name' => 'Category113', 'urn' => 'cat113', 'priority' => 5],
            ['id' => 112, 'pid' => 11, 'name' => 'Category12', 'urn' => 'cat12', 'priority' => 6],
            ['id' => 113, 'pid' => 11, 'name' => 'Category13', 'urn' => 'cat13', 'priority' => 7],
            ['id' => 12, 'pid' => 1, 'name' => 'Category2', 'urn' => 'cat2', 'priority' => 8],
            ['id' => 13, 'pid' => 1, 'name' => 'Category3', 'urn' => 'cat3', 'priority' => 9],
        ]);

        $field = new TestField(['name' => 'select', 'source_type' => 'dictionary', 'source' => 1]);

        $result = $field->stdSource;
        $expected = [
            'cat1' => [
                'name' => 'Category1',
                'children' => [
                    'cat11' => [
                        'name' => 'Category11',
                        'children' => [
                            'cat111' => ['name' => 'Category111'],
                            'cat112' => ['name' => 'Category112'],
                            'cat113' => ['name' => 'Category113'],
                        ],
                    ],
                    'cat12' => ['name' => 'Category12'],
                    'cat13' => ['name' => 'Category13'],
                ],
            ],
            'cat2' => ['name' => 'Category2'],
            'cat3' => ['name' => 'Category3'],
        ];

        $this->assertEquals($expected, $result);

        $sqlQuery = "TRUNCATE TABLE cms_dictionaries";
        Application::i()->SQL->query($sqlQuery);
    }


    /**
     * Проверка метода getSet()
     */
    public function testGetSet()
    {
        $sqlArr = [
            ['id' => 1, 'datatype' => 'text', 'classname' => CustomEntity::class, 'pid' => 1, 'priority' => 1],
            ['id' => 2, 'datatype' => 'text', 'classname' => Attachment::class, 'pid' => 1, 'priority' => 2],
            ['id' => 3, 'datatype' => 'text', 'classname' => CustomEntity::class, 'pid' => 1, 'priority' => 3],
            ['id' => 4, 'datatype' => 'text', 'classname' => Attachment::class, 'pid' => 1, 'priority' => 4],
            ['id' => 5, 'datatype' => 'text', 'classname' => CustomEntity::class, 'pid' => 1, 'priority' => 5],
            ['id' => 6, 'datatype' => 'text', 'classname' => Attachment::class, 'pid' => 1, 'priority' => 6],
        ];
        Application::i()->SQL->add('tmp_fields', $sqlArr);

        $result = TestField2::getSet();

        $this->assertCount(3, $result);
        $this->assertInstanceOf(TestField2::class, $result[0]);
        $this->assertInstanceOf(TestField2::class, $result[1]);
        $this->assertInstanceOf(TestField2::class, $result[2]);
        $this->assertEquals(1, $result[0]->id);
        $this->assertEquals(3, $result[1]->id);
        $this->assertEquals(5, $result[2]->id);
    }


    /**
     * Проверка метода getSet() - с ограничивающим условием
     */
    public function testGetSetWithWhere()
    {
        $sqlArr = [
            ['id' => 1, 'datatype' => 'text', 'classname' => CustomEntity::class, 'pid' => 1, 'priority' => 1],
            ['id' => 2, 'datatype' => 'text', 'classname' => Attachment::class, 'pid' => 1, 'priority' => 2],
            ['id' => 3, 'datatype' => 'text', 'classname' => CustomEntity::class, 'pid' => 1, 'priority' => 3],
            ['id' => 4, 'datatype' => 'text', 'classname' => Attachment::class, 'pid' => 1, 'priority' => 4],
            ['id' => 5, 'datatype' => 'text', 'classname' => CustomEntity::class, 'pid' => 1, 'priority' => 5],
            ['id' => 6, 'datatype' => 'text', 'classname' => Attachment::class, 'pid' => 1, 'priority' => 6],
        ];
        Application::i()->SQL->add('tmp_fields', $sqlArr);

        $result = TestField2::getSet(['where' => "id > 1"]);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(TestField2::class, $result[0]);
        $this->assertInstanceOf(TestField2::class, $result[1]);
        $this->assertEquals(3, $result[0]->id);
        $this->assertEquals(5, $result[1]->id);
    }


    /**
     * Проверка метода reorder()
     */
    public function testReorder()
    {
        $sqlArr = [
            ['id' => 1, 'datatype' => 'text', 'classname' => CustomEntity::class, 'pid' => 1, 'priority' => 30],
            ['id' => 2, 'datatype' => 'text', 'classname' => Attachment::class, 'pid' => 1, 'priority' => 2],
            ['id' => 3, 'datatype' => 'text', 'classname' => CustomEntity::class, 'pid' => 1, 'priority' => 20],
            ['id' => 4, 'datatype' => 'text', 'classname' => Attachment::class, 'pid' => 1, 'priority' => 4],
            ['id' => 5, 'datatype' => 'text', 'classname' => CustomEntity::class, 'pid' => 1, 'priority' => 10],
            ['id' => 6, 'datatype' => 'text', 'classname' => Attachment::class, 'pid' => 1, 'priority' => 6],
        ];
        Application::i()->SQL->add('tmp_fields', $sqlArr);

        $field = new TestField(1);
        $field->reorder(-1, ["1"]);

        $sqlQuery = "SELECT id, priority FROM tmp_fields ORDER BY priority, id";
        $sqlResult = Application::i()->SQL->get($sqlQuery);

        $expected = [
            ['id' => 2, 'priority' => 2],
            ['id' => 4, 'priority' => 4],
            ['id' => 6, 'priority' => 6],
            ['id' => 5, 'priority' => 10],
            ['id' => 1, 'priority' => 20],
            ['id' => 3, 'priority' => 30],
        ];
        $this->assertEquals($expected, $sqlResult);
    }




    /**
     * Проверка метода required()
     */
    public function testRequired()
    {
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'image', 'multiple' => true]);
        $field->commit();

        $this->assertFalse((bool)$field->required);

        $field = new TestField(1);

        $this->assertFalse((bool)$field->required);

        $field->required();

        $this->assertTrue((bool)$field->required);

        $field = new TestField(1);

        $this->assertTrue((bool)$field->required);

        $field->required();

        $this->assertFalse((bool)$field->required);

        $field = new TestField(1);

        $this->assertFalse((bool)$field->required);
    }





    /**
     * Проверка метода show_in_table()
     */
    public function testShowInTable()
    {
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'image', 'multiple' => true]);
        $field->commit();

        $this->assertFalse((bool)$field->show_in_table);

        $field = new TestField(1);

        $this->assertFalse((bool)$field->show_in_table);

        $field->show_in_table();

        $this->assertTrue((bool)$field->show_in_table);

        $field = new TestField(1);

        $this->assertTrue((bool)$field->show_in_table);

        $field->show_in_table();

        $this->assertFalse((bool)$field->show_in_table);

        $field = new TestField(1);

        $this->assertFalse((bool)$field->show_in_table);
    }


    /**
     * Проверка метода delete()
     */
    public function testDelete()
    {
        $field = new TestField(['id' => 1, 'urn' => 'test', 'datatype' => 'image', 'multiple' => true]);
        $field->commit();
        $sqlArr = [
            [
                'pid' => 10,
                'fid' => 1,
                'fii' => 0,
                'value' => json_encode([
                    'vis' => 1,
                    'name' => 'Test',
                    'description' => 'Test description',
                    'attachment' => '111'
                ]),
            ],
            [
                'pid' => 10,
                'fid' => 1,
                'fii' => 1,
                'value' => json_encode([
                    'vis' => 1,
                    'name' => 'Test',
                    'description' => 'Test description',
                    'attachment' => '222'
                ]),
            ],
            [
                'pid' => 10,
                'fid' => 1,
                'fii' => 2,
                'value' => json_encode([
                    'vis' => 1,
                    'name' => 'Test',
                    'description' => 'Test description',
                    'attachment' => '333'
                ]),
            ],
        ];
        Application::i()->SQL->add('tmp_data', $sqlArr);
        TestField::clearCache();
        TestField::prefetch([], [1]);
        $sqlQuery = "SELECT COUNT(*) FROM tmp_data WHERE fid = 1";

        $sqlResult = Application::i()->SQL->getvalue($sqlQuery);
        $this->assertEquals(3, $sqlResult);
        $this->assertEquals([
            '10' => ['1' => [
                json_encode([
                    'vis' => 1,
                    'name' => 'Test',
                    'description' => 'Test description',
                    'attachment' => '111'
                ]),
                json_encode([
                    'vis' => 1,
                    'name' => 'Test',
                    'description' => 'Test description',
                    'attachment' => '222'
                ]),
                json_encode([
                    'vis' => 1,
                    'name' => 'Test',
                    'description' => 'Test description',
                    'attachment' => '333'
                ]),
            ]],
        ], TestField::$cache);

        TestField::delete($field);

        $sqlResult = Application::i()->SQL->getvalue($sqlQuery);
        $this->assertEquals(0, $sqlResult);
        $this->assertEquals([
            '10' => [],
        ], TestField::$cache);
    }




    /**
     * Проверка метода getMaxSize()
     */
    public function testGetMaxSize()
    {
        $field = new Field();

        $result = $field->getMaxSize();

        $this->assertEquals(1920, $result);
    }


    /**
     * Проверка метода getTnSize()
     */
    public function testGetTnSize()
    {
        $field = new Field();

        $result = $field->getTnSize();

        $this->assertEquals(300, $result);
    }
}
