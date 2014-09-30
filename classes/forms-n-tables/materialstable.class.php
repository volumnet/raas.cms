<?php
namespace RAAS\CMS;
use \RAAS\Column;

class MaterialsTable extends \RAAS\Table
{
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Main::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct(array $params = array())
    {
        $view = $this->view;
        $columns = array();
        $columns['name'] = array(
            'caption' => $this->view->_('NAME'),
            'sortable' => Column::SORTABLE_REVERSABLE,
            'callback' => function($row) use ($view, $params) { 
                return '<a href="' . $view->url . '&action=edit_material&id=' . (int)$row->id . '&pid=' . (int)$params['Item']->id . '" ' . (!$row->vis ? 'class="muted"' : '') . '>' 
                     .    htmlspecialchars($row->name) 
                     . '</a>';
            }
        );
        $columns['post_date'] = array(
            'caption' => $this->view->_('CREATED_BY'),
            'sortable' => Column::SORTABLE_REVERSABLE,
            'callback' => function($row) use ($view) { 
                return '<span' . (!$row->vis ? ' class="muted"' : '') . '>' . (strtotime($row->post_date) ? date(DATETIMEFORMAT, strtotime($row->post_date)) : '') . '</span>';
            }
        );
        $columns['modify_date'] = array(
            'caption' => $this->view->_('EDITED_BY'),
            'sortable' => Column::SORTABLE_REVERSABLE,
            'callback' => function($row) use ($view) { 
                return '<span' . (!$row->vis ? ' class="muted"' : '') . '>' . (strtotime($row->modify_date) ? date(DATETIMEFORMAT, strtotime($row->modify_date)) : '') . '</span>';
            }
        );
        foreach (array_filter($params['mtype']->fields, function($x) { return $x->show_in_table; }) as $key => $col) {
            $columns[$col->urn] = array(
                'caption' => $col->name,
                'sortable' => Column::SORTABLE_REVERSABLE,
                'callback' => function($row) use ($col) { return $row->fields[$col->urn]->doRich(); }
            );
        }
        $columns[' '] = array('callback' => function ($row) use ($view) { return rowContextMenu($view->getMaterialContextMenu($row)); });

        $arr = array_merge(array('columns' => $columns), $params);
        parent::__construct($arr);
    }
}