<?php
/**
 * Файл теста рендерера многострочного текстового поля формы
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Класс теста рендерера многострочного текстового поля формы
 * @covers \RAAS\CMS\HtmlAreaFormFieldRenderer
 */
class HtmlAreaFormFieldRendererTest extends BaseTest
{
    public static $tables = [
        'cms_blocks_form',
    ];

    /**
     * Тест получения атрибутов
     */
    public function testGetAttributes()
    {
        $renderer = new HtmlAreaFormFieldRenderer(new Form_Field([
            'datatype' => 'htmlarea',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
        ]), new Block_Form(), 'aaa&');

        $result = $renderer->getAttributes();

        $this->assertEquals('htmlarea', $result['data-type']);
    }
}
