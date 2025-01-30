<?php
/**
 * Тест класса Block
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;
use RAAS\Application;
use RAAS\Controller_Frontend as RAASControllerFrontend;
use RAAS\CMS\Shop\Module as ShopModule;

/**
 * Тест класса Block
 */
#[CoversClass(Block::class)]
class BlockTest extends BaseTest
{
    public static $tables = [
        'attachments',
        'cms_access',
        'cms_access_blocks_cache',
        'cms_access_materials_cache',
        'cms_access_pages_cache',
        'cms_blocks',
        'cms_blocks_html',
        'cms_blocks_material',
        'cms_blocks_material_filter',
        'cms_blocks_material_sort',
        'cms_blocks_menu',
        'cms_blocks_pages_assoc',
        'cms_data',
        'cms_fields',
        'cms_forms',
        'cms_material_types',
        'cms_materials',
        'cms_menus',
        'cms_pages',
        'cms_snippets',
        'cms_templates',
        'cms_users',
        'registry',
    ];


    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        RAASControllerFrontend::i()->exportLang(Application::i(), 'ru');
        RAASControllerFrontend::i()->exportLang(Package::i(), 'ru');
        RAASControllerFrontend::i()->exportLang(ShopModule::i(), 'ru');
    }


    /**
     * Получает тестовый сниппет
     * @return Snippet
     */
    public function getTestSnippet(): Snippet
    {
        $snippet = new Snippet(['urn' => 'testsnippet', 'description' => 'aaa']);
        $snippet->commit();
        return $snippet;
    }


    /**
     * Тест метода spawn()
     */
    public function testSpawn()
    {
        $result = Block::spawn(36); // Спецпредложение

        $this->assertInstanceOf(Block_PHP::class, $result);
        $this->assertEquals('Спецпредложение', $result->name);
    }


    /**
     * Тест метода spawn() - случай с указанием массива
     */
    public function testSpawnWithArray()
    {
        $result = Block::spawn([
            'block_type' => Block_PHP::class,
            'name' => 'Тестовый блок',
            'location' => 'content',
        ]);

        $this->assertInstanceOf(Block_PHP::class, $result);
        $this->assertEquals('Тестовый блок', $result->name);
        $this->assertEquals('content', $result->location);
    }


    /**
     * Тест получения параметра Interface
     */
    public function testGetInterface()
    {
        $snippet = $this->getTestSnippet();

        $block = new Block_PHP(['interface_id' => (int)$snippet->id]); // Наши преимущества
        $block->commit();

        $block = Block::spawn($block->id);

        $result = $block->Interface;

        $this->assertInstanceOf(Snippet::class, $result);
        $this->assertEquals($snippet->id, $result->id);

        Snippet::delete($snippet);
        Block::delete($block);
    }


    /**
     * Тест получения параметра Widget
     */
    public function testGetWidget()
    {
        $snippet = $this->getTestSnippet();

        $block = new Block_PHP(['widget_id' => (int)$snippet->id]); // Наши преимущества
        $block->commit();

        $block = Block::spawn($block->id);

        $result = $block->Widget;

        $this->assertInstanceOf(Snippet::class, $result);
        $this->assertEquals($snippet->id, $result->id);

        Snippet::delete($snippet);
        Block::delete($block);
    }


    /**
     * Тест получения параметра CacheInterface
     */
    public function testGetCacheInterface()
    {
        $snippet = $this->getTestSnippet();

        $block = new Block_PHP(['cache_interface_id' => (int)$snippet->id]); // Наши преимущества
        $block->commit();

        $block = Block::spawn($block->id);

        $result = $block->CacheInterface;

        $this->assertInstanceOf(Snippet::class, $result);
        $this->assertEquals($snippet->id, $result->id);

        Snippet::delete($snippet);
        Block::delete($block);
    }


    /**
     * Тест получения параметра interface
     */
    public function testGetInterfaceDescription()
    {
        $snippet = $this->getTestSnippet();

        $block = new Block_PHP(['interface_id' => (int)$snippet->id]); // Наши преимущества
        $block->commit();

        $block = Block::spawn($block->id);

        $result = $block->interface;

        $this->assertEquals('aaa', $result);

        Snippet::delete($snippet);
        Block::delete($block);
    }


    /**
     * Тест получения параметра widget
     */
    public function testGetWidgetDescription()
    {
        $snippet = $this->getTestSnippet();

        $block = new Block_PHP(['widget_id' => (int)$snippet->id]); // Наши преимущества
        $block->commit();

        $block = Block::spawn($block->id);

        $result = $block->widget;

        $this->assertEquals('aaa', $result);

        Snippet::delete($snippet);
        Block::delete($block);
    }


    /**
     * Тест получения параметра cache_interface
     */
    public function testGetCacheInterfaceDescription()
    {
        $snippet = $this->getTestSnippet();

        $block = new Block_PHP(['cache_interface_id' => (int)$snippet->id]); // Наши преимущества
        $block->commit();

        $block = Block::spawn($block->id);

        $result = $block->cache_interface;

        $this->assertEquals('aaa', $result);

        Snippet::delete($snippet);
        Block::delete($block);
    }


    /**
     * Тест получения параметра parent
     */
    public function testGetParent()
    {
        $block = Block::spawn(12); // Добро пожаловать

        $result = $block->parent;

        $this->assertInstanceOf(Page::class, $result);
        $this->assertEquals(1, $result->id);
    }


    /**
     * Тест получения параметра pid
     */
    public function testGetPid()
    {
        $block = Block::spawn(12); // Добро пожаловать

        $result = $block->pid;

        $this->assertEquals(1, $result);
    }


    /**
     * Тест получения параметра title
     */
    public function testGetTitle()
    {
        $block = new Block_PHP(['name' => '"тестовый"']);

        $result = $block->title;

        $this->assertEquals('&quot;тестовый&quot;', $block->title);
    }


    /**
     * Тест получения параметра pages_assoc
     */
    public function testGetPagesAssoc()
    {
        $block = Block::spawn(12); // Добро пожаловать

        $result = $block->pages_assoc;

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Page::class, $result[0]);
        $this->assertEquals(1, $result[0]->id);
    }


    /**
     * Тест получения параметра config
     */
    public function testGetConfig()
    {
        $block = Block::spawn(16); // Баннеры

        $result = $block->config;

        $this->assertEquals(2, $result['material_type']);
        $this->assertEquals('post_date', $result['sort_field_default']);
    }


    /**
     * Тест получения параметра additionalParams
     */
    public function testGetAdditionalParams()
    {
        $block = Block::spawn(34); // Каталог продукции

        $result = $block->additionalParams;

        $this->assertEquals('template', $result['metaTemplates']);
        $this->assertEquals('1', $result['withChildrenGoods']);
    }


    /**
     * Тест метода commit()
     */
    public function testCommit()
    {
        $page = new Page(1);
        $pageModifyCounter = (int)$page->modify_counter;

        $block = new Block_HTML(['name' => 'Тестовый текст', 'location' => 'content', 'description' => 'Тест', 'cats' => [1]]);
        $block->commit();

        $page = new Page(1);
        $block = Block::spawn($block->id);
        $pageModifyDate = strtotime($page->last_modified);

        $this->assertEquals('Тест', $block->description);
        $this->assertEquals($pageModifyCounter + 1, $page->modify_counter);
        $this->assertLessThanOrEqual(1, time() - $pageModifyDate);
        $this->assertEquals([1], $block->pages_ids);

        $block->cats = [2];
        $block->commit();

        $block = Block::spawn($block->id);

        $this->assertEquals([2], $block->pages_ids);

        Block::delete($block);
    }


    /**
     * Тест метода tuneWithMaterial()
     */
    #[TestWith([Block::BYMATERIAL_BOTH, false, true])]
    #[TestWith([Block::BYMATERIAL_BOTH, true, true])]
    #[TestWith([Block::BYMATERIAL_WITH, false, false])]
    #[TestWith([Block::BYMATERIAL_WITH, true, true])]
    #[TestWith([Block::BYMATERIAL_WITHOUT, false, true])]
    #[TestWith([Block::BYMATERIAL_WITHOUT, true, false])]
    #[TestWith([999, false, true])]
    #[TestWith([999, true, true])]
    public function testTuneWithMaterial(int $visMaterial, bool $withMaterial, bool $expected)
    {
        $page = new Page(7); // Новости
        if ($withMaterial) {
            $page->Material = new Material(7); // Первая новость
        }
        $block = new Block_PHP(['vis_material' => $visMaterial]);

        $result = $block->tuneWithMaterial($page);

        $this->assertEquals($expected, $result);
    }


    /**
     * Тест метода getAddData()
     */
    public function testGetAddData()
    {
        $block = Block::spawn(39); // Информер корзины

        $result = $block->getAddData();

        $this->assertEquals([], $result);
    }


    /**
     * Тест метода swap()
     */
    public function testSwap()
    {
        $page = new Page(1);
        $block1 = Block::spawn(13); // Наши преимущества
        $block2 = Block::spawn(35); // Каталог продукции
        $block3 = Block::spawn(36); // Спецпредложение

        $sqlQuery = "SELECT priority FROM cms_blocks_pages_assoc WHERE page_id = 1 AND block_id = ?";
        $initialPriority1 = Application::i()->SQL->getValue([$sqlQuery, [$block1->id]]);
        $initialPriority2 = Application::i()->SQL->getValue([$sqlQuery, [$block2->id]]);
        $initialPriority3 = Application::i()->SQL->getValue([$sqlQuery, [$block3->id]]);

        $result = $block1->swap(2, $page);
        $priority1 = Application::i()->SQL->getValue([$sqlQuery, [$block1->id]]);
        $priority2 = Application::i()->SQL->getValue([$sqlQuery, [$block2->id]]);
        $priority3 = Application::i()->SQL->getValue([$sqlQuery, [$block3->id]]);

        $this->assertTrue($result);
        $this->assertEquals($initialPriority3, $priority1);
        $this->assertEquals($initialPriority1, $priority2);
        $this->assertEquals($initialPriority2, $priority3);

        $result = $block1->swap(1, $page);
        $priority1 = Application::i()->SQL->getValue([$sqlQuery, [$block1->id]]);
        $priority2 = Application::i()->SQL->getValue([$sqlQuery, [$block2->id]]);
        $priority3 = Application::i()->SQL->getValue([$sqlQuery, [$block3->id]]);

        $this->assertFalse($result);
        $this->assertEquals($initialPriority3, $priority1);
        $this->assertEquals($initialPriority1, $priority2);
        $this->assertEquals($initialPriority2, $priority3);

        $result = $block1->swap(-1, $page);
        $priority1 = Application::i()->SQL->getValue([$sqlQuery, [$block1->id]]);
        $priority2 = Application::i()->SQL->getValue([$sqlQuery, [$block2->id]]);
        $priority3 = Application::i()->SQL->getValue([$sqlQuery, [$block3->id]]);

        $this->assertTrue($result);
        $this->assertEquals($initialPriority2, $priority1);
        $this->assertEquals($initialPriority1, $priority2);
        $this->assertEquals($initialPriority3, $priority3);

        $result = $block1->swap(-1, $page);
        $priority1 = Application::i()->SQL->getValue([$sqlQuery, [$block1->id]]);
        $priority2 = Application::i()->SQL->getValue([$sqlQuery, [$block2->id]]);
        $priority3 = Application::i()->SQL->getValue([$sqlQuery, [$block3->id]]);

        $this->assertTrue($result);
        $this->assertEquals($initialPriority1, $priority1);
        $this->assertEquals($initialPriority2, $priority2);
        $this->assertEquals($initialPriority3, $priority3);
    }


    /**
     * Тест метода unassoc()
     */
    public function testUnassoc()
    {
        $block = new Block_HTML(['name' => 'Тестовый текст', 'location' => 'content', 'description' => 'Тест', 'cats' => [1, 2]]);
        $block->commit();

        $block = Block::spawn($block->id);

        $this->assertEquals([1, 2], $block->pages_ids);

        $block->unassoc(new Page(2));
        $block = Block::spawn($block->id);

        $this->assertEquals([1], $block->pages_ids);

        $block->unassoc(new Page(1));
        $block = Block::spawn($block->id);

        $this->assertEmpty($block->id); // Блок удален
    }


    /**
     * Тест метода process()
     */
    public function testProcess()
    {
        $interfaceCode = '<' . "?php \$GLOBALS['interfaceResult'] = [
            'SITE' => \$SITE,
            'Page' => \$Page,
            'page' => \$page,
            'Block' => \$Block,
            'block' => \$block,
            'Interface' => \$Interface,
            'Widget' => \$Widget,
            'config' => \$config,
        ];
        return ['aaa' => 'bbb'];
        ";
        $interface = new Snippet(['urn' => 'testinterface', 'description' => $interfaceCode]);
        $interface->commit();

        $widgetCode = '<' . "?php \$GLOBALS['widgetResult'] = [
            'SITE' => \$SITE,
            'Page' => \$Page,
            'page' => \$page,
            'Block' => \$Block,
            'block' => \$block,
            'Interface' => \$Interface,
            'Widget' => \$Widget,
            'aaa' => \$aaa,
        ];
        echo 'aaa';
        ";
        $widget = new Snippet(['urn' => 'testwidget', 'description' => $widgetCode]);
        $widget->commit();

        $cacheCode = '<' . "?php \$GLOBALS['cacheResult'] = [
            'SITE' => \$SITE,
            'Page' => \$Page,
            'page' => \$page,
            'Block' => \$Block,
            'block' => \$block,
            'aaa' => \$aaa,
        ];";
        $cache = new Snippet(['urn' => 'testcache', 'description' => $cacheCode]);
        $cache->commit();

        $block = new Block_PHP([
            'cache_type' => Block::CACHE_DATA,
            'interface_id' => $interface->id,
            'widget_id' => $widget->id,
            'cache_interface_id' => $cache->id,
        ]);
        $page = new Page(3); // Услуги

        ob_start();
        $block->process($page);
        $html = ob_get_clean();
        $interfaceResult = $GLOBALS['interfaceResult'];
        $widgetResult = $GLOBALS['widgetResult'];
        $cacheResult = $GLOBALS['cacheResult'];
        unset($GLOBALS['interfaceResult'], $GLOBALS['widgetResult'], $GLOBALS['cacheResult']);

        $this->assertEquals('aaa', $html);

        $this->assertInstanceOf(Page::class, $interfaceResult['SITE']);
        $this->assertEquals(1, $interfaceResult['SITE']->id);
        $this->assertInstanceOf(Page::class, $interfaceResult['Page']);
        $this->assertEquals($page, $interfaceResult['Page']);
        $this->assertEquals($page, $interfaceResult['page']);
        $this->assertEquals($block, $interfaceResult['Block']);
        $this->assertEquals($block, $interfaceResult['block']);
        $this->assertInstanceOf(Snippet::class, $interfaceResult['Interface']);
        $this->assertEquals($interface->id, $interfaceResult['Interface']->id);
        $this->assertInstanceOf(Snippet::class, $interfaceResult['Widget']);
        $this->assertEquals($widget->id, $interfaceResult['Widget']->id);

        $this->assertInstanceOf(Page::class, $widgetResult['SITE']);
        $this->assertEquals(1, $widgetResult['SITE']->id);
        $this->assertInstanceOf(Page::class, $widgetResult['Page']);
        $this->assertEquals($page, $widgetResult['Page']);
        $this->assertEquals($page, $widgetResult['page']);
        $this->assertEquals($block, $widgetResult['Block']);
        $this->assertEquals($block, $widgetResult['block']);
        $this->assertInstanceOf(Snippet::class, $widgetResult['Interface']);
        $this->assertEquals($interface->id, $widgetResult['Interface']->id);
        $this->assertInstanceOf(Snippet::class, $widgetResult['Widget']);
        $this->assertEquals($widget->id, $widgetResult['Widget']->id);
        $this->assertEquals('bbb', $widgetResult['aaa']);

        $this->assertInstanceOf(Page::class, $cacheResult['SITE']);
        $this->assertEquals(1, $cacheResult['SITE']->id);
        $this->assertInstanceOf(Page::class, $cacheResult['Page']);
        $this->assertEquals($page, $cacheResult['Page']);
        $this->assertEquals($page, $cacheResult['page']);
        $this->assertEquals($block, $cacheResult['Block']);
        $this->assertEquals($block, $cacheResult['block']);
        $this->assertEquals('bbb', $cacheResult['aaa']);

        Snippet::delete($interface);
        Snippet::delete($widget);
        Snippet::delete($cache);
        Block::delete($block);
    }


    /**
     * Тест метода process() - случай с указанием класса интерфейса
     */
    public function testProcessWithInterfaceClassname()
    {
        $widget = new Snippet(16);
        $widgetFilename = Package::i()->resourcesDir . '/widgets/materials/features/features_main.tmp.php';
        $widget->description = file_get_contents($widgetFilename);
        $widget->commit();

        $block = new Block_Material([
            'material_type' => 1, // Преимущества
            'interface_classname' => MaterialInterface::class,
            'widget_id' => 16, // Преимущества на главной
            'cats' => [1], // Главная
            'sort_field_default' => 'post_date',
            'sort_order_default' => 'asc',
        ]);
        $page = new Page(1); // Главная

        ob_start();
        $block->process($page);
        $html = ob_get_clean();

        $this->assertStringContainsString('Клиент-ориентированный подход', $html);
        $this->assertStringContainsString('features-main', $html);
    }


    /**
     * Тест метода process() - случай с указанием класса кэширующего интерфейса
     */
    public function testProcessWithCacheInterfaceClassname()
    {
        $block = new Block_Menu(14); // Верхнее меню;
        $block->cache_interface_id = 0;
        $block->cache_interface_classname = CacheInterface::class;
        $page = new Page(1); // Главная
        $filename = Package::i()->cacheDir . '/raas_cache_block' . $block->id . '.php';
        if (is_file($filename)) {
            unlink($filename);
        }

        ob_start();
        $block->process($page);
        $html = ob_get_clean();

        $this->assertFileExists($filename);
        unlink($filename);
    }


    /**
     * Тест метода process() - случай с закрытым доступом
     */
    public function testProcessWithDeniedAccess()
    {
        $block = new Block_PHP(36); // Спецпредложение
        $access = new CMSAccess(['block_id' => $block->id, 'to_type' => CMSAccess::TO_ALL, 'allow' => 0]);
        $access->commit();
        $page = new Page(1);

        ob_start();
        $result = $block->process($page);
        $html = ob_get_clean();

        $this->assertNull($result);
        $this->assertEmpty($html);

        CMSAccess::delete($access);
    }


    /**
     * Тест метода process() - случай с прекэшированным HTML
     */
    public function testProcessWithPrecachedHTML()
    {
        $block = new Block_PHP(['cache_type' => Block::CACHE_HTML]);
        $block->commit();
        $filename = Package::i()->cacheDir . '/raas_cache_block' . $block->id . '.php';
        file_put_contents($filename, 'aaa');
        $page = new Page(1);
        ob_start();
        $block->process($page);
        $html = ob_get_clean();

        $this->assertEquals('aaa', $html);

        unlink($filename);
        Block::delete($block);
    }


    /**
     * Тест метода process() - случай с выводом JSON и кэшем HTML
     */
    public function testProcessWithJSONAndCacheHTML()
    {
        $interfaceCode = '<' . "?php return ['aaa' => 'bbb'];";
        $interface = new Snippet(['urn' => 'testinterface', 'description' => $interfaceCode]);
        $interface->commit();
        $block = new Block_PHP([
            'cache_type' => Block::CACHE_HTML,
            'interface_id' => $interface->id,
            'cache_interface_classname' => CacheInterface::class,
        ]);
        $block->commit();
        $page = new Page(14); // AJAX
        $page->mime = 'application/json';
        ob_start();
        $block->process($page);
        $html = ob_get_clean();
        $filename = Package::i()->cacheDir . '/raas_cache_block' . $block->id . '.php';

        $this->assertEquals('{"aaa":"bbb"}', $html);
        $this->assertFileExists($filename);

        $cacheText = file_get_contents($filename);
        $this->assertEquals('{"aaa":"bbb"}', $cacheText);

        unlink($filename);
        Block::delete($block);
        Snippet::delete($interface);
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
        $block->Widget->commit();
        $page = new Page(7); // Новости
        ob_start();
        $block->process($page);
        ob_get_clean();

        $this->assertEquals('blocks', $callParams[0][0]);
        $this->assertEquals(22, $callParams[0][1]); // ID# блока
        $this->assertIsNumeric($callParams[0][2]);
        $this->assertEquals(null, $callParams[0][3]);
        $this->assertEquals('interfaceTime', $callParams[0][4]);

        $this->assertEquals('snippets', $callParams[1][0]);
        $this->assertEquals(22, $callParams[1][1]); // Сниппет "Новости"
        $this->assertIsNumeric($callParams[1][2]);
        $this->assertEquals('counter', $callParams[1][3]);
        $this->assertEquals('time', $callParams[1][4]);

        $this->assertEquals('blocks', $callParams[2][0]);
        $this->assertEquals(22, $callParams[2][1]); // ID# блока
        $this->assertIsNumeric($callParams[2][2]);
        $this->assertEquals(null, $callParams[2][3]);
        $this->assertEquals('widgetTime', $callParams[2][4]);

        $this->assertEquals('blocks', $callParams[3][0]);
        $this->assertEquals(22, $callParams[3][1]); // ID# блока
        $this->assertIsNumeric($callParams[3][2]);
        $this->assertEquals('counter', $callParams[3][3]);
        $this->assertEquals('time', $callParams[3][4]);
    }


    /**
     * Тест метода process() - проверка диагностики с активным материалом
     */
    public function testProcessCheckDiagWithActiveMaterial()
    {
        $callParams = [];
        $diag = $this->createMock(Diag::class);
        $diag->method('handle')->willReturnCallback(function () use (&$callParams) {
            array_push($callParams, func_get_args());
        });
        RAASControllerFrontend::i()->setDiag($diag);
        $block = Block::spawn(22); // Новости на странице новостей
        $block->Widget->commit();
        $page = new Page(7); // Новости
        $page->Material = new Material(7); // Первая новость
        ob_start();
        $block->process($page);
        ob_get_clean();

        $this->assertEquals('blocks', $callParams[0][0]);
        $this->assertEquals('22@m', $callParams[0][1]); // ID# блока
        $this->assertIsNumeric($callParams[0][2]);
        $this->assertEquals(null, $callParams[0][3]);
        $this->assertEquals('interfaceTime', $callParams[0][4]);

        $this->assertEquals('snippets', $callParams[1][0]);
        $this->assertEquals('22@m', $callParams[1][1]); // Сниппет "Новости"
        $this->assertIsNumeric($callParams[1][2]);
        $this->assertEquals('counter', $callParams[1][3]);
        $this->assertEquals('time', $callParams[1][4]);

        $this->assertEquals('blocks', $callParams[2][0]);
        $this->assertEquals('22@m', $callParams[2][1]); // ID# блока
        $this->assertIsNumeric($callParams[2][2]);
        $this->assertEquals(null, $callParams[2][3]);
        $this->assertEquals('widgetTime', $callParams[2][4]);

        $this->assertEquals('blocks', $callParams[3][0]);
        $this->assertEquals('22@m', $callParams[3][1]); // ID# блока
        $this->assertIsNumeric($callParams[3][2]);
        $this->assertEquals('counter', $callParams[3][3]);
        $this->assertEquals('time', $callParams[3][4]);
    }


    /**
     * Тест метода getCacheFile()
     * @param int $cacheType Тип кэша блока
     * @param ?string $url URL для получения имени файла
     * @param ?int $pageId Страница для получения имени файла,
     * @param ?string $expected Имя относительно папки с кэшами
     */
    #[TestWith([Block::CACHE_NONE, null, null, ''])]
    #[TestWith([Block::CACHE_HTML, null, null, 'raas_cache_block12.php'])]
    #[TestWith([Block::CACHE_HTML, '/aaa/', null, 'raas_cache_block12.localhost%2Faaa%2F.php'])] // Здесь localhost, т.к. берется из HTTP_HOST
    #[TestWith([Block::CACHE_HTML, null, 4, 'raas_cache_block12.test%2Fservices%2Fservice1%2F.php'])] // Здесь test, т.к. берется из страницы
    public function testGetCacheFile(int $cacheType, ?string $url = null, ?int $pageId = null, ?string $expected = null)
    {
        $block = Block::spawn(12); // Текстовый блок
        $block->cache_type = $cacheType;

        if ($url || $pageId) {
            $block->cache_single_page = 1;
        }

        $result = $block->getCacheFile($url, $pageId ? new Page($pageId) : null);

        if ($expected) {
            $this->assertEquals(Package::i()->cacheDir . '/' . $expected, $result);
        } else {
            $this->assertEmpty($result);
        }
    }


    /**
     * Тест метода loadCache()
     */
    public function testLoadCache()
    {
        $block = Block::spawn(12); // Добро пожаловать
        $block->cache_type = Block::CACHE_HTML;
        $filename = Package::i()->cacheDir . '/raas_cache_block12.php';
        file_put_contents($filename, '<' . '?php return "aaa";');

        $result = $block->loadCache();

        $this->assertEquals('aaa', $result);

        unlink($filename);
    }


    /**
     * Тест метода clearCache()
     */
    public function testClearCache()
    {
        $block = Block::spawn(12); // Добро пожаловать
        $block->cache_type = Block::CACHE_HTML;
        $filename = Package::i()->cacheDir . '/raas_cache_block12.localhost%2Faaa%2F.php';
        $filename2 = Package::i()->cacheDir . '/raas_cache_block12.php';
        touch($filename);
        touch($filename2);

        $result = $block->clearCache();

        $this->assertFileDoesNotExist($filename);
        $this->assertFileDoesNotExist($filename2);
    }


    /**
     * Проверка получения свойства Location
     */
    public function testGetLocation()
    {
        $template = new Template(1);
        $template->description = file_get_contents(Package::i()->resourcesDir . '/template.tmp.php');
        $template->commit();
        $block = Block::spawn(12);

        $result = $block->Location;

        $this->assertInstanceOf(Location::class, $result);
        $this->assertEquals('content', $result->urn);
    }


    /**
     * Тест метода _tablename2()
     */
    public function testTablename2()
    {
        $result = Block_HTML::_tablename2();

        $this->assertEquals('cms_blocks_html', $result);
    }


    /**
     * Тест метода delete()
     */
    public function testDelete()
    {
        $block = new Block_HTML(['name' => 'Тестовый текст', 'location' => 'content', 'description' => 'Тест', 'cats' => [1]]);
        $block->commit();
        $blockId = (int)$block->id;

        $sqlQuery = "SELECT description FROM cms_blocks_html WHERE id = ?";
        $sqlBind = [$blockId];
        $sqlResult = Application::i()->SQL->getvalue([$sqlQuery, $sqlBind]);

        $this->assertEquals('Тест', $sqlResult);

        Block::delete($block);

        $sqlResult = Application::i()->SQL->getvalue([$sqlQuery, $sqlBind]);
        $this->assertEmpty($sqlResult);

        $block = Block::spawn($blockId);
        $this->assertInstanceOf(Block_HTML::class, $block);
        $this->assertEmpty($block->id);
    }
}
