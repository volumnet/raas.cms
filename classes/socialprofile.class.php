<?php
namespace RAAS\CMS;

abstract class SocialProfile
{
    const SN_VK = 1;
    const SN_FB = 2;
    const SN_OK = 3;
    const SN_MR = 4;
    const SN_TW = 5;
    const SN_LJ = 6;
    const SN_GO = 7;
    const SN_YA = 8;
    const SN_WM = 9;
    const SN_YT = 10;

    protected $token;
    protected $profile;
    protected $last_name;
    protected $first_name;

    protected static $social = array(
        '(vk\\.com)|(vkontakte\\.ru)' => self::SN_VK,
        '(fb\\.com)|(facebook\\.com)' => self::SN_FB,
        '(ok\\.ru)|(odnoklassniki\\.ru)' => self::SN_OK,
        'my\\.mail\\.ru' => self::SN_MR,
        'twitter\\.(com|ru)' => self::SN_TW,
        'livejournal\\.(com|ru)' => self::SN_LJ,
        'google\\.(com|ru)' => self::SN_GO,
        'yandex\\.(com|ru)' => self::SN_YA,
        'webmoney\\.(com|ru)' => self::SN_WM,
        'youtube\\.(com|ru)' => self::SN_YT
    );

    public function __get($var)
    {
        switch ($var) {
            // case 'token': case 'profile': case 'last_name': case 'first_name':
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


    protected function __construct() {}


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


    abstract protected function parseProfile(array $arr);


    abstract protected static function getProfileURL($token);


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
