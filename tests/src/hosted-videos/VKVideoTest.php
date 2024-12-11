<?php
/**
 * Тест класса VKVideo
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Тест класса VKVideo
 */
class VKVideoTest extends BaseTest
{
    /**
     * Провайдер данных для метода testGetIdFromURL
     * @return array <pre><code>array<[string Входной URL, string|null Ожидаемое значение]></code></pre>
     */
    public function getIdFromURLDataProvider()
    {
        return [
            [
                'https://vkvideo.ru/video-45960892_456246794',
                '-45960892_456246794',
            ],
            [
                'https://vk.com/video-45960892_456246794',
                '-45960892_456246794',
            ],
            [
                'https://vk.ru/video-45960892_456246794',
                '-45960892_456246794',
            ],
            [
                'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=1&autoplay=1',
                '-45960892_456246794',
            ],
            [
                'https://vk.ru/video_ext.php?oid=-45960892&id=456246794&hd=1&autoplay=1',
                '-45960892_456246794',
            ],
            [
                'aaa',
                null,
            ],
        ];
    }


    /**
     * Тест метода getIdFromURL
     * @dataProvider getIdFromURLDataProvider
     * @param string $url Входной URL
     * @param string|null $expected Ожидаемое значение
     */
    public function testGetIdFromURL(string $url, $expected)
    {
        $result = VKVideo::getIdFromURL($url);
        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testGetPageURL
     * @return array <pre><code>array<[
     *     array Опции,
     *     string Ожидаемое значение
     * ]></code></pre>
     */
    public function getPageURLDataProvider(): array
    {
        return [
            [[], 'https://vkvideo.ru/video-45960892_456246794'],
        ];
    }


    /**
     * Тест метода getPageURL
     * @dataProvider getPageURLDataProvider
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    public function testGetPageURL(array $options, string $expected)
    {
        $result = VKVideo::spawnById('-45960892_456246794')->getPageURL($options);
        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testGetIFrameURL
     * @return array <pre><code>array<[
     *     array Опции,
     *     string Ожидаемое значение
     * ]></code></pre>
     */
    public function getIFrameURLDataProvider(): array
    {
        return [
            [
                [],
                'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=4',
            ],
            [
                ['time' => 61],
                'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=4&t=00h01m01s',
            ],
            [
                ['key' => 'aaa'],
                'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=4&hash=aaa',
            ],
            [
                ['autoplay' => true],
                'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=4&autoplay=1',
            ],
            [
                ['width' => 1024],
                'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=3',
            ],
            [
                ['height' => 640],
                'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=3',
            ],
            [
                ['jsapi' => true],
                'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=4&js_api=1',
            ],
            [
                ['loop' => true],
                'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=4&loop=1',
            ],
        ];
    }


    /**
     * Тест метода getIFrameURL
     * @dataProvider getIFrameURLDataProvider
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    public function testGetIFrameURL(array $options, string $expected)
    {
        $result = VKVideo::spawnById('-45960892_456246794')->getIFrameURL($options);
        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testGetCoverURL
     * @return array <pre><code>array<[
     *     array Опции,
     *     string Ожидаемое значение
     * ]></code></pre>
     */
    public function getCoverURLDataProvider(): array
    {
        return [
            [[], 'https://sun3-12.userapi.com/c837131/v837131892/71a7/oF-2Dqg2PTQ.jpg'],
        ];
    }


    /**
     * Тест метода getCoverURL
     * @dataProvider getCoverURLDataProvider
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    public function testGetCoverURL(array $options, string $expected)
    {
        $result = VKVideo::spawnById('-45960892_456246794')->getCoverURL($options);
        $this->assertEquals($expected, $result);
    }
}
