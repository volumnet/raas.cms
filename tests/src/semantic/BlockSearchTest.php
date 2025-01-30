<?php
/**
 * Тест класса Block_Search
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;
use RAAS\Application;
use RAAS\Controller_Frontend as RAASControllerFrontend;

/**
 * Тест класса Block_Search
 */
#[CoversClass(Block_Search::class)]
class BlockSearchTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_pages_cache',
        'cms_blocks',
        'cms_blocks_material',
        'cms_blocks_pages_assoc',
        'cms_blocks_search',
        'cms_blocks_search_languages_assoc',
        'cms_blocks_search_material_types_assoc',
        'cms_blocks_search_pages_assoc',
        'cms_data',
        'cms_feedback',
        'cms_fieldgroups',
        'cms_fields',
        'cms_fields_form_vis',
        'cms_forms',
        'cms_material_types',
        'cms_material_types_affected_pages_for_materials_cache',
        'cms_material_types_affected_pages_for_self_cache',
        'cms_materials',
        'cms_materials_affected_pages_cache',
        'cms_materials_pages_assoc',
        'cms_menus',
        'cms_pages',
        'cms_shop_blocks_yml_pages_assoc',
        'cms_shop_cart_types_material_types_assoc',
    ];


    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        RAASControllerFrontend::i()->exportLang(Application::i(), 'ru');
        RAASControllerFrontend::i()->exportLang(Package::i(), 'ru');
    }


    /**
     * Тест метода commit и конструктора
     */
    public function testCommit()
    {
        $block = new Block_Search([
            'cats' => [3], // Услуги
            'search_var_name' => 'query_string',
            'min_length' => 3,
            'pages_var_name' => 'page',
            'rows_per_page' => 20,
            'mtypes' => [3], // Новости
            'languages' => ['ru'],
            'search_pages_ids' => [3], // Услуги
        ]);
        $block->commit();
        $blockId = $block->id;

        $block = new Block_Search($blockId);
        $searchPagesIds = array_map(function ($x) {
            return (int)$x->id;
        }, $block->search_pages);
        $this->assertStringContainsString('Поиск', $block->name);
        $this->assertContains(3, $block->mtypes);
        $this->assertContains(3, $searchPagesIds);

        Block_Search::delete($block);
    }


    /**
     * Тест метода getAddData()
     */
    public function testGetAddData()
    {
        $block = new Block_Search([
            'cats' => [3], // Услуги
            'search_var_name' => 'query_string',
            'min_length' => 3,
            'pages_var_name' => 'page',
            'rows_per_page' => 20,
            'mtypes' => [3], // Новости
            'languages' => ['ru'],
            'search_pages_ids' => [3], // Услуги
        ]);

        $result = $block->getAddData();

        $this->assertEquals(0, $result['id']);
        $this->assertEquals('query_string', $result['search_var_name']);
        $this->assertEquals(3, $result['min_length']);
        $this->assertEquals('page', $result['pages_var_name']);
        $this->assertEquals(20, $result['rows_per_page']);

        $block->commit();
        $blockId = $block->id;

        $result = $block->getAddData();

        $this->assertEquals($blockId, $result['id']);
        $this->assertEquals('query_string', $result['search_var_name']);
        $this->assertEquals(3, $result['min_length']);
        $this->assertEquals('page', $result['pages_var_name']);
        $this->assertEquals(20, $result['rows_per_page']);

        Block_Search::delete($block);
    }


    /**
     * Тест метода pageCommitEventListener()
     */
    public function testPageCommitEventListener()
    {
        $block = new Block_Search([
            'cats' => [3], // Услуги
            'search_var_name' => 'query_string',
            'min_length' => 3,
            'pages_var_name' => 'page',
            'rows_per_page' => 20,
            'mtypes' => [3], // Новости
            'languages' => ['ru'],
            'search_pages_ids' => [3], // Услуги
        ]);
        $block->commit();
        $blockId = $block->id;
        $page = new Page(['name' => 'test', 'pid' => 3]);
        $page->commit();
        $block = new Block_Search($blockId);

        $searchPagesIds = array_map(function ($x) {
            return (int)$x->id;
        }, $block->search_pages);
        $this->assertContains((int)$page->id, $searchPagesIds);

        Page::delete($page);
        Block_Search::delete($block);
    }


    /**
     * Тест метода materialTypeCommitEventListener()
     */
    public function testMaterialTypeCommitEventListener()
    {
        $block = new Block_Search([
            'cats' => [3], // Услуги
            'search_var_name' => 'query_string',
            'min_length' => 3,
            'pages_var_name' => 'page',
            'rows_per_page' => 20,
            'mtypes' => [3], // Новости
            'languages' => ['ru'],
            'search_pages_ids' => [3], // Услуги
        ]);
        $block->commit();
        $blockId = $block->id;
        $materialType = new Material_Type(['name' => 'test', 'pid' => 3]);
        $materialType->commit();
        $block = new Block_Search($blockId);

        $this->assertContains((int)$materialType->id, array_map('intval', $block->mtypes));

        Material_Type::delete($materialType);
        Block_Search::delete($block);
    }
}
