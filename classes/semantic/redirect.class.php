<?php
/**
 * Редирект
 */
namespace RAAS\CMS;

use SOME\SOME;

/**
 * Класс редиректа
 */
class Redirect extends SOME
{
    protected static $tablename = 'cms_redirects';

    protected static $defaultOrderBy = "priority";

    protected static $aiPriority = true;

    /**
     * Отрабатывает редирект
     * @param string $url Входной URL
     * @return string Выходной URL
     */
    public function process($url)
    {
        if ($this->rx) {
            $url = preg_replace('/' . $this->url_from . '/umi', $this->url_to, $url);
        } else {
            $url = str_ireplace($this->url_from, $this->url_to, $url);
        }
        return $url;
    }


    /**
     * Отрабатывает все редиректы
     * @param string $url Входной URL
     * @return string Выходной URL
     */
    public static function processAll($url)
    {
        $redirects = static::getSet();
        foreach ($redirects as $redirect) {
            $url = $redirect->process($url);
        }
        $url = static::checkStdRedirects($url);
        return $url;
    }


    /**
     * Обрабатывает стандартные редиректы
     * @param string $url Входной URL
     * @return string Выходной URL
     */
    public static function checkStdRedirects($url)
    {
        $url = mb_strtolower($url);
        $url = trim($url);
        $url = str_replace('\\', '/', $url);
        $temp = parse_url($url);
        if (preg_match('/[^\\/]$/i', $temp['path']) &&
            !stristr(basename($temp['path']), '.')
        ) {
            $url = str_replace($temp['path'], $temp['path'] . '/', $url);
        }
        return $url;
    }
}
