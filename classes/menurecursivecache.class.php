<?php
/**
 * Файл рекурсивного кэша меню
 */
namespace RAAS\CMS;

use SOME\Singleton;

/**
 * Класс рекурсивного кэша меню
 * @property-read array<
 *                    string ID# страницы =>
 *                    int ID# страницы
 *                > $allowedIds Набор ID# пунктов меню, доступных для текущего
 *                              пользователя
 */
class MenuRecursiveCache extends VisibleRecursiveCache
{
    protected static $instance;

    protected static $classname = Menu::class;

    /**
     * Набор ID# пунктов меню, доступных для текущего пользователя
     * @var array<string ID# страницы => int ID# страницы>
     */
    protected $allowedIds = [];

    public function __get($var)
    {
        switch ($var) {
            case 'allowedIds':
                return $this->$var;
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    protected function setCache()
    {
        $this->cache = [];
        $classname = static::$classname;
        $sqlQuery = "SELECT * FROM " . $classname::_tablename();
        if ($defaultOrderBy = $classname::_defaultOrderBy()) {
            $sqlQuery .= " ORDER BY " . $defaultOrderBy;
        }
        $sqlResult = $classname::_SQL()->get($sqlQuery);
        foreach ($sqlResult as $sqlRow) {
            $sqlRow['realized'] = true;
            $this->cache[(string)$sqlRow['id']] = $sqlRow;
        }
    }


    protected function setVisibleIds()
    {
        parent::setVisibleIds();
        $this->allowedIds = [];
        $allowedPageIds = PageRecursiveCache::i()->allowedIds;
        foreach ($this->cache as $id => $cacheData) {
            if (!(int)$cacheData['page_id'] ||
                isset($allowedPageIds[$cacheData['page_id']])
            ) {
                $this->allowedIds[(string)$id] = (int)$id;
            }
        }
        $this->visibleIds = array_intersect_key(
            $this->visibleIds,
            $this->allowedIds
        );
    }


    public function refresh()
    {
        PageRecursiveCache::i()->refresh();
        parent::refresh();
    }
}
