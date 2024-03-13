<?php
/**
 * Файл теста рендерера текстового поля формы
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Класс теста рендерера текстового поля формы
 * @covers RAAS\CMS\SelectFormFieldRenderer
 */
class SelectFormFieldRendererTest extends BaseTest
{
    public static $tables = [
        'cms_blocks_form',
    ];

    /**
     * Тест получения атрибутов
     */
    public function testGetAttributes()
    {
        $renderer = new SelectFormFieldRenderer(new Form_Field([
            'datatype' => 'select',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
            'multiple' => true,
        ]), new Block_Form(), 'aaa');

        $result = $renderer->getAttributes();

        $this->assertEmpty($result['type'] ?? null);
        $this->assertEquals('name[]', $result['name']);
        $this->assertEquals('required', $result['required']);
        $this->assertEquals(['form-control' => true], $result['class']);
        $this->assertEmpty($result['placeholder'] ?? null);
        $this->assertEmpty($result['maxlength'] ?? null);
        $this->assertEmpty($result['pattern'] ?? null);
        $this->assertEmpty($result['value'] ?? null);
        $this->assertEmpty($result['data-value'] ?? null);
    }


    /**
     * Тестирует получение дерева опций
     */
    public function testGetOptionsTree()
    {
        $renderer = new SelectFormFieldRenderer(new Form_Field([
            'datatype' => 'select',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
        ]), new Block_Form(), 'aaa');

        $result = $renderer->getOptionsTree([
            'aaa' => ['name' => 'AAA'],
            'bbb' => [
                'name' => 'BBB',
                'children' => [
                    'bbb1' => ['name' => 'BBB1'],
                    'bbb2' => ['name' => 'BBB2'],
                    'bbb3' => ['name' => 'BBB3'],
                ]
            ],
            'ccc' => ['name' => 'CCC'],
        ]);

        $this->assertEquals(
            '<option value>Your name</option>' .
            '<option value="aaa" selected="selected">AAA</option>' .
            '<option value="bbb">BBB</option>' .
            '<option value="bbb1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BBB1</option>' .
            '<option value="bbb2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BBB2</option>' .
            '<option value="bbb3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BBB3</option>' .
            '<option value="ccc">CCC</option>',
            $result
        );
    }


    /**
     * Тестирует получение дерева опций - случай с множественным полем
     */
    public function testGetOptionsTreeWithMultiple()
    {
        $renderer = new SelectFormFieldRenderer(new Form_Field([
            'datatype' => 'select',
            'multiple' => true,
            'required' => true,
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
        ]), new Block_Form(), 'aaa');

        $result = $renderer->getOptionsTree([
            'aaa' => ['name' => 'AAA'],
            'bbb' => [
                'name' => 'BBB',
                'children' => [
                    'bbb1' => ['name' => 'BBB1'],
                    'bbb2' => ['name' => 'BBB2'],
                    'bbb3' => ['name' => 'BBB3'],
                ]
            ],
            'ccc' => ['name' => 'CCC'],
        ]);

        $this->assertStringNotContainsString(
            '<option value>',
            $result
        );
    }


    /**
     * Тестирует получение дерева опций - случай с множественным полем
     * и placeholder'ом
     */
    public function testGetOptionsTreeWithMultipleWithPlaceholder()
    {
        $renderer = new SelectFormFieldRenderer(new Form_Field([
            'datatype' => 'select',
            'required' => true,
            'multiple' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
        ]), new Block_Form(), 'aaa');

        $result = $renderer->getOptionsTree([
            'aaa' => ['name' => 'AAA'],
            'bbb' => [
                'name' => 'BBB',
                'children' => [
                    'bbb1' => ['name' => 'BBB1'],
                    'bbb2' => ['name' => 'BBB2'],
                    'bbb3' => ['name' => 'BBB3'],
                ]
            ],
            'ccc' => ['name' => 'CCC'],
        ]);

        $this->assertStringContainsString(
            '<option value>Your name</option>',
            $result
        );
    }


    /**
     * Тестирует получение дерева опций - случай без значения
     */
    public function testGetOptionsTreeWithoutData()
    {
        $renderer = new SelectFormFieldRenderer(new Form_Field([
            'datatype' => 'select',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
        ]), new Block_Form());

        $result = $renderer->getOptionsTree([
            'aaa' => ['name' => 'AAA'],
            'bbb' => [
                'name' => 'BBB',
                'children' => [
                    'bbb1' => ['name' => 'BBB1'],
                    'bbb2' => ['name' => 'BBB2'],
                    'bbb3' => ['name' => 'BBB3'],
                ]
            ],
            'ccc' => ['name' => 'CCC'],
        ]);

        $this->assertEquals(
            '<option value selected="selected">Your name</option>' .
            '<option value="aaa">AAA</option>' .
            '<option value="bbb">BBB</option>' .
            '<option value="bbb1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BBB1</option>' .
            '<option value="bbb2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BBB2</option>' .
            '<option value="bbb3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BBB3</option>' .
            '<option value="ccc">CCC</option>',
            $result
        );
    }


    /**
     * Тест рендера
     */
    public function testRender()
    {
        $renderer = new SelectFormFieldRenderer(new Form_Field([
            'datatype' => 'select',
            'required' => true,
            'maxlength' => 16,
            'pattern' => '.*',
            'multiple' => true,
            'urn' => 'name',
            'source_type' => 'csv',
            'source' => "AAA;aaa\n"
                      . "BBB;bbb\n"
                      . ";BBB1;bbb1\n"
                      . ";BBB2;bbb2\n"
                      . ";BBB3;bbb3\n"
                      . "CCC;ccc\n"
        ]), new Block_Form(), ['aaa', 'bbb']);

        $result = $renderer->render();

        $this->assertStringContainsString(
            '<option value="aaa" selected="selected">AAA</option>' .
            '<option value="bbb" selected="selected">BBB</option>' .
            '<option value="bbb1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BBB1</option>' .
            '<option value="bbb2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BBB2</option>' .
            '<option value="bbb3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BBB3</option>' .
            '<option value="ccc">CCC</option>',
            $result
        );
    }
}
