<?php
/**
 * Тестовый экземпляр PageRecursiveCache с поддержкой удаления экземпляра
 */
namespace RAAS\CMS;

class TestPageRecursiveCache extends PageRecursiveCache
{
    protected static $instance;

    public static function deleteInstance()
    {
        static::$instance = null;
    }
}
