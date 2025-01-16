<?php
/**
 * Видео на YouTube
 */
declare(strict_types=1);

namespace RAAS\CMS;

use SOME\HTTP;

/**
 * Видео на YouTube
 *
 * Параметры проигрывателя:
 * https://developers.google.com/youtube/player_parameters?hl=ru
 */
class YouTubeVideo extends HostedVideo
{
    /**
     * Возвращает URL страницы видео
     * @param array $options <pre><code>[
     *     'time' => ?int Метка времени начала воспроизведения
     * ]</code></pre> Опции получения
     */
    public function getPageURL(array $options = []): string
    {
        $options = array_merge($this->params, $options);
        $result = 'https://www.youtube.com/watch';
        $urlParams = [];
        if ($this->id) {
            $urlParams['v'] = $this->id;
        }
        if ($options['list'] ?? null) {
            $urlParams['listType'] = 'list';
            $urlParams['list'] = $options['list'];
        }
        if ($time = (int)($options['time'] ?? null)) {
            $urlParams['t'] = $time . 's';
        }
        if ($urlParams) {
            $result .= '?' . http_build_query($urlParams);
        }
        return $result;
    }


    public function getIFrameURL(array $options = []): string
    {
        $options = array_merge($this->params, $options);
        $result = 'https://www.youtube' . (($options['nocookies'] ?? false) ? '-nocookie' : '') . '.com/embed/';
        $urlParams = [];
        if ($options['playlistIds'] ?? []) {
            $lists = [];
            if ($this->id) {
                $lists[] = $this->id;
            }
            $lists = array_merge($lists, (array)$options['playlistIds']);
            $urlParams['playlist'] = implode(',', $lists);
        } elseif ($this->id) {
            $result .= $this->id;
        }
        foreach (['time' => 'start', 't' => 'start', 'end' => 'end'] as $fromURN => $toURN) {
            if ($time = (int)($options[$fromURN] ?? null)) {
                $urlParams[$toURN] = $time;
            }
        }
        foreach (['controls' => 'controls', 'fullscreen' => 'fs'] as $fromURN => $toURN) {
            if (isset($options[$fromURN]) && ($options[$fromURN] !== null) && !$options[$fromURN]) {
                $urlParams[$toURN] = 0;
            }
        }
        foreach (['keyboard' => 'disablekb'] as $fromURN => $toURN) {
            if (isset($options[$fromURN]) && ($options[$fromURN] !== null) && !$options[$fromURN]) {
                $urlParams[$toURN] = 1;
            }
        }
        foreach ([
            'autoplay' => 'autoplay',
            'cc' => 'cc_load_policy',
            'jsapi' => 'enablejsapi',
            'loop' => 'loop',
            'modestbranding' => 'modestbranding',
            'playsinline' => 'playsinline',
        ] as $fromURN => $toURN) {
            if ($options[$fromURN] ?? false) {
                $urlParams[$toURN] = 1;
            }
        }
        foreach (['lang' => 'hl', 'list' => 'list'] as $fromURN => $toURN) {
            if ($options[$fromURN] ?? null) {
                $urlParams[$toURN] = $options[$fromURN];
            }
        }
        foreach (['rel' => 'rel'] as $fromURN => $toURN) {
            if (!($options[$fromURN] ?? null)) {
                $urlParams[$toURN] = 0;
            }
        }
        if (stristr((string)($options['color'] ?? ''), 'fff')) {
            $urlParams['color'] = 'white';
        }
        if (isset($options['iv_load_policy']) && ($options['iv_load_policy'] !== null) && !$options['iv_load_policy']) {
            $urlParams['iv_load_policy'] = 3;
        }
        if ($options['listType'] ?? null) {
            $urlParams['listType'] = $options['listType'];
        } elseif ($options['list'] ?? null) {
            $urlParams['listType'] = 'list';
        }
        if ($urlParams) {
            $result .= '?' . http_build_query($urlParams);
        }
        return $result;
    }


    public function getCoverURL(array $options = []): string
    {
        $result = '';
        if ($this->id) {
            $result = 'https://i.ytimg.com/vi/' . addslashes($this->id) . '/hqdefault.jpg';
        }
        return $result;
    }


    public static function spawnByURL(string $url): ?self
    {
        $url = html_entity_decode($url);
        $urlArr = HTTP::parseURL($url);
        $urlArr['host'] = str_replace('www.', '', $urlArr['host'] ?? '');
        $result = null;
        if (stristr($urlArr['host'], 'youtube.') || stristr($urlArr['host'], 'youtube-nocookie.')) {
            if ($urlArr['query']['v'] ?? null) {
                $result = new static($urlArr['query']['v']);
            } elseif (($urlArr['path'][0] ?? '') == 'embed') {
                $result = new static($urlArr['path'][1]);
            } elseif ($urlArr['query']['list'] ?? null) {
                $result = new static('');
            }
        } elseif ($urlArr['host'] == 'youtu.be') {
            $result = new static($urlArr['path'][0]);
        }
        if ($result) {
            $result->originalURL = $url;
            if (stristr($urlArr['host'], 'youtube-nocookie.')) {
                $result->params['nocookies'] = true;
            }
            foreach ([
                'list' => 'list',
                'listType' => 'listType',
                'hl' => 'lang',
                'list' => 'list',
            ] as $fromURN => $toURN) {
                if ($urlArr['query'][$fromURN] ?? null) {
                    $result->params[$toURN] = $urlArr['query'][$fromURN];
                }
            }
            foreach ([
                'start' => 'time',
                'end' => 'end',
            ] as $fromURN => $toURN) {
                if ($value = (int)($urlArr['query'][$fromURN] ?? null)) {
                    $result->params[$toURN] = $value;
                }
            }
            foreach ([
                'controls' => 'controls',
                'fs' => 'fullscreen',
                'autoplay' => 'autoplay',
                'cc_load_policy' => 'cc',
                'enablejsapi' => 'jsapi',
                'loop' => 'loop',
                'modestbranding' => 'modestbranding',
                'playsinline' => 'playsinline',
                'rel' => 'rel',
            ] as $fromURN => $toURN) {
                if (($urlArr['query'][$fromURN] ?? null) !== null) {
                    $result->params[$toURN] = (bool)(int)$urlArr['query'][$fromURN];
                }
            }
            foreach (['disablekb' => 'keyboard'] as $fromURN => $toURN) {
                if (($urlArr['query'][$fromURN] ?? null) !== null) {
                    $result->params[$toURN] = !(int)$urlArr['query'][$fromURN];
                }
            }
            if ($urlArr['query']['playlist'] ?? null) {
                $result->params['playlistIds'] = explode(',', $urlArr['query']['playlist']);
            }
            if (($urlArr['query']['color'] ?? null) == 'white') {
                $result->params['color'] = '#ffffff';
            }
            if (($urlArr['query']['iv_load_policy'] ?? null) == 3) {
                $result->params['iv_load_policy'] = false;
            }
        }
        return $result;
    }
}
