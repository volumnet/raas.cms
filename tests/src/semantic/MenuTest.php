<?php
/**
 * Файл теста меню
 */
namespace RAAS\CMS;

/**
 * Класс теста меню
 */
class MenuTest extends BaseDBTest
{
    public static $tables = ['cms_menus', 'cms_pages'];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        PageRecursiveCache::i()->refresh();
        MenuRecursiveCache::i()->refresh();
    }

    /**
     * Тест URL
     */
    public function testUrl()
    {
        $menu = new Menu(5);

        $this->assertEquals('/about/', $menu->url);
    }


    /**
     * Тест видимых дочерних элементов
     */
    public function testVisChildren()
    {
        $menu = new Menu(1);

        $result = $menu->visChildren;
        $result = array_map(function ($x) {
            return (int)$x->id;
        }, $result);

        $this->assertEquals([10, 5, 6, 8, 9], $result);
    }


    /**
     * Тест видимых дочерних элементов - случай с виртуальными пунктами
     */
    public function testVisChildrenWithVirtual()
    {
        $menu = new Menu(6);

        $result = $menu->visChildren;

        $this->assertEmpty($result);
    }


    /**
     * Тест сохранения
     */
    public function testCommit()
    {
        $menu = new Menu();
        $menu->name = 'Тест';

        $menu->commit();
        $id = $menu->id;

        $this->assertNotNull($id);

        $menu = new Menu($id);

        $this->assertEquals($id, $menu->id);
        $this->assertEquals('test', $menu->urn);
    }


    /**
     * Тест сохранения с неизвестным именем
     */
    public function testCommitWithNoName()
    {
        $menu = new Menu(['pid' => 3, 'vis' => 1, 'page_id' => 1]);

        $menu->commit();

        $this->assertEquals('Главная', $menu->name);

        Menu::delete($menu);
    }


    /**
     * Проверка сохраняемости меню при ссылке на несуществующую страницу
     *
     * 2019-02-14, AVS: сейчас пытается вставить NULL в url и выдает ошибку
     */
    public function testCommitWithInvalidPage()
    {
        $menu = new Menu([
            'pid' => 3,
            'vis' => 1,
            'url' => '/about/', // Нужно поставить, т.к. обновляется на null
                                // только если установлено неравное значение
            'page_id' => 9999, // Заведомо несуществующая страница
        ]);

        $menu->commit();

        $this->assertEquals('', $menu->url);
        $this->assertEquals(0, $menu->page_id);

        Menu::delete($menu);
    }


    /**
     * Тест распаковки
     */
    public function testRealize()
    {
        $menu = new Menu(4);

        $this->assertEquals(10, $menu->inherit);
        $this->assertEmpty($menu->children);

        $submenu = new Menu([
            'pid' => 4,
            'vis' => 1,
            'name' => 'Тестовый пункт',
            'page_id' => 16,
            'inherit' => 1,
            'priority' => 999,
        ]);
        $submenu->commit();
        $menu->rollback(); // Поскольку children уже сохранились в кэше,
                         // их нужно обновить, чтобы новый пункт туда попал
        $menu->realize();

        $this->assertEquals(0, $menu->inherit);
        $this->assertCount(3, $menu->children);
        $this->assertEquals(9, $menu->children[0]->inherit);
        $this->assertEquals(23, $menu->children[0]->page_id);
        $this->assertEquals(9, $menu->children[1]->inherit);
        $this->assertEquals(24, $menu->children[1]->page_id);
        $this->assertEquals(1, $menu->children[2]->inherit);
        $this->assertEquals(16, $menu->children[2]->page_id);
        $this->assertEquals($submenu->id, $menu->children[2]->id);
    }


    /**
     * Тест подменю с помощью метода getSubMenu
     */
    public function testGetSubMenu()
    {
        $menu = new Menu(1);

        $result = $menu->getSubMenu(false);
        $resultIds = array_map(function ($x) {
            return (int)$x->id;
        }, $result);

        $this->assertEquals([10, 5, 6, 7, 8, 9], $resultIds);
        $this->assertTrue($result[0]->realized);
    }


    /**
     * Тест подменю с помощью метода getSubMenu - случай с виртуальными пунктами
     */
    public function testGetSubMenuWithVirtual()
    {
        $menu = new Menu(6);

        $result = $menu->getSubMenu(false);

        $this->assertCount(3, $result);
        $this->assertEmpty($result[0]->id);
        $this->assertEquals('Услуга 1', $result[0]->name);
        $this->assertEquals(4, $result[0]->page_id);
        $this->assertEquals(8, $result[0]->inherit);
        $this->assertFalse($result[0]->realized);
        $this->assertEmpty($result[1]->id);
        $this->assertEquals('Услуга 2', $result[1]->name);
        $this->assertEquals(5, $result[1]->page_id);
        $this->assertEquals(8, $result[1]->inherit);
        $this->assertFalse($result[1]->realized);
        $this->assertEmpty($result[2]->id);
        $this->assertEquals('Услуга 3', $result[2]->name);
        $this->assertEquals(6, $result[2]->page_id);
        $this->assertEquals(8, $result[2]->inherit);
        $this->assertFalse($result[2]->realized);
    }


    /**
     * Тест подменю с помощью метода getSubMenu - случай с виртуальными пунктами
     * Проверка, что нет пунктов с response_code
     */
    public function testGetSubMenuWithVirtualNoResponseCode()
    {
        $menu = new Menu(3);

        $result = $menu->getSubMenu(false);

        $this->assertCount(5, $result);
        foreach ($result as $resultItem) {
            $this->assertEmpty($resultItem->page->response_code);
        }
    }


    /**
     * Тест видимого подменю с помощью метода getSubMenu
     */
    public function testGetSubMenuWithVisible()
    {
        $menu = new Menu(1);

        $result = $menu->getSubMenu(true);
        $resultIds = array_map(function ($x) {
            return (int)$x->id;
        }, $result);

        $this->assertEquals([10, 5, 6, 8, 9], $resultIds);
        $this->assertTrue($result[0]->realized);
    }


    /**
     * Тест видимого подменю с помощью метода getSubMenu - случай с виртуальными пунктами
     */
    public function testVisSubMenuWithVisibleVirtual()
    {
        $menu = new Menu(6);

        $result = $menu->getSubMenu(true);

        $this->assertCount(1, $result);
        $this->assertEmpty($result[0]->id);
        $this->assertEquals('Услуга 2', $result[0]->name);
        $this->assertEquals(5, $result[0]->page_id);
        $this->assertEquals(8, $result[0]->inherit);
        $this->assertFalse($result[0]->realized);
    }


    /**
     * Тест подменю
     */
    public function testSubMenu()
    {
        $menu = new Menu(1);

        $result = $menu->subMenu;
        $resultIds = array_map(function ($x) {
            return (int)$x->id;
        }, $result);

        $this->assertEquals([10, 5, 6, 7, 8, 9], $resultIds);
        $this->assertTrue($result[0]->realized);
    }


    /**
     * Тест подменю - случай с виртуальными пунктами
     */
    public function testSubMenuWithVirtual()
    {
        $menu = new Menu(6);

        $result = $menu->subMenu;

        $this->assertCount(3, $result);
        $this->assertEmpty($result[0]->id);
        $this->assertEquals('Услуга 1', $result[0]->name);
        $this->assertEquals(4, $result[0]->page_id);
        $this->assertEquals(8, $result[0]->inherit);
        $this->assertFalse($result[0]->realized);
        $this->assertEmpty($result[1]->id);
        $this->assertEquals('Услуга 2', $result[1]->name);
        $this->assertEquals(5, $result[1]->page_id);
        $this->assertEquals(8, $result[1]->inherit);
        $this->assertFalse($result[1]->realized);
        $this->assertEmpty($result[2]->id);
        $this->assertEquals('Услуга 3', $result[2]->name);
        $this->assertEquals(6, $result[2]->page_id);
        $this->assertEquals(8, $result[2]->inherit);
        $this->assertFalse($result[2]->realized);
    }


    /**
     * Тест видимого подменю
     */
    public function testVisSubMenu()
    {
        $menu = new Menu(1);

        $result = $menu->visSubMenu;
        $resultIds = array_map(function ($x) {
            return (int)$x->id;
        }, $result);

        $this->assertEquals([10, 5, 6, 8, 9], $resultIds);
        $this->assertTrue($result[0]->realized);
    }


    /**
     * Тест видимого подменю - случай с виртуальными пунктами
     */
    public function testVisSubMenuWithVirtual()
    {
        $menu = new Menu(6);

        $result = $menu->visSubMenu;

        $this->assertCount(1, $result);
        $this->assertEmpty($result[0]->id);
        $this->assertEquals('Услуга 2', $result[0]->name);
        $this->assertEquals(5, $result[0]->page_id);
        $this->assertEquals(8, $result[0]->inherit);
        $this->assertFalse($result[0]->realized);
    }


    /**
     * Тест поиска страницы
     */
    public function testFindPage()
    {
        $menu = new Menu(1);

        $result = $menu->findPage(new Page(15));

        $this->assertInstanceOf(Menu::class, $result);
        $this->assertEquals(15, $result->page_id);
        $this->assertEquals(9, $result->id);
        $this->assertTrue($result->realized);
    }


    /**
     * Тест поиска страницы
     */
    public function testFindPageWithVirtual()
    {
        $menu = new Menu(1);

        $result = $menu->findPage(new Page(16));

        $this->assertInstanceOf(Menu::class, $result);
        $this->assertEquals(16, $result->page_id);
        $this->assertNull($result->id);
        $this->assertEquals(9, $result->pid);
        $this->assertFalse($result->realized);
    }


    /**
     * Тест поиска страницы - случай когда не найдено
     */
    public function testFindPageWithNotFound()
    {
        $menu = new Menu(1);

        $result = $menu->findPage(new Page(27));

        $this->assertFalse($result);
    }


    /**
     * Тест импорта по URN
     */
    public function testImportByURN()
    {
        $menu = Menu::importByURN('top');

        $this->assertEquals(1, $menu->id);
    }


    /**
     * Тест импорта по URN - случай, когда не найден
     */
    public function testImportByURNWithNotFound()
    {
        $menu = Menu::importByURN('aaa');

        $this->assertNull($menu);
    }
}
