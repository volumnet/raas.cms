<?php
/**
 * Тест класса Page_Field
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Тест класса Page_Field
 * @covers \RAAS\CMS\Page_Field
 */
class PageFieldTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_pages_cache',
        'cms_blocks',
        'cms_blocks_material',
        'cms_blocks_pages_assoc',
        'cms_blocks_search_pages_assoc',
        'cms_data',
        'cms_feedback',
        'cms_fields',
        'cms_material_types',
        'cms_material_types_affected_pages_for_materials_cache',
        'cms_material_types_affected_pages_for_self_cache',
        'cms_materials',
        'cms_materials_affected_pages_cache',
        'cms_materials_pages_assoc',
        'cms_menus',
        'cms_pages',
        'cms_shop_blocks_yml_pages_assoc',
        'cms_users',
    ];

    /**
     * Тест установки свойства Owner
     */
    public function testSetOwner()
    {
        $page = new Page();
        $page->commit();

        $field = new Page_Field();

        $this->assertNull($field->Owner);

        $field->Owner = $page;

        $this->assertEquals($page, $field->Owner);

        Page::delete($page);
    }


    /**
     * Тест наследуемой установки свойств
     */
    public function testSetDefault()
    {
        $field = new Page_Field();

        $this->assertNull($field->urn);

        $field->urn = 'test';

        $this->assertEquals('test', $field->urn);
    }


    /**
     * Тест метода commit()
     */
    public function testCommit()
    {
        $field = new Page_Field([
            'urn' => 'test',
            'classname' => 'aaa',
            'pid' => 1, // 1 - наши преимущества (только для проверки сохранения)
            'vis' => 1,
            'name' => 'Тест'
        ]);
        $field->commit();

        $this->assertEquals(Material_Type::class, $field->classname);
        $this->assertEquals(0, $field->pid);

        Page_Field::delete($field);
    }


    /**
     * Тест метода getSet()
     */
    public function testGetSet()
    {
        $result = Page_Field::getSet();
        $result = array_map(function ($x) {
            return (int)$x->id;
        }, $result);

        $this->assertContains(1, $result); // Описание к страницам
        $this->assertNotContains(12, $result); // Изображение у преимуществ
    }


    /**
     * Тест метода importByURN()
     */
    public function testImportByURN()
    {
        $result = Page_Field::importByURN('_description_');

        $this->assertEquals(1, $result->id); // Описание к страницам
    }


    /**
     * Тест метода importByURN() - поле не найдено
     */
    public function testImportByURNWithNotFound()
    {
        $result = Page_Field::importByURN('icon'); // Значок у преимуществ

        $this->assertNull($result); // Описание к страницам
    }
}
