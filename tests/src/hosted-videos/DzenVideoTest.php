<?php
/**
 * Тест класса DzenVideo
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Тест класса DzenVideo
 */
class DzenVideoTest extends BaseTest
{
    /**
     * Провайдер данных для метода testGetIdFromURL
     * @return array <pre><code>array<[string Входной URL, string|null Ожидаемое значение]></code></pre>
     */
    public function getIdFromURLDataProvider()
    {
        return [
            [
                'https://dzen.ru/video/watch/6373fd921c149b3a052105c4?sid=847798295197551089',
                '6373fd921c149b3a052105c4',
            ],
            [
                'https://dzen.ru/embed/vMDEtpvOH9kw?from_block=partner&from=zen&mute=0&autoplay=0&tv=0',
                '6373fd921c149b3a052105c4',
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
        $result = DzenVideo::getIdFromURL($url);
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
            [[], 'https://dzen.ru/video/watch/6373fd921c149b3a052105c4'],
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
        $result = (new DzenVideo('6373fd921c149b3a052105c4'))->getPageURL($options);
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
                'https://dzen.ru/video/watch/6373fd921c149b3a052105c4?sid=847798295197551089',
                [],
                'https://dzen.ru/embed/vMDEtpvOH9kw',
            ],
            [
                'https://dzen.ru/embed/vMDEtpvOH9kw?from_block=partner&from=zen&mute=0&autoplay=0&tv=0',
                ['controls' => false],
                'https://dzen.ru/embed/vMDEtpvOH9kw?tv=1',
            ],
            [
                'https://dzen.ru/video/watch/6373fd921c149b3a052105c4?sid=847798295197551089',
                ['mute' => true],
                'https://dzen.ru/embed/vMDEtpvOH9kw?mute=1',
            ],
            [
                'https://dzen.ru/embed/vMDEtpvOH9kw?from_block=partner&from=zen&mute=0&autoplay=0&tv=0',
                ['autoplay' => true],
                'https://dzen.ru/embed/vMDEtpvOH9kw?autoplay=1',
            ],
            [
                'https://dzen.ru/video/watch/66c7003f69487d5e44dbb10d',
                ['autoplay' => true],
                'https://dzen.ru/embed/v2nmQZV7eXGA?autoplay=1',
            ],
        ];
    }


    /**
     * Тест метода getIFrameURL
     * @dataProvider getIFrameURLDataProvider
     * @param string $url URL видео
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    public function testGetIFrameURL(string $url, array $options, string $expected)
    {
        $video = DzenVideo::spawnByURL($url);
        $result = $video->getIFrameURL($options);
        $this->assertEquals($expected, $result);
    }


    /**
     * Провайдер данных для метода testGetCoverURL
     * @return array <pre><code>array<[
     *     string URL видео
     *     array Опции,
     *     string Ожидаемое значение
     * ]></code></pre>
     */
    public function getCoverURLDataProvider(): array
    {

        return [
            [
                'https://dzen.ru/video/watch/6373fd921c149b3a052105c4?sid=847798295197551089',
                [],
                'https://avatars.dzeninfra.ru/get-zen-vh/6067314/2a000001847d193706eca812d7e5ce995718/orig',
            ],
            [
                'https://dzen.ru/embed/vMDEtpvOH9kw?from_block=partner&from=zen&mute=0&autoplay=0&tv=0',
                [],
                'https://avatars.dzeninfra.ru/get-zen-vh/6067314/2a000001847d193706eca812d7e5ce995718/orig',
            ],
            [
                'https://dzen.ru/video/watch/66e881f5eb4e2070434f5911?sid=847798295197551089',
                [],
                'https://avatars.dzeninfra.ru/get-zen-vh/271828/2a009f75d2ead2c56a936a9e82a4e7d2273d/orig',
            ]
        ];
    }


    /**
     * Тест метода getCoverURL
     * @dataProvider getCoverURLDataProvider
     * @param string $url URL видео
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    public function testGetCoverURL(string $url, array $options, string $expected)
    {
        $result = DzenVideo::spawnByURL($url)->getCoverURL($options);
        $this->assertEquals($expected, $result);
    }
}
