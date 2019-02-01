<?php
/**
 * Файл теста абстрактного рекурсивного кэша с видимостью
 */
namespace RAAS\CMS;

/**
 * Класс теста абстрактного рекурсивного кэша с видимостью
 */
class VisibleRecursiveCacheTest extends BaseDBTest
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }


    /**
     * Проверяет свойство visibleIds
     */
    public function testVisibleIds()
    {
        $cache = ConcreteVisibleRecursiveCache::i();

        $result = $cache->visibleIds;

        $this->assertEquals(18, $result['18']);
        $this->assertEquals(17, $result['17']);
        $this->assertEquals(1, $result['1']);
        $this->assertNull($result['0']);

        $p = new Page(17);
        $p->vis = 0;
        $p->commit();
        $cache->refresh();
        $result = $cache->visibleIds;

        $this->assertEquals(18, $result['18']);
        $this->assertNull($result['17']);
        $this->assertEquals(1, $result['1']);
        $this->assertNull($result['0']);

        $p->vis = 1;
        $p->commit();
        $cache->refresh();
    }


    /**
     * Проверяет свойство visChildrenIds
     */
    public function testVisChildrenIds()
    {
        $cache = ConcreteVisibleRecursiveCache::i();

        $result = $cache->visChildrenIds;

        $this->assertEquals([17, 21, 22], array_values($result['16']));
        $this->assertEquals([17, 21, 22], array_keys($result['16']));

        $p = new Page(17);
        $p->vis = 0;
        $p->commit();
        $cache->refresh();
        $result = $cache->visChildrenIds;

        $this->assertEquals([21, 22], array_values($result['16']));
        $this->assertEquals([21, 22], array_keys($result['16']));

        $p->vis = 1;
        $p->commit();
        $cache->refresh();
    }


    /**
     * Проверяет свойство visAllChildrenIds
     */
    public function testVisAllChildrenIds()
    {
        $cache = ConcreteVisibleRecursiveCache::i();

        $result = $cache->visAllChildrenIds;

        $this->assertEquals(
            [17, 21, 22, 18, 19, 20],
            array_values($result['16'])
        );
        $this->assertEquals(
            [17, 21, 22, 18, 19, 20],
            array_keys($result['16'])
        );

        $p = new Page(17);
        $p->vis = 0;
        $p->commit();
        $cache->refresh();
        $result = $cache->visChildrenIds;

        $this->assertEquals([21, 22], array_values($result['16']));
        $this->assertEquals([21, 22], array_keys($result['16']));

        $p->vis = 1;
        $p->commit();
        $cache->refresh();
    }


    /**
     * Тест получения родительского свойства
     */
    public function testParentProperty()
    {
        $cache = ConcreteVisibleRecursiveCache::i();

        $result = $cache->cache;

        $this->assertEquals('Услуга 2', $result[5]['name']);
    }


    /**
     * Тест получения ID# видимых дочерних сущностей
     */
    public function testGetVisChildrenIds()
    {
        $cache = ConcreteVisibleRecursiveCache::i();

        $result = $cache->getVisChildrenIds(
            16,
            ConcreteVisibleRecursiveCache::ASSOC_BOTH
        );

        $this->assertEquals(['17' => 17, '21' => 21, '22' => 22], $result);

        $p = new Page(17);
        $p->vis = 0;
        $p->commit();
        $cache->refresh();
        $result = $cache->getVisChildrenIds(
            [new Page(16)],
            ConcreteVisibleRecursiveCache::ASSOC_OUTER
        );

        $this->assertEquals(['16' => [21, 22]], $result);
        $this->assertEquals(['16' => [21, 22]], $result);

        $p->vis = 1;
        $p->commit();
        $cache->refresh();
    }


    /**
     * Тест получения видимых дочерних сущностей
     */
    public function testGetVisChildren()
    {
        $cache = ConcreteVisibleRecursiveCache::i();

        $result = $cache->getVisChildren(
            16,
            ConcreteVisibleRecursiveCache::ASSOC_BOTH
        );
        $result = array_map(function ($x) {
            return $x->id;
        }, $result);

        $this->assertEquals(['17' => 17, '21' => 21, '22' => 22], $result);

        $p = new Page(17);
        $p->vis = 0;
        $p->commit();
        $cache->refresh();
        $result = $cache->getVisChildren(
            [new Page(16)],
            ConcreteVisibleRecursiveCache::ASSOC_OUTER
        );
        $result['16'] = array_map(function ($x) {
            return $x->id;
        }, $result['16']);

        $this->assertEquals(['16' => [21, 22]], $result);
        $this->assertEquals(['16' => [21, 22]], $result);

        $p->vis = 1;
        $p->commit();
        $cache->refresh();
    }


    /**
     * Тест получения ID# видимых дочерних сущностей всех уровней
     */
    public function testGetVisAllChildrenIds()
    {
        $cache = ConcreteVisibleRecursiveCache::i();

        $result = $cache->getVisAllChildrenIds(
            16,
            ConcreteVisibleRecursiveCache::ASSOC_BOTH
        );

        $this->assertEquals([
            '17' => 17,
            '21' => 21,
            '22' => 22,
            '18' => 18,
            '19' => 19,
            '20' => 20
        ], $result);

        $p = new Page(17);
        $p->vis = 0;
        $p->commit();
        $cache->refresh();
        $result = $cache->getVisAllChildrenIds(
            [new Page(16)],
            ConcreteVisibleRecursiveCache::ASSOC_OUTER
        );

        $this->assertEquals(['16' => [21, 22]], $result);
        $this->assertEquals(['16' => [21, 22]], $result);

        $p->vis = 1;
        $p->commit();
        $cache->refresh();
    }


    /**
     * Тест получения дочерних сущностей всех уровней
     */
    public function testGetVisAllChildren()
    {
        $cache = ConcreteVisibleRecursiveCache::i();

        $result = $cache->getVisAllChildren(
            16,
            ConcreteVisibleRecursiveCache::ASSOC_BOTH
        );
        $result = array_map(function ($x) {
            return $x->id;
        }, $result);

        $this->assertEquals([
            '17' => 17,
            '21' => 21,
            '22' => 22,
            '18' => 18,
            '19' => 19,
            '20' => 20
        ], $result);

        $p = new Page(17);
        $p->vis = 0;
        $p->commit();
        $cache->refresh();
        $result = $cache->getVisAllChildren(
            [new Page(16)],
            ConcreteVisibleRecursiveCache::ASSOC_OUTER
        );
        $result['16'] = array_map(function ($x) {
            return $x->id;
        }, $result['16']);

        $this->assertEquals(['16' => [21, 22]], $result);
        $this->assertEquals(['16' => [21, 22]], $result);

        $p->vis = 1;
        $p->commit();
        $cache->refresh();
    }
}
