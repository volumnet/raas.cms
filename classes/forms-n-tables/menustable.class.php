<?php
/**
 * Таблица меню
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\Table;
use RAAS\Row;

/**
 * Класс таблицы меню
 * @property-read ViewSub_Dev $view Представление
 */
class MenusTable extends Table
{
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Dev::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct(array $params = [])
    {
        $item = $params['Item'];
        $defaultParams = [
            'columns' => [],
            'emptyString' => $this->view->_('NO_NOTES_FOUND'),
            'meta' => [
                'realizedCounter' => count(array_filter((array)($params['Set'] ?? []), fn($x) => $x->realized)),
            ]
        ];
        if ($item->id) {
            if ($defaultParams['meta']['realizedCounter']) {
                $defaultParams['data-role'] = 'multitable';
                $defaultParams['meta']['allContextMenu'] = $this->view->getAllMenusContextMenu();
                $defaultParams['meta']['allValue'] = 'all&pid=' . (int)$params['Item']->id;
                $defaultParams['meta']['priorityColumn'] = 'priority';
            }
        }
        $defaultParams['columns']['id'] = [
            'caption' => $this->view->_('ID'),
            'callback' => function (Menu $menu) use ($item) {
                $text = (int)$menu->id ?: '';
                if ($menu->realized || !$item->id) {
                    $this->meta['realizedCounter'] = $this->meta['realizedCounter'] + 1;
                    return '<a href="' . $this->getEditURL($menu) . '" class="' . $this->getLinkClass($menu) . '">
                              ' . $text . '
                            </a>';
                } else {
                    return $text;
                }
            }
        ];
        $defaultParams['columns']['name'] = [
            'caption' => $this->view->_('NAME'),
            'callback' => function (Menu $menu) use ($item) {
                $text = htmlspecialchars($menu->name);
                if ($menu->realized || !$item->id) {
                    return '<a href="' . $this->getEditURL($menu) . '" class="' . $this->getLinkClass($menu) . '">
                              ' . $text . '
                            </a>';
                } else {
                    return $text;
                }
            }
        ];
        if (!$item->id) {
            $defaultParams['columns']['urn'] = [
                'caption' => $this->view->_('URN'),
                'callback' => function (Menu $menu) use ($item) {
                    $text = htmlspecialchars($menu->urn);
                    if ($menu->realized || !$item->id) {
                        return '<a href="' . $this->getEditURL($menu) . '" class="' . $this->getLinkClass($menu) . '">
                                  ' . $text . '
                                </a>';
                    } else {
                        return $text;
                    }
                }
            ];
        }
        $defaultParams['columns']['url'] = [
            'caption' => $this->view->_('URL'),
            'callback' => function (Menu $menu) use ($item) {
                $text = htmlspecialchars($menu->url);
                if ($menu->realized || !$item->id) {
                    return '<span class="' . (!$menu->vis ? ' muted' : '') . ($menu->pvis ? '' : ' cms-inpvis') . '">
                              ' . $text . '
                            </span>';
                } else {
                    return $text;
                }
            }
        ];
        if (!$item->id) {
            $defaultParams['columns']['usage'] = [
                'caption' => $this->view->_('USAGE'),
                'callback' => function (Menu $menu, $i) use ($item) {
                    if ($menu->usage) {
                        return (int)$menu->usage;
                    }
                }
            ];
        }
        $defaultParams['columns']['priority'] = [
            'caption' => $this->view->_('PRIORITY'),
            'callback' => function (Menu $menu, $i) use ($item) {
                if ($menu->realized || !$item->id) {
                    return '<input type="text" class="span1" maxlength="3" name="priority[' . (int)$menu->id . ']" value="' . (($i + 1) * 10) . '" />';
                } else {
                    return htmlspecialchars((string)$menu->priority);
                }
            }
        ];
        $defaultParams['columns'][' '] = [
            'callback' => function (Menu $menu, $i) use ($item, $params) {
                if ($menu->realized || !$item->id) {
                    return rowContextMenu($this->view->getMenuContextMenu(
                        $menu,
                        $i,
                        count((array)$params['Set'])
                    ));
                }
            }
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }


    /**
     * Получает URL редактирования меню
     * @param Menu $menu Меню для редактирования
     * @return string
     */
    public function getEditURL(Menu $menu)
    {
        $url = $this->view->url . '&action=menus&id=' . (int)$menu->id;
        return $url;
    }


    /**
     * Получает класс ссылки
     * @param Menu $menu Меню для получения класса ссылки
     * @return string
     */
    public function getLinkClass(Menu $menu)
    {
        $text = (!$menu->vis ? ' muted' : '')
              . ($menu->pvis ? '' : ' cms-inpvis');
        return $text;
    }
}
