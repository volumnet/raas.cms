<?php
namespace RAAS\CMS;

class FieldsTable extends \RAAS\Table
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


    public function __construct(array $params = array())
    {
        $view = $this->view;
        $editAction = $params['editAction'];
        $ctxMenu = $params['ctxMenu'];
        $shift = isset($params['shift']) ? (int)$params['shift'] : 0;
        unset($params['editAction'], $params['ctxMenu'], $params['shift']);
        $defaultParams = array(
            'columns' => array(
                'name' => array(
                    'caption' => $this->view->_('NAME'), 
                    'callback' => function($row) use ($view, $editAction) { 
                        if ($row->id) {
                            return '<a href="' . $view->url . '&action=' . $editAction . '&id=' . (int)$row->id . '">' . htmlspecialchars($row->name) . '</a>'; 
                        } else {
                            return '<a href="' . $view->url . '&action=' . $editAction . '&id=' . (int)$row->id . '">' . htmlspecialchars($row->name) . '</a>'; 
                        }
                    }
                ),
                'urn' => array(
                    'caption' => $this->view->_('URN'),
                    'callback' => function($row) use ($view) { 
                        return htmlspecialchars($row->urn) 
                             . ($row->multiple ? '<strong title="' . $view->_('MULTIPLE') . '">[]</strong>' : '') 
                             . ($row->required ? ' <span class="text-error" title="' . $view->_('REQUIRED') . '">*</span>' : ''); 
                    }
                ),
                'datatype' => array(
                    'caption' => $this->view->_('DATATYPE'), 
                    'callback' => function($row) use ($view) { return htmlspecialchars($view->_('DATATYPE_' . str_replace('-', '_', strtoupper($row->datatype)))); }
                ),
                'show_in_table' => array(
                    'caption' => $this->view->_('SHOW_IN_TABLE'),
                    'title' => $this->view->_('SHOW_IN_TABLE'),
                    'callback' => function($row) { return $row->show_in_table ? '<i class="icon-ok"></i>' : ''; }
                ),
                ' ' => array('callback' => function ($row, $i) use ($view, $params, $ctxMenu, $shift) { return rowContextMenu($view->$ctxMenu($row, $i - $shift, count($params['Set']) - $shift)); })
            ),
            'Set' => $params['Set'],
            'Pages' => $params['Pages'],
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}