<?php
/**
 * Тест класса FilesProcessorInterface
 */
namespace RAAS\CMS;

use InvalidArgumentException;
use SOME\BaseTest;

/**
 * Тест класса FilesProcessorInterface
 * @covers RAAS\CMS\FilesProcessorInterface
 */
class FilesProcessorInterfaceTest extends BaseTest
{
    public static $tables = [
    ];

    /**
     * Тест конструктора класса
     */
    public function testConstruct()
    {
        $interface = new WatermarkInterface();

        $this->assertInstanceOf(FilesProcessorInterface::class, $interface);
    }
}
