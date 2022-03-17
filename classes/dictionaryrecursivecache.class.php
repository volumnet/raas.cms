<?php
/**
 * Файл рекурсивного кэша справочников
 */
namespace RAAS\CMS;

use SOME\AbstractRecursiveCache;

/**
 * Класс рекурсивного кэша справочников
 */
class DictionaryRecursiveCache extends AbstractRecursiveCache
{
    protected static $instance;

    protected static $classname = Dictionary::class;
}
