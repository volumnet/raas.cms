<?php
/**
 * Файл тестового поля с возвратом объектов из getValues() (мок для CustomField)
 */
namespace RAAS\CMS;

/**
 * Класс тестового поля с возвратом объектов из getValues()
 * @property-read self $parent Родительская сущность
 * @property-read self[] $parents Родительские сущности
 * @property-read self[] $children Дочерние сущности
 */
class TestFieldMockObjectGetValues extends TestField
{
    public function getValues($forceArray = false)
    {
        return [
            new CustomEntity(['name' => 'Entity 1']),
            new CustomEntity(['name' => 'Entity 2']),
            new CustomEntity(['name' => 'Entity 3']),
        ];
    }
}
