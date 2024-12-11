<?php
/**
 * Видео, размещенное на видеохостинге
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Видео, размещенное на видеохостинге
 * @property-read $id ID видео
 * @property-read $url URL страницы видео
 * @property-read $iframe URL iframe видео
 * @property-read $cover URL обложки видео
 */
abstract class HostedVideo
{
    /**
     * ID видео
     * @var string
     */
    protected $id;

    /**
     * Кэш видео по ID
     * @var array <pre><code>array<string[] Класс видео => array<string[] ID видео => self>></code></pre>
     */
    protected static $cache = [];

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

    protected function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __get(string $var)
    {
        switch ($var) {
            case 'id':
                return $this->$var;
                break;
        }
    }


    /**
     * Возвращает URL страницы видео
     * @param array $options <pre><code>array<string[] => mixed></code></pre> Опции получения
     */
    abstract public function getPageURL(array $options = []): string;


    /**
     * Возвращает URL iframe
     * @param array $options <pre><code>array<string[] => mixed></code></pre> Опции получения
     */
    abstract public function getIFrameURL(array $options = []): string;


    /**
     * Возвращает URL обложки
     * @param array $options <pre><code>array<string[] => mixed></code></pre> Опции получения
     */
    abstract public function getCoverURL(array $options = []): string;


    /**
     * Получает ID видео по его ссылке
     * @param string $url Ссылка на видео
     * @return string|null null, если не найдено
     */
    abstract public static function getIdFromURL(string $url);

    /**
     * Получает экземпляр видео по его ссылке
     * @param string $url Ссылка на видео
     * @return static|null null, если не найдено
     */
    public static function spawnByURL(string $url)
    {
        static::fetchURLs([$url]);
        foreach (static::$registeredServices as $serviceClassname) {
            if ($id = $serviceClassname::getIdFromURL($url)) {
                return static::$cache[$serviceClassname][$id] ?? null;
            }
        }
    }

    public static function spawnById(string $id)
    {
        if (static::class == self::class) {
            return;
        }
        static::fetchIds([$id]);
        return static::$cache[static::class][$id] ?? null;
    }


    /**
     * Сохраняет в кэш видео по набору ID
     * @param string[] $ids набор ID видео
     */
    public static function fetchIds(array $ids)
    {
        // @codeCoverageIgnoreStart
        // Не будем проверять этот метод напрямую, т.к. не возвращает значений, а $cache - protected
        if (static::class == self::class) {
            return;
        }
        // @codeCoverageIgnoreEnd
        foreach ($ids as $id) {
            static::$cache[static::class][$id] = new static($id);
        }
    }


    /**
     * Сохраняет в кэш видео по набору ссылок
     * @param string[] $urls набор ссылок видео
     */
    public static function fetchURLs(array $urls)
    {
        $idsToFetch = [];
        foreach ($urls as $url) {
            foreach (static::$registeredServices as $serviceClassname) {
                if (($id = $serviceClassname::getIdFromURL($url)) &&
                    !isset(static::$cache[$serviceClassname][$id]) &&
                    !isset($idsToFetch[$serviceClassname][$id])
                ) {
                    $idsToFetch[$serviceClassname][$id] = $id;
                    break;
                }
            }
        }
        foreach ($idsToFetch as $serviceClassname => $serviceIds) {
            $serviceClassname::fetchIds(array_values($serviceIds));
        }
    }

}
