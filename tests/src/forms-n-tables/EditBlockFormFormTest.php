<?php
/**
 * Тест класса EditBlockFormForm
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
 * Тест класса EditBlockFormForm
 */
#[CoversClass(EditBlockFormForm::class)]
class EditBlockFormFormTest extends BaseTest
{
    public static $tables = [
        'cms_forms',
        'cms_groups',
        'cms_pages',
        'cms_snippet_folders',
        'cms_snippets',
        'cms_templates',
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
        $form = new EditBlockFormForm(['meta' => ['Parent' => new Page(1)]]);
        $interfaceField = $form->children['serviceTab']->children['interface_id'];
        $widgetField = $form->children['commonTab']->children['widget_id'];
        $formField = $form->children['commonTab']->children['form'];

        $this->assertInstanceOf(InterfaceField::class, $interfaceField);
        $this->assertEquals(FormInterface::class, $interfaceField->default);
        $this->assertEquals(FormInterface::class, $interfaceField->meta['rootInterfaceClass']);
        $this->assertInstanceOf(WidgetField::class, $widgetField);
        $this->assertInstanceOf(RAASField::class, $formField);
        $this->assertEquals('select', $formField->type);
    }
}
