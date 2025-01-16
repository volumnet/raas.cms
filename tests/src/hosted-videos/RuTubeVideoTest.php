<?php
/**
 * Тест класса RuTubeVideo
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Тест класса RuTubeVideo
 */
class RuTubeVideoTest extends BaseTest
{
    /**
     * Тест метода spawnByURL
     * @param string $url Входной URL
     * @param ?string $expected Ожидаемое значение
     * @param array $params Дополнительные параметры
     */
    #[TestWith([
        'https://rutube.ru/video/0f8778b4b61fa43667831b7301f33c4e/',
        '0f8778b4b61fa43667831b7301f33c4e',
        [],
    ])]
    #[TestWith([
        'https://rutube.ru/play/embed/0f8778b4b61fa43667831b7301f33c4e/?t=4',
        '0f8778b4b61fa43667831b7301f33c4e',
        ['time' => 4],
    ])]
    #[TestWith([
        'https://rutube.ru/embed/0f8778b4b61fa43667831b7301f33c4e/?t=4',
        '0f8778b4b61fa43667831b7301f33c4e',
        ['time' => 4],
    ])]
    #[TestWith([
        'aaa',
        null,
        [],
    ])]
    #[TestWith([
        'https://rutube.ru/play/embed/0f8778b4b61fa43667831b7301f33c4e/?t=4&skinColor=abcdef',
        '0f8778b4b61fa43667831b7301f33c4e',
        ['time' => 4, 'color' => '#abcdef'],
    ])]
    #[TestWith([
        'https://rutube.ru/video/private/privateVideoId/?p=some_key',
        'privateVideoId',
        ['key' => 'some_key'],
    ])]
    #[TestWith([
        'https://rutube.ru/shorts/3faa805779fca591ff881dc5f7988cfd/',
        '3faa805779fca591ff881dc5f7988cfd',
        ['shorts' => true],
    ])]
    public function testSpawnByURL(string $url, ?string $expected, array $params)
    {
        $result = RuTubeVideo::spawnByURL($url);
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
     * @param string $id ID видео
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    #[TestWith([
        '0f8778b4b61fa43667831b7301f33c4e',
        [],
        'https://rutube.ru/video/0f8778b4b61fa43667831b7301f33c4e/'
    ])]
    #[TestWith([
        '0f8778b4b61fa43667831b7301f33c4e',
        ['time' => 61],
        'https://rutube.ru/video/0f8778b4b61fa43667831b7301f33c4e/?t=61'
    ])]
    #[TestWith([
        '3faa805779fca591ff881dc5f7988cfd',
        ['shorts' => true],
        'https://rutube.ru/shorts/3faa805779fca591ff881dc5f7988cfd/'
    ])]
    public function testGetPageURL(string $id, array $options, string $expected)
    {
        $result = (new RuTubeVideo($id))->getPageURL($options);
        $this->assertEquals($expected, $result);
    }


    /**
     * Тест метода getIFrameURL
     * @dataProvider getIFrameURLDataProvider
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    #[TestWith([
        [],
        'https://rutube.ru/play/embed/0f8778b4b61fa43667831b7301f33c4e',
    ])]
    #[TestWith([
        ['time' => 61],
        'https://rutube.ru/play/embed/0f8778b4b61fa43667831b7301f33c4e?t=61',
    ])]
    #[TestWith([
        ['color' => '#fff'],
        'https://rutube.ru/play/embed/0f8778b4b61fa43667831b7301f33c4e?skinColor=ffffff',
    ])]
    #[TestWith([
        ['end' => 10],
        'https://rutube.ru/play/embed/0f8778b4b61fa43667831b7301f33c4e?stopTime=10',
    ])]
    #[TestWith([
        ['key' => 'aaa'],
        'https://rutube.ru/play/embed/0f8778b4b61fa43667831b7301f33c4e?p=aaa',
    ])]
    public function testGetIFrameURL(array $options, string $expected)
    {
        $result = (new RuTubeVideo('0f8778b4b61fa43667831b7301f33c4e'))->getIFrameURL($options);
        $this->assertEquals($expected, $result);
    }


    /**
     * Тест метода getCoverURL
     * @param string $url URL видео
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    #[TestWith([
        'https://rutube.ru/play/embed/0f8778b4b61fa43667831b7301f33c4e',
        [],
        'https://pic.rutube.ru/video/b5/8d/b58d46a63a4c0d106f999b628c4f950d.jpg'
    ])]
    public function testGetCoverURL(string $url, array $options, string $expected)
    {
        $result = RuTubeVideo::spawnByURL($url)->getCoverURL($options);
        $this->assertEquals($expected, $result);
    }
}
