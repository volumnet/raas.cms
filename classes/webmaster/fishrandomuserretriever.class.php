<?php
namespace RAAS\CMS;

use \SOME\Text;

class FishRandomUserRetriever
{
    public static $url = 'http://randomuser.ru/api.json';

    public function retrieve()
    {
        $text = file_get_contents(self::$url);
        $json = json_decode($text, true);
        $json = $json[0]['user'];
        $pic = $json['picture']['large'];
        $text = file_get_contents($pic);
        $tempname = tempnam(sys_get_temp_dir(), 'RAAS');
        @file_put_contents($tempname, $text);
        $json['pic'] = array('name' => basename($pic), 'filepath' => $tempname);
        return $json;
    }
}
