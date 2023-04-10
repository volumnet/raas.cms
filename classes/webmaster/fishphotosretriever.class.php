<?php
/**
 * Модуль получения произвольных картинок
 */
namespace RAAS\CMS;

/**
 * Класс получения произвольных картинок
 */
class FishPhotosRetriever
{
    /**
     * URL API, к которому обращаемся
     */
    public static $url = 'https://pixabay.com/api/?key=2454887-55d55e2392a4a1a17b3d116f5&image_type=photo&per_page=100';

    /**
     * Полученные URL картинок
     */
    protected $imagesRetrieved = [];

    /**
     * Получить картинки
     * @param int $number Количество картинок для получения
     * @param string $search Тематика картинок для получения
     * @param bool $refresh Обновить данные по картинкам
     * @return array<string URL картинки>
     */
    public function retrieve($number, $search = '', $refresh = false)
    {
        if (!$this->imagesRetrieved || $refresh) {
            $url = static::$url . ($search ? '&q=' . urlencode($search) : '');
            $text = @file_get_contents($url);
            $result = @(array)json_decode($text, true);
            $result = $result['hits'] ?? [];
            $this->imagesRetrieved = array_map(function ($x) {
                return $x['largeImageURL'];
            }, $result);
        }
        $images = $this->imagesRetrieved;
        shuffle($images);
        $images = array_slice($images, 0, $number);
        return $images;
    }
}
