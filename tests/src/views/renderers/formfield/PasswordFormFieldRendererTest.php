<?php
/**
 * Файл теста рендерера парольного поля формы
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера парольного поля формы
 * @covers RAAS\CMS\PasswordFormFieldRenderer
 */
class PasswordFormFieldRendererTest extends BaseTest
{
    /**
     * Тест рендера
     */
    public function testRender()
    {
        $renderer = new PasswordFormFieldRenderer(new Form_Field([
            'datatype' => 'password',
            'required' => true,
            'urn' => 'pass',
        ]), new Block_Form(), 'aaa');

        $result = $renderer->render(['data-test' => 'test']);

        $this->assertStringNotContainsString(' value="', $result);
        $this->assertStringNotContainsString(' data-value="', $result);
    }


    /**
     * Тест рендера - случай множественного поля
     */
    public function testRenderWithMultiple()
    {
        $renderer = new PasswordFormFieldRenderer(new Form_Field([
            'datatype' => 'password',
            'required' => true,
            'urn' => 'pass',
            'multiple' => true,
        ]), new Block_Form(), ['aaa', 'bbb']);

        $result = $renderer->render();

        $this->assertStringNotContainsString(' value="', $result);
        $this->assertStringNotContainsString(' data-value="', $result);
    }
}
