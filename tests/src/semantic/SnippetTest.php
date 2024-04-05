<?php
/**
 * Тест класса Snippet
 */
namespace RAAS\CMS;

use SOME\BaseTest;
use RAAS\Application;
use RAAS\Controller_Frontend as RAASControllerFrontend;
use RAAS\User as RAASUser;
use RAAS\CMS\Shop\PriceLoader;
use RAAS\CMS\Shop\ImageLoader;

/**
 * Тест класса Snippet
 * @covers RAAS\CMS\Snippet
 */
class SnippetTest extends BaseTest
{
    public static $tables = [
        'attachments',
        'cms_blocks',
        'cms_blocks_form',
        'cms_blocks_material',
        'cms_blocks_material_filter',
        'cms_blocks_material_sort',
        'cms_data',
        'cms_feedback',
        'cms_fields',
        'cms_forms',
        'cms_materials',
        'cms_pages',
        'cms_shop_imageloaders',
        'cms_shop_priceloaders',
        'cms_shop_priceloaders_columns',
        'cms_snippets',
        'users',
    ];

    /**
     * Тест получения свойства filename
     */
    public function testGetFilename()
    {
        $snippet = Snippet::importByURN('news');

        $result = $snippet->filename;

        $this->assertEquals(Package::i()->cacheDir . '/system/snippets/news.tmp.php', $result);
    }


    /**
     * Тест метода commit()
     */
    public function testCommit()
    {
        $this->assertFileDoesNotExist(Package::i()->cacheDir . '/system/snippets/test.tmp.php');

        Application::i()->user = new RAASUser(1);
        $code = '<' . "?php\n"
            . "/**\n"
            . " * Тест\n"
            . " */\n"
            . "return ['aaa' => \$input ?? null];\n";
        $snippet = new Snippet(['description' => $code]);
        $snippet->commit();

        $this->assertEquals('test', $snippet->urn);
        $this->assertFileExists(Package::i()->cacheDir . '/system/snippets/test.tmp.php');
        $this->assertFileDoesNotExist(Package::i()->cacheDir . '/system/snippets/test1.tmp.php');
        $this->assertEquals(date('Y-m-d'), date('Y-m-d', strtotime($snippet->post_date)));
        $this->assertEquals(date('Y-m-d'), date('Y-m-d', strtotime($snippet->modify_date)));
        $this->assertEquals(1, $snippet->author_id);
        $this->assertEquals(1, $snippet->editor_id);

        $snippet->urn = 'test1';
        $snippet->commit();

        $this->assertFileDoesNotExist(Package::i()->cacheDir . '/system/snippets/test.tmp.php');
        $this->assertFileExists(Package::i()->cacheDir . '/system/snippets/test1.tmp.php');

        Snippet::delete($snippet);
    }


    /**
     * Тест метода process()
     */
    public function testProcess()
    {
        $code = '<' . "?php\n"
            . "return ['aaa' => \$input ?? null];\n";
        $snippet = new Snippet(['urn' => 'test', 'description' => $code]);
        $snippet->commit();

        $result = $snippet->process(['input' => 'bbb']);

        $this->assertEquals(['aaa' => 'bbb'], $result);

        Snippet::delete($snippet);
    }


    /**
     * Тест метода process() - проверка диагностики
     */
    public function testProcessCheckDiag()
    {
        $callParams = [];
        $diag = $this->createMock(Diag::class);
        $diag->method('handle')->willReturnCallback(function () use (&$callParams) {
            array_push($callParams, func_get_args());
        });
        RAASControllerFrontend::i()->setDiag($diag);
        $block = Block::spawn(22); // Новости на странице новостей
        $page = new Page(7); // Новости
        $page->Material = new Material(7); // Первая новость
        $code = '<' . "?php\n"
            . "return 'aaa';\n";
        $snippet = new Snippet(['urn' => 'test', 'description' => $code]);
        $snippet->commit();

        $result = $snippet->process(['Block' => $block, 'Page' => $page]);

        $this->assertEquals('snippets', $callParams[0][0]);
        $this->assertEquals($snippet->id . '@m', $callParams[0][1]); // Стандартный интерфейс новостей
        $this->assertIsNumeric($callParams[0][2]);
        $this->assertEquals('counter', $callParams[0][3]);
        $this->assertEquals('time', $callParams[0][4]);

        Snippet::delete($snippet);
    }


    /**
     * Тест метода process() - случай с удаленным файлом
     */
    public function testProcessWithDeletedFile()
    {
        $code = '<' . "?php\n"
            . "return 'aaa';\n";
        $snippet = new Snippet(['urn' => 'test', 'description' => $code]);
        $snippet->commit();
        $file = Package::i()->cacheDir . '/system/snippets/test.tmp.php';
        if (is_file($file)) {
            unlink($file);
        }

        $this->assertFileDoesNotExist($file);

        $result = $snippet->process();

        $this->assertFileExists($file);

        Snippet::delete($snippet);
    }


    /**
     * Тест получения свойства usingSnippets
     */
    public function testGetUsingSnippets()
    {
        $code1 = '<' . "?php\n"
            . "return 'aaa';\n";
        $snippet1 = new Snippet(['urn' => 'test1', 'description' => $code1]);
        $snippet1->commit();

        $code2 = '<' . "?php\n"
            . "return Snippet::importByURN('test1')->process();\n";
        $snippet2 = new Snippet(['urn' => 'test2', 'description' => $code2]);
        $snippet2->commit();

        $snippet1 = Snippet::importByURN('test1');

        $this->assertCount(1, $snippet1->usingSnippets);
        $this->assertEquals('test2', $snippet1->usingSnippets[0]->urn);

        Snippet::delete($snippet1);
        Snippet::delete($snippet2);
    }


