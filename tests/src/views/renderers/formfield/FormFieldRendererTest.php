<?php
/**
 * Файл теста рендерера поля формы
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Класс теста рендерера поля формы
 * @covers \RAAS\CMS\FormFieldRenderer
 */
class FormFieldRendererTest extends BaseTest
{
    public static $tables = [
        'cms_blocks_form',
        'cms_fields', // Не используется в полном тесте
    ];

    /**
     * Провайдер данных для метода testSpawn()
     * @return array <pre>array<[
     *     string Тип данных поля,
     *     string Класс рендерера
     * ]></pre>
     */
    public function spawnDataProvider()
    {
        return [
            ['text', TextFormFieldRenderer::class],
            ['color', TextFormFieldRenderer::class],
            ['date', TextFormFieldRenderer::class],
            ['datetime', TextFormFieldRenderer::class],
            ['email', TextFormFieldRenderer::class],
            ['number', NumberFormFieldRenderer::class],
            ['range', NumberFormFieldRenderer::class],
            ['tel', TextFormFieldRenderer::class],
            ['time', TextFormFieldRenderer::class],
            ['url', TextFormFieldRenderer::class],
            ['month', TextFormFieldRenderer::class],
            ['password', PasswordFormFieldRenderer::class],
            ['checkbox', CheckboxFormFieldRenderer::class],
            ['radio', RadioFormFieldRenderer::class],
            ['file', FileFormFieldRenderer::class],
            ['image', ImageFormFieldRenderer::class],
            ['select', SelectFormFieldRenderer::class],
            ['textarea', TextAreaFormFieldRenderer::class],
            ['htmlarea', HtmlAreaFormFieldRenderer::class],
            ['material', HiddenFormFieldRenderer::class],
        ];
    }

    /**
     * Тест рендера поля подписи - случай с текстовым полем
     * @dataProvider spawnDataProvider
     * @param string $datatype Тип данных поля
     * @param string $rendererClassName Класс рендерера
     */
    public function testSpawn($datatype, $rendererClassName)
    {
        $field = new Form_Field(['datatype' => $datatype]);

        $result = FormFieldRenderer::spawn($field, new Block_Form(), []);

        $this->assertInstanceOf($rendererClassName, $result);
    }


    /**
     * Тест получения атрибутов
     */
    public function testGetAttributes()
    {
        $renderer = new TextFormFieldRenderer(new Form_Field([
            'type' => 'text',
            'required' => true,
            'urn' => 'name',
            'placeholder' => 'Placeholder',
        ]), new Block_Form());

        $result = $renderer->getAttributes();

        $this->assertEquals('', $result['data-raas-field']);
        $this->assertEquals('name', $result['name']);
        $this->assertStringStartsWith('name', $result['id']);
        $this->assertEquals('required', $result['required']);
        $this->assertEquals('Placeholder', $result['data-placeholder']);
    }

    /**
     * Тест получения атрибутов - случай множественного поля
     */
    public function testGetAttributesWithMultiple()
    {
        $renderer = new TextFormFieldRenderer(new Form_Field([
            'datatype' => 'text',
            'required' => true,
            'urn' => 'name',
            'multiple' => 'true',
        ]), new Block_Form());

        $result = $renderer->getAttributes();

        $this->assertEquals('', $result['data-raas-field']);
        $this->assertEquals('text', $result['data-type']);
        $this->assertEquals('name[]', $result['name']);
        $this->assertEmpty($result['id'] ?? null);
        $this->assertEquals('multiple', $result['data-multiple']);
        $this->assertEmpty($result['multiple'] ?? null);
        $this->assertEquals('required', $result['required']);
    }


    /**
     * Тест рендера
     */
    public function testRender()
    {
        $renderer = new TextFormFieldRenderer(
            new Form_Field([
                'datatype' => 'text',
                'required' => true,
                'urn' => 'name',
                'multiple' => 'true',
            ]),
            new Block_Form(),
            ['name' => 'aaa']
        );

        $result = $renderer->render();

        $this->assertStringContainsString('<input', $result);
        $this->assertStringContainsString(' data-raas-field ', $result);
        $this->assertStringContainsString(' data-type="text"', $result);
        $this->assertStringContainsString('name="name[]"', $result);
        $this->assertStringContainsString('data-multiple="multiple"', $result);
        $this->assertStringContainsString('required="required"', $result);
    }


    /**
     * Тест рендера - случай с файловым полем
     */
    public function testRenderWithFile()
    {
        $renderer = new FileFormFieldRenderer(
            new Form_Field([
                'datatype' => 'file',
                'required' => true,
                'urn' => 'somefile',
                'multiple' => 'true',
            ]),
            new Block_Form(),
            ['name' => 'aaa']
        );

        $result = $renderer->render(['data-aaa' => 'bbb']);

        $this->assertStringContainsString('<input', $result);
        $this->assertStringContainsString(' data-raas-field ', $result);
        $this->assertStringContainsString(' data-type="file"', $result);
        $this->assertStringContainsString('name="somefile[]"', $result);
        $this->assertStringContainsString('type="file"', $result);
        $this->assertStringContainsString('data-aaa="bbb"', $result);
        $this->assertStringContainsString('required="required"', $result);
    }


    /**
     * Тест рендера - случай с валидным атрибутом multiple
     */
    public function testRenderWithValidMultiple()
    {
        $renderer = new SelectFormFieldRenderer(
            new Form_Field([
                'datatype' => 'select',
                'urn' => 'name',
                'multiple' => 'true',
            ]),
            new Block_Form()
        );

        $result = $renderer->render();

        $this->assertStringContainsString(' multiple="multiple"', $result);
    }
}
