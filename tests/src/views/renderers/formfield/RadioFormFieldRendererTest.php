<?php
/**
 * Файл теста рендерера текстового поля формы
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Класс теста рендерера текстового поля формы
 * @covers RAAS\CMS\RadioFormFieldRenderer
 */
class RadioFormFieldRendererTest extends BaseTest
{
    public static $tables = [
        'cms_blocks_form',
    ];

    /**
     * Тест получения атрибутов
     */
    public function testGetAttributes()
    {
        $renderer = new RadioFormFieldRenderer(new Form_Field([
            'datatype' => 'radio',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
            'defval' => 2,
        ]), new Block_Form(), 2);

        $result = $renderer->getAttributes();

        $this->assertEquals('radio', $result['type']);
        $this->assertEquals('name', $result['name']);
        $this->assertEquals('required', $result['required']);
        $this->assertEmpty($result['class'] ?? null);
        $this->assertEmpty($result['placeholder'] ?? null);
        $this->assertEmpty($result['maxlength'] ?? null);
        $this->assertEmpty($result['pattern'] ?? null);
        $this->assertEmpty($result['multiple'] ?? null);
        $this->assertEmpty($result['data-multiple'] ?? null);
        $this->assertEmpty($result['checked'] ?? null);
        $this->assertEmpty($result['value'] ?? null);
        $this->assertEmpty($result['data-value'] ?? null);
        $this->assertEmpty($result['id'] ?? null);
    }


    /**
     * Тестирует получение дерева опций
     */
    public function testGetOptionsTree()
    {
        $renderer = new RadioFormFieldRenderer(new Form_Field([
            'datatype' => 'radio',
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
        ]), new Block_Form(), 'bbb');

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
            '<li>' .
              '<label><input type="radio" name="name" value /> Your name</label>' .
            '</li>' .
            '<li>' .
              '<label><input type="radio" name="name" value="aaa" /> AAA</label>' .
            '</li>' .
            '<li>' .
              '<label><input type="radio" name="name" value="bbb" checked="checked" /> BBB</label>' .
              '<ul>' .
                '<li>' .
                  '<label><input type="radio" name="name" value="bbb1" /> BBB1</label>' .
                '</li>' .
                '<li>' .
                  '<label><input type="radio" name="name" value="bbb2" /> BBB2</label>' .
                '</li>' .
                '<li>' .
                  '<label><input type="radio" name="name" value="bbb3" /> BBB3</label>' .
                '</li>' .
              '</ul>' .
            '</li>' .
            '<li>' .
              '<label><input type="radio" name="name" value="ccc" /> CCC</label>' .
            '</li>',
            $result
        );
    }


    /**
     * Тестирует получение дерева опций - случай с выбранным placeholder'ом
     */
    public function testGetOptionsTreeWithPlaceholderSelected()
    {
        $renderer = new RadioFormFieldRenderer(new Form_Field([
            'datatype' => 'radio',
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
        ]), new Block_Form(), '');

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
            '<li>' .
              '<label><input type="radio" name="name" value checked="checked" /> Your name</label>' .
            '</li>' .
            '<li>' .
              '<label><input type="radio" name="name" value="aaa" /> AAA</label>' .
            '</li>' .
            '<li>' .
              '<label><input type="radio" name="name" value="bbb" /> BBB</label>' .
              '<ul>' .
                '<li>' .
                  '<label><input type="radio" name="name" value="bbb1" /> BBB1</label>' .
                '</li>' .
                '<li>' .
                  '<label><input type="radio" name="name" value="bbb2" /> BBB2</label>' .
                '</li>' .
                '<li>' .
                  '<label><input type="radio" name="name" value="bbb3" /> BBB3</label>' .
                '</li>' .
              '</ul>' .
            '</li>' .
            '<li>' .
              '<label><input type="radio" name="name" value="ccc" /> CCC</label>' .
            '</li>',
            $result
        );
    }


    /**
     * Тест рендера
     */
    public function testRender()
    {
        $renderer = new RadioFormFieldRenderer(new Form_Field([
            'datatype' => 'radio',
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
            'defval' => 2,
            'source_type' => 'csv',
            'source' => "AAA;aaa\n"
                      . "BBB;bbb\n"
                      . ";BBB1;bbb1\n"
                      . ";BBB2;bbb2\n"
                      . ";BBB3;bbb3\n"
                      . "CCC;ccc\n"
        ]), new Block_Form(), 'bbb');

        $result = $renderer->render(['data-test' => 'test']);

        $this->assertEquals(
            '<ul data-raas-field data-type="radio" class="checkbox-tree checkbox-tree_radio" data-role="checkbox-tree" data-test="test">' .
              '<li>' .
                '<label><input type="radio" name="name" value /> Your name</label>' .
              '</li>' .
              '<li>' .
                '<label><input type="radio" name="name" value="aaa" /> AAA</label>' .
              '</li>' .
              '<li>' .
                '<label><input type="radio" name="name" value="bbb" checked="checked" /> BBB</label>' .
                '<ul>' .
                  '<li>' .
                    '<label><input type="radio" name="name" value="bbb1" /> BBB1</label>' .
                  '</li>' .
                  '<li>' .
                    '<label><input type="radio" name="name" value="bbb2" /> BBB2</label>' .
                  '</li>' .
                  '<li>' .
                    '<label><input type="radio" name="name" value="bbb3" /> BBB3</label>' .
                  '</li>' .
                '</ul>' .
              '</li>' .
              '<li>' .
                '<label><input type="radio" name="name" value="ccc" /> CCC</label>' .
              '</li>' .
            '</ul>',
            $result
        );
    }
}
