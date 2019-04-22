<?php
/**
 * Файл трейта кэширования
 */
namespace RAAS\CMS;

/**
 * Трейт кэширования
 */
trait CacheTrait
{
    /**
     * Данные кэша
     * @var array
     */
    protected $data = array();

    /**
     * Возвращает данные кэша
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * Получает (обновляет) данные кэша
     */
    abstract public function getCache();

    /**
     * Получает имя файла основного кэша
     * @return string
     */
    abstract public function getFilename();


    /**
     * Получает имя файла временного кэша
     * @return string
     */
    public function getTmpFilename()
    {
        $filename = $this->getFilename();
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $tmpFilename = preg_replace(
            '/\\.' . preg_quote($ext, '/') . '$/umi',
            '.tmp$0',
            $filename
        );
        return $tmpFilename;
    }


    /**
     * Получает имя файла debug-версии кэша
     * @return string
     */
    public function getDebugFilename()
    {
        $filename = $this->getFilename();
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $debugFilename = preg_replace(
            '/\\.' . preg_quote($ext, '/') . '$/umi',
            '.debug$0',
            $filename
        );
        return $debugFilename;
    }


    public function load()
    {
        if (is_file($this->getFilename())) {
            $this->data = include $this->getFilename();
            return true;
        }
        return false;
    }


    public function save()
    {
        $cacheId = 'RAASCACHE' . date('YmdHis') . md5(rand());
        $text = '<' . '?php return unserialize(<<' . "<'" . $cacheId . "'\n" . serialize($this->data) . "\n" . $cacheId . "\n);\n";
        $debugText = '<' . '?php return ' . var_export((array)$this->data, true) . ";\n";

        $ok = (bool)file_put_contents($this->getTmpFilename(), $text);
        file_put_contents($this->getDebugFilename(), $debugText);
        if (file_exists($this->getTmpFilename())) {
            if (file_exists($this->getFilename())) {
                $ok &= unlink($this->getFilename());
            }
            $ok &= rename($this->getTmpFilename(), $this->getFilename());
        }
        return $ok;
    }


    public function clear()
    {
        $this->data = array();
    }
}
