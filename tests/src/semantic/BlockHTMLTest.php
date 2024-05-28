<?php
/**
 * Тест класса Block_HTML
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Тест класса Block_HTML
 * @covers RAAS\CMS\Block_HTML
 */
class BlockHTMLTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_blocks_cache',
        'cms_blocks',
        'cms_blocks_html',
        'cms_blocks_pages_assoc',
        'cms_fields',
        'cms_forms',
        'cms_pages',
        'cms_snippets',
        'cms_users',
    ];

    /**
     * Тест метода commit()
     */
    public function testCommit()
    {
        $block = new Block_HTML([
            'description' => 'Какой-то очень длинный текст текстового блока для проверки формирования его названия',
            'location' => 'content',
            'cats' => [1]
        ]);
        $block->commit();

        $this->assertEquals('Какой-то очень длинный текст...', $block->name);

        Block_HTML::delete($block);
    }


    /**
     * Тест метода process()
     */
    public function testProcess()
    {
        $text = 'Какой-то очень длинный текст текстового блока для проверки формирования его названия';
        $block = new Block_HTML([
            'description' => $text,
            'location' => 'content',
            'cats' => [1]
        ]);

        ob_start();
        $block->process(new Page(1));
        $result = ob_get_clean();

        $this->assertEquals($text, $result);
    }


    /**
     * Тест метода process() - случай с закрытым доступом
     */
    public function testProcessWithDeniedAccess()
    {
        $block = new Block_HTML([
            'description' => 'Какой-то очень длинный текст текстового блока для проверки формирования его названия',
            'location' => 'content',
            'cats' => [1]
        ]);
        $block->commit();
        $access = new CMSAccess(['block_id' => $block->id, 'to_type' => CMSAccess::TO_ALL, 'allow' => 0]);
        $access->commit();
        $page = new Page(1);

        ob_start();
        $result = $block->process($page);
        $html = ob_get_clean();

        $this->assertNull($result);
        $this->assertEmpty($html);

        CMSAccess::delete($access);

        Block_HTML::delete($block);
    }


    /**
     * Тест метода process() - случай с установленным виджетом
     */
    public function testProcessWithWidgetOrInterface()
    {
        $code = '<' . "?php\n"
            . "/**\n"
            . " * Тест\n"
            . " */\n"
            . "echo mb_substr(\$Block->description, 0, 10);\n"
            . "return 'aaa';\n";
        $snippet = new Snippet(['description' => $code]);
        $snippet->commit();
        $block = new Block_HTML([
            'description' => 'Какой-то очень длинный текст текстового блока для проверки формирования его названия',
            'location' => 'content',
            'widget_id' => $snippet->id,
            'cats' => [1]
        ]);
        $block->commit();
        $page = new Page(1);

        ob_start();
        $block->process($page);
        $result = ob_get_clean();

        $this->assertEquals('Какой-то о', $result);

        Snippet::delete($snippet);
        Block_HTML::delete($block);
    }


    /**
     * Тест метода getAddData
     */
    public function testGetAddData()
    {
        $text = 'Какой-то очень длинный текст текстового блока для проверки формирования его названия';
        $block = new Block_HTML([
            'description' => $text,
            'wysiwyg' => 1,
            'location' => 'content',
            'cats' => [1]
        ]);
        $block->commit();
        $blockId = $block->id;

        $result = $block->getAddData();

        $this->assertCount(3, $result);
        $this->assertEquals($blockId, $result['id']);
        $this->assertEquals($text, $result['description']);
        $this->assertEquals(1, $result['wysiwyg']);

        Block_HTML::delete($block);
    }
}
