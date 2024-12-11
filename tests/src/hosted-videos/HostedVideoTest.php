<?php
/**
 * Тест класса HostedVideo
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Тест класса HostedVideo
 */
class HostedVideoTest extends BaseTest
{
    /**
     * Тест метода spawnByURL
     */
    public function testSpawnByURL()
    {
        $result = YouTubeVideo::spawnByURL('https://www.youtube.com/watch?v=1Oe3pfnJCAI');
        $this->assertInstanceOf(YouTubeVideo::class, $result);
        $this->assertEquals('1Oe3pfnJCAI', $result->id);
    }


    /**
     * Тест метода spawnById
     */
    public function testSpawnById()
    {
        $result = YouTubeVideo::spawnById('1Oe3pfnJCAI');
        $this->assertInstanceOf(YouTubeVideo::class, $result);
        $this->assertEquals('1Oe3pfnJCAI', $result->id);
    }

    /**
     * Тест метода spawnById - случай корневого класса
     */
    public function testSpawnByIdWithRootClass()
    {
        $result = HostedVideo::spawnById('1Oe3pfnJCAI');
        $this->assertNull($result);
    }

    /**
     * Тест метода spawnByURL - случай с недействительной ссылкой
     */
    public function testSpawnByURLWithInvalidURL()
    {
        $result = YouTubeVideo::spawnByURL('aaa');
        $this->assertNull($result);
    }
}
