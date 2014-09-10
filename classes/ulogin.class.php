<?php 
namespace RAAS\CMS;

class ULogin extends SocialProfile
{
    protected function parseProfile(array $arr)
    {
        if (isset($arr['error']) || !isset($arr['profile'])) {
            return false;
        }
        foreach (array('profile', 'last_name', 'first_name') as $key) {
            $this->$key = $arr[$key];
        }
        return true;
    }


    protected static function getProfileURL($token)
    {
        return 'http://ulogin.ru/token.php?token=' . $token . '&host=' . $_SERVER['HTTP_HOST'];
    }
}