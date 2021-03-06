<?php
/**
 * Файл теста рендерера поля изображения формы
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера поля изображения формы
 * @covers RAAS\CMS\ImageFormFieldRenderer
 */
class ImageFormFieldRendererTest extends BaseTest
{
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
        $this->assertEmpty($result['class']);
        $this->assertEmpty($result['placeholder']);
        $this->assertEmpty($result['maxlength']);
        $this->assertEmpty($result['pattern']);
        $this->assertEmpty($result['value']);
        $this->assertEmpty($result['data-value']);
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
