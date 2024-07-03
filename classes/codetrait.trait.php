<?php
/**
 * Трейт сущности с кэшируемым кодом
 */
declare(strict_types=1);

namespace RAAS\CMS;

use Exception;
use phpDocumentor\Reflection\DocBlockFactory;
use RAAS\Application;

/**
 * Трейт сущности с кэшируемым кодом
 */
trait CodeTrait
{
    /**
     * Сохраняет файл
     */
    public function saveFile()
    {
        $this->prepareDir();
        $filename = $this->filename;
        if (!$filename) {
            return;
        }
        file_put_contents($filename, $this->description);
        chmod($filename, 0777);
    }


    /**
     * Удаляет файл
     */
    protected function deleteFile()
    {
        if ($this->id && $this->filename && is_file($this->filename)) {
            @unlink($this->filename);
        }
    }


    /**
     * Подготавливает директорию для файла
     */
    protected function prepareDir()
    {
        $dir = static::getDirName();
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        if (!is_file($dir . '/.htaccess')) {
            file_put_contents($dir . '/.htaccess', "Order deny,allow\nDeny from all");
            chmod($dir . '/.htaccess', 0755);
        }
    }


    public static function getDirName()
    {
        return Application::i()->baseDir . '/inc/snippets';
    }


    /**
     * Возвращает код
     * @return string
     */
    protected function _description(): string
    {
        if (!$this->filename || !is_file($this->filename)) {
            return '';
        }
        return file_get_contents($this->filename);
    }


    /**
     * Возвращает наименование
     * @return string
     */
    protected function _name(): string
    {
        if ($description = $this->description) {
            $tokens = token_get_all($description);
            $docBlockTexts = array_values(array_filter($tokens, function ($item) {
                return $item[0] == T_DOC_COMMENT;
            }));
            if ($docBlockTexts) {
                $docBlockText = $docBlockTexts[0][1];
                $docBlockFactory  = DocBlockFactory::createInstance();
                try {
                    $docBlock = $docBlockFactory->create($docBlockText);
                    $result = $docBlock->getSummary();
                    if (trim($result)) {
                        return trim($result);
                    }
                } catch (Exception $e) {
                }
            }
        }
        if ($this->urn) {
            return $this->urn;
        }
        return '';
    }
}
