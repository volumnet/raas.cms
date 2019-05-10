<?php
/**
 * Таблица шаблонов
 */
namespace RAAS\CMS;

use RAAS\Table;

/**
 * Класс таблицы шаблонов
 * @property-read ViewSub_Dev $view Представление
 */
class TemplatesTable extends Table
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
        $view = $this->view;
        $defaultParams = [
            'columns' => [
                'name' => [
                    'caption' => $this->view->_('NAME'),
                    'callback' => function ($row) use ($view) {
                        return '<a href="' . $view->url . '&action=edit_template&id=' . (int)$row->id . '">' .
                                  htmlspecialchars($row->name) .
                               '</a>';
                    }
                ],
                'urn' => [
                    'caption' => $this->view->_('URN'),
                    'callback' => function ($row) use ($view, $Item) {
                        return '<a href="' . $view->url . '&action=edit_template&id=' . (int)$row->id . '">' .
                                  htmlspecialchars($row->urn) .
                               '</a>';
                    }
                ],
                ' ' => [
                    'callback' => function (
                        $row,
                        $i
                    ) use (
                        $view,
                        $contextMenuName,
                        $IN
                    ) {
                        return rowContextMenu($view->getTemplateContextMenu(
                            $row,
                            $i,
                            count($IN['Set'])
                        ));
                    }
                ]
            ],
            'emptyString' => $this->view->_('NO_TEMPLATES_FOUND'),
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}
