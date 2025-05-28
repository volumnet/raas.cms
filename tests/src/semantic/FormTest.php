<?php
/**
 * Тест класса Form
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;
use RAAS\CMS\Shop\Cart_Type;

/**
 * Тест класса Form
 */
#[CoversClass(Form::class)]
class FormTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_blocks_cache',
        'cms_blocks',
        'cms_blocks_form',
        'cms_blocks_html',
        'cms_blocks_pages_assoc',
        'cms_data',
        'cms_feedback',
        'cms_fieldgroups',
        'cms_fields',
        'cms_forms',
        'cms_pages',
        'cms_shop_cart_types',
        'cms_shop_cart_types_material_types_assoc',
        'cms_users_blocks_register',
    ];

    /**
     * Тест метода commit()
     */
    public function testCommit()
    {
        $form = new Form(['name' => 'Тест']);
        $form->commit();

        $this->assertEquals('test', $form->urn);

        Form::delete($form);
    }


    /**
     * Тест метода delete()
     */
    public function testDelete()
    {
        $form = new Form(['name' => 'Тест']);
        $form->commit();
        $field = new Form_Field([
            'classname' => Form::class,
            'pid' => $form->id,
            'datatype' => 'text',
            'urn' => 'testfield',
        ]);
        $field->commit();
        $fieldId = $field->id;
        $block = new Block_Form([
            'form' => $form->id,
            'name' => 'Тестовый блок',
            'location' => 'content',
            'cats' => [1]
        ]);
        $block->commit();
        $blockId = $block->id;

        $this->assertNotEmpty($fieldId);
        $this->assertNotEmpty($blockId);

        Form::delete($form);

        $block = Block::spawn($blockId);
        $field = new Field($fieldId);

        $this->assertEmpty($block->id);
        $this->assertEmpty($field->id);
    }


    /**
     * Тест получения свойства fields
     */
    public function testGetFields()
    {
        $form = new Form(['name' => 'Тест']);
        $form->commit();
        $field1 = new Form_Field([
            'classname' => Form::class,
            'pid' => $form->id,
            'datatype' => 'text',
            'urn' => 'testfield',
        ]);
        $field1->commit();
        $field2 = new Form_Field([
            'classname' => Form::class,
            'pid' => $form->id,
            'datatype' => 'text',
            'urn' => 'testfield2',
        ]);
        $field2->commit();
        $form = new Form($form->id);

        $result = $form->fields;

        $this->assertNotEmpty($field1->id);
        $this->assertNotEmpty($field2->id);
        $this->assertCount(2, $result);
        $this->assertEquals($field1->id, $result['testfield']->id);
        $this->assertEquals($field2->id, $result['testfield2']->id);

        Form_Field::delete($field1);
        Form_Field::delete($field2);
        Form::delete($form);
    }


    /**
     * Тест получения свойства visFields
     */
    public function testGetVisFields()
    {
        $form = new Form(['name' => 'Тест']);
        $form->commit();
        $field1 = new Form_Field([
            'classname' => Form::class,
            'vis' => true,
            'pid' => $form->id,
            'datatype' => 'text',
            'urn' => 'testfield',
        ]);
        $field1->commit();
        $field2 = new Form_Field([
            'classname' => Form::class,
            'vis' => false,
            'pid' => $form->id,
            'datatype' => 'text',
            'urn' => 'testfield2',
        ]);
        $field2->commit();
        $form = new Form($form->id);

        $result = $form->visFields;

        $this->assertNotEmpty($field1->id);
        $this->assertNotEmpty($field2->id);
        $this->assertCount(1, $result);
        $this->assertEquals($field1->id, $result['testfield']->id);
        $this->assertNull($result['testfield2'] ?? null);

        Form_Field::delete($field1);
        Form_Field::delete($field2);
        Form::delete($form);
    }


    /**
     * Тест получения свойства unreadFeedbacks
     */
    public function testGetUnreadFeedbacks()
    {
        $form = new Form(['name' => 'Тест']);
        $form->commit();
        $feedback1 = new Feedback(['pid' => $form->id, 'vis' => 0]);
        $feedback1->commit();
        $feedback2 = new Feedback(['pid' => $form->id, 'vis' => 1]);
        $feedback2->commit();

        $this->assertEquals(1, $form->unreadFeedbacks);

        Form::delete($form);
        Feedback::delete($feedback1);
        Feedback::delete($feedback2);
    }


    /**
     * Тест метода getSignature()
     */
    public function testGetSignature()
    {
        $form = new Form(1); // Обратная связь
        $block = Block::spawn(6); // Обратная связь (всплывающее окно)

        $result = $form->getSignature($block);
        $expected = md5('form16');

        $this->assertEquals($expected, $result);
    }


    /**
     * Тест получения свойства usingBlocks
     */
    public function testGetUsingBlocks()
    {
        $form = new Form(['name' => 'Тест']);
        $form->commit();
        $block = new Block_Form([
            'form' => $form->id,
            'name' => 'Тестовый блок',
            'location' => 'content',
            'cats' => [1]
        ]);
        $block->commit();

        $result = $form->usingBlocks;

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Block_Form::class, $result[0]);
        $this->assertEquals($block->id, $result[0]->id);

        Form::delete($form);
        Block_Form::delete($block);
    }


    /**
     * Тест получения свойства usingCartTypes
     */
    public function testGetUsingCartTypes()
    {
        $form = new Form(['name' => 'Тест']);
        $form->commit();
        $cartType = new Cart_Type([
            'form_id' => $form->id,
            'name' => 'Тестовый блок',
        ]);
        $cartType->commit();

        $result = $form->usingCartTypes;

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Cart_Type::class, $result[0]);
        $this->assertEquals($cartType->id, $result[0]->id);

        Form::delete($form);
        Cart_Type::delete($cartType);
    }
}
