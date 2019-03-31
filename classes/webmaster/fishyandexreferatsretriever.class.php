<?php
namespace RAAS\CMS;

use phpQuery;
use \SOME\Text;

class FishYandexReferatsRetriever
{
    public static $url = 'https://yandex.ru/referats/?t=astronomy+geology+gyroscope+literature+marketing+mathematics+music+polit+agrobiologia+law+psychology+geography+physics+philosophy+chemistry+estetica';

    public function retrieve()
    {
        $text = file_get_contents(self::$url);
        $pq = phpQuery::newDocument($text);
        $pq = pq('.referats__text', $pq);
        $headerEl = pq('strong', $pq);
        $divEl = pq('div', $pq);
        $pEl = pq('p:eq(0)', $pq);
        $name = $headerEl->text();
        $name = preg_replace('/«(.*?)»/umi', '$1', $name);
        $name = str_replace('Тема: ', '', $name);
        $name = trim($name);
        $headerEl->remove();
        $divEl->remove();
        $text = trim($pq->html());
        $brief = Text::cuttext(trim($pEl->text()), 256, '...');
        $arr = array('name' => $name, 'text' => $text, 'brief' => $brief);
        return $arr;
    }
}
