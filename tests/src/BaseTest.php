<?php
/**
 * Файл базового теста
 */
namespace RAAS\CMS;

use PHPUnit\Framework\TestCase;
use RAAS\Application;

/**
 * Класс базового теста
 */
class BaseTest extends TestCase
{
    /**
     * Получение папки с ресурсами
     */
    public function getResourcesDir()
    {
        return __DIR__ . '/../resources';
    }
}
