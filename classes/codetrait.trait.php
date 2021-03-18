<?php
/**
 * Трейт сущности с кэшируемым кодом
 */
namespace RAAS\CMS;

/**
 * Трейт сущности с кэшируемым кодом
 */
trait CodeTrait
{
    /**
     * Сохраняет кэш-файл
     */
    public function saveFile()
    {
        if (!$this->id) {
            return;
        }
        $filename = $this->filename;
        $dir = dirname($filename);
        if (!is_dir($dir)) {
            $this->prepareDir();
        }
        file_put_contents($filename, $this->description);
    }


    /**
     * Удаляет кэш-файл
     */
    protected function deleteFile()
    {
        if (!$this->id) {
            return;
        }
        unlink($this->filename);
    }


    /**
     * Проверяет, требуется ли обновление кэш-файла
     * @return bool
     */
    public function updateNeeded()
    {
        $filename = $this->filename;
        if (!is_file($filename)) {
            return true;
        }
        $mt = strtotime($this->modify_date);
        $ft = filemtime($filename);
        if ($mt > $ft) {
            return true;
        }
        return false;
    }


    /**
     * Подготавливает директорию для кэш-файла
     */
    protected function prepareDir()
    {
        $filename = $this->filename;
        $dir = dirname($filename);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
            file_put_contents(
                $dir . '/.htaccess',
                "Order deny,allow\nDeny from all"
            );
            chmod($dir . '/.htaccess', 0755);
        }
    }
}
