<?php
/**
 * Тест класса EditBlockPHPForm
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
 * Тест класса EditBlockPHPForm
 */
#[CoversClass(EditBlockPHPForm::class)]
class EditBlockPHPFormTest extends BaseTest
{
    public static $tables = [
        'cms_fields',
        'cms_groups',
        'cms_pages',
        'cms_snippet_folders',
        'cms_snippets',
    ];

    /**
     * Тест конструктора класса
     */
    public function testConstruct()
    {
        $form = new EditBlockPHPForm();
        $interfaceField = $form->children['serviceTab']->children['interface_id'];
        $widgetField = $form->children['commonTab']->children['widget_id'];

        $this->assertInstanceOf(InterfaceField::class, $interfaceField);
        $this->assertEquals(BlockInterface::class, $interfaceField->meta['rootInterfaceClass']);
        $this->assertInstanceOf(WidgetField::class, $widgetField);
    }
}
