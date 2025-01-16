<?php
/**
 * Тест класса VKVideo
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Тест класса VKVideo
 */
class VKVideoTest extends BaseTest
{
    /**
     * Тест метода spawnByURL
     * @param string $url Входной URL
     * @param ?string $expected Ожидаемое значение
     * @param array $params Дополнительные параметры
     */
    #[TestWith([
        'https://vkvideo.ru/video-45960892_456246794',
        '-45960892_456246794',
        [],
    ])]
    #[TestWith([
        'https://vk.com/video-45960892_456246794',
        '-45960892_456246794',
        [],
    ])]
    #[TestWith([
        'https://vk.ru/video-45960892_456246794',
        '-45960892_456246794',
        [],
    ])]
    #[TestWith([
        'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=1&autoplay=1',
        '-45960892_456246794',
        ['autoplay' => true],
    ])]
    #[TestWith([
        'https://vk.ru/video_ext.php?oid=-45960892&id=456246794&hd=1&autoplay=1&t=12h34m56s',
        '-45960892_456246794',
        ['autoplay' => true, 'time' => 45296], // (12 * 3600) + (34 * 60) + 56 = 45296
    ])]
    #[TestWith([
        'aaa',
        null,
        [],
    ])]

    #[TestWith([
        'https://vk.ru/video_ext.php?oid=-45960892&id=456246794&hd=1&autoplay=1&hash=someKey',
        '-45960892_456246794',
        ['autoplay' => true, 'key' => 'someKey'],
    ])]
    public function testSpawnByURL(string $url, ?string $expected, array $params)
    {
        $result = VKVideo::spawnByURL($url);
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
    #[TestWith([[], 'https://vkvideo.ru/video-45960892_456246794'])]
    public function testGetPageURL(array $options, string $expected)
    {
        $result = (new VKVideo('-45960892_456246794'))->getPageURL($options);
        $this->assertEquals($expected, $result);
    }


    /**
     * Тест метода getIFrameURL
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    #[TestWith([
        [],
        'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=4',
    ])]
    #[TestWith([
        ['time' => 61],
        'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=4&t=00h01m01s',
    ])]
    #[TestWith([
        ['key' => 'aaa'],
        'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=4&hash=aaa',
    ])]
    #[TestWith([
        ['autoplay' => true],
        'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=4&autoplay=1',
    ])]
    #[TestWith([
        ['width' => 1024],
        'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=3',
    ])]
    #[TestWith([
        ['height' => 640],
        'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=3',
    ])]
    #[TestWith([
        ['jsapi' => true],
        'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=4&js_api=1',
    ])]
    #[TestWith([
        ['loop' => true],
        'https://vkvideo.ru/video_ext.php?oid=-45960892&id=456246794&hd=4&loop=1',
    ])]
    public function testGetIFrameURL(array $options, string $expected)
    {
        $result = (new VKVideo('-45960892_456246794'))->getIFrameURL($options);
        $this->assertEquals($expected, $result);
    }


    /**
     * Тест метода getCoverURL
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    #[TestWith([[], 'https://sun9-31.userapi.com/c837131/v837131892/71a7/oF-2Dqg2PTQ.jpg'])]
    public function testGetCoverURL(array $options, string $expected)
    {
        $result = (new VKVideo('-45960892_456246794'))->getCoverURL($options);
        $this->assertEquals($expected, $result);
    }
}
