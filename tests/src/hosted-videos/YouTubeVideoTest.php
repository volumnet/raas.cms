<?php
/**
 * Тест класса YouTubeVideo
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Тест класса YouTubeVideo
 */
class YouTubeVideoTest extends BaseTest
{
    /**
     * Провайдер данных для метода testGetIdFromURL
     * @return array <pre><code>array<[string Входной URL, string|null Ожидаемое значение]></code></pre>
     */
    public function getIdFromURLDataProvider()
    {
        return [
            [
                'https://www.youtube.com/watch?v=1Oe3pfnJCAI&pp=ygUZ0YLQtdGB0YIg0LzQvtC90LjRgtC-0YDQsA%3D%3D',
                '1Oe3pfnJCAI',
            ],
            [
                'https://youtube.ru/watch?v=1Oe3pfnJCAI&pp=ygUZ0YLQtdGB0YIg0LzQvtC90LjRgtC-0YDQsA%3D%3D',
                '1Oe3pfnJCAI',
            ],
            [
                'https://youtube-nocookie.ru/watch?v=1Oe3pfnJCAI&pp=ygUZ0YLQtdGB0YIg0LzQvtC90LjRgtC-0YDQsA%3D%3D',
                '1Oe3pfnJCAI',
            ],
            [
                'https://aaa.bbb/watch?v=1Oe3pfnJCAI&pp=ygUZ0YLQtdGB0YIg0LzQvtC90LjRgtC-0YDQsA%3D%3D',
                null,
            ],
            [
                'https://youtu.be/1Oe3pfnJCAI?si=RjTHkKrhrRWG75yL',
                '1Oe3pfnJCAI',
            ],
            [
                'https://www.youtube.com/embed/1Oe3pfnJCAI?si=RjTHkKrhrRWG75yL&amp;start=61',
                '1Oe3pfnJCAI',
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
        $result = YouTubeVideo::getIdFromURL($url);
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
            [[], 'https://www.youtube.com/watch?v=1Oe3pfnJCAI'],
            [['time' => 61], 'https://www.youtube.com/watch?v=1Oe3pfnJCAI&t=61s'],
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
        $result = YouTubeVideo::spawnById('1Oe3pfnJCAI')->getPageURL($options);
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
                'https://www.youtube.com/embed/1Oe3pfnJCAI?rel=0',
            ],
            [
                ['time' => 61],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?start=61&rel=0',
            ],
            [
                ['controls' => false],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?controls=0&rel=0',
            ],
            [
                ['nocookies' => true],
                'https://www.youtube-nocookie.com/embed/1Oe3pfnJCAI?rel=0',
            ],
            [
                ['autoplay' => true],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?autoplay=1&rel=0',
            ],
            [
                ['cc' => true],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?cc_load_policy=1&rel=0',
            ],
            [
                ['color' => '#fff'],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?color=white&rel=0',
            ],
            [
                ['keyboard' => false],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?disablekb=1&rel=0',
            ],
            [
                ['jsapi' => true],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?enablejsapi=1&rel=0',
            ],
            [
                ['end' => 10],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?end=10&rel=0',
            ],
            [
                ['fullscreen' => false],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?fs=0&rel=0',
            ],
            [
                ['lang' => 'en'],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?hl=en&rel=0',
            ],
            [
                ['iv_load_policy' => false],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?iv_load_policy=3&rel=0',
            ],
            [
                ['rel' => true],
                'https://www.youtube.com/embed/1Oe3pfnJCAI',
            ],
            [
                ['listType' => 'playlist', 'list' => 'PLaaa'],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?listType=playlist&list=PLaaa&rel=0',
            ],
            [
                ['loop' => true],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?loop=1&rel=0',
            ],
            [
                ['modestbranding' => true],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?modestbranding=1&rel=0',
            ],
            [
                ['playlistIds' => ['aaa']],
                'https://www.youtube.com/embed/?playlist=1Oe3pfnJCAI%2Caaa&rel=0',
            ],
            [
                ['playsinline' => true],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?playsinline=1&rel=0',
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
        $result = YouTubeVideo::spawnById('1Oe3pfnJCAI')->getIFrameURL($options);
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
            [[], 'https://i.ytimg.com/vi/1Oe3pfnJCAI/hqdefault.jpg'],
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
        $result = YouTubeVideo::spawnById('1Oe3pfnJCAI')->getCoverURL($options);
        $this->assertEquals($expected, $result);
    }
}
