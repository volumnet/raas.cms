<?php
/**
 * Тест класса EditFieldForm
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
 * Тест класса EditFieldForm
 */
#[CoversClass(EditFieldForm::class)]
class EditFieldFormTest extends BaseTest
{
    public static $tables = [
        'cms_data',
        'cms_dictionaries',
        'cms_fieldgroups',
        'cms_fields',
        'cms_fields_form_vis',
        'cms_material_types',
        'cms_snippet_folders',
        'cms_snippets',
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
        $form = new EditFieldForm();
        $preprocessorField = $form->children['common']->children['preprocessor_id'];
        $postprocessorField = $form->children['common']->children['postprocessor_id'];

        $this->assertInstanceOf(InterfaceField::class, $preprocessorField);
        $this->assertEquals(FilesProcessorInterface::class, $preprocessorField->meta['rootInterfaceClass']);
        $this->assertEquals('preprocessor_classname', $preprocessorField->meta['interfaceClassnameFieldName']);
        $this->assertInstanceOf(InterfaceField::class, $postprocessorField);
        $this->assertEquals(FilesProcessorInterface::class, $postprocessorField->meta['rootInterfaceClass']);
        $this->assertEquals('postprocessor_classname', $postprocessorField->meta['interfaceClassnameFieldName']);
    }


    /**
     * Тест конструктора класса - случай с проверкой правильности полей
     */
    public function testConstructWithCheck()
    {
        $oldPost = $_POST;
        $oldServer = $_SERVER;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['name' => 'test', 'urn' => 'test', 'datatype' => 'select', 'multiple' => 1];
        $form = new EditFieldForm(['Item' => new Field(), 'meta' => ['Parent' => new Material_Type(1)]]); // 1 - преимущества
        $result = $form->process();

        $this->assertCount(1, $form->localError);
        $this->assertEquals('MISSED', $form->localError[0]['name']);
        $this->assertEquals('source', $form->localError[0]['value']);

        $_POST = $oldPost;
        $_SERVER = $oldServer;
    }


    /**
     * Тест конструктора класса - случай с группами полей
     */
    public function testConstructWithGroups()
    {
        $fieldGroup1 = new FieldGroup(['name' => 'test1', 'pid' => 1]);
        $fieldGroup1->commit();
        $fieldGroup2 = new FieldGroup(['name' => 'test2', 'pid' => 1]);
        $fieldGroup2->commit();

        Material_Type::clearSelfFieldsCache(); // Чтобы очистить кэш группы полей
        $form = new EditFieldForm(['Item' => new Field(), 'meta' => ['Parent' => new Material_Type(1)]]); // 1 - преимущества

        $this->assertInstanceOf(RAASField::class, $form->children['common']->children['gid']);
        $this->assertCount(3, $form->children['common']->children['gid']->children);
        $this->assertEquals(0, $form->children['common']->children['gid']->children[0]->value);
        $this->assertEquals($fieldGroup1->id, $form->children['common']->children['gid']->children[1]->value);
        $this->assertEquals($fieldGroup2->id, $form->children['common']->children['gid']->children[2]->value);

        FieldGroup::delete($fieldGroup1);
        FieldGroup::delete($fieldGroup2);
    }


    /**
     * Тест конструктора класса - случай с POST'ом
     */
    public function testConstructWithProcess()
    {
        $oldPost = $_POST;
        $oldServer = $_SERVER;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['name' => 'test', 'urn' => 'test', 'datatype' => 'text'];
        $field = new Material_Field();
        $form = new EditFieldForm(['Item' => $field, 'meta' => ['Parent' => new Material_Type(1)]]); // 1 - преимущества

        $form->process();

        $this->assertNotEmpty($field->id);
        $this->assertEquals(1, $field->pid);
        $this->assertEquals(Material_Type::class, $field->classname);

        Material_Field::delete($field);
        $_POST = $oldPost;
        $_SERVER = $oldServer;
    }
}
