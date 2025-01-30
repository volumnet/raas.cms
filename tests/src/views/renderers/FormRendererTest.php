<?php
/**
 * Файл теста рендерера формы
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Класс теста рендерера формы
 */
#[CoversClass(FormRenderer::class)]
class FormRendererTest extends BaseTest
{
    public static $tables = [
        'cms_blocks',
        'cms_blocks_form',
    ];


    /**
     * Тест ошибки от 2024-05-28 11:38
     * Uncaught TypeError: trim(): Argument #1 ($string) must be of type string, null given in D:\web\home\libs\raas.cms\classes\views\renderers\formrenderer.class.php on line 111
     */
    public function test202405281138()
    {
        $renderer = new FormRenderer(
            new Form([
                'antispam' => 'hidden',
                'antispam_field_name' => '_question'
            ]),
            new Block_Form(),
        );

        $result = $renderer->renderHiddenAntispamField(['data-test' => 'test']);

        $this->assertStringContainsString('></textarea', $result);
    }

    /**
     * Тест рендера поля подписи
     */
    public function testRenderSignatureField()
    {
        $renderer = new FormRenderer(
            new Form(['signature' => true]),
            new Block_Form()
        );

        $result = $renderer->renderSignatureField(['data-test' => 'test']);

        $this->assertStringContainsString('<input type="hidden"', $result);
        $this->assertStringContainsString('name="form_signature"', $result);
        $this->assertStringContainsString('value="', $result);
        $this->assertStringContainsString('data-test="test"', $result);
    }

    /**
     * Тест рендера поля подписи - случай с формой без подписи
     */
    public function testRenderSignatureFieldWithNoSignature()
    {
        $renderer = new FormRenderer(new Form(), new Block_Form());

        $result = $renderer->renderSignatureField(['data-test' => 'test']);

        $this->assertEquals('', $result);
    }


    /**
     * Тест рендера скрытого антиспам-поля
     */
    public function testRenderHiddenAntispamField()
    {
        $renderer = new FormRenderer(
            new Form([
                'antispam' => 'hidden',
                'antispam_field_name' => '_question'
            ]),
            new Block_Form(),
            ['_question' => 'aaa']
        );

        $result = $renderer->renderHiddenAntispamField(['data-test' => 'test']);

        $this->assertStringContainsString('<textarea', $result);
        $this->assertStringContainsString('>aaa</textarea', $result);
        $this->assertStringContainsString('name="_question"', $result);
        $this->assertStringContainsString('style="', $result);
        $this->assertStringContainsString('position: absolute', $result);
        $this->assertStringContainsString('left: -', $result);
        $this->assertStringContainsString('autocomplete="off"', $result);
        $this->assertStringContainsString('data-test="test"', $result);
    }


    /**
     * Тест рендера скрытого антиспам-поля -
     * случай, когда антиспам-поле не скрытое
     */
    public function testRenderHiddenAntispamFieldWithoutHidden()
    {
        $renderer = new FormRenderer(
            new Form([
                'antispam' => 'captcha',
                'antispam_field_name' => '_question'
            ]),
            new Block_Form(),
            ['_question' => 'aaa']
        );

        $result = $renderer->renderHiddenAntispamField(['data-test' => 'test']);

        $this->assertEquals('', $result);
    }
}
