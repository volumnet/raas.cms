<?php
/**
 * Видео на VK Video
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Видео на VK Video
 */
class VKVideo extends HostedVideo
{
    /**
     * URL обложки
     * @var string
     */
    protected $coverURL = '';

    public function getPageURL(array $options = []): string
    {
        $result = 'https://vkvideo.ru/video' . $this->id;
        return $result;
    }


    public function getIFrameURL(array $options = []): string
    {
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
        if ($options['autoplay'] ?? null) {
            $result .= '&autoplay=1';
        }
        if ($options['loop'] ?? null) {
            $result .= '&loop=1';
        }
        if ($options['jsapi'] ?? null) {
            $result .= '&js_api=1';
        }
        return $result;
    }


    public function getCoverURL(array $options = []): string
    {
        if (!$this->coverURL) {
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
                $this->coverURL = $result;
            }

            // @codeCoverageIgnoreStart
            // Fallback, если по какой-то причине не найдет через background-image
            if (!$result) {
                $rx = '/var playerParams = ({.*?});/umis';
                if (preg_match($rx, $text, $regs)) {
                    $json = @json_decode($regs[1], true);
                    if ($json['params'][0]['jpg'] ?? null) {
                        $this->coverURL = $json['params'][0]['jpg'];
                    }
                }
            }
            // @codeCoverageIgnoreEnd
        }
        return $this->coverURL;
    }


    public static function getIdFromURL(string $url)
    {
        $urlArr = parse_url($url);
        $host = str_replace('www.', '', $urlArr['host'] ?? '');
        $pathArr = explode('/', trim($urlArr['path'] ?? '', '/'));
        if (stristr($host, 'vkvideo.') || stristr($host, 'vk.')) {
            if (preg_match('/^video([\\d_\\-]+?)$/umis', $pathArr[0], $regs)) {
                return $regs[1];
            } elseif (stristr($pathArr[0], 'video_ext.php')) {
                parse_str(trim($urlArr['query'] ?? '', ' ?'), $queryArr);
                return ($queryArr['oid'] ?? '') . '_' . ($queryArr['id'] ?? '');
            }
        }
        return null;
    }
}
