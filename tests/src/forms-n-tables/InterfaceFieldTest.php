<?php
/**
 * Тест класса InterfaceField
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;
use RAAS\Application;
use RAAS\Controller_Frontend as ControllerFrontend;
use RAAS\Field as RAASField;
use RAAS\Form as RAASForm;
use RAAS\FormTab;
use RAAS\User as RAASUser;

/**
 * Тест класса InterfaceField
 */
#[CoversClass(InterfaceField::class)]
class InterfaceFieldTest extends BaseTest
{
    public static $tables = [
    ];

    public static function setUpBeforeClass(): void
    {
        ControllerFrontend::i()->exportLang(Application::i(), 'ru');
        ControllerFrontend::i()->exportLang(Package::i(), 'ru');
    }


    public function testGetInterfaceClasses()
    {
        $field = new InterfaceField();

        $result = $field->getInterfaceClasses(AbstractInterface::class);

        $this->assertEquals(FormInterface::class, $result[0]['value']);
        $this->assertNotEmpty($result[0]['children']);
    }

    /**
     * Тест конструктора класса
     */
    public function testConstruct()
    {
        $field = new InterfaceField(['meta' => ['rootInterfaceClass' => FormInterface::class]]);

        // $this->assertCount(3, $field->children);
        $this->assertEquals('', $field->children[0]->value);
        $this->assertEquals('disabled', $field->children[0]->disabled);
        $this->assertEquals('Классы интерфейсов', $field->children[0]->caption);
        $this->assertEquals(FormInterface::class, $field->children[0]->children[0]->value);
        $this->assertNotEmpty($field->children[0]->children[0]->children);
        $this->assertEquals('Интерфейсы', $field->children[1]->caption);
        $this->assertEquals('8', $field->children[2]->value);
        $this->assertEquals('dummy', $field->children[2]->caption);
        $this->assertNull($field->children[2]->disabled);
    }


    /**
     * Тест метода exportDefault()
     */
    public function testExportDefault()
    {
        $oldPost = $_POST;
        $snippet = new Snippet(['urn' => 'testinterface']);
        $snippet->commit();
        $block = new Block_PHP();

        $form = new RAASForm([
            'Item' => $block,
            'children' => [
                'interface_id' => new InterfaceField([
                    'name' => 'interface_id',
                    'meta' => ['interfaceClassnameFieldName' => 'interface_classname'],
                ])
            ],
        ]);
        $_POST['interface_id'] = $snippet->id;
        $form->children['interface_id']->exportDefault();

        $this->assertEquals($snippet->id, $block->interface_id);

        $_POST = $oldPost;
        Snippet::delete($snippet);
    }


    /**
     * Тест метода exportDefault()
     */
    public function testExportDefaultWithClassname()
    {
        $oldPost = $_POST;
        $block = new Block_PHP();

        $form = new RAASForm([
            'Item' => $block,
            'children' => [
                'interface_id' => new InterfaceField([
                    'name' => 'interface_id',
                    'meta' => ['interfaceClassnameFieldName' => 'interface_classname'],
                ])
            ],
        ]);
        $_POST['interface_id'] = FormInterface::class;
        $form->children['interface_id']->exportDefault();

        $this->assertEquals(FormInterface::class, $block->interface_classname);

        $_POST = $oldPost;
    }


    /**
     * Тест метода importDefault()
     */
    public function testImportDefault()
    {
        $snippet = new Snippet(['urn' => 'testinterface']);
        $snippet->commit();
        $block = new Block_PHP(['interface_id' => $snippet->id, ['cats' => 1]]);
        $block->commit(); // Для корректного импорта

        $form = new RAASForm([
            'Item' => $block,
            'children' => [
                'interface_id' => new InterfaceField([
                    'name' => 'interface_id',
                    'meta' => ['interfaceClassnameFieldName' => 'interface_classname'],
                ])
            ],
        ]);
        $form->process(['Item' => $block]);

        $this->assertEquals($snippet->id, $form->DATA['interface_id']);

        Snippet::delete($snippet);
        Block_PHP::delete($block);
    }


    /**
     * Тест метода importDefault() - случай с указанием класса интерфейса
     */
    public function testImportDefaultWithClassname()
    {
        $block = new Block_PHP(['interface_classname' => MaterialInterface::class, ['cats' => 1]]);
        $block->commit(); // Для корректного импорта

        $form = new RAASForm([
            'Item' => $block,
            'children' => [
                'interface_id' => new InterfaceField([
                    'name' => 'interface_id',
                    'meta' => ['interfaceClassnameFieldName' => 'interface_classname'],
                ])
            ],
        ]);
        $form->process(['Item' => $block]);

        $this->assertEquals(MaterialInterface::class, $form->DATA['interface_id']);

        Block_PHP::delete($block);
    }
}
