<?php
/**
 * Тест класса BlockInterface
 */
namespace RAAS\CMS;

use InvalidArgumentException;
use SOME\BaseTest;

/**
 * Тест класса BlockInterface
 * @covers RAAS\CMS\BlockInterface
 */
class BlockInterfaceTest extends BaseTest
{
    public static $tables = [
        'cms_blocks',
        'cms_blocks_menu',
        'cms_pages',
    ];

    /**
     * Тест конструктора класса
     */
    public function testConstruct()
    {
        $block = Block::spawn(14);
        $page = new Page(15);
        $interface = new MenuInterface($block, $page);

        $result = $interface->page;

        $this->assertSame($page, $result);
    }
}
