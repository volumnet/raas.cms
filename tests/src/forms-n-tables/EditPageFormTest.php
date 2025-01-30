<?php
/**
 * Тест класса EditPageForm
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;
use RAAS\Application;
use RAAS\Field as RAASField;
use RAAS\FormTab;
use RAAS\User as RAASUser;

/**
 * Тест класса EditPageForm
 */
#[CoversClass(EditPageForm::class)]
class EditPageFormTest extends BaseTest
{
    public static $tables = [
        'attachments',
        'cms_access',
        'cms_access_pages_cache',
        'cms_blocks',
        'cms_blocks_material',
        'cms_blocks_pages_assoc',
        'cms_blocks_search_pages_assoc',
        'cms_data',
        'cms_fields',
        'cms_groups',
        'cms_material_types',
        'cms_material_types_affected_pages_for_materials_cache',
        'cms_material_types_affected_pages_for_self_cache',
        'cms_materials',
        'cms_materials_affected_pages_cache',
        'cms_materials_pages_assoc',
        'cms_menus',
        'cms_pages',
        'cms_shop_blocks_yml_pages_assoc',
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
     * Тест конструктора класса
     */
    public function testConstruct()
    {
        $form = new EditPageForm(['Item' => new Page(2)]);

        $this->assertEquals('Редактирование страницы', $form->caption);
        $this->assertInstanceOf(FormTab::class, $form->children['common']);
        $this->assertInstanceOf(FormTab::class, $form->children['seo']);
        $this->assertInstanceOf(FormTab::class, $form->children['service']);
        $this->assertInstanceOf(FormTab::class, $form->children['access']);
    }


    /**
     * Тест конструктора класса - случай новой страницы внутри существующей
     */
    public function testConstructWithNewItemAndWithParent()
    {
        $form = new EditPageForm(['Item' => new Page(), 'Parent' => new Page(1)]);

        $this->assertEquals('Создание страницы', $form->caption);
    }


    /**
     * Тест конструктора класса - случай нового сайта
     */
    public function testConstructWithNewItemAndNoParent()
    {
        $form = new EditPageForm(['Item' => new Page()]);

        $this->assertEquals('Создание сайта', $form->caption);
    }


    /**
     * Тест конструктора класса - случай корневой страницы
     */
    public function testConstructWithRootItemAndNoParent()
    {
        $form = new EditPageForm(['Item' => new Page(1)]);

        $this->assertEquals('Редактирование сайта', $form->caption);
    }


    /**
     * Тест получения свойства view
     */
    public function testGetView()
    {
        $form = new EditPageForm();

        $this->assertInstanceOf(ViewSub_Main::class, $form->view);
    }


    /**
     * Тест получения наследуемых свойств
     */
    public function testGetDefault()
    {
        $form = new EditPageForm(['Item' => new Page(1)]);

        $result = $form->Item;

        $this->assertInstanceOf(Page::class, $result);
        $this->assertEquals(1, $result->id);
    }


    /**
     * Тест метода process()
     */
    public function testProcess()
    {
        $form = new EditPageForm(['Item' => new Page(1)]);

        $result = $form->process();

        $this->assertEquals('', $result['DATA']['response_code']);
        $this->assertEquals('test', $result['DATA']['urn'][0]);
        $this->assertEquals(false, $result['DATA']['inherit_noindex']);
    }


    /**
     * Тест метода process() - случай с сохранением
     */
    public function testProcessWithExport()
    {
        $oldPost = $_POST;
        $oldServer = $_SERVER;
        Application::i()->user = new RAASUser(1);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'urn' => ['test2', 'test2.volumnet.ru'],
            'name' => 'Тестовый домен 2',
            '_description_' => 'aaa',
            'inherit__description_' => 1
        ];
        $form = new EditPageForm(['Item' => new Page()]);

        $result = $form->process();

        $item = $result['Item'];

        $this->assertInstanceOf(Page::class, $item);
        $this->assertNotEmpty($item->id);
        $this->assertEquals('Тестовый домен 2', $item->name);
        $this->assertEquals('test2 test2.volumnet.ru', $item->urn);
        $this->assertEquals('aaa', $item->_description_);
        $this->assertEquals(true, $item->fields['_description_']->inherited);
        $this->assertEquals(1, $item->author_id);
        $this->assertEquals(1, $item->editor_id);

        Application::i()->user = new RAASUser();
        $_POST = $oldPost;
        $_SERVER = $oldServer;
        Page::delete($item);
    }
}
