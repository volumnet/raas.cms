<?php
namespace RAAS\CMS;

class FishPhotosRetreiver
{
    public function retreive($number)
    {
        $file = Package::i()->resourcesDir . '/photos.txt';
        $photosURLs = file($file);
        $photosURLs = array_map('trim', $photosURLs);
        $photosURLs = array_filter($photosURLs, 'trim');
        $photosURLs = array_values($photosURLs);
        shuffle($photosURLs);
        $retreived = array();
        for ($i = 0; ($i < count($photosURLs)) && (count($retreived) < $number); $i++) {
            $url = $photosURLs[$i];
            $text = file_get_contents($url);
            if ($text) {
                $tempname = tempnam(sys_get_temp_dir(), 'RAAS');
                @file_put_contents($tempname, $text);
                if (getimagesize($tempname)) {
                    $retreived[$tempname] = basename($url);
                }
            }
        }
        return $retreived;
    }
}