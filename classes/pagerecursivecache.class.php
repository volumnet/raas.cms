<?php
/**
 * Файл рекурсивного кэша страниц
 */
namespace RAAS\CMS;

use SOME\Singleton;

/**
 * Класс рекурсивного кэша страниц
 * @property-read array<
 *                    string ID# страницы =>
 *                    int ID# страницы
 *                > $allowedIds Набор ID# страниц, доступных для текущего
 *                              пользователя
 * @property-read array<
 *                    string ID# страницы =>
 *                    int ID# страницы
 *                > $systemIds Набор ID# служебных страниц
 */
class PageRecursiveCache extends VisibleRecursiveCache
{
    protected static $instance;

    protected static $classname = Page::class;

    /**
     * Набор ID# страниц, доступных для текущего пользователя
     * @var array<string ID# страницы => int ID# страницы>
     */
    protected $allowedIds = [];

    /**
     * Набор ID# служебных страниц
     * @var array<string ID# страницы => int ID# страницы>
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
            $st = microtime(1);
            $this->load();
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
    public function updateNeeded()
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
    public function getFilename()
    {
        return Package::i()->cacheDir . '/system/pagerecursivecache.php';
    }


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
     * Записывает данные в файл
     * @return bool Удалось ли записать данные
     */
    public function save()
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
        ] as $key) {
            $data[$key] = $this->$key;
        }

        $cacheId = 'RAASCACHE' . date('YmdHis') . md5(rand());
        $text = '<' . '?php return unserialize(<<' . "<'" . $cacheId . "'\n"
              . serialize($data) . "\n" . $cacheId . "\n);\n";

        $ok = (bool)file_put_contents($this->getTmpFilename(), $text);
        $filename = $this->getFilename();
        $tmpname = $this->getTmpFilename();
        if (file_exists($tmpname)) {
            if (file_exists($filename)) {
                $ok &= unlink($filename);
            }
            $ok &= rename($tmpname, $filename);
        }
        return $ok;
    }


    /**
     * Загружает данные из файла
     * @return bool Удалось ли загрузить данные
     */
    public function load()
    {
        if (is_file($this->getFilename())) {
            $data = include $this->getFilename();
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
            ] as $key) {
                $this->$key = $data[$key];
            }
            return true;
        }
        return false;
    }
}
