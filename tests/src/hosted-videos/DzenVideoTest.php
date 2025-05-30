<?php
/**
 * Тест класса DzenVideo
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Тест класса DzenVideo
 */
#[CoversClass(DzenVideo::class)]
class DzenVideoTest extends BaseTest
{
    /**
     * Тест метода spawnByURL
     * @param string $url Входной URL
     * @param ?string $expected Ожидаемое значение
     * @param array $params Дополнительные параметры
     */
    #[TestWith([
        'https://dzen.ru/video/watch/6373fd921c149b3a052105c4?sid=847798295197551089',
        '6373fd921c149b3a052105c4',
        [],
    ])]
    #[TestWith([
        'https://dzen.ru/embed/vMDEtpvOH9kw?from_block=partner&from=zen&mute=0&autoplay=0&tv=0',
        '6373fd921c149b3a052105c4',
        ['autoplay' => false, 'mute' => false, 'controls' => true],
    ])]
    #[TestWith([
        'aaa',
        null,
        [],
    ])]
    public function testSpawnByURL(string $url, ?string $expected, array $params)
    {
        $result = DzenVideo::spawnByURL($url);
        if ($expected !== null) {
            $this->assertEquals($expected, $result->id);
            $this->assertEquals(html_entity_decode($url), $result->originalURL);
            $this->assertEquals($params, $result->params);
        } else {
            $this->assertNull($result);
        }
    }


    /**
     * Тест метода getPageURL
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    #[TestWith([[], 'https://dzen.ru/video/watch/6373fd921c149b3a052105c4'])]
    public function testGetPageURL(array $options, string $expected)
    {
        $result = (new DzenVideo('6373fd921c149b3a052105c4'))->getPageURL($options);
        $this->assertEquals($expected, $result);
    }


    /**
     * Тест метода getIFrameURL
     * @param string $url URL видео
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    #[TestWith([
        'https://dzen.ru/video/watch/6373fd921c149b3a052105c4?sid=847798295197551089',
        [],
        'https://dzen.ru/embed/vMDEtpvOH9kw',
    ])]
    #[TestWith([
        'https://dzen.ru/embed/vMDEtpvOH9kw?from_block=partner&from=zen&mute=0&autoplay=0&tv=0',
        ['controls' => false],
        'https://dzen.ru/embed/vMDEtpvOH9kw?tv=1',
    ])]
    #[TestWith([
        'https://dzen.ru/video/watch/6373fd921c149b3a052105c4?sid=847798295197551089',
        ['mute' => true],
        'https://dzen.ru/embed/vMDEtpvOH9kw?mute=1',
    ])]
    #[TestWith([
        'https://dzen.ru/embed/vMDEtpvOH9kw?from_block=partner&from=zen&mute=0&autoplay=0&tv=0',
        ['autoplay' => true],
        'https://dzen.ru/embed/vMDEtpvOH9kw?autoplay=1',
    ])]
    #[TestWith([
        'https://dzen.ru/video/watch/66c7003f69487d5e44dbb10d',
        ['autoplay' => true],
        'https://dzen.ru/embed/v2nmQZV7eXGA?autoplay=1',
    ])]
    public function testGetIFrameURL(string $url, array $options, string $expected)
    {
        $video = DzenVideo::spawnByURL($url);
        $result = $video->getIFrameURL($options);
        $this->assertEquals($expected, $result);
    }


    /**
     * Тест метода getCoverURL
     * @param string $url URL видео
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    #[TestWith([
        'https://dzen.ru/video/watch/6373fd921c149b3a052105c4?sid=847798295197551089',
        [],
        'https://', // Адреса у них меняются постоянно
    ])]
    #[TestWith([
        'https://dzen.ru/embed/vMDEtpvOH9kw?from_block=partner&from=zen&mute=0&autoplay=0&tv=0',
        [],
        'https://', // Адреса у них меняются постоянно
    ])]
    #[TestWith([
        'https://dzen.ru/video/watch/66e881f5eb4e2070434f5911?sid=847798295197551089',
        [],
        'https://', // Адреса у них меняются постоянно
    ])]
    public function testGetCoverURL(string $url, array $options, string $expected)
    {
        $video = DzenVideo::spawnByURL($url);
        $result = $video->getCoverURL($options);
        $this->assertStringContainsString($expected, $result); // 2026-02-19, AVS: заменил на домен, т.к. адреса меняются периодически
    }
}
