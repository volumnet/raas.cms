<?php
/**
 * Файл теста рендерера числового поля формы
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Класс теста рендерера числового поля формы
 */
#[CoversClass(NumberFormFieldRenderer::class)]
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
