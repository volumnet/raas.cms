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
        $isFullRedirect = (
            stristr($this->url_to, ':') && !stristr($this->url_from, ':')
        );
        if ($this->rx) {
            $rx = $this->url_from;
            if ($isFullRedirect) {
                if (preg_match('/' . $this->url_from . '/umi', $url)) {
                    $url = $this->url_to;
                }
            } else {
                $newUrl = preg_replace('/' . $this->url_from . '/umi', $this->url_to, $url);
                $url = $newUrl ?: $url;
            }
        } else {
            if ($isFullRedirect) {
                if (stristr($url, $this->url_from)) {
                    $url = $this->url_to;
                }
            } else {
                $url = str_ireplace($this->url_from, $this->url_to, $url);
            }
        }
        $resolveInternalUrl = static::getInternalLink($url);
        return $resolveInternalUrl ?: $url;
    }

    /**
     * Обрабатывает внутреннюю ссылку
     * @param string $url Входной URL
     * @return string|null Реальный URL, либо null, если не найдено
     */
    public static function getInternalLink($url)
    {
        $url = str_ireplace('http://raas://', 'raas://', $url);
        $url = str_ireplace('https://raas://', 'raas://', $url);
        $url = str_ireplace('//raas://', 'raas://', $url);

        if (parse_url($url, PHP_URL_SCHEME) == 'raas') {
            $internalUrlArr = explode('/', trim(parse_url($url, PHP_URL_HOST) . parse_url($url, PHP_URL_PATH), '/'));
            switch ($internalUrlArr[0]) {
                case 'page':
                    $p = new Page((int)$internalUrlArr[1]);
                    return $p->url;
                    break;
                case 'material':
                    $m = new Material((int)$internalUrlArr[1]);
                    return $m->url;
                    break;
                case 'domain':
                    switch ($internalUrlArr[1]) {
                        case 'page':
                            $p = new Page((int)$internalUrlArr[2]);
                            return $p->domain . $p->url;
                            break;
                        case 'material':
                            $m = new Material((int)$internalUrlArr[2]);
                            return $m->urlParent->domain . $m->url;
                            break;
                    }
                    break;
            }
        }
        return null;
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
        $url = trim($url);
        $url = str_replace('\\', '/', $url);
        $temp = parse_url($url);
        if (preg_match('/[^\\/]$/i', $temp['path']) &&
            !stristr(basename($temp['path']), '.')
        ) {
            $url = str_replace($temp['path'], $temp['path'] . '/', $url);
        }
        $url = preg_replace_callback('/\\/.*\\//umis', function ($matches) {
            return mb_strtolower($matches[0]);
        }, $url);
        return $url;
    }
}
