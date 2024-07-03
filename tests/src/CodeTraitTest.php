<?php
/**
 * Тест трейта CodeTrait
 */
namespace RAAS\CMS;

use SOME\BaseTest;
use SOME\File;
use RAAS\Application;

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
     * Провайдер данных для метода testGetName
     * @return array <pre><code>array<[
     *     string Код,
     *     string Установленный URN,
     *     string Ожидаемое значение,
     * ]></code></pre>
     */
    public function getNameDataProvider(): array
    {
        return [
            [
                (
                    '<' . "?php\n" .
                    "/**\n" .
                    " * Тест\n" .
                    " */\n"
                ),
                '',
                'Тест',
            ],
            [
                (
                    '<' . "?php\n" .
                    "/**\n" .
                    " */\n"
                ),
                'test',
                'test',
            ],
            [
                (
                    '<' . "?php\n" .
                    "/**\n" .
                    " * Тест\n" .
                    " * @param array<[string]>\n" . // Некорректный тег PHPDoc для проверки исключения
                    " */\n"
                ),
                'test1',
                'test1',
            ],
            [
                (
                    '<' . "?php\n" .
                    "return 'aaa';\n"
                ),
                'test',
                'test',
            ],
            [
                (
                    '<' . "?php\n" .
                    "return 'aaa';\n"
                ),
                '',
                '',
            ],
        ];
    }


    /**
     * Тест получения свойства name
     * @param string $code Код
     * @param string $urn Установленный URN
     * @param string $expected Ожидаемое значение
     * @dataProvider getNameDataProvider
     */
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
