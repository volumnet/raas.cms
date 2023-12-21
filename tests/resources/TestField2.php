<?php
/**
 * Файл тестового поля с родительским классом (мок для CustomField)
 */
namespace RAAS\CMS;

/**
 * Класс тестового поля
 * @property-read self $parent Родительская сущность
 * @property-read self[] $parents Родительские сущности
 * @property-read self[] $children Дочерние сущности
 */
class TestField2 extends TestField
{
    protected static $references = [
        'parent' => [
            'classname' => CustomEntity::class,
            'FK' => 'pid',
        ],
    ];
}
