<?php
/**
 * Тест класса EditBlockSearchForm
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
 * Тест класса EditBlockSearchForm
 */
#[CoversClass(EditBlockSearchForm::class)]
class EditBlockSearchFormTest extends BaseTest
{
    public static $tables = [
        'cms_fields',
        'cms_groups',
        'cms_material_types',
        'cms_pages',
        'cms_snippet_folders',
        'cms_snippets',
        'registry',
    ];

    /**
     * Тест конструктора класса
     */
    public function testConstruct()
    {
        $form = new EditBlockSearchForm();
        $interfaceField = $form->children['serviceTab']->children['interface_id'];
        $widgetField = $form->children['commonTab']->children['widget_id'];

        $this->assertInstanceOf(InterfaceField::class, $interfaceField);
        $this->assertEquals(SearchInterface::class, $interfaceField->default);
        $this->assertEquals(SearchInterface::class, $interfaceField->meta['rootInterfaceClass']);
        $this->assertInstanceOf(WidgetField::class, $widgetField);
        $this->assertNotEquals(0, $form->children['serviceTab']->children['rows_per_page']->default);
        $this->assertEquals('page', $form->children['serviceTab']->children['pages_var_name']->default);
    }
}
