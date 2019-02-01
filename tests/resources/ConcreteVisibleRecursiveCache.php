<?php
/**
 * Файл мока для проверки абстрактного рекурсивного кэша с видимостью
 */
namespace RAAS\CMS;

/**
 * Класс мока для проверки абстрактного рекурсивного кэша с видимостью
 */
class ConcreteVisibleRecursiveCache extends VisibleRecursiveCache
{
    protected static $instance;

    protected static $classname = Page::class;
}
