<?php
/**
 * Файл класса меню
 */
namespace RAAS\CMS;

use SOME\SOME;

/**
 * Класс меню
 * @property-read string $url URL пункта меню
 * @property-read array<Menu> $visSubMenu Видимое подменю
 * @property-read array<Menu> $visChildren Видимые реальные дочерние пункты
 * @property-read array<Menu> $subMenu Подменю (реальные или виртуальные
 *                                     дочерние пункты)
 * @property-read Menu $parent Родительский элемент
 * @property-read Page $page Страница, к которой привязан пункт меню
 * @property-read array<Menu> $parents Набор родительских элементов, начиная с
 *                                     корневого
 * @property-read array<Menu> $children Набор дочерних элементов
 */
class Menu extends SOME
{
    use RecursiveTrait;

    protected static $tablename = 'cms_menus';

    protected static $defaultOrderBy = "priority";

    protected static $cognizableVars = [
        'subMenu',
        'visSubMenu',
        'selfAndChildren',
        'selfAndChildrenIds',
        'selfAndParents',
        'selfAndParentsIds',
    ];

    protected static $references = [
        'parent' => [
            'FK' => 'pid',
            'classname' => Menu::class,
            'cascade' => true
        ],
        'page' => [
            'FK' => 'page_id',
            'classname' => Page::class,
            'cascade' => true
        ]
    ];

    protected static $parents = ['parents' => 'parent'];

    protected static $children = [
        'children' => ['classname' => Menu::class, 'FK' => 'pid']
    ];

    protected static $caches = [
        'pvis' => [
            'affected' => ['parent'],
            'sql' => "IF(parent.id, (parent.vis AND parent.pvis), 1)",
        ]
    ];

    public function __get($var)
    {
        switch ($var) {
            case 'url':
                // 2015-09-23, AVS: сделал, чтобы при переносе страницы
                // URL сохранялся
                if ($this->page->id) {
                    return $this->page->url;
                }
                return parent::__get($var);
                break;
            case 'visChildren':
                return array_values(array_filter(
                    $this->children,
                    function ($x) {
                        return $x->vis;
                    }
                ));
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function commit()
    {
        if ($this->page_id) {
            if ($this->page->id) {
                $this->url = $this->page->url;
                if (!$this->name) {
                    $this->name = $this->page->getMenuName();
                }
            } else {
                $this->url = '';
                $this->page_id = 0;
            }
        }
        if (!$this->pid) {
            if (!$this->urn && $this->name) {
                $this->urn = $this->name;
            }
            Package::i()->getUniqueURN($this);
        } elseif (!$this->domain_id && $this->parent->domain_id) {
            $this->domain_id = $this->parent->domain_id;
        }
        if ($this->id && ($this->updates['domain_id'] != $this->properties['domain_id'])) {
            $updateChildrenDomain = true;
        }
        parent::commit();
        if ($updateChildrenDomain) {
            foreach ($this->children as $child) {
                $child->domain_id = $this->domain_id;
                $child->commit();
            }
        }
    }


    /**
     * Распаковывает меню
     *
     * Делает все дочерние виртуальные пункты реальными с уровнем наследования
     * на 1 ниже текущего, у текущего делает уровень наследования 0
     */
    public function realize()
    {
        $realized = [];
        if ($this->page->id && ($this->inherit > 0)) {
            $i = 0;
            foreach ($this->children as $child) {
                if ($child->page_id) {
                    $realized[(string)$child->page_id] = $child->page_id;
                }
            }
            foreach ($this->page->visChildren as $childPage) {
                if (!isset($realized[$childPage->id]) &&
                    !$childPage->response_code
                ) {
                    $row = new Menu();
                    $row->pid = $this->id;
                    $row->vis = ($childPage->vis && $childPage->pvis);
                    $row->pvis = ($this->vis && $this->pvis);
                    $row->name = $childPage->getMenuName();
                    $row->url = $childPage->url;
                    $row->page_id = $childPage->id;
                    $row->inherit = $this->inherit - 1;
                    $row->priority = $i++;
                    $row->realized = false;
                    $row->commit();
                }
            }
            $this->inherit = 0;
            if (!$this->pid) {
                $this->page_id = 0;
            }
            $this->commit();
        }
        $this->rollback();
    }


    /**
     * Находит пункт меню по странице
     * @param Page $page Страница для поиска
     * @return Menu|false Найденный пункт или false, если не найден
     */
    public function findPage(Page $page)
    {
        if (($this->page_id == $page->id) || ($this->url == $page->url)) {
            return $this;
        }
        foreach ($this->visSubMenu as $row) {
            if ($row2 = $row->findPage($page)) {
                return $row2;
            }
        }
        return false;
    }


    /**
     * Импортирует сущность по URN
     * @param string $urn URN для импорта
     * @return static
     */
    public static function importByURN($urn = '')
    {
        $sqlQuery = "SELECT * FROM " . static::_tablename() . " WHERE urn = ?";
        $sqlResult = static::$SQL->getline([$sqlQuery, $urn]);
        if ($sqlResult) {
            return new static($sqlResult);
        }
        return null;
    }


    /**
     * Подменю (с учетом виртуальных пунктов)
     * @return array<Menu>
     */
    protected function _subMenu()
    {
        return $this->getSubMenu(false);
    }


    /**
     * Видимое подменю (с учетом виртуальных пунктов)
     * @return array<Menu>
     */
    protected function _visSubMenu()
    {
        return $this->getSubMenu(true);
    }


    /**
     * Получает подменю (с учетом виртуальных пунктов)
     * @param bool $visOnly Возвращать только видимые/доступные пункты
     * @return array<Menu>
     */
    public function getSubMenu($visOnly = false)
    {
        $temp = [];
        $realized = [];
        $id = (int)$this->id;
        $cache = MenuRecursiveCache::i();
        if ($id) {
            if ($visOnly) {
                $childrenIds = $cache->visChildrenIds[$id];
            } else {
                $childrenIds = $cache->childrenIds[$id];
            }
            foreach ($childrenIds as $childId) {
                $childData = $cache->cache[$childId];
                if ($pageId = $childData['page_id']) {
                    $realized[(string)$pageId] = $pageId;
                }
                $temp[] = new Menu($childData);
            }
        }
        $pvis = ($this->vis && $this->pvis);
        $inherit = $this->inherit;
        $pageId = $this->page_id;
        $cache = PageRecursiveCache::i();
        if (($inherit > 0) && $pageId) {
            $i = 0;
            if ($visOnly) {
                $childrenPagesIds = $cache->visChildrenIds[$pageId];
            } else {
                $childrenPagesIds = $cache->childrenIds[$pageId];
            }
            foreach ($childrenPagesIds as $childPageId) {
                $childPageData = $cache->cache[$childPageId];
                if (!isset($realized[$childPageData['id']]) && !$childPageData['response_code']) {
                    $row = [
                        'pid' => $id,
                        'vis' => true,
                        'pvis' => $pvis,
                        'name' => trim($childPageData['menu_name']) ?:
                                  trim($childPageData['name']),
                        'url' => $childPageData['cache_url'],
                        'page_id' => $childPageId,
                        'inherit' => $inherit - 1,
                        'priority' => $childPageData['priority'],
                        'realized' => false,
                    ];
                    $temp[] = new Menu($row);
                }
            }
        }
        return $temp;
    }
}
