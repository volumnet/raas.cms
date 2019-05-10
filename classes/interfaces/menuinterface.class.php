<?php
/**
 * Файл класса интерфейса меню
 */
namespace RAAS\CMS;

/**
 * Класс интерфейса меню
 */
class MenuInterface extends AbstractInterface
{
    /**
     * Конструктор класса
     * @param Block_Menu|null $block Блок, для которого применяется интерфейс
     * @param Page|null $page Страница, для которой применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @param array $post Поля $_POST параметров
     * @param array $cookie Поля $_COOKIE параметров
     * @param array $session Поля $_SESSION параметров
     * @param array $server Поля $_SERVER параметров
     * @param array $files Поля $_FILES параметров
     */
    public function __construct(
        Block_Menu $block = null,
        Page $page = null,
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


    public function process()
    {
        $out = [];
        $menu = $this->block->Menu;
        if (!(int)$this->block->full_menu) {
            $menu = $menu->findPage($this->page);
        }
        $out['Item'] = $menu;
        $out['menuArr'] = ['children' => $this->getVisSubmenu($menu)];
        return $out;
    }


    /**
     * Получает видимое подменю
     * @param Menu $menu Входное меню
     * @return array<[
     *             'name' => string Наименование пункта,
     *             'url' => string URL пункта,
     *             'children' =>? array рекурсивно такой же массив
     *         ]>
     */
    public function getVisSubmenu(Menu $menu)
    {
        $result = [];
        $subMenu = $menu->visSubMenu;
        foreach ($subMenu as $child) {
            $childData = [
                'url' => $child->url,
                'name' => $child->name,
                'children' => $this->getVisSubmenu($child)
            ];
            if ($child->page_id) {
                $childData['page_id'] = (int)$child->page_id;
            }
            $result[] = $childData;
        }
        return $result;
    }
}
