<?php
/**
 * Тест класса WidgetField
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
 * Тест класса WidgetField
 */
#[CoversClass(WidgetField::class)]
class WidgetFieldTest extends BaseTest
{
    public static $tables = [
        'cms_snippet_folders',
        'cms_snippets',
    ];

    /**
     * Тест конструктора класса
     */
    public function testConstruct()
    {
        $field = new WidgetField();

        $this->assertCount(2, $field->children);
        $this->assertEquals('', $field->children[0]->value);
        $this->assertEquals('Представления', $field->children[0]->caption);
        $this->assertEquals('disabled', $field->children[0]->disabled);
        $this->assertEquals('8', $field->children[1]->value);
        $this->assertEquals('dummy', $field->children[1]->caption);
        $this->assertNull($field->children[1]->disabled);
    }
}
