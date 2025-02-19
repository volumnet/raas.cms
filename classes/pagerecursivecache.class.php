<?php
/**
 * Файл рекурсивного кэша страниц
 */
declare(strict_types=1);

namespace RAAS\CMS;

use Error;
use SOME\Singleton;

/**
 * Класс рекурсивного кэша страниц
 * @property-read array <pre><code>array<
 *     string ID# страницы =>
 *     int ID# страницы
 * ></code></pre> $allowedIds Набор ID# страниц, доступных для текущего пользователя
 * @property-read array <pre><code>array<
 *     string ID# страницы =>
 *     int ID# страницы
 * ></code></pre> $systemIds Набор ID# служебных страниц
 */
class PageRecursiveCache extends VisibleRecursiveCache
{
    protected static $instance;

    protected static $classname = Page::class;

    /**
     * Набор ID# страниц, доступных для текущего пользователя
     * @var array <pre><code>array<string ID# страницы => int ID# страницы></code></pre>
     */
    protected $allowedIds = [];

    /**
     * Набор ID# служебных страниц
     * @var array <pre><code>array<string ID# страницы => int ID# страницы></code></pre>
     */
    protected $systemIds = [];

    public function __get($var)
    {
        switch ($var) {
            case 'allowedIds':
            case 'systemIds':
                return $this->$var;
                break;
            default:
                return parent::__get($var);
                break;
        }
    }

    protected function init()
    {
        if ($this->updateNeeded()) {
            $this->refresh();
            $this->save();
        } else {
            $st = microtime(true);
            $this->load();
            // var_dump(microtime(true) - $st); exit;
        }
    }


    protected function setVisibleIds()
    {
        parent::setVisibleIds();
        $this->allowedIds = $this->systemIds = [];
        $sqlQuery = "SELECT page_id, allow
                       FROM cms_access_pages_cache
                      WHERE uid = ?";
        $uid = (int)Controller_Frontend::i()->user->id;
        $allowedIds['0'] = 1;
        $sqlResult = Page::_SQL()->get([$sqlQuery, $uid]);
        foreach ($sqlResult as $sqlRow) {
            $allowedIds[(string)$sqlRow['page_id']] = (int)$sqlRow['allow'];
        }
        $ch = array_filter(['0' => $this->childrenIds['0']]);
        while ($ch) {
            $newCh = [];
            foreach ($ch as $pid => $chIds) {
                foreach ($chIds as $chId) {
                    if (!isset($allowedIds[$chId])) {
                        $allowedIds[$chId] = $allowedIds[$pid];
                    }
                    $newCh[$chId] = $this->childrenIds[$chId];
                }
            }
            $ch = $newCh;
        };
        $allowedIds = array_keys(array_filter($allowedIds, function ($x) {
            return $x > 0;
        }));
        foreach ($allowedIds as $id) {
            if ((int)$id) {
                $this->allowedIds[(string)$id] = (int)$id;
            }
        }
        $this->visibleIds = array_intersect_key(
            $this->visibleIds,
            $this->allowedIds
        );

        foreach ($this->cache as $id => $cacheData) {
            if ((int)$cacheData['response_code']) {
                $this->systemIds[(string)$id] = (int)$id;
            }
        }
    }


    /**
     * Определяет, требуется ли обновление
     * @return bool
     */
    public function updateNeeded(): bool
    {
        $filename = $this->getFilename();
        if (!is_file($filename)) {
            return true;
        }
        $ft = filemtime($filename);
        $sqlQuery = "SELECT MAX(UNIX_TIMESTAMP(modify_date))
                       FROM " . Page::_tablename()
                  . " WHERE 1";
        $lastModified = Material::_SQL()->getvalue($sqlQuery);

        return $lastModified > $ft;
    }


    /**
     * Получает имя файла основного кэша
     * @return string
     */
    public function getFilename(): string
    {
        return Package::i()->cacheDir . '/system/pagerecursivecache.php';
    }


    /**
     * Получает имя файла временного кэша
     * @return string
     */
    public function getTmpFilename(): string
    {
        $filename = $this->getFilename();
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $tmpFilename = preg_replace('/\\.' . preg_quote($ext, '/') . '$/umi', '.tmp$0', $filename);
        return $tmpFilename;
    }


    /**
     * Записывает данные в файл
     * @return bool Удалось ли записать данные
     */
    public function save(): bool
    {
        $data = [];
        foreach ([
            'cache',
            'parentId',
            'parentsIds',
            'selfAndParentsIds',
            'childrenIds',
            'allChildrenIds',
            'selfAndChildrenIds',
            'allowedIds',
            'systemIds',
            'visibleIds',
            'visChildrenIds',
            'visAllChildrenIds',
        ] as $key) {
            $data[$key] = $this->$key;
        }

        $cacheId = 'RAASCACHE' . date('YmdHis') . md5((string)rand());
        $text = '<' . '?php return unserialize(<<' . "<'" . $cacheId . "'\n"
              . serialize($data) . "\n" . $cacheId . "\n);\n";

        $filename = $this->getFilename();
        $tmpname = $this->getTmpFilename();

        if (!@file_put_contents($tmpname, $text)) {
            return false;
        }
        if (file_exists($filename)) {
            if (!@unlink($filename)) {
                return false;
            }
        }
        // @codeCoverageIgnoreStart
        // 2024-04-09, AVS: Не могу проверить некорректное переименование - ошибка возникает только при конфликте прав
        // доступа, который в рамках теста воспроизвести затруднительно
        if (!is_file($tmpname) || !rename($tmpname, $filename)) {
            return false;
        }
        // @codeCoverageIgnoreEnd
        return true;
    }


    /**
     * Загружает данные из файла
     * @return bool Удалось ли загрузить данные
     */
    public function load(): bool
    {
        if (is_file($this->getFilename())) {
            $data = [];
            try {
                $data = include $this->getFilename();
            } catch (Error $e) {
            }
            foreach ([
                'cache',
                'parentId',
                'parentsIds',
                'selfAndParentsIds',
                'childrenIds',
                'allChildrenIds',
                'selfAndChildrenIds',
                'allowedIds',
                'systemIds',
                'visibleIds',
                'visChildrenIds',
                'visAllChildrenIds',
            ] as $key) {
                $this->$key = $data[$key] ?? [];
            }
            return true;
        }
        return false;
    }
}
