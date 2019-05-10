<?php
/**
 * Интеграция с uLogin
 */
namespace RAAS\CMS;

/**
 * Класс интеграции с uLogin
 */
class ULogin extends SocialProfile
{
    protected function parseProfile(array $arr)
    {
        if (isset($arr['error']) || !isset($arr['profile'])) {
            return false;
        }
        foreach ($arr as $key => $val) {
            $this->$key = $arr[$key];
        }
        return true;
    }


    protected static function getProfileURL($token)
    {
        return 'http://ulogin.ru/token.php?token=' . $token . '&host=' .
               $_SERVER['HTTP_HOST'];
    }
}
