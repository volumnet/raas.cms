<?php
/**
 * Файл теста стандартного интерфейса меню
 */
namespace RAAS\CMS;

use InvalidArgumentException;
use SOME\BaseTest;

/**
 * Класс теста стандартного интерфейса меню
 * @covers RAAS\CMS\MenuInterface
 */
class MenuInterfaceTest extends BaseTest
{
    public static $tables = [
        'cms_access_pages_cache',
        'cms_blocks',
        'cms_blocks_menu',
        'cms_fields',
        'cms_menus',
        'cms_pages',
    ];

    /**
     * Тест метода getCurrentPage()
     */
    public function testGetCurrentPage()
    {
        $block = Block::spawn(14);
        $page = new Page(15);
        $interface = new MenuInterface($block, $page);

        $result = $interface->getCurrentPage();

        $this->assertSame($page, $result);
    }


    /**
     * Тест метода getCurrentPage() - случай с AJAX-ом
     */
    public function testGetCurrentPageWithAJAX()
    {
        $block = Block::spawn(14);
        $block->full_menu = false;
        $page = new Page(14); // AJAX
        $interface = new MenuInterface($block, $page, ['id' => 1]);

        $result = $interface->getCurrentPage();

        $this->assertInstanceOf(Page::class, $result);
        $this->assertEquals(1, $result->id);
    }


    /**
     * Тест обработки видимых дочерних элементов меню
     */
    public function testGetVisSubmenu()
    {
        $block = Block::spawn(14);
        $page = new Page(15);
        $interface = new MenuInterface($block, $page);

        $menu = new Menu(4);


        $result = $interface->getVisSubmenu($menu);

        $this->assertCount(3, $menu->visSubMenu);
        $this->assertNotEmpty($result);
        $this->assertEquals('/catalog/category1/', $result[0]['url']);
        $this->assertEquals('Категория 1', $result[0]['name']);
        $this->assertEquals(16, $result[0]['page_id']);
        $this->assertEquals('/catalog/category1/category11/', $result[0]['children'][0]['url']);
        $this->assertEquals('Категория 11', $result[0]['children'][0]['name']);
        $this->assertEquals(17, $result[0]['children'][0]['page_id']);
    }


    /**
     * Тест обработки видимых дочерних элементов меню - случай с некорректным аргументом
     */
    public function testGetVisSubmenuWithInvalidMenu()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must be of');
        $block = Block::spawn(14);
        $page = new Page(15);
        $interface = new MenuInterface($block, $page);

        $result = $interface->getVisSubmenu('aaa');
    }

    /**
     * Тест обработки интерфейса
     */
    public function testProcess()
    {
        $block = Block::spawn(37);
        $page = new Page(15);
        $interface = new MenuInterface($block, $page);

        $result = $interface->process();

        $this->assertInstanceOf(Menu::class, $result['Item']);
        $this->assertEquals(4, $result['Item']->id);
        $this->assertNotEmpty($result['menuArr']['children']);
        $this->assertEquals('/catalog/category1/', $result['menuArr']['children'][0]['url']);
        $this->assertEquals('Категория 1', $result['menuArr']['children'][0]['name']);
        $this->assertEquals(16, $result['menuArr']['children'][0]['page_id']);
        $this->assertEquals('/catalog/category1/category11/', $result['menuArr']['children'][0]['children'][0]['url']);
        $this->assertEquals('Категория 11', $result['menuArr']['children'][0]['children'][0]['name']);
        $this->assertEquals(17, $result['menuArr']['children'][0]['children'][0]['page_id']);
    }


    /**
     * Тест обработки интерфейса - случай с подменю
     */
    public function testProcessWithSubmenu()
    {
        $block = Block::spawn(14);
        $block->full_menu = 0;
        $page = new Page(15);
        $interface = new MenuInterface($block, $page);

        $result = $interface->process();

        $this->assertInstanceOf(Menu::class, $result['Item']);
        $this->assertEquals(9, $result['Item']->id);
    }
}
