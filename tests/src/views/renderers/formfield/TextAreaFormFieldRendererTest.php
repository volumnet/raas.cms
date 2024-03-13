<?php
/**
 * Файл теста рендерера многострочного текстового поля формы
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Класс теста рендерера многострочного текстового поля формы
 * @covers RAAS\CMS\TextAreaFormFieldRenderer
 */
class TextAreaFormFieldRendererTest extends BaseTest
{
    public static $tables = [
        'cms_blocks_form',
    ];

    /**
     * Тест рендера
     */
    public function testRender()
    {
        $renderer = new TextAreaFormFieldRenderer(new Form_Field([
            'datatype' => 'textarea',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
        ]), new Block_Form(), 'aaa&');

        $result = $renderer->render(['data-test' => 'test']);

        $this->assertStringContainsString('<textarea', $result);
        $this->assertStringNotContainsString(' type', $result);
        $this->assertStringContainsString(' class="form-control"', $result);
        $this->assertStringContainsString(' placeholder="Your name"', $result);
        $this->assertStringContainsString(' maxlength="16"', $result);
        $this->assertStringContainsString(' data-test="test"', $result);
        $this->assertStringContainsString('>aaa&amp;</textarea>', $result);
        $this->assertStringNotContainsString(' value="', $result);
    }


    /**
     * Тест рендера - случай множественного поля
     */
    public function testRenderWithMultiple()
    {
        $renderer = new TextAreaFormFieldRenderer(new Form_Field([
            'datatype' => 'text',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
            'multiple' => true,
        ]), new Block_Form(), ['aaa', 'bbb']);

        $result = $renderer->render();

        $this->assertStringContainsString('<textarea', $result);
        $this->assertStringNotContainsString(' type="', $result);
        $this->assertStringContainsString(' class="form-control"', $result);
        $this->assertStringContainsString(' placeholder="Your name"', $result);
        $this->assertStringContainsString(' maxlength="16"', $result);
        $this->assertStringContainsString(' data-multiple="multiple"', $result);
        $this->assertStringContainsString(
            ' data-value="' . htmlspecialchars(json_encode(['aaa', 'bbb'])) . '"',
            $result
        );
        $this->assertStringNotContainsString(' value="', $result);
        $this->assertStringContainsString('></textarea>', $result);
    }


    /**
     * Тест рендера - случай множественного поля с пустыми данными
     */
    public function testRenderWithMultipleWithoutData()
    {
        $renderer = new TextFormFieldRenderer(new Form_Field([
            'datatype' => 'email',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
            'multiple' => true,
        ]), new Block_Form());

        $result = $renderer->render();

        $this->assertStringContainsString(
            ' data-value="' . htmlspecialchars(json_encode([])) . '"',
            $result
        );
    }
}
