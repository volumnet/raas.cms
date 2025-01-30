<?php
/**
 * Тест класса SnippetField
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;
use RAAS\Application;
use RAAS\Controller_Frontend as ControllerFrontend;
use RAAS\Field as RAASField;
use RAAS\FormTab;
use RAAS\User as RAASUser;

/**
 * Тест класса SnippetField
 */
#[CoversClass(SnippetField::class)]
class SnippetFieldTest extends BaseTest
{
    public static $tables = [
        'cms_snippet_folders',
        'cms_snippets',
    ];

    public static function setUpBeforeClass(): void
    {
        ControllerFrontend::i()->exportLang(Application::i(), 'ru');
        ControllerFrontend::i()->exportLang(Package::i(), 'ru');
    }


    /**
     * Тест метода getChildrenArr()
     */
    public function testGetChildrenArr()
    {
        $field = new SnippetField();
        $result = $field->getChildrenArr(['__raas_views']);

        $this->assertCount(2, $result);
        $this->assertEquals('', $result[0]['value']);
        $this->assertEquals('Интерфейсы', $result[0]['caption']);
        $this->assertEquals('disabled', $result[0]['disabled']);
        $this->assertEquals('8', $result[1]['value']);
        $this->assertEquals('dummy', $result[1]['caption']);
        $this->assertNull($result[1]['disabled'] ?? null);
    }

    /**
     * Тест конструктора класса
     */
    public function testConstruct()
    {
        $field = new SnippetField(['meta' => ['ignoredSnippetFoldersURNs' => ['__raas_views']]]);

        $this->assertEquals('select', $field->type);
        $this->assertEquals('input-xxlarge', $field->class);
        $this->assertCount(2, $field->children);
        $this->assertEquals('', $field->children[0]->value);
        $this->assertEquals('Интерфейсы', $field->children[0]->caption);
        $this->assertEquals('disabled', $field->children[0]->disabled);
        $this->assertEquals('8', $field->children[1]->value);
        $this->assertEquals('dummy', $field->children[1]->caption);
        $this->assertNull($field->children[1]->disabled);
        $this->assertEquals('Нет', $field->placeholder);
    }
}
