<?php
/**
 * Тест класса RuTubeVideo
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Тест класса RuTubeVideo
 */
class RuTubeVideoTest extends BaseTest
{
    /**
     * Провайдер данных для метода testGetIdFromURL
     * @return array <pre><code>array<[string Входной URL, string|null Ожидаемое значение]></code></pre>
     */
    public function getIdFromURLDataProvider()
    {
        return [
            [
                'https://rutube.ru/video/0f8778b4b61fa43667831b7301f33c4e/',
                '0f8778b4b61fa43667831b7301f33c4e',
            ],
            [
                'https://rutube.ru/play/embed/0f8778b4b61fa43667831b7301f33c4e/?t=4',
                '0f8778b4b61fa43667831b7301f33c4e',
            ],
            [
                'https://rutube.ru/embed/0f8778b4b61fa43667831b7301f33c4e/?t=4',
                '0f8778b4b61fa43667831b7301f33c4e',
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
        $result = RuTubeVideo::getIdFromURL($url);
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
            [[], 'https://rutube.ru/video/0f8778b4b61fa43667831b7301f33c4e/'],
            [['time' => 61], 'https://rutube.ru/video/0f8778b4b61fa43667831b7301f33c4e/?t=61'],
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
        $result = (new RuTubeVideo('0f8778b4b61fa43667831b7301f33c4e'))->getPageURL($options);
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
                'https://rutube.ru/play/embed/0f8778b4b61fa43667831b7301f33c4e',
            ],
            [
                ['time' => 61],
                'https://rutube.ru/play/embed/0f8778b4b61fa43667831b7301f33c4e?t=61',
            ],
            [
                ['color' => '#fff'],
                'https://rutube.ru/play/embed/0f8778b4b61fa43667831b7301f33c4e?skinColor=ffffff',
            ],
            [
                ['end' => 10],
                'https://rutube.ru/play/embed/0f8778b4b61fa43667831b7301f33c4e?stopTime=10',
            ],
            [
                ['key' => 'aaa'],
                'https://rutube.ru/play/embed/0f8778b4b61fa43667831b7301f33c4e?p=aaa',
            ]
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
        $result = (new RuTubeVideo('0f8778b4b61fa43667831b7301f33c4e'))->getIFrameURL($options);
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
            [[], 'https://pic.rutubelist.ru/video/b5/8d/b58d46a63a4c0d106f999b628c4f950d.jpg'],
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
        $result = (new RuTubeVideo('0f8778b4b61fa43667831b7301f33c4e'))->getCoverURL($options);
        $this->assertEquals($expected, $result);
    }
}
