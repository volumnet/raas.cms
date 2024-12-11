<?php
/**
 * Видео на Дзен
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Видео на Дзен
 */
class DzenVideo extends HostedVideo
{
    /**
     * ID для встраивания по ID видео
     * @var array <pre><code>array<string[] ID видео => string ID видео для встраивания></code></pre>
     */
    protected static $iframeIds = [];

    /**
     * URL обложки по ID видео
     * @var array <pre><code>array<string[] ID видео => string URL обложки></code></pre>
     */
    protected static $idsToCoverURLs = [];

    public function getPageURL(array $options = []): string
    {
        $result = 'https://dzen.ru/video/watch/' . $this->id;
        return $result;
    }


    public function getIFrameURL(array $options = []): string
    {
        $result = '';
        if (!(static::$iframeIds[$this->id] ?? null)) {
            $this->parsePage();
        }
        if (static::$iframeIds[$this->id] ?? null) {
            $result = static::getIFrameByIFrameId(static::$iframeIds[$this->id]);
            $urlParams = [];
            if (isset($options['controls']) && ($options['controls'] !== null) && !$options['controls']) {
                $urlParams['tv'] = 1;
            }
            if ($options['autoplay'] ?? false) {
                $urlParams['autoplay'] = 1;
            }
            if ($options['mute'] ?? false) {
                $urlParams['mute'] = 1;
            }
            if ($urlParams) {
                $result .= '?' . http_build_query($urlParams);
            }
        }
        return $result;
    }


    public function getCoverURL(array $options = []): string
    {
        $result = '';
        if (!(static::$idsToCoverURLs[$this->id] ?? null)) {
            $this->parsePage();
        }
        return static::$idsToCoverURLs[$this->id] ?? '';
    }


    public static function getIdFromURL(string $url)
    {
        $urlArr = parse_url($url);
        $host = str_replace('www.', '', $urlArr['host'] ?? '');
        $pathArr = explode('/', trim($urlArr['path'] ?? '', '/'));
        if (stristr($host, 'dzen.')) {
            parse_str(trim($urlArr['query'] ?? '', ' ?'), $queryArr);
            if ((($pathArr[0] ?? '') == 'video') && (($pathArr[1] ?? '') == 'watch')) {
                return $pathArr[2];
            } elseif ($pathArr[0] ?? '' == 'embed') {
                $iframeId = $pathArr[1];
                $id = array_search($iframeId, static::$iframeIds);
                if (!$id) {
                    static::parseIFrame($iframeId);
                    $id = array_search($iframeId, static::$iframeIds);
                }
                return $id ?: null;
            }
        }
        return null;
    }


    /**
     * Парсит страницу видео и устанавливает кэши
     */
    protected function parsePage()
    {
        $url = 'https://dzen.ru/video/watch/' . $this->id;
        $iframeId = null;
        $coverURL = null;
        $ctx = stream_context_create(['http'=> ['timeout' => 5, 'header' => 'Cookie: zen_sso_checked=1']]);
        $text = file_get_contents($url, false, $ctx);
        if (preg_match('/"Thumbnail":"(.+?)"/umis', $text, $regs)) {
            $coverURL = $regs[1];
        }
        if (preg_match('/"embedUrl":".*?\\/embed\\/(.+?)"/umis', $text, $regs)) {
            $iframeId = $regs[1];
        }
        if ($iframeId) {
            static::$iframeIds[$this->id] = $iframeId;
        }
        if ($coverURL) {
            static::$idsToCoverURLs[$this->id] = $coverURL;
        }
    }


    /**
     * Получает URL iframe по ID для встраивания
     * @param string $iframeId ID для встраивания
     * @return string
     */
    protected static function getIFrameByIFrameId(string $iframeId): string
    {
        return 'https://dzen.ru/embed/' . $iframeId;
    }


    /**
     * Парсит iframe видео и устанавливает кэши
     * @param string $iframeId ID для встраивания
     */
    protected static function parseIFrame(string $iframeId)
    {
        $url = static::getIFrameByIFrameId($iframeId);
        $id = null;
        $coverURL = null;
        $text = file_get_contents($url, false, stream_context_create(['http'=> ['timeout' => 5]]));
        if (preg_match('/"video_url":".*?\\/video\\/watch\\/([\\w]+?)"/umis', $text, $regs)) {
            $id = $regs[1];
        }
        if (preg_match('/\\<link.*?rel="image_src".*?\\>/umis', $text, $regs)) {
            $linkTag = $regs[0];
            if (preg_match('/href="(.+?)"/umis', $linkTag, $regs)) {
                $coverURL = $regs[1];
            }
        }
        if ($id) {
            static::$iframeIds[$id] = $iframeId;
            if ($coverURL) {
                static::$idsToCoverURLs[$id] = $coverURL;
            }
        }
    }
}
