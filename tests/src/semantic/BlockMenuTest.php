<?php
/**
 * Тест класса Block_Menu
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Тест класса Block_Menu
 */
#[CoversClass(Block_Menu::class)]
class BlockMenuTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_blocks_cache',
        'cms_blocks',
        'cms_blocks_menu',
        'cms_blocks_pages_assoc',
        'cms_menus',
        'cms_pages',
    ];

    /**
     * Тест метода commit и конструктора
     */
    public function testCommit()
    {
        $block = new Block_Menu([
            'menu' => 1, // Верхнее меню
            'cats' => [3], // Услуги
        ]);
        $block->commit();
        $blockId = $block->id;

        $block = new Block_Menu($blockId);
        $this->assertEquals('Верхнее меню', $block->name);

        Block_Menu::delete($block);
    }


    /**
     * Тест метода getAddData()
     */
    public function testGetAddData()
    {
        $block = new Block_Menu([
            'menu' => 1, // Верхнее меню
            'cats' => [3], // Услуги
            'full_menu' => true,
        ]);

        $result = $block->getAddData();

        $this->assertEquals(0, $result['id']);
        $this->assertEquals(1, $result['menu']);
        $this->assertEquals(true, $result['full_menu']);

        $block->commit();
        $blockId = $block->id;

        $result = $block->getAddData();

        $this->assertEquals($blockId, $result['id']);
        $this->assertEquals(1, $result['menu']);
        $this->assertEquals(true, $result['full_menu']);

        Block_Menu::delete($block);
    }
}
