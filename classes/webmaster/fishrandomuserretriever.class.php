<?php
/**
 * Генератор пользователей из RANDOMUSER.ME
 */
namespace RAAS\CMS;

/**
 * Класс генератора пользователей из RANDOMUSER.ME
 */
class FishRandomUserRetriever
{
    /**
     * URL для получения пользователей
     * @var string
     */
    public static $url = 'https://randomuser.me/api/';

    /**
     * Получает пользователя
     * @return array
     */
    public function retrieve()
    {
        $text = file_get_contents(self::$url);
        $json = json_decode($text, true);
        $json = $json['results'][0];
        $pic = $json['picture']['large'];
        $text = file_get_contents($pic);
        $tempname = tempnam(sys_get_temp_dir(), 'RAAS');
        @file_put_contents($tempname, $text);
        $json['pic'] = ['name' => basename($pic), 'filepath' => $tempname];
        return $json;
    }
}
