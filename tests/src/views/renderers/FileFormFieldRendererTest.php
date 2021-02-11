<?php
/**
 * Файл теста рендерера файлового поля формы
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера файлового поля формы
 * @covers RAAS\CMS\FileFormFieldRenderer
 */
class FileFormFieldRendererTest extends BaseTest
{
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
        $this->assertEmpty($result['class']);
        $this->assertEmpty($result['placeholder']);
        $this->assertEquals('Your photo', $result['data-placeholder']);
        $this->assertEmpty($result['maxlength']);
        $this->assertEmpty($result['pattern']);
        $this->assertEmpty($result['value']);
        $this->assertEquals('multiple', $result['multiple']);
        $this->assertEmpty($result['data-multiple']);
        $this->assertEmpty($result['data-value']);
        $this->assertEquals('.pdf,.doc,.jpg', $result['accept']);
        $this->assertEmpty($result['id']);
    }
}
