<?php
/**
 * Файл теста рендерера текстового поля формы
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера текстового поля формы
 * @covers RAAS\CMS\CheckboxFormFieldRenderer
 */
class CheckboxFormFieldRendererTest extends BaseTest
{
    /**
     * Тест получения атрибутов
     */
    public function testGetAttributes()
    {
        $renderer = new CheckboxFormFieldRenderer(new Form_Field([
            'datatype' => 'checkbox',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
            'defval' => 2,
        ]), new Block_Form(), 2);

        $result = $renderer->getAttributes();

        $this->assertEquals('checkbox', $result['type']);
        $this->assertEquals('name', $result['name']);
        $this->assertEquals('required', $result['required']);
        $this->assertEmpty($result['class']);
        $this->assertEmpty($result['placeholder']);
        $this->assertEmpty($result['maxlength']);
        $this->assertEmpty($result['pattern']);
        $this->assertEquals(2, $result['value']);
        $this->assertEquals('checked', $result['checked']);
        $this->assertEmpty($result['data-value']);
    }


    /**
     * Тест получения атрибутов - случай со множественным полем
     */
    public function testGetAttributesWithMultiple()
    {
        $renderer = new CheckboxFormFieldRenderer(new Form_Field([
            'datatype' => 'checkbox',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
            'multiple' => true,
        ]), new Block_Form(), 'aaa');

        $result = $renderer->getAttributes();

        $this->assertEquals('checkbox', $result['type']);
        $this->assertEquals('name[]', $result['name']);
        $this->assertEmpty($result['required']);
        $this->assertEmpty($result['class']);
        $this->assertEmpty($result['placeholder']);
        $this->assertEmpty($result['maxlength']);
        $this->assertEmpty($result['pattern']);
        $this->assertEmpty($result['value']);
        $this->assertEmpty($result['checked']);
        $this->assertEmpty($result['multiple']);
        $this->assertEmpty($result['data-multiple']);
        $this->assertEmpty($result['data-value']);
    }


    /**
     * Тестирует получение дерева опций
     */
    public function testGetOptionsTree()
    {
        $renderer = new CheckboxFormFieldRenderer(new Form_Field([
            'datatype' => 'checkbox',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
            'multiple' => true,
        ]), new Block_Form(), ['aaa', 'bbb']);

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
              '<label><input type="checkbox" name="name[]" value="aaa" checked="checked" /> AAA</label>' .
            '</li>' .
            '<li>' .
              '<label><input type="checkbox" name="name[]" value="bbb" checked="checked" /> BBB</label>' .
              '<ul>' .
                '<li>' .
                  '<label><input type="checkbox" name="name[]" value="bbb1" /> BBB1</label>' .
                '</li>' .
                '<li>' .
                  '<label><input type="checkbox" name="name[]" value="bbb2" /> BBB2</label>' .
                '</li>' .
                '<li>' .
                  '<label><input type="checkbox" name="name[]" value="bbb3" /> BBB3</label>' .
                '</li>' .
              '</ul>' .
            '</li>' .
            '<li>' .
              '<label><input type="checkbox" name="name[]" value="ccc" /> CCC</label>' .
            '</li>',
            $result
        );
    }


    /**
     * Тест рендера
     */
    public function testRender()
    {
        $renderer = new CheckboxFormFieldRenderer(new Form_Field([
            'datatype' => 'checkbox',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
            'defval' => 2,
        ]), new Block_Form(), 2);

        $result = $renderer->render();

        $this->assertStringContainsString('<input ', $result);
        $this->assertStringContainsString(' type="checkbox"', $result);
        $this->assertStringContainsString(' name="name"', $result);
        $this->assertStringContainsString(' required="required"', $result);
        $this->assertStringNotContainsString(' class="', $result);
        $this->assertStringNotContainsString(' placeholder="', $result);
        $this->assertStringNotContainsString(' maxlength="', $result);
        $this->assertStringNotContainsString(' pattern="', $result);
        $this->assertStringContainsString(2, $result);
        $this->assertStringContainsString('checked', $result);
        $this->assertStringNotContainsString(' data-value="', $result);
    }


    /**
     * Тест рендера - случай со множественным полем
     */
    public function testRenderWithMultiple()
    {
        $renderer = new CheckboxFormFieldRenderer(new Form_Field([
            'datatype' => 'checkbox',
            'required' => true,
            'placeholder' => 'Your name',
            'maxlength' => 16,
            'pattern' => '.*',
            'urn' => 'name',
            'multiple' => true,
            'source_type' => 'csv',
            'source' => "AAA;aaa\n"
                      . "BBB;bbb\n"
                      . ";BBB1;bbb1\n"
                      . ";BBB2;bbb2\n"
                      . ";BBB3;bbb3\n"
                      . "CCC;ccc\n"
        ]), new Block_Form(), ['aaa', 'bbb']);

        $result = $renderer->render(['data-test' => 'test']);

        $this->assertEquals(
            '<ul data-raas-field data-type="checkbox" class="checkbox-tree" data-role="checkbox-tree" data-test="test">' .
              '<li>' .
                '<label><input type="checkbox" name="name[]" value="aaa" checked="checked" /> AAA</label>' .
              '</li>' .
              '<li>' .
                '<label><input type="checkbox" name="name[]" value="bbb" checked="checked" /> BBB</label>' .
                '<ul>' .
                  '<li>' .
                    '<label><input type="checkbox" name="name[]" value="bbb1" /> BBB1</label>' .
                  '</li>' .
                  '<li>' .
                    '<label><input type="checkbox" name="name[]" value="bbb2" /> BBB2</label>' .
                  '</li>' .
                  '<li>' .
                    '<label><input type="checkbox" name="name[]" value="bbb3" /> BBB3</label>' .
                  '</li>' .
                '</ul>' .
              '</li>' .
              '<li>' .
                '<label><input type="checkbox" name="name[]" value="ccc" /> CCC</label>' .
              '</li>' .
            '</ul>',
            $result
        );
    }
}
