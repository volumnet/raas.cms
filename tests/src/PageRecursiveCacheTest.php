<?php
/**
 * Файл теста рекурсивного кэша страниц
 */
namespace RAAS\CMS;

/**
 * Класс теста рекурсивного кэша страниц
 */
class PageRecursiveCacheTest extends BaseDBTest
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }


    /**
     * Проверяет свойство allowedIds
     */
    public function testAllowedIds()
    {
        $cache = PageRecursiveCache::i();

        $result = $cache->allowedIds;

        $this->assertEquals(18, $result['18']);
        $this->assertEquals(17, $result['17']);
        $this->assertEquals(1, $result['1']);
        $this->assertNull($result['0']);
        $this->assertNull($result['4']);

        $cmsAccess = new CMSAccess(['page_id' => 17, 'allow' => 0, 'to_type' => 1]);
        $cmsAccess->commit();
        CMSAccess::refreshPagesAccessCache();
        $cache->refresh();
        $result = $cache->allowedIds;

        $this->assertNull($result['18']);
        $this->assertNull($result['17']);
        $this->assertEquals(1, $result['1']);
        $this->assertNull($result['0']);
        $this->assertNull($result['4']);

        CMSAccess::delete($cmsAccess);
        CMSAccess::refreshPagesAccessCache();
        $cache->refresh();
    }


    /**
     * Проверяет свойство systemIds
     */
    public function testSystemIds()
    {
        $cache = PageRecursiveCache::i();

        $result = $cache->systemIds;

        $this->assertEquals(9, $result['9']);
        $this->assertEquals(10, $result['10']);
        $this->assertEquals(11, $result['11']);
        $this->assertNull($result['0']);
        $this->assertNull($result['1']);
        $this->assertNull($result['16']);
    }


    /**
     * Проверяет свойство visibleIds
     */
    public function testVisibleIds()
    {
        $cache = PageRecursiveCache::i();

        $result = $cache->visibleIds;

        $this->assertEquals(18, $result['18']);
        $this->assertEquals(17, $result['17']);
        $this->assertEquals(1, $result['1']);
        $this->assertNull($result['0']);
        $this->assertNull($result['4']);

        $cmsAccess = new CMSAccess(['page_id' => 17, 'allow' => 0, 'to_type' => 1]);
        $cmsAccess->commit();
        CMSAccess::refreshPagesAccessCache();
        $cache->refresh();
        $result = $cache->visibleIds;

        $this->assertNull($result['18']);
        $this->assertNull($result['17']);
        $this->assertEquals(1, $result['1']);
        $this->assertNull($result['0']);
        $this->assertNull($result['4']);

        CMSAccess::delete($cmsAccess);
        CMSAccess::refreshPagesAccessCache();
        $cache->refresh();
    }


    /**
     * Тест получения ID# видимых дочерних сущностей всех уровней
     */
    public function testGetVisAllChildrenIds()
    {
        $cache = PageRecursiveCache::i();

        $result = $cache->getVisAllChildrenIds(
            16,
            PageRecursiveCache::ASSOC_BOTH
        );

        $this->assertEquals([
            '17' => 17,
            '21' => 21,
            '22' => 22,
            '18' => 18,
            '19' => 19,
            '20' => 20
        ], $result);

        $cmsAccess = new CMSAccess(['page_id' => 17, 'allow' => 0, 'to_type' => 1]);
        $cmsAccess->commit();
        CMSAccess::refreshPagesAccessCache();
        $cache->refresh();
        $result = $cache->getVisAllChildrenIds(
            [new Page(16)],
            PageRecursiveCache::ASSOC_OUTER
        );

        $this->assertEquals(['16' => [21, 22]], $result);
        $this->assertEquals(['16' => [21, 22]], $result);

        CMSAccess::delete($cmsAccess);
        CMSAccess::refreshPagesAccessCache();
        $cache->refresh();
    }
}
