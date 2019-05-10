<?php
/**
 * Файл класса абстрактного интерфейса CMS
 */
namespace RAAS\CMS;

/**
 * Класс абстрактного интерфейса CMS
 * @property-read Block|null $block Блок, для которого применяется интерфейс
 * @property-read Page|null $page Страница, для которой применяется интерфейс
 * @property-read array $get Поля $_GET параметров
 * @property-read array $post Поля $_POST параметров
 * @property-read array $cookie Поля $_COOKIE параметров
 * @property-read array $session Поля $_SESSION параметров
 * @property-read array $server Поля $_SERVER параметров
 * @property-read array $files Поля $_FILES параметров
 */
abstract class AbstractInterface
{
    /**
     * Блок, для которого применяется интерфейс
     * @var Block|null
     */
    protected $block = null;

    /**
     * Страница, для которой применяется интерфейс
     * @var Page|null
     */
    protected $page = null;

    /**
     * Поля $_GET параметров
     * @var array
     */
    protected $get = [];

    /**
     * Поля $_POST параметров
     * @var array
     */
    protected $post = [];

    /**
     * Поля $_COOKIE параметров
     * @var array
     */
    protected $cookie = [];

    /**
     * Поля $_SESSION параметров
     * @var array
     */
    protected $session = [];

    /**
     * Поля $_SERVER параметров
     * @var array
     */
    protected $server = [];

    /**
     * Поля $_FILES параметров
     * @var array
     */
    protected $files = [];

    public function __get($var)
    {
        switch ($var) {
            case 'block':
            case 'page':
            case 'get':
            case 'post':
            case 'cookie':
            case 'session':
            case 'server':
                return $this->$var;
                break;
        }
    }


    /**
     * Конструктор класса
     * @param Block|null $block Блок, для которого применяется интерфейс
     * @param Page|null $page Страница, для которой применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @param array $post Поля $_POST параметров
     * @param array $cookie Поля $_COOKIE параметров
     * @param array $session Поля $_SESSION параметров
     * @param array $server Поля $_SERVER параметров
     * @param array $files Поля $_FILES параметров
     */
    public function __construct(
        Block $block = null,
        Page $page = null,
        array $get = [],
        array $post = [],
        array $cookie = [],
        array $session = [],
        array $server = [],
        array $files = []
    ) {
        $this->block = $block;
        $this->page = $page;
        $this->get = $get;
        $this->post = $post;
        $this->cookie = $cookie;
        $this->session = $session;
        $this->server = $server;
        $this->files = $files;
    }

    /**
     * Выполнить интерфейс
     * @return mixed Выходные данные для виджета
     */
    abstract public function process();


    /**
     * Используется ли соединение по HTTPS
     * @return bool
     */
    public function isHTTPS()
    {
        return (bool)$this->server['HTTPS'];
    }


    /**
     * Возвращает адрес текущего сервера без протокола
     * @return string
     */
    public function getCurrentHostName()
    {
        return $this->server['HTTP_HOST'];
    }


    /**
     * Возвращает адрес текущего сервера с протоколом
     * @return string
     */
    public function getCurrentHostURL()
    {
        return 'http' . ($this->isHTTPS() ? 's' : '') . '://' .
               $this->server['HTTP_HOST'];
    }
}
