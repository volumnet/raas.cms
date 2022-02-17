<?php
/**
 * Таблица типов материалов
 */
namespace RAAS\CMS;

use RAAS\Table;

/**
 * Класс таблицы типов материалов
 * @property-read ViewSub_Dev $view Представление
 */
class MaterialTypesTable extends Table
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
        $f = function (Material_Type $node) use (&$f) {
            static $level = 0;
            $Set = [];
            foreach ($node->children as $row) {
                $row->level = $level;
                $Set[] = $row;
                $level++;
                $Set = array_merge($Set, $f($row));
                $level--;
            }
            return $Set;
        };
        $columns = [];
        $columns['id'] = [
            'caption' => $this->view->_('ID'),
            'callback' => function ($row) use ($view) {
                return '<a href="' . $view->url . '&action=edit_material_type&id=' . (int)$row->id . '">' .
                          (int)$row->id .
                       '</a>';
            }
        ];
        $columns['name'] = [
            'caption' => $this->view->_('NAME'),
            'callback' => function ($row) use ($view) {
                return '<a style="padding-left: ' . ($row->level * 30) . 'px" href="' . $view->url . '&action=edit_material_type&id=' . (int)$row->id . '">' .
                          htmlspecialchars($row->name) .
                       '</a>';
            }
        ];
        $columns['urn'] = ['caption' => $this->view->_('URN')];
        $columns['global_type'] = [
            'caption' => $this->view->_('IS_GLOBAL_TYPE'),
            'title' => $this->view->_('GLOBAL_MATERIALS'),
            'callback' => function ($row) {
                return $row->global_type ? '<i class="icon-ok"></i>' : '';
            }
        ];
        $columns[' '] = [
            'callback' => function ($row) use ($view) {
                return rowContextMenu($view->getMaterialTypeContextMenu($row));
            }
        ];
        $defaultParams = [
            'emptyString' => $this->view->_('NO_MATERIAL_TYPES_FOUND'),
            'Set' => $f($params['Item'] ?: new Material_Type()),
        ];
        $arr = array_merge($defaultParams, $params);
        $arr['columns'] = $columns;
        parent::__construct($arr);
    }
}
