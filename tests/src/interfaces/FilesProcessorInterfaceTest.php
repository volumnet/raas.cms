<?php
/**
 * Тест класса FilesProcessorInterface
 */
namespace RAAS\CMS;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Тест класса FilesProcessorInterface
 */
#[CoversClass(FilesProcessorInterface::class)]
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
