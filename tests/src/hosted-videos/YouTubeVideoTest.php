<?php
/**
 * Тест класса YouTubeVideo
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Тест класса YouTubeVideo
 */
class YouTubeVideoTest extends BaseTest
{
    /**
     * Тест метода spawnByURL
     * @param string $url Входной URL
     * @param ?string $expected Ожидаемое значение
     * @param array $params Дополнительные параметры
     */
    #[TestWith([
        'https://www.youtube.com/watch?v=1Oe3pfnJCAI&pp=ygUZ0YLQtdGB0YIg0LzQvtC90LjRgtC-0YDQsA%3D%3D',
        '1Oe3pfnJCAI',
        [],
    ])]
    #[TestWith([
        'https://youtube.ru/watch?v=1Oe3pfnJCAI&pp=ygUZ0YLQtdGB0YIg0LzQvtC90LjRgtC-0YDQsA%3D%3D',
        '1Oe3pfnJCAI',
        [],
    ])]
    #[TestWith([
        'https://youtube-nocookie.ru/watch?v=1Oe3pfnJCAI&pp=ygUZ0YLQtdGB0YIg0LzQvtC90LjRgtC-0YDQsA%3D%3D',
        '1Oe3pfnJCAI',
        ['nocookies' => true],
    ])]
    #[TestWith([
        'https://aaa.bbb/watch?v=1Oe3pfnJCAI&pp=ygUZ0YLQtdGB0YIg0LzQvtC90LjRgtC-0YDQsA%3D%3D',
        null,
        [],
    ])]
    #[TestWith([
        'https://youtu.be/1Oe3pfnJCAI?si=RjTHkKrhrRWG75yL',
        '1Oe3pfnJCAI',
        [],
    ])]
    #[TestWith([
        'https://www.youtube.com/embed/1Oe3pfnJCAI?si=RjTHkKrhrRWG75yL&amp;start=61',
        '1Oe3pfnJCAI',
        ['time' => 61],
    ])]
    #[TestWith([
        'https://www.youtube.com/watch?listType=list&list=testlist',
        '',
        ['listType' => 'list', 'list' => 'testlist'],
    ])]
    #[TestWith([
        'https://www.youtube.com/watch?v=test&listType=list&list=testlist',
        'test',
        ['listType' => 'list', 'list' => 'testlist'],
    ])]
    #[TestWith([
        'https://www.youtube.com/watch?v=test&loop=1&disablekb=1&playlist=aaa,bbb&color=white&iv_load_policy=3',
        'test',
        ['loop' => true, 'keyboard' => false, 'playlistIds' => ['aaa', 'bbb'], 'color' => '#ffffff', 'iv_load_policy' => false],
    ])]
    public function testSpawnByURL(string $url, ?string $expected, array $params)
    {
        $result = YouTubeVideo::spawnByURL($url);
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
    #[TestWith(['1Oe3pfnJCAI', [], 'https://www.youtube.com/watch?v=1Oe3pfnJCAI'])]
    #[TestWith(['1Oe3pfnJCAI', ['time' => 61], 'https://www.youtube.com/watch?v=1Oe3pfnJCAI&t=61s'])]
    #[TestWith(['', ['list' => 'testlist'], 'https://www.youtube.com/watch?listType=list&list=testlist'])]
    #[TestWith(['test', ['list' => 'testlist'], 'https://www.youtube.com/watch?v=test&listType=list&list=testlist'])]
    public function testGetPageURL(string $id, array $options, string $expected)
    {
        $result = (new YouTubeVideo($id))->getPageURL($options);
        $this->assertEquals($expected, $result);
    }


    /**
     * Тест метода getIFrameURL
     * @param string $id ID видео
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    #[TestWith([
        '1Oe3pfnJCAI',
        [],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?rel=0',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['time' => 61],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?start=61&rel=0',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['controls' => false],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?controls=0&rel=0',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['nocookies' => true],
        'https://www.youtube-nocookie.com/embed/1Oe3pfnJCAI?rel=0',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['autoplay' => true],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?autoplay=1&rel=0',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['cc' => true],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?cc_load_policy=1&rel=0',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['color' => '#fff'],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?rel=0&color=white',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['keyboard' => false],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?disablekb=1&rel=0',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['jsapi' => true],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?enablejsapi=1&rel=0',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['end' => 10],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?end=10&rel=0',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['fullscreen' => false],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?fs=0&rel=0',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['lang' => 'en'],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?hl=en&rel=0',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['iv_load_policy' => false],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?rel=0&iv_load_policy=3',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['rel' => true],
        'https://www.youtube.com/embed/1Oe3pfnJCAI',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['listType' => 'playlist', 'list' => 'PLaaa'],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?list=PLaaa&rel=0&listType=playlist',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['loop' => true],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?loop=1&rel=0',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['modestbranding' => true],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?modestbranding=1&rel=0',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['playlistIds' => ['aaa']],
        'https://www.youtube.com/embed/?playlist=1Oe3pfnJCAI%2Caaa&rel=0',
    ])]
    #[TestWith([
        '1Oe3pfnJCAI',
        ['playsinline' => true],
        'https://www.youtube.com/embed/1Oe3pfnJCAI?playsinline=1&rel=0',
    ])]
    #[TestWith([
        '',
        ['list' => 'testlist'],
        'https://www.youtube.com/embed/?list=testlist&rel=0&listType=list'
    ])]
    #[TestWith([
        'test',
        ['list' => 'testlist'],
        'https://www.youtube.com/embed/test?list=testlist&rel=0&listType=list'
    ])]
    public function testGetIFrameURL(string $id, array $options, string $expected)
    {
        $result = (new YouTubeVideo($id))->getIFrameURL($options);
        $this->assertEquals($expected, $result);
    }


    /**
     * Тест метода getCoverURL
     * @param string $id ID видео
     * @param array $options Опции
     * @param string $expected Ожидаемое значение
     */
    #[TestWith(['1Oe3pfnJCAI', [], 'https://i.ytimg.com/vi/1Oe3pfnJCAI/hqdefault.jpg'])]
    #[TestWith(['', [], ''])]
    #[TestWith(['test', [], 'https://i.ytimg.com/vi/test/hqdefault.jpg'])]
    public function testGetCoverURL(string $id, array $options, string $expected)
    {
        $result = (new YouTubeVideo($id))->getCoverURL($options);
        $this->assertEquals($expected, $result);
    }
}
