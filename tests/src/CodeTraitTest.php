<?php
/**
 * Тест трейта CodeTrait
 */
namespace RAAS\CMS;

use SOME\BaseTest;
use SOME\File;

/**
 * Тест трейта CodeTrait
 * @covers RAAS\CMS\CodeTrait
 */
class CodeTraitTest extends BaseTest
{
    public static $tables = [
        'cms_fields',
        'cms_forms',
        'cms_snippets',
    ];

    /**
     * Тест метода saveFile()
     */
    public function testSaveFile()
    {
        $snippet = Snippet::importByURN('news');
        $filename = Package::i()->cacheDir . '/system/snippets/news.tmp.php';
        if (is_file($filename)) {
            unlink($filename);
        }

        $this->assertFileDoesNotExist($filename);

        $snippet->saveFile();

        $this->assertFileExists($filename);
    }


    /**
     * Тест метода saveFile() - случай с пустым id
     */
    public function testSaveFileWithNoId()
    {
        $filename = Package::i()->cacheDir . '/system/snippets/test.tmp.php';
        if (is_file($filename)) {
            unlink($filename);
        }
        $this->assertFileDoesNotExist($filename);

        $snippet = new Snippet(['urn' => 'test', 'description' => '']);
        $snippet->saveFile();

        $this->assertFileDoesNotExist($filename);
    }


    /**
     * Тест метода deleteFile()
     */
    public function testDeleteFile()
    {
        $this->assertFileDoesNotExist(Package::i()->cacheDir . '/system/snippets/test.tmp.php');

        $snippet = new Snippet(['urn' => 'test', 'description' => '']);
        $snippet->commit();

        $this->assertEquals('test', $snippet->urn);
        $this->assertFileExists(Package::i()->cacheDir . '/system/snippets/test.tmp.php');

        Snippet::delete($snippet);

        $this->assertFileDoesNotExist(Package::i()->cacheDir . '/system/snippets/test.tmp.php');
    }


    /**
     * Тест метода deleteFile() - случай с пустым id
     */
    public function testDeleteFileWithNoId()
    {
        $filename = Package::i()->cacheDir . '/system/snippets/test.tmp.php';
        if (is_file($filename)) {
            unlink($filename);
        }

        $snippet = new Snippet(['urn' => 'test', 'description' => '']);

        $this->assertEquals('test', $snippet->urn);
        $this->assertFileDoesNotExist($filename);

        touch($filename);

        $this->assertFileExists($filename);

        Snippet::delete($snippet);

        $this->assertFileExists($filename);

        unlink($filename);
    }


    /**
     * Тест метода updateNeeded()
     */
    public function testUpdateNeeded()
    {
        $snippet = Snippet::importByURN('news');
        $filename = Package::i()->cacheDir . '/system/snippets/news.tmp.php';
        if (is_file($filename)) {
            unlink($filename);
        }

        $result = $snippet->updateNeeded();

        $this->assertTrue($result);

        touch($filename);

        $result = $snippet->updateNeeded();

        $this->assertFalse($result);

        $snippet->modify_date = date('Y-m-d H:i:s', time() + 10);

        $result = $snippet->updateNeeded();

        $this->assertTrue($result);
    }


    /**
     * Тест метода prepareDir()
     */
    public function testPrepareDir()
    {
        $dir = Package::i()->cacheDir . '/system/snippets';
        if (is_dir($dir)) {
            File::unlink($dir);
        }

        $this->assertDirectoryDoesNotExist($dir);

        $snippet = Snippet::importByURN('news');
        $snippet->commit();

        $this->assertDirectoryExists($dir);
        $this->assertFileExists($dir . '/.htaccess');

        $htaccessText = file_get_contents($dir . '/.htaccess');
        $this->assertStringContainsString('Deny from all', $htaccessText);
    }
}
