<?php
/**
 * Файл класса абстрактного интерфейса CMS
 */
namespace RAAS\CMS;

/**
 * Класса абстрактного интерфейса CMS
 */
abstract class AbstractInterface
{
    /**
     * Блок, для которого применяется интерфейс
     * @var Block
     */
    protected $block;

    /**
     * Страница, для которой применяется интерфейс
     * @var Page|null
     */
    protected $page = null;

    /**
     * Поля $_GET параметров
     * @var array
     */
    protected $get = array();

    /**
     * Поля $_POST параметров
     * @var array
     */
    protected $post = array();

    /**
     * Поля $_COOKIE параметров
     * @var array
     */
    protected $cookie = array();

    /**
     * Поля $_SESSION параметров
     * @var array
     */
    protected $session = array();

    /**
     * Поля $_SERVER параметров
     * @var array
     */
    protected $server = array();

    /**
     * Поля $_FILES параметров
     * @var array
     */
    protected $files = array();

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
    public function __construct(Block $block = null, Page $page = null, array $get = array(), array $post = array(), array $cookie = array(), array $session = array(), array $server = array(), array $files = array())
    {
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
        return ($this->server['HTTPS'] == 'on');
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
        return 'http' . ($this->isHTTPS() ? 's' : '') . '://' . $this->server['HTTP_HOST'];
    }
}
