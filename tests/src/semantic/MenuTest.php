<?php
/**
 * Файл теста меню
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Класс теста меню
 */
#[CoversClass(Menu::class)]
class MenuTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_blocks_cache',
        'cms_access_pages_cache',
        'cms_blocks',
        'cms_blocks_html',
        'cms_blocks_material',
        'cms_blocks_menu',
        'cms_blocks_pages_assoc',
        'cms_blocks_search_pages_assoc',
        'cms_data',
        'cms_feedback',
        'cms_fields',
        'cms_material_types',
        'cms_material_types_affected_pages_for_materials_cache',
        'cms_material_types_affected_pages_for_self_cache',
        'cms_materials',
        'cms_materials_affected_pages_cache',
        'cms_materials_pages_assoc',
        'cms_menus',
        'cms_pages',
        'cms_shop_blocks_yml_pages_assoc',
        'cms_users',
    ];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        PageRecursiveCache::i()->refresh();
        MenuRecursiveCache::i()->refresh();
    }


    /**
     * Тест ошибки от 2024-05-02
     * Выдает ошибку Warning: Undefined array key 18 in D:\web\home\libs\raas.cms\classes\semantic\menu.class.php on line 274
     * И Warning: foreach() argument must be of type array|object, null given in D:\web\home\libs\raas.cms\classes\semantic\menu.class.php on line 276
     * При вызове getSubMenuData() если пункт привязан к несуществующей странице
     */
    public function test20240502()
    {
        $menu = new Menu(['page_id' => 12345, 'inherit' => 1]);

        $result = $menu->getSubMenu();

        $this->assertEquals([], $result);
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
        // $menu->page_id = 3;

        $menu->commit();
        $id = $menu->id;

        $this->assertNotNull($id);

        $menu = new Menu($id);

        $this->assertEquals($id, $menu->id);
        $this->assertEquals('test', $menu->urn);

        Menu::delete($menu);
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
     * Тест сохранения
     */
    public function testCommitWithNewDomain()
    {
        $page = new Page(['name' => 'Новый домен']);
        $page->commit();

        $menu = new Menu(1);
        $menu->domain_id = 1;
        $menu->commit();

        $menu->domain_id = $page->id;
        $menu->commit();

        $submenu = new Menu(10);
        $submenu2 = new Menu(['pid' => 1]);
        $submenu2->commit();

        $this->assertNotEmpty($page->id);
        $this->assertEmpty($page->pid);
        $this->assertEquals($page->id, $submenu->domain_id);
        $this->assertEquals($page->id, $submenu2->domain_id);

        $menu->domain_id = 0;
        $menu->commit();

        $submenu = new Menu(10);
        $submenu2 = new Menu($submenu2->id);
        $this->assertEquals(0, $submenu->domain_id);
        $this->assertEquals(0, $submenu2->domain_id);

        Page::delete($page);
        Menu::delete($submenu2);
        MenuRecursiveCache::i()->refresh();
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
        $menu = new Menu(5); // Верхнее меню / О компании

        $result = $menu->findPage(new Page(2)); // О компании

        $this->assertInstanceOf(Menu::class, $result);
        $this->assertEquals(2, $result->page_id);
        $this->assertEquals(5, $result->id);
        $this->assertTrue($result->realized);
    }


    /**
     * Тест поиска страницы - случай текущего пункта/страницы
     */
    public function testFindPageWithSelf()
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


    public function testDelete()
    {
        $menu = new Menu();
        $menu->name = 'Тест';
        $menu->commit();
        $menuId = $menu->id;
        $block = new Block_Menu([
            'name' => 'Тестовое меню',
            'location' => 'content',
            'menu' => $menuId,
            'full_menu' => true,
            'cats' => [1]
        ]);
        $block->commit();
        $blockId = $block->id;

        Menu::delete($menu);

        $block = Block::spawn($blockId);

        $this->assertEmpty($block->id);
    }


    public function testUsingBlocks()
    {
        $menu = new Menu();
        $menu->name = 'Тест';
        $menu->commit();
        $menuId = $menu->id;
        $block = new Block_Menu([
            'name' => 'Тестовое меню',
            'location' => 'content',
            'menu' => $menuId,
            'full_menu' => true,
            'cats' => [1]
        ]);
        $block->commit();
        $blockId = $block->id;

        $result = $menu->usingBlocks;

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Block_Menu::class, $result[0]);
        $this->assertEquals($blockId, $result[0]->id);

        Menu::delete($menu);
    }
}
