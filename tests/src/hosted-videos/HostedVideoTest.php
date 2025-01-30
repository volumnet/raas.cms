<?php
/**
 * Тест класса HostedVideo
 */
namespace RAAS\CMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SOME\BaseTest;

/**
 * Тест класса HostedVideo
 */
#[CoversClass(HostedVideo::class)]
class HostedVideoTest extends BaseTest
{
    /**
     * Тест метода spawnByURL
     */
    public function testSpawnByURL()
    {
        $result = HostedVideo::spawnByURL('https://www.youtube.com/watch?v=1Oe3pfnJCAI&aaa=bbb');
        $this->assertInstanceOf(YouTubeVideo::class, $result);
        $this->assertEquals('https://www.youtube.com/watch?v=1Oe3pfnJCAI&aaa=bbb', $result->originalURL);
        $this->assertEquals($result->getPageURL(), $result->pageURL);
        $this->assertEquals($result->getIFrameURL(), $result->iframeURL);
        $this->assertEquals($result->getCoverURL(), $result->coverURL);
        $this->assertEquals('1Oe3pfnJCAI', $result->id);
    }

    /**
     * Тест метода spawnByURL - случай с недействительной ссылкой
     */
    public function testSpawnByURLWithInvalidURL()
    {
        $result = HostedVideo::spawnByURL('aaa');
        $this->assertNull($result);
    }
}
