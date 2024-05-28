<?php
/**
 * Тест класса EditBlockFormForm
 */
namespace RAAS\CMS;

use SOME\BaseTest;
use RAAS\Application;
use RAAS\Field as RAASField;
use RAAS\FormTab;
use RAAS\User as RAASUser;

/**
 * Тест класса EditBlockFormForm
 * @covers RAAS\CMS\EditBlockFormForm
 */
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
        $snippet = Snippet::importByURN('__raas_form_interface');

        $this->assertInstanceOf(RAASField::class, $interfaceField);
        $this->assertEquals($snippet->id, $interfaceField->default);
        $this->assertInstanceOf(RAASField::class, $widgetField);
        $this->assertInstanceOf(RAASField::class, $formField);
        $this->assertEquals('select', $formField->type);
    }
}
