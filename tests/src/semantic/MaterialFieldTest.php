<?php
/**
 * Тест класса Material_Field
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Тест класса Material_Field
 * @covers \RAAS\CMS\Material_Field
 */
class MaterialFieldTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_materials_cache',
        'cms_blocks',
        'cms_blocks_material',
        'cms_blocks_pages_assoc',
        'cms_data',
        'cms_feedback',
        'cms_fields',
        'cms_fields_form_vis',
        'cms_material_types',
        'cms_material_types_affected_pages_for_materials_cache',
        'cms_material_types_affected_pages_for_self_cache',
        'cms_materials',
        'cms_materials_affected_pages_cache',
        'cms_materials_pages_assoc',
        'cms_pages',
    ];

    /**
     * Тест установки свойства Owner
     */
    public function testSetOwner()
    {
        $material = new Material();
        $material->commit();

        $field = new Material_Field();

        $this->assertNull($field->Owner);

        $field->Owner = $material;

        $this->assertEquals($material, $field->Owner);

        Material::delete($material);
    }


    /**
     * Тест наследуемой установки свойств
     */
    public function testSetDefault()
    {
        $field = new Material_Field();

        $this->assertNull($field->urn);

        $field->urn = 'test';

        $this->assertEquals('test', $field->urn);
    }


    /**
     * Тест метода commit()
     */
    public function testCommit()
    {
        $field = new Material_Field(['urn' => 'test', 'pid' => 1, 'vis' => 1, 'name' => 'Тест']); // 1 - наши преимущества
        $field->commit();
        $fieldId = (int)$field->id;
        $materialType = new Material_Type(1); // 1 - наши преимущества

        $this->assertEmpty(Material_Type::$selfFieldsCache[1] ?? null);
        $this->assertEmpty(Material_Type::$visSelfFieldsCache[1] ?? null);
        $this->assertEmpty(Material_Type::$fieldsCache[1] ?? null);
        $this->assertEmpty(Material_Type::$visFieldsCache[1] ?? null);
        $this->assertContains($fieldId, $materialType->formFields_ids);

        $field->pid = 2; // Баннеры
        $field->commit();
        $oldMaterialType = new Material_Type(1); // 1 - наши преимущества
        $newMaterialType = new Material_Type(2); // 2 - баннеры

        $this->assertNotContains($fieldId, $oldMaterialType->formFields_ids);
        $this->assertContains($fieldId, $newMaterialType->formFields_ids);

        Material_Field::delete($field);
    }


    /**
     * Тест метода getSet()
     */
    public function testGetSet()
    {
        $result = Material_Field::getSet();
        $result = array_map(function ($x) {
            return (int)$x->id;
        }, $result);

        $this->assertNotContains(1, $result); // Описание к страницам
        $this->assertContains(12, $result); // Изображение у преимуществ
    }
}
