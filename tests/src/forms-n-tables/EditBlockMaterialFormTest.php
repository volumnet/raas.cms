<?php
/**
 * Тест класса EditBlockMaterialForm
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
 * Тест класса EditBlockMaterialForm
 */
#[CoversClass(EditBlockMaterialForm::class)]
class EditBlockMaterialFormTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_blocks_cache',
        'cms_blocks',
        'cms_blocks_material',
        'cms_blocks_material_filter',
        'cms_blocks_material_sort',
        'cms_blocks_pages_assoc',
        'cms_fields',
        'cms_groups',
        'cms_material_types',
        'cms_material_types_affected_pages_for_materials_cache',
        'cms_material_types_affected_pages_for_self_cache',
        'cms_materials',
        'cms_materials_affected_pages_cache',
        'cms_materials_pages_assoc',
        'cms_pages',
        'cms_snippet_folders',
        'cms_snippets',
        'cms_templates',
        'cms_users',
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
        $form = new EditBlockMaterialForm();
        $interfaceField = $form->children['serviceTab']->children['interface_id'];
        $widgetField = $form->children['commonTab']->children['widget_id'];

        $this->assertInstanceOf(InterfaceField::class, $interfaceField);
        $this->assertEquals(MaterialInterface::class, $interfaceField->default);
        $this->assertEquals(MaterialInterface::class, $interfaceField->meta['rootInterfaceClass']);
        $this->assertInstanceOf(WidgetField::class, $widgetField);
    }


    /**
     * Тест конструктора класса
     */
    public function testConstructWithImport()
    {
        $block = new Block_Material([
            'material_type' => 1, // Преимущества
            'cats' => [3], // Услуги
            'pages_var_name' => 'page',
            'rows_per_page' => 20,
            'sort_var_name' => 'sort',
            'order_var_name' => 'order',
            'sort_field_default' => 'post_date',
            'sort_order_default' => 'desc!',
            'filter' => [
                ['var' => 'name', 'relation' => '=', 'field' => 'name'],
            ],
            'sort' => [
                ['var' => '13', 'field' => '13', 'relation' => 'asc'], // 13 - значок
            ],
        ]);
        $block->commit();

        $form = new EditBlockMaterialForm(['Item' => $block]);
        $form->process();

        $this->assertContains(13, array_map(function ($x) {
            // 12 - поле "Значок" у преимуществ (изображение не подходит, т.к. фильтрация по медиа-полям)
            return (int)$x->value;
        }, (array)$form->children['serviceTab']->children['sorting_params']->children['sort_field_default']->children));
        $this->assertNotContains(14, array_map(function ($x) {
            // 14 - поле "Адрес ссылки" у баннеров
            return (int)$x->value;
        }, (array)$form->children['serviceTab']->children['sorting_params']->children['sort_field_default']->children));
        $this->assertEquals('name', $form->DATA['filter_var'][0] ?? null);
        $this->assertEquals('=', $form->DATA['filter_relation'][0] ?? null);
        $this->assertEquals('name', $form->DATA['filter_field'][0] ?? null);
        $this->assertEquals('13', $form->DATA['sort_var'][0] ?? null);
        $this->assertEquals('13', $form->DATA['sort_field'][0] ?? null);
        $this->assertEquals('asc', $form->DATA['sort_relation'][0] ?? null);

        Block_Material::delete($block);
    }


    /**
     * Тест конструктора - случай с сохранением
     */
    public function testConstructWithExport()
    {
        $oldPost = $_POST;
        $oldServer = $_SERVER;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'material_type' => 1,
            'name' => 'Тестовый блок',
            'cats' => [1],
            'location' => 'content',
            'filter_var' => ['name'],
            'filter_relation' => ['='],
            'filter_field' => ['name'],
            'sort_var' => ['13'],
            'sort_field' => ['13'],
            'sort_relation' => ['asc'],
        ];
        $block = new Block_Material();
        $form = new EditBlockMaterialForm(['Item' => $block, 'meta' => ['Parent' => new Page(1)]]);
        $result = $form->process();

        $this->assertNotEmpty($block->id);
        $this->assertEquals(1, $block->material_type);
        $this->assertContains(13, array_map(function ($x) {
            // 12 - поле "Значок" у преимуществ (изображение не подходит, т.к. фильтрация по медиа-полям)
            return (int)$x->value;
        }, (array)$form->children['serviceTab']->children['sorting_params']->children['sort_field_default']->children));
        $this->assertNotContains(14, array_map(function ($x) {
            // 14 - поле "Адрес ссылки" у баннеров
            return (int)$x->value;
        }, (array)$form->children['serviceTab']->children['sorting_params']->children['sort_field_default']->children));
        $this->assertEquals('name', $block->filter[0]['var'] ?? null);
        $this->assertEquals('=', $block->filter[0]['relation'] ?? null);
        $this->assertEquals('name', $block->filter[0]['field'] ?? null);
        $this->assertEquals('13', $block->sort[0]['var'] ?? null);
        $this->assertEquals('13', $block->sort[0]['field'] ?? null);
        $this->assertEquals('asc', $block->sort[0]['relation'] ?? null);

        $_POST = $oldPost;
        $_SERVER = $oldServer;
        Block_Material::delete($block);
    }
}
