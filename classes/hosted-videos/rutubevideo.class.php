<?php
/**
 * Видео на RuTube
 *
 * Параметры проигрывателя:
 * https://rutube.ru/info/embed/
 */
declare(strict_types=1);

namespace RAAS\CMS;

use SOME\HTTP;

/**
 * Видео на RuTube
 */
class RuTubeVideo extends HostedVideo
{
    /**
     * URL обложки
     * @var string
     */
    protected $coverURL = '';

    public function getPageURL(array $options = []): string
    {
        $options = array_merge($this->params, $options);
        $result = 'https://rutube.ru/' . (($options['shorts'] ?? null) ? 'shorts' : 'video') . '/' . $this->id . '/';
        if ($time = (int)($options['time'] ?? null)) {
            $result .= '?t=' . $time;
        }
        return $result;
    }


    public function getIFrameURL(array $options = []): string
    {
        $options = array_merge($this->params, $options);
        $result = 'https://rutube.ru/play/embed/' . $this->id;
        $urlParams = [];
        foreach (['time' => 't', 'end' => 'stopTime'] as $fromURN => $toURN) {
            if ($time = (int)($options[$fromURN] ?? null)) {
                $urlParams[$toURN] = $time;
            }
        }
        if ($color = (string)($options['color'] ?? '')) {
            $color = trim($color, '#');
            if (strlen($color) == 3) {
                $color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
            }
            $color = substr($color, 0, 6);
            $urlParams['skinColor'] = $color;
        }
        if ($options['key'] ?? null) {
            $urlParams['p'] = $options['key'];
        }
        if ($urlParams) {
            $result .= '?' . http_build_query($urlParams);
        }
        return $result;
    }


    public function getCoverURL(array $options = []): string
    {
        $options = array_merge($this->params, $options);
        if (!$this->coverURL) {
            $url = 'https://rutube.ru/api/play/options/' . $this->id . '/';
            $urlParams = [];
            // @codeCoverageIgnoreStart
            // Не могу в публичном тесте проверять ключ
            if ($options['key'] ?? null) {
                $urlParams['p'] = $options['key'];
            }
            if ($urlParams) {
                $url .= '?' . http_build_query($urlParams);
            }
            // @codeCoverageIgnoreEnd
            $text = (string)file_get_contents($url, false, stream_context_create(['http'=> ['timeout' => 5]]));
            $json = @(array)json_decode($text, true);
            $this->coverURL = $json['thumbnail_url'] ?? '';
        }
        return $this->coverURL;
    }


    public static function spawnByURL(string $url): ?self
    {
        $url = html_entity_decode($url);
        $urlArr = HTTP::parseURL($url);
        $urlArr['host'] = str_replace('www.', '', $urlArr['host'] ?? '');
        $result = null;
        if (stristr($urlArr['host'], 'rutube.')) {
            if (in_array($urlArr['path'][0] ?? '', ['embed', 'video', 'shorts'])) {
                if (($urlArr['path'][1] ?? '') == 'private') {
                    $result = new static($urlArr['path'][2]);
                } else {
                    $result = new static($urlArr['path'][1]);
                }
            } elseif (($urlArr['path'][0] == 'play') && ($urlArr['path'][1] == 'embed')) {
                $result = new static($urlArr['path'][2]);
            }
        }
        if ($result) {
            $result->originalURL = $url;
            if (($urlArr['path'][0] ?? '') == 'shorts') {
                $result->params['shorts'] = true;
            }
            foreach ([
                't' => 'time',
                'stopTime' => 'end',
            ] as $fromURN => $toURN) {
                if ($value = (int)($urlArr['query'][$fromURN] ?? null)) {
                    $result->params[$toURN] = $value;
                }
            }
            foreach ([
                'p' => 'key',
            ] as $fromURN => $toURN) {
                if ($urlArr['query'][$fromURN] ?? null) {
                    $result->params[$toURN] = $urlArr['query'][$fromURN];
                }
            }
            if ($color = (string)($urlArr['query']['skinColor'] ?? '')) {
                $result->params['color'] = '#' . trim($color, '#');
            }
        }
        return $result;
    }
}
