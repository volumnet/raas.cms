<?php
/**
 * Файл теста рендерера числового поля формы
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Класс теста рендерера числового поля формы
 * @covers RAAS\CMS\NumberFormFieldRenderer
 */
class NumberFormFieldRendererTest extends BaseTest
{
    public static $tables = [
        'cms_blocks_form',
    ];

    /**
     * Тест получения атрибутов
     */
    public function testGetAttributes()
    {
        $renderer = new NumberFormFieldRenderer(new Form_Field([
            'datatype' => 'range',
            'min_val' => 1,
            'max_val' => 10,
            'step' => 2,
        ]), new Block_Form());

        $result = $renderer->getAttributes();

        $this->assertEquals('range', $result['type']);
        $this->assertEquals('1', $result['min']);
        $this->assertEquals('10', $result['max']);
        $this->assertEquals('2', $result['step']);
    }
}
