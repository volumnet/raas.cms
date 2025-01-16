<?php
/**
 * Видео на Дзен
 */
declare(strict_types=1);

namespace RAAS\CMS;

use SOME\HTTP;

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
    public static $idsToCoverURLs = [];

    public function getPageURL(array $options = []): string
    {
        $result = 'https://dzen.ru/video/watch/' . $this->id;
        return $result;
    }


    public function getIFrameURL(array $options = []): string
    {
        $options = array_merge($this->params, $options);
        $result = '';
        if (!(static::$iframeIds[$this->id] ?? null)) {
            $this->parsePage();
        }
        if (static::$iframeIds[$this->id] ?? null) {
            $result = static::getIFrameByIFrameId(static::$iframeIds[$this->id]);
            $urlParams = [];
            foreach ([
                'autoplay' => 'autoplay',
                'mute' => 'mute',
            ] as $fromURN => $toURN) {
                if ($options[$fromURN] ?? false) {
                    $urlParams[$toURN] = 1;
                }
            }
            foreach (['controls' => 'tv'] as $fromURN => $toURN) {
                if (isset($options[$fromURN]) && ($options[$fromURN] !== null) && !$options[$fromURN]) {
                    $urlParams[$toURN] = 1;
                }
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


    public static function spawnByURL(string $url): ?self
    {
        $url = html_entity_decode($url);
        $urlArr = HTTP::parseURL($url);
        $urlArr['host'] = str_replace('www.', '', $urlArr['host'] ?? '');
        $result = null;
        if (stristr($urlArr['host'], 'dzen.')) {
            if ((($urlArr['path'][0] ?? '') == 'video') && (($urlArr['path'][1] ?? '') == 'watch')) {
                $result = new static($urlArr['path'][2]);
            } elseif ($urlArr['path'][0] ?? '' == 'embed') {
                $iframeId = $urlArr['path'][1];
                $id = array_search($iframeId, static::$iframeIds);
                if (!$id) {
                    static::parseIFrame($iframeId);
                    $id = array_search($iframeId, static::$iframeIds);
                }
                $result = new static($id ?: null);
            }
        }
        if ($result) {
            $result->originalURL = $url;
            foreach ([
                'autoplay' => 'autoplay',
                'mute' => 'mute',
            ] as $fromURN => $toURN) {
                if (($urlArr['query'][$fromURN] ?? null) !== null) {
                    $result->params[$toURN] = (bool)(int)$urlArr['query'][$fromURN];
                }
            }
            foreach (['tv' => 'controls'] as $fromURN => $toURN) {
                if (($urlArr['query'][$fromURN] ?? null) !== null) {
                    $result->params[$toURN] = !(int)$urlArr['query'][$fromURN];
                }
            }
        }
        return $result;
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
        // @codeCoverageIgnoreStart
        // Fallback, если по какой-то причине не найдет через background-image
        if (preg_match('/"Thumbnail":"(.+?)"/umis', $text, $regs)) {
            $coverURL = $regs[1];
        } elseif (preg_match('/"thumbnailUrl":"(.+?)"/umis', $text, $regs)) {
            $coverURL = $regs[1];
        }
        // @codeCoverageIgnoreEnd
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
