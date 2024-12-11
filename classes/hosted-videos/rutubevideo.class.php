<?php
/**
 * Видео на RuTube
 */
declare(strict_types=1);

namespace RAAS\CMS;

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

    /**
     * Возвращает URL страницы видео
     * @param array $options <pre><code>[
     *     'time' =>? int Метка времени начала воспроизведения
     * ]</code></pre> Опции получения
     */
    public function getPageURL(array $options = []): string
    {
        $result = 'https://rutube.ru/video/' . $this->id . '/';
        if ($time = (int)($options['time'] ?? null)) {
            $result .= '?t=' . $time;
        }
        return $result;
    }


    /**
     * Возвращает URL iframe
     * @param array $options <pre><code>[
     *     'time' =>? int Метка времени начала воспроизведения, сек.,
     *     'color' =>? string Цвет полосы прогресса воспроизведения
     *         (HEX-формат - #fff или #ffffff)
     *     'end' =>? int Метка времени окончания воспроизведения, сек.,
     *     'key' =>? string Ключ приватного доступа
     * ]</code></pre> Опции получения
     */
    public function getIFrameURL(array $options = []): string
    {
        $result = 'https://rutube.ru/play/embed/' . $this->id;
        $urlParams = [];
        if ($time = (int)($options['time'] ?? null)) {
            $urlParams['t'] = $time;
        }
        if ($color = (string)($options['color'] ?? '')) {
            $color = trim($color, '#');
            if (strlen($color) == 3) {
                $color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
            }
            $color = substr($color, 0, 6);
            $urlParams['skinColor'] = $color;
        }
        if ($endTime = (int)($options['end'] ?? null)) {
            $urlParams['stopTime'] = $endTime;
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
        if (!$this->coverURL) {
            $url = 'https://rutube.ru/api/video/' . $this->id . '/thumbnail/';
            $text = file_get_contents($url, false, stream_context_create(['http'=> ['timeout' => 5]]));
            $json = @(array)json_decode($text, true);
            $this->coverURL = $json['url'] ?? '';
        }
        return $this->coverURL;
    }


    public static function getIdFromURL(string $url)
    {
        $urlArr = parse_url($url);
        $host = str_replace('www.', '', $urlArr['host'] ?? '');
        if (stristr($host, 'rutube.')) {
            $pathArr = explode('/', trim($urlArr['path'] ?? '', '/'));
            if (in_array($pathArr[0] ?? '', ['embed', 'video'])) {
                return $pathArr[1];
            } elseif (($pathArr[0] == 'play') && ($pathArr[1] == 'embed')) {
                return $pathArr[2];
            }
        }
        return null;
    }
}
