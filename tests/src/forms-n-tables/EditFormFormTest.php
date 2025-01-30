<?php
/**
 * Тест класса EditFormForm
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;
use RAAS\Application;
use RAAS\Field as RAASField;
use RAAS\FormTab;
use RAAS\User as RAASUser;

/**
 * Тест класса EditFormForm
 */
#[CoversClass(EditFormForm::class)]
class EditFormFormTest extends BaseTest
{
    public static $tables = [
        'cms_blocks',
        'cms_blocks_form',
        'cms_forms',
        'cms_material_types',
        'cms_shop_cart_types',
        'cms_snippet_folders',
        'cms_snippets',
        'cms_users_blocks_register',
    ];


    public static function setUpBeforeClass(): void
    {
        Application::i()->initPackages();
        parent::setUpBeforeClass();
    }


    /**
     * Тест конструктора класса
     */
    public function testConstruct()
    {
        $form = new EditFormForm();
        $interfaceField = $form->children['interface_id'];

        $this->assertInstanceOf(InterfaceField::class, $interfaceField);
        $this->assertCount(2, $interfaceField->children);
        $this->assertEquals('', $interfaceField->children[0]->value);
        $this->assertEquals('Интерфейсы', $interfaceField->children[0]->caption);
        $this->assertEquals('disabled', $interfaceField->children[0]->disabled);
        $this->assertEquals('8', $interfaceField->children[1]->value);
        $this->assertEquals('dummy', $interfaceField->children[1]->caption);
        $this->assertNull($interfaceField->children[1]->disabled);
    }


    /**
     * Тест конструктора класса - случай с используемыми блоками
     */
    public function testConstructWithUsingBlocks()
    {
        $form = new EditFormForm(['Item' => new Form(1)]); // Обратная связь

        $this->assertInstanceOf(EntityUsersTable::class, $form->meta['blocksTable']);
        $this->assertCount(2, $form->meta['blocksTable']->Set);
        $this->assertEquals(6, $form->meta['blocksTable']->Set[0]->id);
        $this->assertEquals(27, $form->meta['blocksTable']->Set[1]->id);
    }


    /**
     * Тест конструктора класса - случай с используемыми типами корзин
     */
    public function testConstructWithUsingCartTypes()
    {
        $form = new EditFormForm(['Item' => new Form(3)]); // Форма заказа

        $this->assertInstanceOf(EntityUsersTable::class, $form->meta['cartTypesTable']);
        $this->assertCount(1, $form->meta['cartTypesTable']->Set);
        $this->assertEquals(1, $form->meta['cartTypesTable']->Set[0]->id);
    }
}
