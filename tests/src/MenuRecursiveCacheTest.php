<?php
/**
 * Файл теста рекурсивного кэша меню
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Класс теста рекурсивного кэша меню
 */
class MenuRecursiveCacheTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_pages_cache',
        'cms_fields',
        'cms_groups',
        'cms_material_types_affected_pages_for_materials_cache',
        'cms_materials',
        'cms_materials_affected_pages_cache',
        'cms_materials_pages_assoc',
        'cms_menus',
        'cms_pages',
        'cms_users',
        'cms_users_groups_assoc',
    ];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $cmsAccess = new CMSAccess(['page_id' => 3, 'allow' => 0, 'to_type' => 1]);
        $cmsAccess->commit();
        CMSAccess::refreshPagesAccessCache();
    }


    /**
     * Тест свойства $cache
     */
    public function testCache()
    {
        $cache = MenuRecursiveCache::i();

        $result = $cache->cache;

        $this->assertEquals('О компании', $result['5']['name']);
        $this->assertEquals(true, $result['5']['realized']);
    }


    /**
     * Проверяет свойство allowedIds
     */
    public function testAllowedIds()
    {
        $cache = MenuRecursiveCache::i();

        $result = $cache->allowedIds;

        $this->assertEquals(10, $result['10']);
        $this->assertEquals(5, $result['5']);
        $this->assertEquals(7, $result['7']);
        $this->assertNull($result['0'] ?? null);
        $this->assertNull($result['6'] ?? null);
    }


    /**
     * Проверяет свойство visibleIds
     */
    public function testVisibleIds()
    {
        $cache = MenuRecursiveCache::i();

        $result = $cache->visibleIds;

        $this->assertEquals(10, $result['10']);
        $this->assertEquals(5, $result['5']);
        $this->assertNull($result['7'] ?? null);
        $this->assertNull($result['0'] ?? null);
        $this->assertNull($result['6'] ?? null);
    }


    /**
     * Тест обновления всех данных кэша
     */
    public function testRefresh()
    {
        $cache = MenuRecursiveCache::i();
        $page = new Page(15);
        $menu = new Menu(9);

        $page->urn = 'production';
        $page->commit();
        $menu->name = 'Продукция';
        $menu->commit();
        $result = $cache->cache[9];
        $pageResult = PageRecursiveCache::i()->cache[15];

        $this->assertEquals('Каталог продукции', $result['name']);
        $this->assertEquals('catalog', $pageResult['urn']);

        $cache->refresh();
        $result = $cache->cache[9];
        $pageResult = PageRecursiveCache::i()->cache[15];

        $this->assertEquals('Продукция', $result['name']);
        $this->assertEquals('production', $pageResult['urn']);

        $page->urn = 'catalog';
        $page->commit();
        $menu->name = 'Каталог продукции';
        $menu->commit();
        $cache->refresh();
    }
}
