<?php
/**
 * Файл теста рекурсивного кэша страниц
 */
namespace RAAS\CMS;

use SOME\BaseTest;
use SOME\File;

/**
 * Класс теста рекурсивного кэша страниц
 * @covers RAAS\CMS\PageRecursiveCache
 */
class PageRecursiveCacheTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_pages_cache',
        'cms_fields',
        'cms_groups',
        'cms_materials',
        'cms_pages',
        'cms_users',
        'cms_users_groups_assoc',
    ];


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
        $this->assertNull($result['0'] ?? null);
        $this->assertNull($result['4'] ?? null);

        $cmsAccess = new CMSAccess(['page_id' => 17, 'allow' => 0, 'to_type' => 1]);
        $cmsAccess->commit();
        CMSAccess::refreshPagesAccessCache();
        $cache->refresh();
        $result = $cache->allowedIds;

        $this->assertNull($result['18'] ?? null);
        $this->assertNull($result['17'] ?? null);
        $this->assertEquals(1, $result['1']);
        $this->assertNull($result['0'] ?? null);
        $this->assertNull($result['4'] ?? null);

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
        $this->assertNull($result['0'] ?? null);
        $this->assertNull($result['1'] ?? null);
        $this->assertNull($result['16'] ?? null);
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
        $this->assertNull($result['0'] ?? null);
        $this->assertNull($result['4'] ?? null);

        $cmsAccess = new CMSAccess(['page_id' => 17, 'allow' => 0, 'to_type' => 1]);
        $cmsAccess->commit();
        CMSAccess::refreshPagesAccessCache();
        $cache->refresh();
        $result = $cache->visibleIds;

        $this->assertNull($result['18'] ?? null);
        $this->assertNull($result['17'] ?? null);
        $this->assertEquals(1, $result['1']);
        $this->assertNull($result['0'] ?? null);
        $this->assertNull($result['4'] ?? null);

        CMSAccess::delete($cmsAccess);
        CMSAccess::refreshPagesAccessCache();
        $cache->refresh();
    }


    /**
     * Тест метода init()
     */
    public function testInit()
    {
        $filename = Package::i()->cacheDir . '/system/pagerecursivecache.php';

        TestPageRecursiveCache::deleteInstance();
        $cache = TestPageRecursiveCache::i();

        $this->assertEquals('Главная', $cache->cache[1]['name']);
    }


    /**
     * Тест метода init() - случай с необходимостью обновления
     */
    public function testInitWithUpdateNeeded()
    {
        $filename = Package::i()->cacheDir . '/system/pagerecursivecache.php';
        if (is_file($filename)) {
            unlink($filename);
        }

        $this->assertFileDoesNotExist($filename);

        TestPageRecursiveCache::deleteInstance();
        $cache = TestPageRecursiveCache::i();

        $this->assertFileExists($filename);
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


    /**
     * Тест метода updateNeeded()
     */
    public function testUpdateNeeded()
    {
        $page = new Page(1);
        $cache = PageRecursiveCache::i();
        $filename = Package::i()->cacheDir . '/system/pagerecursivecache.php';
        touch($filename);

        $result = $cache->updateNeeded();

        $this->assertFalse($result);

        sleep(1);
        $page->commit();

        $result = $cache->updateNeeded();

        $this->assertTrue($result);

        $cache->refresh();
        $cache->save();

        $result = $cache->updateNeeded();

        $this->assertFalse($result);

        unlink($filename);

        $result = $cache->updateNeeded();

        $this->assertTrue($result);
    }


    /**
     * Тест метода getFilename()
     */
    public function testGetFilename()
    {
        $cache = PageRecursiveCache::i();
        $filename = Package::i()->cacheDir . '/system/pagerecursivecache.php';

        $result = $cache->getFilename();

        $this->assertEquals($filename, $result);
    }


    /**
     * Тест метода getTmpFilename()
     */
    public function testGetTmpFilename()
    {
        $cache = PageRecursiveCache::i();
        $filename = Package::i()->cacheDir . '/system/pagerecursivecache.tmp.php';

        $result = $cache->getTmpFilename();

        $this->assertEquals($filename, $result);
    }


    /**
     * Тест метода save()
     */
    public function testSave()
    {
        $filename = Package::i()->cacheDir . '/system/pagerecursivecache.php';
        if (is_file($filename)) {
            unlink($filename);
        }

        $this->assertFileDoesNotExist($filename);

        $cache = PageRecursiveCache::i();
        $result = $cache->save();

        $this->assertFileExists($filename);

        $this->assertTrue($result);

        $data = include $filename;

        $this->assertEquals('Главная', $data['cache']['1']['name']);
    }


    /**
     * Тест метода save() - случай с невозможностью сохранить временный файл
     */
    public function testSaveWithCannotSaveTmpFile()
    {
        $filename = Package::i()->cacheDir . '/system/pagerecursivecache.tmp.php';
        $cache = PageRecursiveCache::i();

        mkdir($filename, true, 0777);

        $this->assertDirectoryExists($filename);

        $result = $cache->save();

        $this->assertFalse($result);

        rmdir($filename);
    }


    /**
     * Тест метода save() - случай с невозможностью удалить старый файл
     */
    public function testSaveWithCannotDeleteOldFile()
    {
        $filename = Package::i()->cacheDir . '/system/pagerecursivecache.php';
        $bakFile = Package::i()->cacheDir . '/system/pagerecursivecache.php.bak';
        $cache = PageRecursiveCache::i();
        rename($filename, $bakFile);

        $this->assertFileDoesNotExist($filename);

        mkdir($filename, true, 0777);

        $this->assertDirectoryExists($filename);

        $result = $cache->save();

        $this->assertFalse($result);

        rmdir($filename);
        rename($bakFile, $filename);

        $this->assertFileExists($filename);
        $this->assertFileDoesNotExist($bakFile);
    }


    /**
     * Тест метода load()
     */
    public function testLoad()
    {
        $cache = PageRecursiveCache::i();
        $result = $cache->load();

        $this->assertTrue($result);
        $this->assertEquals('Главная', $cache->cache[1]['name']);
    }


    /**
     * Тест метода load()
     */
    public function testLoadWithNoFile()
    {
        $cache = PageRecursiveCache::i();
        $filename = Package::i()->cacheDir . '/system/pagerecursivecache.php';
        unlink($filename);

        $result = $cache->load();

        $this->assertFalse($result);

        $cache->save();
    }


    /**
     * Тест метода load() - случай с некорректным файлом
     */
    public function testLoadWithInvalidFile()
    {
        $cache = PageRecursiveCache::i();
        $filename = Package::i()->cacheDir . '/system/pagerecursivecache.php';
        file_put_contents($filename, '<?php aaa bbb ccc'); // некорректный код

        $result = $cache->load();

        $this->assertTrue($result);
        $this->assertEmpty($cache->cache);

        $cache->save();
    }
}
