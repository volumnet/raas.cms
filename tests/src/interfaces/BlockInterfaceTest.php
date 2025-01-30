<?php
/**
 * Тест класса BlockInterface
 */
namespace RAAS\CMS;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Тест класса BlockInterface
 */
#[CoversClass(BlockInterface::class)]
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
