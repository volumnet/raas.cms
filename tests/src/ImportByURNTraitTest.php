<?php
/**
 * Тест трейта ImportByURNTrait
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;
use SOME\File;
use RAAS\Application;

/**
 * Тест трейта ImportByURNTrait
 */
class ImportByURNTraitTest extends BaseTest
{
    public static $tables = [
        'cms_snippets',
    ];

    /**
     * Тест метода importByURN()
     */
    public function testImportByURN()
    {
        $result = Snippet::importByURN('news');

        $this->assertInstanceOf(Snippet::class, $result);
        $this->assertEquals('news', $result->urn);
        $this->assertNotEmpty($result->id);
    }

    /**
     * Тест метода importByURN() - случай с несуществующей сущностью
     */
    public function testImportByURNWithNotExist()
    {
        $result = Snippet::importByURN('news1');

        $this->assertNull($result);
    }
}
