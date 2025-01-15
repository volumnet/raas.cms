<?php
/**
 * Стандартный интерфейс меню
 */
declare(strict_types=1);

namespace RAAS\CMS;

use InvalidArgumentException;

/**
 * Стандартный интерфейс меню
 */
class MenuInterface extends BlockInterface
{
    /**
     * Конструктор класса
     * @param ?Block_Menu $block Блок, для которого применяется интерфейс
     * @param ?Page $page Страница, для которой применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @param array $post Поля $_POST параметров
     * @param array $cookie Поля $_COOKIE параметров
     * @param array $session Поля $_SESSION параметров
     * @param array $server Поля $_SERVER параметров
     * @param array $files Поля $_FILES параметров
     */
    public function __construct(
        ?Block_Menu $block = null,
        ?Page $page = null,
        array $get = [],
        array $post = [],
        array $cookie = [],
        array $session = [],
        array $server = [],
        array $files = []
    ) {
        parent::__construct(
            $block,
            $page,
            $get,
            $post,
            $cookie,
            $session,
            $server,
            $files
        );
    }


    public function process(): array
    {
        $out = [];
        $menu = $this->block->Menu;

        if (!(int)$this->block->full_menu) {
            $menu = $menu->findPage($this->getCurrentPage());
        }
        $out['Item'] = $menu;
        $out['menuArr'] = [
            'children' => $menu ? $this->getVisSubmenu($menu) : []
        ];
        return $out;
    }


    /**
     * Получает текущую страницу (с учетом AJAX'а)
     */
    public function getCurrentPage()
    {
        if (!(int)$this->block->full_menu && stristr($this->page->url, '/ajax/')) {
            return new Page($this->get['id']);
        }
        return $this->page;
    }


    /**
     * Получает видимое подменю
     * @param Menu|array $menu Входное меню или его копия
     * @return array<[
     *             'name' => string Наименование пункта,
     *             'url' => string URL пункта,
     *             'children' =>? array рекурсивно такой же массив
     *         ]>
     */
    public function getVisSubmenu($menu)
    {
        $st = microtime(true);
        $result = [];
        if ($menu instanceof Menu) {
            $menuData = $menu->getArrayCopy();
        } elseif (is_array($menu)) {
            $menuData = $menu;
        } else {
            throw new InvalidArgumentException('Menu must be of a class Menu or array');
        }
        // $subMenu = $menu->getSubMenu(true);
        $subMenu = Menu::getSubMenuData($menuData, true);
        foreach ($subMenu as $child) {
            $childData = [
                'url' => $child['url'],
                'name' => $child['name'],
                'children' => $this->getVisSubmenu($child)
            ];
            if ($child['page_id']) {
                $childData['page_id'] = (int)$child['page_id'];
                $pageCache = PageRecursiveCache::i()->cache[$child['page_id']] ?? [];
                if ($pageCache) {
                    // 2025-01-09, AVS: исправление ошибки - при изменении URL страницы адрес в меню автоматом не менялся
                    $childData['url'] = $pageCache['cache_url'];
                }
            }
            $result[] = $childData;
        }
        return $result;
    }
}
