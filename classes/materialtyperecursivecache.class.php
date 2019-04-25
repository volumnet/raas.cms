<?php
/**
 * Файл рекурсивного кэша типов материалов
 */
namespace RAAS\CMS;

use SOME\AbstractRecursiveCache;

/**
 * Класс рекурсивного кэша типов материалов
 */
class MaterialTypeRecursiveCache extends AbstractRecursiveCache
{
    protected static $instance;

    protected static $classname = Material_Type::class;
}
