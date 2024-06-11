<?php
/**
 * Тест класса Block_Form
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Тест класса Block_Form
 * @covers RAAS\CMS\Block_Form
 */
class BlockFormTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_blocks_cache',
        'cms_blocks',
        'cms_blocks_form',
        'cms_blocks_pages_assoc',
        'cms_forms',
        'cms_pages',
    ];

    /**
     * Тест метода commit()
     */
    public function testCommit()
    {
        $block = new Block_Form(['form' => 1, 'location' => 'content', 'cats' => [1]]); // Form#1 - обратная связь, Page#1 - главная
        $block->commit();

        $this->assertEquals('Обратная связь', $block->name);

        Block_Form::delete($block);
    }


    /**
     * Тест метода getAddData()
     */
    public function testGetAddData()
    {
        $block = new Block_Form(['form' => 1, 'location' => 'content', 'cats' => [1]]);
        $block->commit();
        $blockId = $block->id;

        $result = $block->getAddData();
        $this->assertEquals($blockId, $result['id']);
        $this->assertEquals(1, $result['form']);

        Block_Form::delete($block);
    }
}
