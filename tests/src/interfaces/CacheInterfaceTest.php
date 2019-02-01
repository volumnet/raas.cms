<?php
/**
 * Файл теста стандартного интерфейса кэширования
 */
namespace RAAS\CMS;

/**
 * Класс теста стандартного интерфейса кэширования
 */
class CacheInterfaceTest extends BaseDBTest
{
    /**
     * Тест получения кода для кэширования произвольных данных
     */
    public function testGetDataCacheCode()
    {
        $block = new Block_Menu();
        $page = new Page();
        $testData = ['aaa' => 'bbb', 'ccc' => 'ddd', 'eee' => 'fff'];
        $interface = new CacheInterface($block, $page);

        $result = $interface->getDataCacheCode($testData);

        $resultData = eval('?' . '>' . $result);

        $this->assertEquals($testData, $resultData);
    }

    /**
     * Тест получения кода для кэширования HTML-данных
     */
    public function testGetHtmlCacheCode()
    {
        $block = new Block_HTML();
        $page = new Page();
        $testData = '<' . '?xml version="1.0" encoding="UTF-8"?>' . "\n"
                  . '<testdata>aaa</testdata>';
        $interface = new CacheInterface($block, $page);

        $result = $interface->getHtmlCacheCode($testData);

        $this->assertEquals(
            '<' . '?php echo \'<\' . \'?xml version="1.0" encoding="UTF-8"?\' . ">\\n"?' . ">\n" .
            '<testdata>aaa</testdata>',
            $result
        );
    }


    /**
     * Тест обработки интерфейса - случай с кэшированием данных
     */
    public function testProcessWithData()
    {
        $block = Block::spawn(15);
        $block->cache_type = Block::CACHE_DATA;
        $filename = $block->getCacheFile();
        $page = new Page();
        $testData = ['aaa' => 'bbb', 'ccc' => 'ddd', 'eee' => 'fff'];
        $interface = new CacheInterface($block, $page, [], [], [], [], [], [], $testData);

        @unlink($filename);
        $result = $interface->process();

        $this->assertEquals($testData, $result);

        $this->assertFileExists($filename);
        $cachedResult = include $filename;

        $this->assertEquals($testData, $cachedResult);

        unlink($filename);
    }


    /**
     * Тест обработки интерфейса - случай с кэшированием HTML
     */
    public function testProcessWithHtml()
    {
        $block = new Block_HTML(1);
        $block->cache_type = Block::CACHE_HTML;
        $filename = $block->getCacheFile();
        $page = new Page();
        $testData = ['aaa' => 'bbb', 'ccc' => 'ddd', 'eee' => 'fff'];
        $text = '<' . '?xml version="1.0" encoding="UTF-8"?>' . "\n"
              . '<testdata>aaa</testdata>';
        $interface = new CacheInterface($block, $page, [], [], [], [], [], [], $testData);

        @unlink($filename);
        ob_start();
        echo $text;
        $result = $interface->process();
        ob_end_clean();

        $this->assertEquals($testData, $result);

        $this->assertFileExists($filename);
        $cachedResult = file_get_contents($filename);

        $this->assertEquals(
            '<' . '?php echo \'<\' . \'?xml version="1.0" encoding="UTF-8"?\' . ">\\n"?' . ">\n" .
            '<testdata>aaa</testdata>',
            $cachedResult
        );

        unlink($filename);
    }


    /**
     * Тест обработки интерфейса - случай неопознанного типа кэширования
     */
    public function testProcessWithUndefined()
    {
        $block = Block::spawn(15);
        $block->cache_type = 9999; // заведомо несуществующий тип кэширования
        $filename = $block->getCacheFile();
        $page = new Page();
        $testData = ['aaa' => 'bbb', 'ccc' => 'ddd', 'eee' => 'fff'];
        $interface = new CacheInterface($block, $page, [], [], [], [], [], [], $testData);

        @unlink($filename);
        $result = $interface->process();

        $this->assertEquals($testData, $result);

        $this->assertFileNotExists($filename);
    }
}
