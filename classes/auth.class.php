<?php
namespace RAAS\CMS;
use \RAAS\Application;

class Auth
{
    const SESSION_VAR = 'SITE_USER';
    const COOKIE_VAR = 'SITE_USER';

    protected $user;
    
    public function __get($var)
    {
        switch ($var) {
            case 'user':
                return $this->user;
                break;
        }
    }


    public function __construct(User $User = null)
    {
        if (!$User) {
            $User = new User();
        }
        $this->user = $User;
    }


    public function setSession()
    {
        $_SESSION[self::SESSION_VAR] = (int)$this->user->id;
    }


    public function getSession()
    {
        $this->user = new User((int)$_SESSION[self::SESSION_VAR]);
    }


    public function setCookie()
    {
        setcookie(self::COOKIE_VAR, $_COOKIE[self::COOKIE_VAR] = $this->user->loginKey, time() + Application::i()->registryGet('cookieLifetime') * 86400, '/');
    }


    public function getCookie()
    {
        $User = User::importByLoginKey($_COOKIE[self::COOKIE_VAR]);
        if ($User) {
            $this->user = $User;
        }
    }


    public function login($login, $password, $savePassword = false)
    {
        $User = User::importByLoginPassword($login, $password);
        if ($User) {
            $this->user = $User;
            $this->setSession();
            if ($savePassword) {
                $this->setCookie();
            }
            return true;
        }
        return false;
    }


    public function logout()
    {
        $this->user = new User();
        unset($_SESSION[self::SESSION_VAR]);
        setcookie(self::COOKIE_VAR, $_COOKIE[self::COOKIE_VAR] = '', time() - Application::i()->registryGet('cookieLifetime') * 86400, '/');
    }


    public function auth()
    {
        if (!$this->user->id) {
            $this->getSession();
        }
        if (!$this->user->id) {
            $this->getCookie();
            if ($this->user->id) {
                $this->setSession();
            }
        }
        return $user;
    }

}