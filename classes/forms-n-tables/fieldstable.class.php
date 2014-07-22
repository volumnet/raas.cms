<?php
namespace RAAS\CMS;

class FieldsTable extends \RAAS\Table
{
    protected $_view;

    public function __construct(array $params = array())
    {
        $this->_view = $view = isset($params['view']) ? $params['view'] : null;
        $editAction = $params['editAction'];
        $ctxMenu = $params['ctxMenu'];
        $shift = isset($params['shift']) ? (int)$params['shift'] : 0;
        unset($params['view'], $params['editAction'], $params['ctxMenu'], $params['shift']);
        $defaultParams = array(
            'columns' => array(
                'name' => array(
                    'caption' => $this->_view->_('NAME'), 
                    'callback' => function($row) use ($view, $editAction) { 
                        return '<a href="' . $view->url . '&action=' . $editAction . '&id=' . (int)$row->id . '">' . htmlspecialchars($row->name) . '</a>'; 
                    }
                ),
                'urn' => array(
                    'caption' => $this->_view->_('URN'),
                    'callback' => function($row) use ($view) { 
                        return htmlspecialchars($row->urn) 
                             . ($row->multiple ? '<strong title="' . $view->_('MULTIPLE') . '">[]</strong>' : '') 
                             . ($row->required ? ' <span class="text-error" title="' . $view->_('REQUIRED') . '">*</span>' : ''); 
                    }
                ),
                'datatype' => array(
                    'caption' => $this->_view->_('DATATYPE'), 
                    'callback' => function($row) use ($view) { return htmlspecialchars($view->_('DATATYPE_' . str_replace('-', '_', strtoupper($row->datatype)))); }
                ),
                'show_in_table' => array(
                    'caption' => $this->_view->_('SHOW_IN_TABLE'),
                    'title' => $this->_view->_('SHOW_IN_TABLE'),
                    'callback' => function($row) { return $row->show_in_table ? '<i class="icon-ok"></i>' : ''; }
                ),
                ' ' => array('callback' => function ($row, $i) use ($view, $params, $ctxMenu, $shift) { return rowContextMenu($view->$ctxMenu($row, $i - $shift, count($params['Set']))); })
            ),
            'Set' => $params['Set'],
            'Pages' => $params['Pages'],
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}