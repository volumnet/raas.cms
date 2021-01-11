<?php
/**
 * Файл теста рендерера текстового поля формы
 */
namespace RAAS\CMS;

/**
 * Класс теста рендерера текстового поля формы
 * @covers RAAS\CMS\SelectFormFieldRenderer
 */
class SelectFormFieldRendererTest extends BaseTest
{
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

        $this->assertEmpty($result['type']);
        $this->assertEquals('name[]', $result['name']);
        $this->assertEquals('required', $result['required']);
        $this->assertEquals(['form-control' => true], $result['class']);
        $this->assertEmpty($result['placeholder']);
        $this->assertEmpty($result['maxlength']);
        $this->assertEmpty($result['pattern']);
        $this->assertEmpty($result['value']);
        $this->assertEmpty($result['data-value']);
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
            '<option>Your name</option>' .
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
            '<option selected="selected">Your name</option>' .
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
