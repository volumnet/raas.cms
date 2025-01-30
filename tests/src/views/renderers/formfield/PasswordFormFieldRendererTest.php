<?php
/**
 * Файл теста рендерера парольного поля формы
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Класс теста рендерера парольного поля формы
 */
#[CoversClass(PasswordFormFieldRenderer::class)]
class PasswordFormFieldRendererTest extends BaseTest
{
    public static $tables = [
        'cms_blocks_form',
    ];

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
