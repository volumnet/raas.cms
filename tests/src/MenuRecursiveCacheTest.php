<?php
/**
 * Файл теста рекурсивного кэша меню
 */
namespace RAAS\CMS;

/**
 * Класс теста рекурсивного кэша меню
 */
class MenuRecursiveCacheTest extends BaseDBTest
{
    public static function setUpBeforeClass()
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
        $this->assertNull($result['0']);
        $this->assertNull($result['6']);
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
        $this->assertNull($result['7']);
        $this->assertNull($result['0']);
        $this->assertNull($result['6']);
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
