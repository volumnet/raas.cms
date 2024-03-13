<?php
/**
 * Файл теста рендерера скрытого поля формы
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Класс теста рендерера скрытого поля формы
 * @covers RAAS\CMS\HiddenFormFieldRenderer
 */
class HiddenFormFieldRendererTest extends BaseTest
{
    const HTML_VALID_MULTIPLE = true;

    public static $tables = [
        'cms_blocks_form',
    ];

    /**
     * Тест получения атрибутов
     */
    public function testGetAttributes()
    {
        $renderer = new HiddenFormFieldRenderer(new Form_Field([
            'datatype' => 'material',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
        ]), new Block_Form(), 'aaa');

        $result = $renderer->getAttributes();

        $this->assertEquals('hidden', $result['type']);
        $this->assertEquals('name', $result['name']);
        $this->assertEquals('required', $result['required']);
        $this->assertEmpty($result['class'] ?? null);
        $this->assertEmpty($result['placeholder'] ?? null);
        $this->assertEmpty($result['maxlength'] ?? null);
        $this->assertEmpty($result['pattern'] ?? null);
        $this->assertEmpty($result['id'] ?? null);
    }

    /**
     * Тест рендера
     */
    public function testRender()
    {
        $renderer = new HiddenFormFieldRenderer(new Form_Field([
            'datatype' => 'material',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
        ]), new Block_Form(), 'aaa');

        $result = $renderer->render();

        $this->assertStringContainsString('<input', $result);
        $this->assertStringContainsString(' type="hidden"', $result);
        $this->assertStringNotContainsString(' class="', $result);
        $this->assertStringNotContainsString(' placeholder="', $result);
        $this->assertStringNotContainsString(' maxlength="', $result);
        $this->assertStringContainsString(' value="aaa"', $result);
    }


    /**
     * Тест рендера - случай множественного поля
     */
    public function testRenderWithMultiple()
    {
        $renderer = new HiddenFormFieldRenderer(new Form_Field([
            'datatype' => 'material',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
            'multiple' => true,
        ]), new Block_Form(), ['aaa', 'bbb']);

        $result = $renderer->render();

        $this->assertStringContainsString('<input', $result);
        $this->assertStringContainsString(' type="hidden"', $result);
        $this->assertStringNotContainsString(' class="', $result);
        $this->assertStringNotContainsString(' placeholder="', $result);
        $this->assertStringNotContainsString(' maxlength="', $result);
        $this->assertStringContainsString(' value="aaa"', $result);
        $this->assertStringContainsString(' value="bbb"', $result);
        $this->assertStringNotContainsString(' multiple="multiple"', $result);
        $this->assertStringContainsString(' data-multiple="multiple"', $result);
        $this->assertStringNotContainsString(' data-value="', $result);
    }
}
