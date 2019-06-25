<?php
/**
 * Объект авторизации
 */
namespace RAAS\CMS;

use RAAS\Application;

/**
 * Класс объекта авторизации
 * @property-read User $user Текущий пользователь
 */
class Auth
{
    /**
     * Переменная в сессии для хранения ID# пользователя
     */
    const SESSION_VAR = 'SITE_USER';

    /**
     * Переменная в cookie для хранения ключа входа пользователя
     */
    const COOKIE_VAR = 'SITE_USER';

    /**
     * Переменная в сессии для хранения подтвержденной авторизации из соц. сети
     */
    const SESSION_CONFIRMED_SOCIAL_VAR = 'confirmedSocial';

    /**
     * Текущий пользователь
     * @var User
     */
    protected $user;

    public function __get($var)
    {
        switch ($var) {
            case 'user':
                return $this->user;
                break;
        }
    }


    /**
     * Конструктор класса
     * @param User $user Текущий пользователь
     */
    public function __construct(User $user = null)
    {
        if (!$user) {
            $user = new User();
        }
        $this->user = $user;
    }


    /**
     * Устанавливает значение переменной сессии
     */
    public function setSession()
    {
        $_SESSION[self::SESSION_VAR] = (int)$this->user->id;
    }


    /**
     * Загружает текущего пользователя из сессии
     */
    public function getSession()
    {
        $this->user = new User((int)$_SESSION[self::SESSION_VAR]);
    }


    /**
     * Устанавливает значение переменной cookie
     */
    public function setCookie()
    {
        setcookie(
            self::COOKIE_VAR,
            $_COOKIE[self::COOKIE_VAR] = $this->user->loginKey,
            time() + Application::i()->registryGet('cookieLifetime') * 86400,
            '/'
        );
    }


    /**
     * Загружает текущего пользователя из cookie
     */
    public function getCookie()
    {
        $user = User::importByLoginKey($_COOKIE[self::COOKIE_VAR]);
        if ($user) {
            $this->user = $user;
        }
    }


    /**
     * Авторизует пользователя по логину и паролю
     * @param string $login Логин
     * @param string $password Пароль
     * @param bool $savePassword Сохранять ли пароль
     * @return bool Удалось ли авторизовать пользователя
     */
    public function login($login, $password, $savePassword = false)
    {
        $User = User::importByLoginPassword($login, $password);
        if ($User) {
            if (!$User->vis) {
                return -1;
            }
            $this->user = $User;
            $this->setSession();
            if ($savePassword) {
                $this->setCookie();
            }
            return true;
        }
        return false;
    }


    /**
     * Авторизует пользователя по адресу соц. сети
     * @param string $profile Адрес соц. сети
     * @return bool Удалось ли авторизовать пользователя
     */
    public function loginBySocialNetwork($profile)
    {
        $User = User::importBySocialNetwork($profile);
        if ($User) {
            $this->user = $User;
            $this->setSession();
            return true;
        }
        return false;
    }


    /**
     * Выход пользователя из системы
     */
    public function logout()
    {
        $this->user = new User();
        unset(
            $_SESSION[self::SESSION_VAR],
            $_SESSION[static::SESSION_CONFIRMED_SOCIAL_VAR]
        );
        setcookie(
            self::COOKIE_VAR,
            $_COOKIE[self::COOKIE_VAR] = '',
            time() - Application::i()->registryGet('cookieLifetime') * 86400,
            '/'
        );
        setcookie(
            session_name(),
            $_COOKIE[session_name()] = '',
            time() - Application::i()->registryGet('cookieLifetime') * 86400,
            '/'
        );
        session_destroy();
    }


    /**
     * Авторизует пользователя через сессию или cookie
     * @return User|null Авторизованный пользователь
     */
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
        return $this->user;
    }

}