    /**
     * Тест получения свойства usingBlocks
     */
    public function testGetUsingBlocks()
    {
        $snippet = Snippet::importByURN('banners');

        $this->assertCount(1, $snippet->usingBlocks);
        $this->assertInstanceOf(Block_Material::class, $snippet->usingBlocks[0]);
        $this->assertEquals(16, $snippet->usingBlocks[0]->id);
    }


    /**
     * Тест получения свойства usingForms
     */
    public function testGetUsingForms()
    {
        $snippet = new Snippet(['urn' => 'test']);
        $snippet->commit();
        $form = new Form(['urn' => 'testform', 'name' => 'Тестовая форма', 'interface_id' => $snippet->id]);
        $form->commit();

        $snippet = Snippet::importByURN('test');

        $this->assertCount(1, $snippet->usingForms);
        $this->assertInstanceOf(Form::class, $snippet->usingForms[0]);
        $this->assertEquals('testform', $snippet->usingForms[0]->urn);

        Snippet::delete($snippet);
        Form::delete($form);
    }


    /**
     * Тест получения свойства usingFields
     */
    public function testGetUsingFields()
    {
        $snippet = new Snippet(['urn' => 'test']);
        $snippet->commit();
        $field = new Page_Field([
            'urn' => 'testfield',
            'datatype' => 'image',
            'name' => 'Тестовое поле',
            'preprocessor_id' => $snippet->id,
        ]);
        $field->commit();

        $snippet = Snippet::importByURN('test');

        $this->assertCount(1, $snippet->usingFields);
        $this->assertInstanceOf(Field::class, $snippet->usingFields[0]);
        $this->assertEquals('testfield', $snippet->usingFields[0]->urn);

        Snippet::delete($snippet);
        Page_Field::delete($field);
    }


    /**
     * Тест получения свойства usingPriceloaders
     */
    public function testGetUsingPriceloaders()
    {
        $snippet = new Snippet(['urn' => 'test']);
        $snippet->commit();
        $loader = new PriceLoader([
            'urn' => 'testloader',
            'interface_id' => $snippet->id,
        ]);
        $loader->commit();

        $snippet = Snippet::importByURN('test');

        $this->assertCount(1, $snippet->usingPriceloaders);
        $this->assertInstanceOf(PriceLoader::class, $snippet->usingPriceloaders[0]);
        $this->assertEquals('testloader', $snippet->usingPriceloaders[0]->urn);

        Snippet::delete($snippet);
        PriceLoader::delete($loader);
    }


    /**
     * Тест получения свойства usingImageloaders
     */
    public function testGetUsingImageloaders()
    {
        $snippet = new Snippet(['urn' => 'test']);
        $snippet->commit();
        $loader = new ImageLoader([
            'urn' => 'testloader',
            'interface_id' => $snippet->id,
        ]);
        $loader->commit();

        $snippet = Snippet::importByURN('test');

        $this->assertCount(1, $snippet->usingImageloaders);
        $this->assertInstanceOf(ImageLoader::class, $snippet->usingImageloaders[0]);
        $this->assertEquals('testloader', $snippet->usingImageloaders[0]->urn);

        Snippet::delete($snippet);
        ImageLoader::delete($loader);
    }


    /**
     * Провайдер данных для метода testGetName
     * @return array <pre><code>array<[
     *     string Код сниппета,
     *     string Установленный URN,
     *     string Ожидаемое значение,
     * ]></code></pre>
     */
    public function getNameDataProvider(): array
    {
        return [
            [
                (
                    '<' . "?php\n" .
                    "/**\n" .
                    " * Тест\n" .
                    " */\n"
                ),
                '',
                'Тест',
            ],
            [
                (
                    '<' . "?php\n" .
                    "/**\n" .
                    " */\n"
                ),
                'test',
                'test',
            ],
            [
                (
                    '<' . "?php\n" .
                    "/**\n" .
                    " * Тест\n" .
                    " * @param array<[string]>\n" . // Некорректный тег PHPDoc для проверки исключения
                    " */\n"
                ),
                'test1',
                'test1',
            ],
            [
                (
                    '<' . "?php\n" .
                    "return 'aaa';\n"
                ),
                'test',
                'test',
            ],
            [
                (
                    '<' . "?php\n" .
                    "return 'aaa';\n"
                ),
                '',
                '',
            ],
        ];
    }


    /**
     * Тест получения свойства name
     * @param string $code Код сниппета
     * @param string $urn Установленный URN
     * @param string $expected Ожидаемое значение
     * @dataProvider getNameDataProvider
     */
    public function testGetName(string $code, string $urn, string $expected)
    {
        $snippet = new Snippet(['description' => $code]);
        if ($urn) {
            $snippet->urn = $urn;
        }

        $this->assertEquals($expected, $snippet->name);
    }


    /**
     * Тест получения свойства name
     */
    public function testGetNameWithNoDocBlock()
    {
        $code = '<' . "?php\n"
            . "return 'aaa';\n";
        $snippet = new Snippet(['urn' => 'test', 'description' => $code]);
        $snippet->commit();

        $this->assertEquals('test', $snippet->name);

        Snippet::delete($snippet);
    }


    /**
     * Тест метода delete()
     */
    public function testDelete()
    {
        $this->assertFileDoesNotExist(Package::i()->cacheDir . '/system/snippets/test.tmp.php');

        $snippet = new Snippet(['urn' => 'test']);
        $snippet->commit();

        $this->assertFileExists(Package::i()->cacheDir . '/system/snippets/test.tmp.php');

        Snippet::delete($snippet);

        $this->assertFileDoesNotExist(Package::i()->cacheDir . '/system/snippets/test.tmp.php');
    }
}
