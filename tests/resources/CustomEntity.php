<?php
/**
 * Файл некоторой сущности (мок для SOME)
 */
namespace RAAS\CMS;

use SOME\SOME;

/**
 * Класс некоторой сущности
 * @property-read self $parent Родительская сущность
 * @property-read self[] $parents Родительские сущности
 * @property-read self[] $children Дочерние сущности
 */
class CustomEntity extends SOME
{
    protected static $tablename = 'tmp_entities';

    protected static $defaultOrderBy = "priority";

    protected static $references = [
        'parent' => [
            'FK' => 'pid',
            'classname' => self::class,
            'cascade' => true
        ],
    ];

    protected static $parents = [
        'parents' => 'parent'
    ];

    protected static $children = [
        'children' => [
            'classname' => self::class,
            'FK' => 'pid'
        ],
        'children2' => [
            'classname' => self::class,
            'FK' => 'pid'
        ],
    ];
}
