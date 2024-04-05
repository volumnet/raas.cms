<?php
/**
 * Тест класса Block_PHP
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Тест класса Block_PHP
 * @covers RAAS\CMS\Block_PHP
 */
class BlockPHPTest extends BaseTest
{
    public static $tables = [
        'cms_access_blocks_cache',
        'cms_blocks',
        'cms_blocks_pages_assoc',
        'cms_pages',
        'cms_snippets',
    ];

    /**
     * Тест метода commit() - случай с установленным виджетом, без интерфейса
     */
    public function testCommitWithWidget()
    {
        $code = '<' . "?php\n"
            . "/**\n"
            . " * Тест\n"
            . " */\n";
        $snippet = new Snippet(['description' => $code]);
        $snippet->commit();
        $block = new Block_PHP(['widget_id' => $snippet->id, 'location' => 'content', 'cats' => [1]]);
        $block->commit();

        $this->assertEquals('Тест', $block->name);

        Snippet::delete($snippet);
        Block_PHP::delete($block);
    }


    /**
     * Тест метода commit() - случай с установленным интерфейсом, без виджета
     */
    public function testCommitWithInterface()
    {
        $code = '<' . "?php\n"
            . "/**\n"
            . " * Тест\n"
            . " */\n";
        $snippet = new Snippet(['description' => $code]);
        $snippet->commit();
        $block = new Block_PHP(['interface_id' => $snippet->id, 'location' => 'content', 'cats' => [1]]);
        $block->commit();

        $this->assertEquals('Тест', $block->name);

        Snippet::delete($snippet);
        Block_PHP::delete($block);
    }
}
