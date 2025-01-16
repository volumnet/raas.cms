<?php
/**
 * Видео, размещенное на видеохостинге
 *
 * <pre><code>
 * Предустановленные типы:
 *
 * ПараметрыПроигрывателя => [
 *     'time' => ?int Метка времени начала воспроизведения, сек.,
 *     'controls' => ?bool Отображать панель управления (по умолчанию true),
 *     'nocookies' => ?bool Вариант без cookies,
 *     'playlistIds' => ?string[] Массив дополнительных видео для воспроизведения плейлиста
 *     'autoplay' => ?bool Автовоспроизведение
 *     'cc' => ?bool Субтитры
 *     'color' => ?string Цвет полосы прогресса воспроизведения
 *         (HEX-формат, поддерживается только белый - #fff или #ffffff, по умолчанию - красный)
 *     'keyboard' => ?bool Включено ли управление клавиатурой (по умолчанию true)
 *     'jsapi' => ?bool Включен ли JS API
 *     'end' => ?int Метка времени окончания воспроизведения, сек.,
 *     'fullscreen' => ?bool Включена ли возможность перехода в полноэкранный режим (по умолчанию true)
 *     'lang' => ?string Язык интерфейса (явно заданный)
 *     'iv_load_policy' => ?bool Показывать видеоаннотации (по умолчанию true)
 *     'listType' => ?string В сочетании с параметром list этот параметр определяет,
 *         какой контент будет загружен в проигрыватель:
 *         playlist – параметр list определяет идентификатор плейлиста YouTube. Значение параметра должно начинаться с букв PL.
 *         search – параметр list задает поисковый запрос, который используется для выбора контента.
 *         user_uploads – параметр list определяет название канала YouTube, с которого загружаются ролики,
 *     'list' => ?string
 *     'loop' => ?bool Циклическое воспроизведение
 *     'modestbranding' => ?bool Убрать логотип YouTube
 *     'playsinline' => ?bool
 *         true - Встроенное воспроизведение роликов UIWebViews, созданных с помощью свойства
 *             allowsInlineMediaPlayback со значением TRUE.
 *         false - ролики воспроизводятся в полноэкранном режиме. Это значение по умолчанию может быть изменено.
 *     'rel' => ?bool Предлагать связанные видео (по умолчанию false)
 *     'key' => ?string Ключ приватного доступа,
 *     'width' => ?int Ширина окна, px
 *     'height' => ?int Высота окна, px,
 *     'shorts' => ?bool Shorts
 * ]
 * </code></pre>
 */
declare(strict_types=1);

namespace RAAS\CMS;

use SOME\HTTP;

/**
 * Видео, размещенное на видеохостинге
 * @property-read $id ID видео
 */
abstract class HostedVideo
{
    /**
     * Оригинальный URL
     * @var string
     */
    protected string $originalURL = '';

    /**
     * Дополнительные параметры
     * @var array <pre><code><ПараметрыПроигрывателя></code></pre>
     */
    public array $params = [];

    /**
     * Зарегистрированные сервисы
     * @var string[]
     */
    protected static $registeredServices = [
        YouTubeVideo::class,
        RuTubeVideo::class,
        VKVideo::class,
        DzenVideo::class,
    ];

    /**
     * Конструктор класса
     * @param string $id ID видео
     */
    public function __construct(protected string $id)
    {
    }


    public function __get(string $var)
    {
        switch ($var) {
            case 'id':
            case 'originalURL':
                return $this->$var;
                break;
            case 'pageURL':
                return $this->getPageURL();
                break;
            case 'iframeURL':
                return $this->getIFrameURL();
                break;
            case 'coverURL':
                return $this->getCoverURL();
                break;
        }
    }


    /**
     * Возвращает URL страницы видео
     * @param array $options <pre><code><ПараметрыПроигрывателя></code></pre> Опции получения
     */
    abstract public function getPageURL(array $options = []): string;


    /**
     * Возвращает URL iframe
     * @param array $options <pre><code><ПараметрыПроигрывателя></code></pre> Опции получения
     */
    abstract public function getIFrameURL(array $options = []): string;


    /**
     * Возвращает URL обложки
     * @param array $options <pre><code><ПараметрыПроигрывателя></code></pre> Опции получения
     */
    abstract public function getCoverURL(array $options = []): string;


    /**
     * Получает экземпляр видео по его ссылке
     * @param string $url Ссылка на видео
     * @return ?self null, если не найдено
     */
    public static function spawnByURL(string $url): ?self
    {
        foreach (static::$registeredServices as $serviceClassname) {
            if ($video = $serviceClassname::spawnByURL($url)) {
                return $video;
            }
        }
        return null;
    }
}
