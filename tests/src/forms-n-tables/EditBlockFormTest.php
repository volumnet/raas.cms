<?php
/**
 * Тест класса EditBlockForm
 */
namespace RAAS\CMS;

use SOME\BaseTest;
use RAAS\Application;
use RAAS\Field as RAASField;
use RAAS\FormTab;
use RAAS\User as RAASUser;

/**
 * Тест класса EditBlockForm
 * @covers RAAS\CMS\EditBlockForm
 */
class EditBlockFormTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_blocks_cache',
        'cms_access_pages_cache',
        'cms_blocks',
        'cms_blocks_material',
        'cms_blocks_material_filter',
        'cms_blocks_material_sort',
        'cms_blocks_pages_assoc',
        'cms_fields',
        'cms_groups',
        'cms_material_types',
        'cms_menus',
        'cms_pages',
        'cms_snippet_folders',
        'cms_snippets',
        'cms_templates',
        'cms_users',
        'users',
    ];


    public static function setUpBeforeClass(): void
    {
        Application::i()->initPackages();
        parent::setUpBeforeClass();
    }


    /**
     * Тест получения свойства view
     */
    public function testGetView()
    {
        $form = new EditBlockForm();

        $result = $form->view;

        $this->assertInstanceOf(ViewSub_Main::class, $result);
    }


    /**
     * Тест получения наследуемых свойств
     */
    public function testGetDefault()
    {
        $form = new EditBlockForm(['Item' => Block::spawn(39)]); // 39 - блок информера корзины

        $result = $form->Item;

        $this->assertInstanceOf(Block_PHP::class, $result);
        $this->assertEquals(39, $result->id);
    }


    /**
     * Тест метода getMetaCats()
     */
    public function testGetMetaCats()
    {
        $form = new EditBlockPHPForm();

        $result = $form->getMetaCats();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals(1, $result[0]['value']);
        $this->assertEquals('Главная', $result[0]['caption']);
        $this->assertEquals(1, $result[0]['data-group']);
        $this->assertEquals(2, $result[0]['children'][0]['value']);
        $this->assertEquals('О компании', $result[0]['children'][0]['caption']);
        $this->assertEquals(1, $result[0]['children'][0]['data-group']);
        $this->assertEquals(3, $result[0]['children'][1]['value']);
        $this->assertEquals('Услуги', $result[0]['children'][1]['caption']);
        $this->assertEquals(1, $result[0]['children'][1]['data-group']);
    }


    /**
     * Тест конструктора класса
     */
    public function testConstruct()
    {
        $oldGet = $_GET;
        $oldServer = $_SERVER;
        $_GET = ['loc' => 'content'];
        $_SERVER['REQUEST_URI'] = '/admin/?p=cms&action=edit_block&loc=content&type=Block_PHP';
        $form = new EditBlockMenuForm();
        $interfaceField = $form->children['serviceTab']->children['interface_id'];
        $cacheInterfaceField = $form->children['serviceTab']->children['cache_interface_id'];
        $widgetField = $form->children['commonTab']->children['widget_id'];

        $this->assertNull($form->Item);
        $this->assertInstanceOf(InterfaceField::class, $interfaceField);
        $this->assertEquals(MenuInterface::class, $interfaceField->meta['rootInterfaceClass']);
        $this->assertEquals(MenuInterface::class, $interfaceField->default);
        $this->assertInstanceOf(InterfaceField::class, $cacheInterfaceField);
        $this->assertEquals(CacheInterface::class, $cacheInterfaceField->meta['rootInterfaceClass']);
        $this->assertEquals(CacheInterface::class, $cacheInterfaceField->default);
        $this->assertInstanceOf(WidgetField::class, $widgetField);
        $this->assertInstanceOf(FormTab::class, $form->children['commonTab']);
        $this->assertInstanceOf(FormTab::class, $form->children['serviceTab']);
        $this->assertInstanceOf(FormTab::class, $form->children['pagesTab']);
        $this->assertEquals('content', $form->children['pagesTab']->children['location']->default);
        $this->assertStringContainsString('loc=content', $form->newUrl);

        $_GET = $oldGet;
        $_SERVER = $oldServer;
    }


    /**
     * Тест конструктора класса - случай с полями постраничной разбивки
     */
    public function testConstructWithPaginationFields()
    {
        $form = new EditBlockMaterialForm([]);

        $pagesVarField = $form->children['serviceTab']->children['pages_var_name'];
        $rowsPerPageField = $form->children['serviceTab']->children['rows_per_page'];

        $this->assertInstanceOf(RAASField::class, $pagesVarField);
        $this->assertEquals('pages_var_name', $pagesVarField->name);
        $this->assertInstanceOf(RAASField::class, $rowsPerPageField);
        $this->assertEquals('rows_per_page', $rowsPerPageField->name);
        $this->assertEquals('number', $rowsPerPageField->type);
    }


    /**
     * Тест конструктора класса - случай с импортом полей
     */
    public function testConstructWithImport()
    {
        // 34 - блок "Каталог продукции", 15 - страница "Каталог продукции"
        $form = new EditBlockMaterialForm(['Item' => Block::spawn(34), 'meta' => ['Parent' => new Page(15)]]);
        $result = $form->process();

        $this->assertEquals('metaTemplates', $form->DATA['params_name'][0]);
        $this->assertEquals('template', $form->DATA['params_value'][0]);
        $this->assertEquals('withChildrenGoods', $form->DATA['params_name'][1]);
        $this->assertEquals('1', $form->DATA['params_value'][1]);
        $this->assertEquals(
            ['value' => 'head_counters', 'caption' => 'head_counters'],
            $form->meta['CONTENT']['locations'][0]
        );
        $this->assertEquals(
            ['value' => 'logo', 'caption' => 'logo'],
            $form->meta['CONTENT']['locations'][1]
        );
    }


    /**
     * Тест конструктора класса - случай с проверкой правильности полей
     */
    public function testConstructWithCheck()
    {
        $oldPost = $_POST;
        $oldServer = $_SERVER;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [];
        $form = new EditBlockForm(['Item' => new Block_PHP(), 'meta' => ['Parent' => new Page(1)]]);
        $result = $form->process();

        $this->assertCount(1, $form->localError);
        $this->assertEquals('MISSED', $form->localError[0]['name']);
        $this->assertEquals('cats', $form->localError[0]['value']);

        $_POST = $oldPost;
        $_SERVER = $oldServer;
    }


    /**
     * Тест конструктора - случай с сохранением
     */
    public function testConstructWithExport()
    {
        $oldPost = $_POST;
        $oldServer = $_SERVER;
        Application::i()->user = new RAASUser(1);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'name' => 'Тестовый блок',
            'cats' => [1],
            'location' => 'content',
            'params_name' => ['param1', 'param2'],
            'params_value' => ['aaa', 'bbb'],
        ];
        $block = new Block_PHP();
        $form = new EditBlockForm(['Item' => $block, 'meta' => ['Parent' => new Page(1)]]);
        $result = $form->process();

        $this->assertNotEmpty($block->id);
        $this->assertEquals('Тестовый блок', $block->name);
        $this->assertEquals('content', $block->location);
        $this->assertEquals(1, $block->author_id);
        $this->assertEquals(1, $block->editor_id);
        $this->assertEquals('param1=aaa&param2=bbb', $block->params);

        $_POST = $oldPost;
        $_SERVER = $oldServer;
        Application::i()->user = new RAASUser();
        Block_PHP::delete($block);
    }
}
