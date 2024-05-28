<?php
/**
 * Файл класса меню
 */
declare(strict_types=1);

namespace RAAS\CMS;

use SOME\SOME;

/**
 * Класс меню
 * @property-read array<Menu> $visSubMenu Видимое подменю
 * @property-read array<Menu> $subMenu Подменю (реальные или виртуальные
 *                                     дочерние пункты)
 * @property-read Menu $parent Родительский элемент
 * @property-read Page $page Страница, к которой привязан пункт меню
 * @property-read array<Menu> $parents Набор родительских элементов, начиная с
 *                                     корневого
 * @property-read array<Menu> $children Набор дочерних элементов
 * @property-read string $url URL пункта меню
 * @property-read array<Menu> $visChildren Видимые реальные дочерние пункты
 * @property-read Block_Menu[] $usingBlocks Блоки, использующие меню
 */
class Menu extends SOME
{
    use RecursiveTrait;
    use ImportByURNTrait;

    protected static $tablename = 'cms_menus';

    protected static $defaultOrderBy = "priority";

    protected static $cognizableVars = [
        'subMenu',
        'visSubMenu',
        'selfAndChildren',
        'selfAndChildrenIds',
        'selfAndParents',
        'selfAndParentsIds',
        'usingBlocks',
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
        $updateChildrenDomain = false;
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
        } elseif (/*!$this->domain_id && */$this->parent->id) {
            $this->domain_id = $this->parent->domain_id;
        }
        if ($this->id && (($this->updates['domain_id'] ?? 0) != ($this->properties['domain_id'] ?? 0))) {
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
        $pageData = $page->getArrayCopy();
        if (($this->page_id == $pageData['id']) || ($this->url == $pageData['cache_url'])) {
            $this->realized = true;
            return $this;
        }
        $subMenuData = static::getSubMenuData($this->getArrayCopy(), true);
        $result = static::findPageBySubMenuData($subMenuData, $pageData);
        if ($result) {
            return new Menu($result);
        }
        return false;
    }


    public static function findPageBySubMenuData(array $subMenuData, array $pageData)
    {
        foreach ($subMenuData as $row) {
            if (($row['page_id'] == $pageData['id']) || ($row['url'] == $pageData['cache_url'])) {
                return $row;
            }
            $childData = static::getSubMenuData($row, true);
            if ($childData) {
                if ($childResult = static::findPageBySubMenuData($childData, $pageData)) {
                    return $childResult;
                }
            }
        }
        return false;
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
        $subMenuData = static::getSubMenuData($this->getArrayCopy(), $visOnly);
        $result = Menu::getArraySet($subMenuData);
        return $result;
    }


    public static function getSubMenuData($menuData, $visOnly = false)
    {
        $result = [];
        $realized = [];
        $id = (int)($menuData['id'] ?? 0);
        $pvis = (($menuData['vis'] ?? false) && ($menuData['pvis'] ?? false));
        $inherit = (int)($menuData['inherit'] ?? 0);
        $pageId = (int)($menuData['page_id'] ?? 0);
        $cache = MenuRecursiveCache::i();
        if ($id) {
            if ($visOnly) {
                $childrenIds = $cache->visChildrenIds[$id];
            } else {
                $childrenIds = $cache->childrenIds[$id];
            }
            foreach ($childrenIds as $childId) {
                $childData = $cache->cache[$childId];
                if ($pageId = (int)($childData['page_id'] ?? 0)) {
                    $realized[(string)$pageId] = $pageId;
                }
                $result[] = $childData;
            }
        }
        $cache = PageRecursiveCache::i();
        if (($inherit > 0) && $pageId) {
            $i = 0;
            if ($visOnly) {
                $childrenPagesIds = $cache->visChildrenIds[$pageId] ?? [];
            } else {
                $childrenPagesIds = $cache->childrenIds[$pageId] ?? [];
            }
            foreach ($childrenPagesIds as $childPageId) {
                $childPageData = $cache->cache[$childPageId];
                if (!isset($realized[$childPageData['id']]) && !($childPageData['response_code'] ?? null)) {
                    $row = [
                        'pid' => $id,
                        'vis' => true,
                        'pvis' => $pvis,
                        'name' => trim($childPageData['menu_name'] ?? '') ?:
                                  trim($childPageData['name'] ?? ''),
                        'url' => $childPageData['cache_url'] ?? '',
                        'page_id' => $childPageId,
                        'inherit' => $inherit - 1,
                        'priority' => $childPageData['priority'] ?? 0,
                        'realized' => false,
                    ];
                    $result[] = $row;
                }
            }
        }
        return $result;
    }


    public static function delete(SOME $object)
    {
        $id = (int)$object->id;
        parent::delete($object);
        // 2020-05-07, AVS: Удаление блоков делаем после основного,
        // иначе в методе SOME:ondelete класс Block_Menu подхватывается
        // в качестве ссылки, а поскольку там ссылка на Menu идет из вторичной
        // таблицы, возникает ошибка MySQL
        $sqlQuery = "SELECT id
                      FROM " . Block::_dbprefix() . "cms_blocks_menu
                     WHERE menu = ?";
        $blocksIds = Block_Menu::_SQL()->getcol([$sqlQuery, (int)$id]);
        foreach ($blocksIds as $blockId) {
            $block = new Block_Menu($blockId);
            Block_Menu::delete($block);
        }
    }


    /**
     * Блоки, использующие это меню
     * @return Block_Menu[]
     */
    protected function _usingBlocks()
    {
        $blockMenuReferences = Block_Menu::_references();
        $blockMenuMenuMatchingReferences = array_values(array_filter($blockMenuReferences, function ($x) {
            return $x['classname'] == Menu::class;
        }));
        $blockMenuMenuReference = $blockMenuMenuMatchingReferences[0];
        $sqlQuery = "SELECT tB." . Block::_idN() . "
                       FROM " . Block_Menu::_tablename() . " AS tB
                       JOIN " . Block_Menu::_tablename2() . " AS tBM ON tBM." . Block::_idN() . " = tB." . Block::_idN() . "
                      WHERE tBM." . $blockMenuMenuReference['FK'] . " = " . (int)$this->id
                  . " ORDER BY tB." . Block::_idN();
        $sqlResult = Block::_SQL()->getcol($sqlQuery);
        $result = [];
        foreach ($sqlResult as $sqlVal) {
            $result[] = Block::spawn($sqlVal);
        }
        return $result;
    }
}
