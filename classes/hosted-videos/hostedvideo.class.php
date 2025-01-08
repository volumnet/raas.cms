<?php
/**
 * Видео, размещенное на видеохостинге
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Видео, размещенное на видеохостинге
 * @property-read $id ID видео
 */
abstract class HostedVideo
{
    /**
     * ID видео
     * @var string
     */
    protected $id;

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
    public function __construct(string $id)
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
        foreach (static::$registeredServices as $serviceClassname) {
            if ($id = $serviceClassname::getIdFromURL($url)) {
                return new $serviceClassname($id);
            }
        }
    }
}
