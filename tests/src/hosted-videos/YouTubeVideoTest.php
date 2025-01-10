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
            [
                'https://www.youtube.com/watch?listType=list&list=testlist',
                '@testlist',
            ],
            [
                'https://www.youtube.com/watch?v=test&listType=list&list=testlist',
                'test@testlist',
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
     *     string ID видео,
     *     array Опции,
     *     string Ожидаемое значение
     * ]></code></pre>
     */
    public function getPageURLDataProvider(): array
    {
        return [
            ['1Oe3pfnJCAI', [], 'https://www.youtube.com/watch?v=1Oe3pfnJCAI'],
            ['1Oe3pfnJCAI', ['time' => 61], 'https://www.youtube.com/watch?v=1Oe3pfnJCAI&t=61s'],
            ['@testlist', [], 'https://www.youtube.com/watch?listType=list&list=testlist'],
            ['test@testlist', [], 'https://www.youtube.com/watch?v=test&listType=list&list=testlist'],
        ];
    }


    /**
     * Тест метода getPageURL
     * @dataProvider getPageURLDataProvider
     * @param string $id ID видео
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    public function testGetPageURL(string $id, array $options, string $expected)
    {
        $result = (new YouTubeVideo($id))->getPageURL($options);
        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testGetIFrameURL
     * @return array <pre><code>array<[
     *     string ID видео,
     *     array Опции,
     *     string Ожидаемое значение
     * ]></code></pre>
     */
    public function getIFrameURLDataProvider(): array
    {
        return [
            [
                '1Oe3pfnJCAI',
                [],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['time' => 61],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?start=61&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['controls' => false],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?controls=0&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['nocookies' => true],
                'https://www.youtube-nocookie.com/embed/1Oe3pfnJCAI?rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['autoplay' => true],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?autoplay=1&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['cc' => true],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?cc_load_policy=1&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['color' => '#fff'],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?color=white&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['keyboard' => false],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?disablekb=1&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['jsapi' => true],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?enablejsapi=1&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['end' => 10],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?end=10&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['fullscreen' => false],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?fs=0&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['lang' => 'en'],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?hl=en&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['iv_load_policy' => false],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?iv_load_policy=3&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['rel' => true],
                'https://www.youtube.com/embed/1Oe3pfnJCAI',
            ],
            [
                '1Oe3pfnJCAI',
                ['listType' => 'playlist', 'list' => 'PLaaa'],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?listType=playlist&list=PLaaa&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['loop' => true],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?loop=1&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['modestbranding' => true],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?modestbranding=1&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['playlistIds' => ['aaa']],
                'https://www.youtube.com/embed/?playlist=1Oe3pfnJCAI%2Caaa&rel=0',
            ],
            [
                '1Oe3pfnJCAI',
                ['playsinline' => true],
                'https://www.youtube.com/embed/1Oe3pfnJCAI?playsinline=1&rel=0',
            ],
            [
                '@testlist',
                [],
                'https://www.youtube.com/embed/?listType=list&list=testlist&rel=0'
            ],
            [
                'test@testlist',
                [],
                'https://www.youtube.com/embed/test?listType=list&list=testlist&rel=0'
            ],
        ];
    }


    /**
     * Тест метода getIFrameURL
     * @dataProvider getIFrameURLDataProvider
     * @param string $id ID видео
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    public function testGetIFrameURL(string $id, array $options, string $expected)
    {
        $result = (new YouTubeVideo($id))->getIFrameURL($options);
        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testGetCoverURL
     * @return array <pre><code>array<[
     *     string ID видео,
     *     array Опции,
     *     string Ожидаемое значение
     * ]></code></pre>
     */
    public function getCoverURLDataProvider(): array
    {
        return [
            ['1Oe3pfnJCAI', [], 'https://i.ytimg.com/vi/1Oe3pfnJCAI/hqdefault.jpg'],
            ['@testlist', [], ''],
            ['test@testlist', [], 'https://i.ytimg.com/vi/test/hqdefault.jpg'],
        ];
    }


    /**
     * Тест метода getCoverURL
     * @dataProvider getCoverURLDataProvider
     * @param string $id ID видео
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    public function testGetCoverURL(string $id, array $options, string $expected)
    {
        $result = (new YouTubeVideo($id))->getCoverURL($options);
        $this->assertEquals($expected, $result);
    }
}
