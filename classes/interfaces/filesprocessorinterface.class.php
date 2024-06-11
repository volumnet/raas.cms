<?php
/**
 * Интерфейс обработчика файлов
 */
declare(strict_types=1);

namespace RAAS\CMS;

/**
 * Интерфейс обработчика файлов
 */
abstract class FilesProcessorInterface extends AbstractInterface
{
    /**
     * Конструктор класса
     * @param array $get Поля $_GET параметров
     * @param array $post Поля $_POST параметров
     * @param array $cookie Поля $_COOKIE параметров
     * @param array $session Поля $_SESSION параметров
     * @param array $server Поля $_SERVER параметров
     * @param array $files Поля $_FILES параметров
     */
    public function __construct(
        array $get = [],
        array $post = [],
        array $cookie = [],
        array $session = [],
        array $server = [],
        array $files = []
    ) {
        parent::__construct(null, null, $get, $post, $cookie, $session, $server, $files);
    }


    /**
     * Обработка интерфейса
     * @param string[] $files Файлы для обработки
     */
    abstract public function process(array $files = []);
}
