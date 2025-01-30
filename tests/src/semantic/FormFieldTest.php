<?php
/**
 * Тест класса Form_Field
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Тест класса Form_Field
 */
#[CoversClass(Form_Field::class)]
class FormFieldTest extends BaseTest
{
    public static $tables = [
        'cms_blocks',
        'cms_blocks_form',
        'cms_data',
        'cms_feedback',
        'cms_fields',
    ];

    /**
     * Тест установки свойства Owner
     */
    public function testSetOwner()
    {
        $feedback = new Feedback();
        $feedback->commit();

        $field = new Form_Field();

        $this->assertNull($field->Owner);

        $field->Owner = $feedback;

        $this->assertEquals($feedback, $field->Owner);

        Feedback::delete($feedback);
    }


    /**
     * Тест наследуемой установки свойств
     */
    public function testSetDefault()
    {
        $field = new Form_Field();

        $this->assertNull($field->urn);

        $field->urn = 'test';

        $this->assertEquals('test', $field->urn);
    }


    /**
     * Тест метода getHTMLId()
     */
    public function testGetHTMLId()
    {
        $field = new Form_Field(['urn' => 'test']);
        $field->commit();
        $fieldId = $field->id;
        $block = Block::spawn(6); // Обратная связь (всплывающее окно)

        $result = $field->getHTMLId($block, 2);

        $this->assertEquals('test' . $fieldId . '_6@2', $result);

        Form_Field::delete($field);
    }
}
