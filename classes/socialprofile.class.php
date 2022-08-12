<?php
/**
 * Профиль социальной сети
 */
namespace RAAS\CMS;

/**
 * Класс профиля социальной сети
 * @property-read string $full_name Полное имя
 * @property-read int $socialNetwork Код соц. сети из констант self::SN_*
 * @property-read string $token Токен входа
 * @property-read string $profile URL/URN пользователя
 * @property-read string $last_name Фамилия
 * @property-read string $first_name Имя
 */
abstract class SocialProfile
{
    /**
     * ВКонтакте
     */
    const SN_VK = 1;

    /**
     * Facebook
     */
    const SN_FB = 2;

    /**
     * Одноклассники
     */
    const SN_OK = 3;

    /**
     * Мой мир (mail.ru)
     */
    const SN_MR = 4;

    /**
     * Twitter
     */
    const SN_TW = 5;

    /**
     * LiveJournal
     */
    const SN_LJ = 6;

    /**
     * Google
     */
    const SN_GO = 7;

    /**
     * Яндекс
     */
    const SN_YA = 8;

    /**
     * WebMoney
     */
    const SN_WM = 9;

    /**
     * YouTube
     */
    const SN_YT = 10;

    /**
     * Instagram
     */
    const SN_IN = 11;

    /**
     * WhatsApp
     */
    const SN_WA = 12;

    /**
     * Telegram
     */
    const SN_TG = 13;

    /**
     * Токен входа
     * @var string
     */
    protected $token;

    /**
     * URL/URN пользователя
     * @var string
     */
    protected $profile;

    /**
     * Фамилия
     * @var string
     */
    protected $last_name;

    /**
     * Имя
     * @var string
     */
    protected $first_name;

    /**
     * Привязка типов адресов (регулярные выражения) к константам соц. сетей
     * @var array<string[] => int>
     */
    protected static $social = [
        '(vk\\.com)|(vkontakte\\.ru)' => self::SN_VK,
        '(fb\\.com)|(facebook\\.com)' => self::SN_FB,
        '(ok\\.ru)|(odnoklassniki\\.ru)' => self::SN_OK,
        'my\\.mail\\.ru' => self::SN_MR,
        'twitter\\.(com|ru)' => self::SN_TW,
        'livejournal\\.(com|ru)' => self::SN_LJ,
        'google\\.(com|ru)' => self::SN_GO,
        'yandex\\.(com|ru)' => self::SN_YA,
        'webmoney\\.(com|ru)' => self::SN_WM,
        'youtube\\.(com|ru)' => self::SN_YT,
        'instagram\\.(com|ru)' => self::SN_IN,
        '(whatsapp|wa)\\.(com|ru|me|php)' => self::SN_WA,
        '((t)\\.(com|ru|me))|telegram' => self::SN_TG,
    ];

    public function __get($var)
    {
        switch ($var) {
            // case 'token':
            // case 'profile':
            // 2021-11-26, AVS: восстановили, т.к. возможно понадобится получать все данные
            case 'last_name':
            case 'first_name':
                return trim($this->$var);
                break;
            case 'full_name':
                return trim($this->last_name . ' ' . $this->first_name);
                break;
            case 'socialNetwork':
                return static::getSocialNetwork($this->profile);
                break;
            default:
                return $this->$var;
                break;
        }
    }


    /**
     * Получает профиль из токена доступа
     * @param string $token Токен
     * @return self|null null, если не удалось получить
     */
    public static function getProfile($token)
    {
        $url = static::getProfileURL($token);
        $text = file_get_contents($url);
        if ($text) {
            $arr = json_decode($text, true);
            if ($arr) {
                $User = new static();
                if ($User->parseProfile($arr)) {
                    $User->token = $token;
                    return $User;
                }
            }
        }
        return null;
    }


    /**
     * Разбирает профиль из массива
     * @param array $arr Массив данных пользователя
     * @return bool Удалось ли разобрать массив
     */
    abstract protected function parseProfile(array $arr);


    /**
     * Получает URL профиля по токену доступа
     * @param string $token Токен
     * @return string
     */
    abstract protected static function getProfileURL($token);


    /**
     * Определяет соц. сеть из адреса
     * @param string $url Адрес
     * @return int|null Код соц. сети из констант self::SN_*, либо null, если
     *                  не удалось определить
     */
    public static function getSocialNetwork($url)
    {
        foreach (static::$social as $key => $val) {
            if (preg_match('/' . $key . '/i', $url)) {
                return $val;
            }
        }
        return null;
    }
}
