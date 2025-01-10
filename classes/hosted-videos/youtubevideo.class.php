<?php
/**
 * Видео на YouTube
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Видео на YouTube
 */
class YouTubeVideo extends HostedVideo
{
    /**
     * Возвращает URL страницы видео
     * @param array $options <pre><code>[
     *     'time' =>? int Метка времени начала воспроизведения
     * ]</code></pre> Опции получения
     */
    public function getPageURL(array $options = []): string
    {
        $idParams = explode('@', $this->id);
        $id = $idParams[0] ?? '';
        $list = $idParams[1] ?? '';

        $result = 'https://www.youtube.com/watch';
        $urlParams = [];
        if ($id) {
            $urlParams['v'] = $id;
        }
        if ($list) {
            $urlParams['listType'] = 'list';
            $urlParams['list'] = $list;
        }
        if ($time = (int)($options['time'] ?? null)) {
            $urlParams['t'] = $time . 's';
        }
        if ($urlParams) {
            $result .= '?' . http_build_query($urlParams);
        }
        return $result;
    }


    /**
     * Возвращает URL iframe
     * @param array $options <pre><code>[
     *     'time' =>? int Метка времени начала воспроизведения, сек.,
     *     'controls' =>? bool Отображать панель управления (по умолчанию true),
     *     'nocookies' =>? bool Вариант без cookies,
     *     'playlistIds' =>? string[] Массив дополнительных видео для воспроизведения плейлиста
     *     'autoplay' =>? bool Автовоспроизведение
     *     'cc' =>? bool Субтитры
     *     'color' =>? string Цвет полосы прогресса воспроизведения
     *         (HEX-формат, поддерживается только белый - #fff или #ffffff, по умолчанию - красный)
     *     'keyboard' =>? bool Включено ли управление клавиатурой (по умолчанию true)
     *     'jsapi' =>? bool Включен ли JS API
     *     'end' =>? int Метка времени окончания воспроизведения, сек.,
     *     'fullscreen' =>? bool Включена ли возможность перехода в полноэкранный режим (по умолчанию true)
     *     'lang' =>? string Язык интерфейса (явно заданный)
     *     'iv_load_policy' =>? bool Показывать видеоаннотации (по умолчанию true)
     *     'listType' =>? string В сочетании с параметром list этот параметр определяет,
     *         какой контент будет загружен в проигрыватель:
     *         playlist – параметр list определяет идентификатор плейлиста YouTube. Значение параметра должно начинаться с букв PL.
     *         search – параметр list задает поисковый запрос, который используется для выбора контента.
     *         user_uploads – параметр list определяет название канала YouTube, с которого загружаются ролики,
     *     'list' =>? string
     *     'loop' =>? bool Циклическое воспроизведение
     *     'modestbranding' =>? bool Убрать логотип YouTube
     *     'playsinline' =>? bool
     *         true - Встроенное воспроизведение роликов UIWebViews, созданных с помощью свойства
     *             allowsInlineMediaPlayback со значением TRUE.
     *         false - ролики воспроизводятся в полноэкранном режиме. Это значение по умолчанию может быть изменено.
     *     'rel' =>? bool Предлагать связанные видео
     * ]</code></pre> Опции получения
     */
    public function getIFrameURL(array $options = []): string
    {
        $idParams = explode('@', $this->id);
        $id = $idParams[0] ?? '';
        $list = $idParams[1] ?? '';

        $result = 'https://www.youtube' . (($options['nocookies'] ?? false) ? '-nocookie' : '') . '.com/embed/';
        $urlParams = [];
        if ($options['playlistIds'] ?? []) {
            $lists = [];
            if ($id) {
                $lists[] = $id;
            }
            $lists = array_merge($lists, (array)$options['playlistIds']);
            $urlParams['playlist'] = implode(',', $lists);
        } elseif ($id) {
            $result .= $id;
        }
        if ($time = (int)($options['time'] ?? null)) {
            $urlParams['start'] = $time;
        }
        if (isset($options['controls']) && ($options['controls'] !== null) && !$options['controls']) {
            $urlParams['controls'] = 0;
        }
        if ($options['autoplay'] ?? false) {
            $urlParams['autoplay'] = 1;
        }
        if ($options['cc'] ?? false) {
            $urlParams['cc_load_policy'] = 1;
        }
        if (stristr((string)($options['color'] ?? ''), 'fff')) {
            $urlParams['color'] = 'white';
        }
        if (isset($options['keyboard']) && ($options['keyboard'] !== null) && !$options['keyboard']) {
            $urlParams['disablekb'] = 1;
        }
        if ($options['jsapi'] ?? false) {
            $urlParams['enablejsapi'] = 1;
        }
        if ($endTime = (int)($options['end'] ?? null)) {
            $urlParams['end'] = $endTime;
        }
        if (isset($options['fullscreen']) && ($options['fullscreen'] !== null) && !$options['fullscreen']) {
            $urlParams['fs'] = 0;
        }
        if ($options['lang'] ?? null) {
            $urlParams['hl'] = $options['lang'];
        }
        if (isset($options['iv_load_policy']) && ($options['iv_load_policy'] !== null) && !$options['iv_load_policy']) {
            $urlParams['iv_load_policy'] = 3;
        }
        if ($options['listType'] ?? null) {
            $urlParams['listType'] = $options['listType'];
        } elseif ($list) {
            $urlParams['listType'] = 'list';
        }
        if ($options['list'] ?? null) {
            $urlParams['list'] = $options['list'];
        } elseif ($list) {
            $urlParams['list'] = $list;
        }
        if ($options['loop'] ?? false) {
            $urlParams['loop'] = 1;
        }
        if ($options['modestbranding'] ?? false) {
            $urlParams['modestbranding'] = 1;
        }
        if ($options['playsinline'] ?? false) {
            $urlParams['playsinline'] = 1;
        }
        if (!($options['rel'] ?? null)) {
            $urlParams['rel'] = 0;
        }
        if ($urlParams) {
            $result .= '?' . http_build_query($urlParams);
        }
        return $result;
    }


    public function getCoverURL(array $options = []): string
    {
        $idParams = explode('@', $this->id);
        $id = $idParams[0] ?? '';
        $list = $idParams[1] ?? '';

        $result = '';
        if ($id) {
            $result = 'https://i.ytimg.com/vi/' . addslashes($id) . '/hqdefault.jpg';
        }
        return $result;
    }


    public static function getIdFromURL(string $url)
    {
        $urlArr = parse_url($url);
        $host = str_replace('www.', '', $urlArr['host'] ?? '');
        $pathArr = explode('/', trim($urlArr['path'] ?? '', '/'));
        $id = null;
        $list = null;
        if (stristr($host, 'youtube.') || stristr($host, 'youtube-nocookie.')) {
            parse_str(trim($urlArr['query'] ?? '', ' ?'), $queryArr);
            if ($queryArr['v'] ?? null) {
                $id = $queryArr['v'];
            } elseif (($pathArr[0] ?? '') == 'embed') {
                $id = $pathArr[1];
            }
            if ($queryArr['list'] ?? null) {
                $list = $queryArr['list'];
            }
        } elseif ($host == 'youtu.be') {
            $id = $pathArr[0];
        }
        if ($id || $list) {
            $result = '';
            if ($id) {
                $result .= $id;
            }
            if ($list) {
                $result .= '@' . $list;
            }
            return $result;
        }
        return null;
    }
}
