<?php
/**
 * Файл теста рендерера поля формы
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера поля формы
 * @covers RAAS\CMS\FormFieldRenderer
 */
class FormFieldRendererTest extends BaseTest
{
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
        ]), new Block_Form());

        $result = $renderer->getAttributes();

        $this->assertEquals('', $result['data-raas-field']);
        $this->assertEquals('name', $result['name']);
        $this->assertStringStartsWith('name', $result['id']);
        $this->assertEquals('required', $result['required']);
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
