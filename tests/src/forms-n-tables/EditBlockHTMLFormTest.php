<?php
/**
 * Тест класса EditBlockHTMLForm
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
 * Тест класса EditBlockHTMLForm
 */
#[CoversClass(EditBlockHTMLForm::class)]
class EditBlockHTMLFormTest extends BaseTest
{
    public static $tables = [
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
        $form = new EditBlockHTMLForm(['meta' => ['Parent' => new Page(1)]]);
        $interfaceField = $form->children['serviceTab']->children['interface_id'];
        $widgetField = $form->children['serviceTab']->children['widget_id'];
        $descriptionField = $form->children['commonTab']->children['description'];
        $wysiwygField = $form->children['commonTab']->children['wysiwyg'];

        $this->assertInstanceOf(InterfaceField::class, $interfaceField);
        $this->assertInstanceOf(WidgetField::class, $widgetField);
        $this->assertEquals('htmlcodearea', $descriptionField->type);
        $this->assertEquals('text/html', $descriptionField->{'data-mime'});
        $this->assertEquals('checkbox', $wysiwygField->type);
    }


    /**
     * Тест конструктора класса - случай размещения на не-HTML странице
     */
    public function testConstructWithAJAX()
    {
        $form = new EditBlockHTMLForm(['meta' => ['Parent' => new Page(13)]]); // 13 - custom.css
        $descriptionField = $form->children['commonTab']->children['description'];
        $this->assertEquals('codearea', $descriptionField->type);
        $this->assertEquals('text/css', $descriptionField->{'data-mime'});
    }

}
