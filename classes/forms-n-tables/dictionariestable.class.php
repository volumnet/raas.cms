<?php
/**
 * Таблица справочников
 */
namespace RAAS\CMS;

use RAAS\Table;

/**
 * Класс таблицы справочников
 * @property-read ViewSub_Dev $view Представление
 */
class DictionariesTable extends Table
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
        $columns = [];
        $columns['name'] = [
            'caption' => $this->view->_('NAME'),
            'callback' => function ($row) use ($view) {
                return '<a href="' . $view->url . '&action=dictionaries&id=' . (int)$row->id . '" class="' . (!$row->vis ? ' muted' : '') . ($row->pvis ? '' : ' cms-inpvis') . '">'
                     .    htmlspecialchars($row->name)
                     . '</a>';
            }
        ];
        $columns['urn'] = [
            'caption' => $this->view->_($params['Item']->id ? 'VALUE' : 'URN'),
            'callback' => function ($row) use ($view) {
                return '<a href="' . $view->url . '&action=dictionaries&id=' . (int)$row->id . '" class="' . (!$row->vis ? ' muted' : '') . ($row->pvis ? '' : ' cms-inpvis') . '">'
                     .    htmlspecialchars($row->urn)
                     . '</a>';
            }
        ];
        $columns['priority'] = [
            'caption' => $this->view->_('PRIORITY'),
            'callback' => function ($row) {
                return '<input type="number" name="priority[' . (int)$row->id . ']" value="' . ($row->priority ? (int)$row->priority : '') . '" class="span1" min="0" />';
            }
        ];
        $columns[' '] = [
            'callback' => function ($row, $i) use ($view, $params) {
                return rowContextMenu($view->getDictionaryContextMenu(
                    $row,
                    $i,
                    count($params['Set'])
                ));
            }
        ];
        $defaultParams = [
            'emptyString' => $this->view->_('NO_NOTES_FOUND'),
            'template' => 'dev_dictionaries',
            'data-role' => 'multitable',
            'meta' => [
                'allContextMenu' => $view->getAllDictionariesContextMenu(),
                'allValue' => 'all&pid=' . (int)$params['Item']->id,
            ],
        ];
        $arr = array_merge($defaultParams, $params);
        $arr['columns'] = $columns;
        parent::__construct($arr);
    }
}
