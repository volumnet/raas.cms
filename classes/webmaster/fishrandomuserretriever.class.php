<?php
/**
 * Генератор пользователей из RANDOMUSER.RU
 */
namespace RAAS\CMS;

/**
 * Класс генератора пользователей из RANDOMUSER.RU
 */
class FishRandomUserRetriever
{
    /**
     * URL для получения пользователей
     * @var string
     */
    public static $url = 'http://randomuser.ru/api.json';

    /**
     * Получает пользователя
     * @return [
     *             'name' => [
     *                 'first' => string Имя,
     *                 'last' => string Фамилия,
     *                 'middle' => string Отчество
     *             ],
     *             'location' => [
     *                 'building' => int Дом,
     *                 'street' => string Улица,
     *                 'city' => string Город,
     *                 'state' => string Область,
     *                 'zip' => int Индекс,
     *             ],
     *             'username' => string Логин,
     *             'email' => string E-mail,
     *             'password' => string Пароль,
     *             'salt' => string Соль для пароля,
     *             'md5' => string MD5 пароля,
     *             'sha1' => string SHA1 пароля,
     *             'sha256' => string SHA256 пароля,
     *             'phone' => string Телефон,
     *             'cell' => string Мобильный телефон,
     *             'picture' => [
     *                 'large' => string URL большой картинки,
     *                 'medium' => string URL средней картинки,
     *                 'thumbnail' => string URL эскиза
     *             ],
     *             'pic' => [
     *                 'name' => string Имя оригинального файла,
     *                 'filepath' => string Местоположение файла во временной
     *                                      папке
     *             ],
     *         ]
     */
    public function retrieve()
    {
        $text = file_get_contents(self::$url);
        $json = json_decode($text, true);
        $json = $json[0]['user'];
        $pic = $json['picture']['large'];
        $text = file_get_contents($pic);
        $tempname = tempnam(sys_get_temp_dir(), 'RAAS');
        @file_put_contents($tempname, $text);
        $json['pic'] = ['name' => basename($pic), 'filepath' => $tempname];
        return $json;
    }
}
