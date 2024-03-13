<?php
/**
 * Файл теста рендерера текстового поля формы
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Класс теста рендерера текстового поля формы
 * @covers \RAAS\CMS\TextFormFieldRenderer
 */
class TextFormFieldRendererTest extends BaseTest
{
    public static $tables = [
        'cms_blocks_form',
    ];

    /**
     * Тест получения атрибутов
     */
    public function testGetAttributes()
    {
        $renderer = new TextFormFieldRenderer(new Form_Field([
            'datatype' => 'text',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
        ]), new Block_Form(), 'aaa');

        $result = $renderer->getAttributes();

        $this->assertEquals('text', $result['type']);
        $this->assertEquals('name', $result['name']);
        $this->assertEquals('required', $result['required']);
        $this->assertEquals(['form-control' => true], $result['class']);
        $this->assertEquals('Your name', $result['placeholder']);
        $this->assertEquals('16', $result['maxlength']);
        $this->assertEquals('.*', $result['pattern']);
        $this->assertEquals('aaa', $result['value']);
        $this->assertStringStartsWith('name', $result['id']);
    }


    /**
     * Тест получения атрибутов - случай с единичным полем и
     * множественными данными
     */
    public function testGetAttributesWithMultipleData()
    {
        $renderer = new TextFormFieldRenderer(new Form_Field([
            'datatype' => 'text',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
        ]), new Block_Form(), ['aaa', 'bbb', 'ccc']);

        $result = $renderer->getAttributes();

        $this->assertEquals('text', $result['type']);
        $this->assertEquals('name', $result['name']);
        $this->assertEquals('required', $result['required']);
        $this->assertEquals(['form-control' => true], $result['class']);
        $this->assertEquals('Your name', $result['placeholder']);
        $this->assertEquals('16', $result['maxlength']);
        $this->assertEquals('.*', $result['pattern']);
        $this->assertEquals('aaa', $result['value']);
        $this->assertStringStartsWith('name', $result['id']);
    }



    /**
     * Тест получения атрибутов - случай множественного поля
     */
    public function testGetAttributesWithMultiple()
    {
        $renderer = new TextFormFieldRenderer(new Form_Field([
            'datatype' => 'text',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
            'multiple' => true,
        ]), new Block_Form(), ['aaa', 'bbb']);

        $result = $renderer->getAttributes();

        $this->assertEquals('text', $result['type']);
        $this->assertEquals('name[]', $result['name']);
        $this->assertEquals('required', $result['required']);
        $this->assertEquals(['form-control' => true], $result['class']);
        $this->assertEquals('Your name', $result['placeholder']);
        $this->assertEquals('16', $result['maxlength']);
        $this->assertEquals('.*', $result['pattern']);
        $this->assertEquals(json_encode(['aaa', 'bbb']), $result['value']);
        $this->assertEmpty($result['id'] ?? null);
    }


    /**
     * Тест рендера
     */
    public function testRender()
    {
        $renderer = new TextFormFieldRenderer(new Form_Field([
            'datatype' => 'text',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
        ]), new Block_Form(), 'aaa');

        $result = $renderer->render(['data-test' => 'test']);

        $this->assertStringContainsString('<input', $result);
        $this->assertStringContainsString(' type="text"', $result);
        $this->assertStringContainsString(' class="form-control"', $result);
        $this->assertStringContainsString(' placeholder="Your name"', $result);
        $this->assertStringContainsString(' maxlength="16"', $result);
        $this->assertStringContainsString(' data-test="test"', $result);
        $this->assertStringContainsString(' value="aaa"', $result);
    }


    /**
     * Тест рендера - случай множественного поля
     */
    public function testRenderWithMultiple()
    {
        $renderer = new TextFormFieldRenderer(new Form_Field([
            'datatype' => 'email',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
            'multiple' => true,
        ]), new Block_Form(), ['aaa', 'bbb']);

        $result = $renderer->render();

        $this->assertStringContainsString('<input', $result);
        $this->assertStringContainsString(' type="email"', $result);
        $this->assertStringContainsString(' class="form-control"', $result);
        $this->assertStringContainsString(' placeholder="Your name"', $result);
        $this->assertStringContainsString(' maxlength="16"', $result);
        $this->assertStringContainsString(' data-multiple="multiple"', $result);
        $this->assertStringContainsString(
            ' data-value="' . htmlspecialchars(json_encode(['aaa', 'bbb'])) . '"',
            $result
        );
        $this->assertStringNotContainsString(' value="', $result);
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
