<?php
/**
 * Файл теста рендерера поля изображения формы
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Класс теста рендерера поля изображения формы
 * @covers \RAAS\CMS\ImageFormFieldRenderer
 */
class ImageFormFieldRendererTest extends BaseTest
{
    public static $tables = [
        'cms_blocks_form',
    ];

    /**
     * Тест получения атрибутов
     */
    public function testGetAttributes()
    {
        $renderer = new ImageFormFieldRenderer(new Form_Field([
            'datatype' => 'image',
            'required' => true,
            'placeholder' => 'Your photo',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'photo',
        ]), new Block_Form(), 'aaa');

        $result = $renderer->getAttributes();

        $this->assertEquals('file', $result['type']);
        $this->assertEquals('image', $result['data-type']);
        $this->assertEquals('photo', $result['name']);
        $this->assertEquals('required', $result['required']);
        $this->assertEmpty($result['class'] ?? null);
        $this->assertEmpty($result['placeholder'] ?? null);
        $this->assertEmpty($result['maxlength'] ?? null);
        $this->assertEmpty($result['pattern'] ?? null);
        $this->assertEmpty($result['value'] ?? null);
        $this->assertEmpty($result['data-value'] ?? null);
        $this->assertEquals('image/jpeg,image/png,image/gif', $result['accept']);
        $this->assertStringStartsWith('photo', $result['id']);
    }


    /**
     * Тест получения атрибутов - случай с разрешенными расширениями
     */
    public function testGetAttributesWithAllowedExtensions()
    {
        $renderer = new ImageFormFieldRenderer(new Form_Field([
            'datatype' => 'image',
            'required' => true,
            'placeholder' => 'Your photo',
            'maxlength' => 16,
            'pattern' => '.*',
            'source' => 'PDF,DOC,JPG',
            'urn' => 'photo',
        ]), new Block_Form(), 'aaa');

        $result = $renderer->getAttributes();

        $this->assertEquals('.jpg', $result['accept']);
    }
}
