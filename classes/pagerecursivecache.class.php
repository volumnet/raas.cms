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
}
