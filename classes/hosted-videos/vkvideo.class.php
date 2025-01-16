<?php
/**
 * Видео на VK Video
 *
 * Параметры проигрывателя:
 * https://dev.vk.com/ru/widgets/video
 */
declare(strict_types=1);

namespace RAAS\CMS;

use SOME\HTTP;

/**
 * Видео на VK Video
 */
class VKVideo extends HostedVideo
{
    /**
     * URL обложки
     * @var string
     */
    protected $realCoverURL = '';

    public function getPageURL(array $options = []): string
    {
        $result = 'https://vkvideo.ru/video' . $this->id;
        return $result;
    }


    public function getIFrameURL(array $options = []): string
    {
        $options = array_merge($this->params, $options);
        list($oid, $id) = explode('_', $this->id);
        $result = 'https://vkvideo.ru/video_ext.php?oid=' . $oid . '&id=' . $id;
        if ((int)($options['width'] ?? 0) || (int)($options['height'] ?? 0)) { // Если какой-то из размеров задан явно
            $width = (int)($options['width'] ?? 0);
            $height = (int)($options['height'] ?? 0);
        } else {
            $width = 1920;
            $height = 1080;
        }
        $hdArr = [[640, 360], [853, 480], [1280, 720]];
        $hd = 4;
        foreach ($hdArr as $i => $hdWH) {
            if (($width <= $hdWH[0]) && ($height <= $hdWH[1])) {
                $hd = $i + 1;
                break;
            }
        }
        $result .= '&hd=' . $hd;
        if ($options['key'] ?? null) {
            $result .= '&hash=' . $options['key'];
        }
        if ($options['time'] ?? null) {
            $time = (int)$options['time'];
            $hh = (int)($time / 3600);
            $mm = (int)($time / 60 - $hh * 60);
            $ss = (int)($time - $hh * 3600 - $mm * 60);
            $timeText = str_pad((string)$hh, 2, '0', STR_PAD_LEFT) . 'h'
                      . str_pad((string)$mm, 2, '0', STR_PAD_LEFT) . 'm'
                      . str_pad((string)$ss, 2, '0', STR_PAD_LEFT) . 's';
            $result .= '&t=' . $timeText;
        }
        foreach (['autoplay' => 'autoplay', 'loop' => 'loop', 'jsapi' => 'js_api'] as $fromURN => $toURN) {
            if ($options[$fromURN] ?? null) {
                $result .= '&' . $toURN . '=1';
            }
        }
        return $result;
    }


    public function getCoverURL(array $options = []): string
    {
        if (!$this->realCoverURL) {
            list($oid, $id) = explode('_', $this->id);
            $url = 'https://vkvideo.ru/video_ext.php?oid=' . $oid . '&id=' . $id . '&hd=4';
            $ctx = stream_context_create(['http'=> ['timeout' => 5, 'header' => 'Cookie: _ignoreAutoLogin=1']]);
            $text = file_get_contents($url, false, $ctx);
            // Встречаются некорректные сочетания UTF-8, исправим
            $text = iconv('UTF-8', 'Windows-1251//IGNORE', $text);
            $text = iconv('Windows-1251', 'UTF-8', $text);
            $rx = '/\\<div [^\\>]+video_box_msg_background[^\\>]+background-image[^\\>]*:[^\\>]*url\\(([^\\>]+)\\)[^\\>]+\\>/umis';
            $result = '';
            if (preg_match($rx, $text, $regs)) {
                $result = $regs[1];
                $result = trim($result, ' \'"');
                $this->realCoverURL = $result;
            }

            // @codeCoverageIgnoreStart
            // Fallback, если по какой-то причине не найдет через background-image
            if (!$result) {
                $rx = '/var playerParams = ({.*?});/umis';
                if (preg_match($rx, $text, $regs)) {
                    $json = @json_decode($regs[1], true);
                    if ($json['params'][0]['jpg'] ?? null) {
                        $this->realCoverURL = $json['params'][0]['jpg'];
                    }
                }
            }
            // @codeCoverageIgnoreEnd
        }
        return $this->realCoverURL;
    }


    public static function spawnByURL(string $url): ?self
    {
        $url = html_entity_decode($url);
        $urlArr = HTTP::parseURL($url);
        $urlArr['host'] = str_replace('www.', '', $urlArr['host'] ?? '');
        $result = null;
        if (stristr($urlArr['host'], 'vkvideo.') || stristr($urlArr['host'], 'vk.')) {
            if (preg_match('/^video([\\d_\\-]+?)$/umis', $urlArr['path'][0], $regs)) {
                $result = new static($regs[1]);
            } elseif (stristr($urlArr['path'][0], 'video_ext.php')) {
                $result = new static(($urlArr['query']['oid'] ?? '') . '_' . ($urlArr['query']['id'] ?? ''));
            }
        }
        if ($result) {
            $result->originalURL = $url;

            foreach ([
                'hash' => 'key',
            ] as $fromURN => $toURN) {
                if ($urlArr['query'][$fromURN] ?? null) {
                    $result->params[$toURN] = $urlArr['query'][$fromURN];
                }
            }
            foreach ([
                'autoplay' => 'autoplay',
                'loop' => 'loop',
                'js_api' => 'jsapi',
            ] as $fromURN => $toURN) {
                if (($urlArr['query'][$fromURN] ?? null) !== null) {
                    $result->params[$toURN] = (bool)(int)$urlArr['query'][$fromURN];
                }
            }
            if ($urlArr['query']['t'] ?? null) {
                $regs = preg_split('/\\D+/umis', $urlArr['query']['t']);
                $regs = array_filter($regs, function ($x) {
                    return $x !== '';
                });
                $regs = array_reverse($regs);
                $regs = array_map('intval', $regs);
                $t = 0;
                for ($i = 0; $i < count($regs); $i++) {
                    $t += $regs[$i] * pow(60, $i);
                }
                $result->params['time'] = $t;
            }
        }
        return $result;
    }
}
