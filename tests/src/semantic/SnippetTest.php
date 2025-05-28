<?php
/**
 * Тест класса Snippet
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;
use RAAS\Application;
use RAAS\Controller_Frontend as RAASControllerFrontend;
use RAAS\User as RAASUser;
use RAAS\CMS\Shop\PriceLoader;
use RAAS\CMS\Shop\ImageLoader;

/**
 * Тест класса Snippet
 */
#[CoversClass(Snippet::class)]
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
        'cms_fieldgroups',
        'cms_fields',
        'cms_forms',
        'cms_materials',
        'cms_pages',
        'cms_shop_imageloaders',
        'cms_shop_priceloaders',
        'cms_shop_priceloaders_columns',
        'cms_snippet_folders',
        'cms_snippets',
        'users',
    ];

    /**
     * Тест ошибки от 2024-05-15 12:34
     * Fatal error: Uncaught TypeError: stristr(): Argument #1 ($haystack) must be of type string, null given
     * in D:\web\home\libs\raas.cms\classes\semantic\snippet.class.php on line 175
     */
    public function test202405151234()
    {
        $snippet = new Snippet(['urn' => 'test']);
        $snippet->commit();

        $result = $snippet->usingSnippets;

        $this->assertEmpty($result);

        Snippet::delete($snippet);
    }


    /**
     * Тест получения свойства filename
     */
    public function testGetFilename()
    {
        $snippet = Snippet::importByURN('news');

        $result = $snippet->filename;

        $this->assertEquals(Application::i()->baseDir . '/inc/snippets/news.tmp.php', $result);
    }


    /**
     * Тест получения свойства lockedFilename
     */
    public function testGetLockedFilename()
    {
        $snippet = Snippet::importByURN('__raas_form_notify');

        $result = $snippet->lockedFilename;

        $this->assertEquals(realpath(Package::i()->resourcesDir . '/interfaces/form_notification.php'), realpath($result));
    }


    /**
     * Тест получения свойства lockedFilename - случай с незаблокированным сниппетом
     */
    public function testGetLockedFilenameWithNotLocked()
    {
        $snippet = Snippet::importByURN('banners');

        $result = $snippet->lockedFilename;

        $this->assertNull($result);
    }


    /**
     * Тест получения свойства lockedFilename - случай с неизвестным модулем
     */
    public function testGetLockedFilenameWithNoModule()
    {
        $snippet = new Snippet(['urn' => 'test', 'locked' => 'aaa/bbb.php']);

        $result = $snippet->lockedFilename;

        $this->assertNull($result);

        Snippet::delete($snippet);
    }


    /**
     * Тест получения свойства oldFilename
     */
    public function testGetOldFilename()
    {
        $snippet = Snippet::importByURN('banners');
        $snippet->urn = 'banners1';

        $result = $snippet->oldFilename;

        $this->assertEquals(Application::i()->baseDir . '/inc/snippets/banners.tmp.php', $result);
    }


    /**
     * Тест получения свойства oldFilename - случай с заблокированным сниппетом
     */
    public function testGetOldFilenameWithLocked()
    {
        $snippet = Snippet::importByURN('__raas_form_notify');

        $result = $snippet->oldFilename;

        $this->assertEquals(realpath(Package::i()->resourcesDir . '/interfaces/form_notification.php'), realpath($result));
    }


    /**
     * Тест получения свойства post_date
     */
    public function testGetPostDate()
    {
        $snippet = Snippet::importByURN('banners');
        $filename = Application::i()->baseDir . '/inc/snippets/banners.tmp.php';
        if (is_file($filename)) {
            unlink($filename);
        }

        $result = $snippet->post_date;

        $this->assertEquals('0000-00-00 00:00:00', $result);

        touch($filename);

        $result = $snippet->post_date;

        $this->assertGreaterThan('0000-00-00 00:00:00', $result);

        unlink($filename);
    }


    /**
     * Тест получения свойства modify_date
     */
    public function testGetModifyDate()
    {
        $snippet = Snippet::importByURN('banners');
        $filename = Application::i()->baseDir . '/inc/snippets/banners.tmp.php';
        if (is_file($filename)) {
            unlink($filename);
        }

        $result = $snippet->modify_date;

        $this->assertEquals('0000-00-00 00:00:00', $result);

        touch($filename);

        $result = $snippet->modify_date;

        $this->assertGreaterThan('0000-00-00 00:00:00', $result);

        unlink($filename);
    }


    /**
     * Тест метода commit()
     */
    public function testCommit()
    {
        $this->assertFileDoesNotExist(Application::i()->baseDir . '/inc/snippets/test.tmp.php');

        Application::i()->user = new RAASUser(1);
        $code = '<' . "?php\n"
            . "/**\n"
            . " * Тест\n"
            . " */\n"
            . "return ['aaa' => \$input ?? null];\n";
        $snippet = new Snippet(['description' => $code]);
        $snippet->commit();

        $this->assertEquals('test', $snippet->urn);
        $this->assertFileExists(Application::i()->baseDir . '/inc/snippets/test.tmp.php');
        $this->assertFileDoesNotExist(Application::i()->baseDir . '/inc/snippets/test1.tmp.php');
        $this->assertEquals(date('Y-m-d'), date('Y-m-d', strtotime($snippet->post_date)));
        $this->assertEquals(date('Y-m-d'), date('Y-m-d', strtotime($snippet->modify_date)));
        $this->assertEquals(1, $snippet->author_id);
        $this->assertEquals(1, $snippet->editor_id);

        $snippet->urn = 'test1';
        $snippet->commit();

        $this->assertFileDoesNotExist(Application::i()->baseDir . '/inc/snippets/test.tmp.php');
        $this->assertFileExists(Application::i()->baseDir . '/inc/snippets/test1.tmp.php');

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
     * Тест метода process() - случай без файла
     */
    public function testProcessWithNoFilename()
    {
        $code = '<' . "?php\n"
            . "return ['aaa' => \$input ?? null];\n";
        $snippet = new Snippet(['urn' => '', 'description' => $code]);
        $snippet->commit();

        $result = $snippet->process(['input' => 'bbb']);

        $this->assertNull($result);

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
     * Тест метода delete()
     */
    public function testDelete()
    {
        $this->assertFileDoesNotExist(Application::i()->baseDir . '/inc/snippets/test.tmp.php');

        $snippet = new Snippet(['urn' => 'test']);
        $snippet->commit();

        $this->assertFileExists(Application::i()->baseDir . '/inc/snippets/test.tmp.php');

        Snippet::delete($snippet);

        $this->assertFileDoesNotExist(Application::i()->baseDir . '/inc/snippets/test.tmp.php');
    }


    /**
     * Тест метода checkSnippets
     */
    public function testCheckSnippets()
    {
        Snippet::checkSnippets();

        $widget = Snippet::importByURN('test');
        $interface = Snippet::importByURN('testinterface');

        $this->assertEmpty($widget);
        $this->assertEmpty($interface);

        touch(Application::i()->baseDir . '/inc/snippets/banners.tmp.php'); // Уже существующий, для покрытия
        touch(Application::i()->baseDir . '/inc/snippets/test.tmp.php');
        touch(Application::i()->baseDir . '/inc/snippets/testinterface.tmp.php');
        touch(Application::i()->baseDir . '/inc/snippets/template1.tmp.php'); // Шаблон, для покрытия

        Snippet::checkSnippets();

        $widget = Snippet::importByURN('test');
        $interface = Snippet::importByURN('testinterface');
        $template = Snippet::importByURN('template1');

        $this->assertNotEmpty($widget->id);
        $this->assertNotEmpty($interface->id);
        $this->assertEmpty($template);
        $this->assertEquals('__raas_views', $widget->parent->urn);
        $this->assertEquals('__raas_interfaces', $interface->parent->urn);
    }
}
