<?php
/**
 * Тест класса Block_PHP
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Тест класса Block_PHP
 */
#[CoversClass(Block_PHP::class)]
class BlockPHPTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_blocks_cache',
        'cms_blocks',
        'cms_blocks_pages_assoc',
        'cms_fields',
        'cms_forms',
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
     * Тест метода commit() - случай с установленным классом интерфейса, без виджета
     */
    public function testCommitWithInterfaceClassname()
    {
        $block = new Block_PHP([
            'interface_classname' => SitemapInterface::class,
            'location' => 'content',
            'cats' => [1],
        ]);
        $block->commit();

        $this->assertStringContainsString('sitemap.xml', $block->name);

        Block_PHP::delete($block);
    }


    /**
     * Тест метода commit() - случай с некорректным установленным классом интерфейса, без виджета
     */
    public function testCommitWithInvalidInterfaceClassname()
    {
        $block = new Block_PHP([
            'interface_classname' => 'AAA\BBB\SomeUnexistingClass',
            'location' => 'content',
            'cats' => [1],
        ]);
        $block->commit();

        $this->assertEquals('AAA\BBB\SomeUnexistingClass', $block->name);

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
