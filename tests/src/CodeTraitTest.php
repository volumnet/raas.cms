<?php
/**
 * Тест трейта CodeTrait
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;
use SOME\File;
use RAAS\Application;

/**
 * Тест трейта CodeTrait
 */
class CodeTraitTest extends BaseTest
{
    public static $tables = [
        'cms_fields',
        'cms_forms',
        'cms_snippets',
    ];


    /**
     * Тест получения свойства description
     */
    public function testGetDescription()
    {
        $snippet = new Snippet(['urn' => 'test', 'description' => 'aaa']);
        $snippet->commit();
        $snippet = new Snippet($snippet->id);

        $result = $snippet->description;

        $this->assertEquals('aaa', $result);

        Snippet::delete($snippet);
    }


    /**
     * Тест получения свойства name
     * @param string $code Код
     * @param string $urn Установленный URN
     * @param string $expected Ожидаемое значение
     */
    #[TestWith([
        (
            '<' . "?php\n" .
            "/**\n" .
            " * Тест\n" .
            " */\n"
        ),
        '',
        'Тест',
    ])]
    #[TestWith([
        (
            '<' . "?php\n" .
            "/**\n" .
            " */\n"
        ),
        'test',
        'test',
    ])]
    #[TestWith([
        (
            '<' . "?php\n" .
            "/**\n" .
            " * Тест\n" .
            " * @param array<[string]>\n" . // Некорректный тег PHPDoc для проверки исключения
            " */\n"
        ),
        'test1',
        'test1',
    ])]
    #[TestWith([
        (
            '<' . "?php\n" .
            "return 'aaa';\n"
        ),
        'test',
        'test',
    ])]
    #[TestWith([
        (
            '<' . "?php\n" .
            "return 'aaa';\n"
        ),
        '',
        '',
    ])]
    public function testGetName(string $code, string $urn, string $expected)
    {
        $snippet = new Snippet(['description' => $code]);
        if ($urn) {
            $snippet->urn = $urn;
        }

        $this->assertEquals($expected, $snippet->name);
    }


    /**
     * Тест получения свойства name
     */
    public function testGetNameWithNoDocBlock()
    {
        $code = '<' . "?php\n"
            . "return 'aaa';\n";
        $snippet = new Snippet(['urn' => 'test', 'description' => $code]);
        $snippet->commit();

        $this->assertEquals('test', $snippet->name);

        Snippet::delete($snippet);
    }


    /**
     * Тест метода saveFile()
     */
    public function testSaveFile()
    {
        $snippet = Snippet::importByURN('news');
        $filename = Application::i()->baseDir . '/inc/snippets/news.tmp.php';
        if (is_file($filename)) {
            unlink($filename);
        }

        $this->assertFileDoesNotExist($filename);

        $snippet->saveFile();

        $this->assertFileExists($filename);
    }

    /**
     * Тест метода saveFile() - случай без URN
     */
    public function testSaveFileWithNoURN()
    {
        $snippet = new Snippet();
        $snippet->commit();

        $filename = Application::i()->baseDir . '/inc/snippets/.tmp.php';
        if (is_file($filename)) {
            unlink($filename);
        }

        $this->assertFileDoesNotExist($filename);

        $snippet->saveFile();

        $this->assertFileDoesNotExist($filename);

        Snippet::delete($snippet);
    }


    /**
     * Тест метода deleteFile()
     */
    public function testDeleteFile()
    {
        $this->assertFileDoesNotExist(Application::i()->baseDir . '/inc/snippets/test.tmp.php');

        $snippet = new Snippet(['urn' => 'test', 'description' => '']);
        $snippet->commit();

        $this->assertEquals('test', $snippet->urn);
        $this->assertFileExists(Application::i()->baseDir . '/inc/snippets/test.tmp.php');

        Snippet::delete($snippet);

        $this->assertFileDoesNotExist(Application::i()->baseDir . '/inc/snippets/test.tmp.php');
    }


    /**
     * Тест метода deleteFile() - случай с пустым id
     */
    public function testDeleteFileWithNoId()
    {
        $filename = Application::i()->baseDir . '/inc/snippets/test.tmp.php';
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
     * Тест метода prepareDir()
     */
    public function testPrepareDir()
    {
        $dir = Application::i()->baseDir . '/inc/snippets';
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
