<?php
/**
 * Файл теста рендерера файлового поля формы
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Класс теста рендерера файлового поля формы
 * @covers RAAS\CMS\FileFormFieldRenderer
 */
class FileFormFieldRendererTest extends BaseTest
{
    public static $tables = [
        'cms_blocks_form',
    ];

    /**
     * Тест получения атрибутов
     */
    public function testGetAttributes()
    {
        $renderer = new FileFormFieldRenderer(new Form_Field([
            'datatype' => 'file',
            'required' => true,
            'placeholder' => 'Your photo',
            'maxlength' => 16,
            'pattern' => '.*',
            'source' => 'PDF,DOC,JPG',
            'urn' => 'photo',
            'multiple' => true,
        ]), new Block_Form(), ['aaa', 'bbb']);

        $result = $renderer->getAttributes();

        $this->assertEquals('file', $result['type']);
        $this->assertEquals('photo[]', $result['name']);
        $this->assertEquals('required', $result['required']);
        $this->assertEmpty($result['class'] ?? null);
        $this->assertEmpty($result['placeholder'] ?? null);
        $this->assertEquals('Your photo', $result['data-placeholder']);
        $this->assertEmpty($result['maxlength'] ?? null);
        $this->assertEmpty($result['pattern'] ?? null);
        $this->assertEmpty($result['value'] ?? null);
        $this->assertEquals('multiple', $result['multiple']);
        $this->assertEmpty($result['data-multiple'] ?? null);
        $this->assertEmpty($result['data-value'] ?? null);
        $this->assertEquals('.pdf,.doc,.jpg', $result['accept']);
        $this->assertEmpty($result['id'] ?? null);
    }
}
