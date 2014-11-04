<?php
namespace RAAS\CMS;

class MaterialFieldsTable extends FieldsTable
{
    public function __construct(array $params = array())
    {
        $view = $this->view;
        $editAction = 'edit_material_field';
        $ctxMenu = 'getMaterialFieldContextMenu';
        $shift = 2 + count($params['Item']->fields) - count($params['Item']->selfFields);
        unset($params['editAction'], $params['ctxMenu'], $params['shift']);
        $defaultParams = array(
            'columns' => array(
                'name' => array(
                    'caption' => $this->view->_('NAME'), 
                    'callback' => function($row) use ($view, $editAction, $params) { 
                        if ($row->id && ($row->pid == $params['Item']->id)) {
                            return '<a href="' . $view->url . '&action=' . $editAction . '&id=' . (int)$row->id . '">' . htmlspecialchars($row->name) . '</a>'; 
                        } else {
                            return htmlspecialchars($row->name); 
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
                ' ' => array(
                    'callback' => function ($row, $i) use ($view, $params, $ctxMenu, $shift) { 
                        if ($row->id && ($row->pid == $params['Item']->id)) {
                            return rowContextMenu($view->$ctxMenu($row, $i - $shift, count($params['Set']) - $shift)); 
                        }
                    }
                )
            ),
            'Set' => $params['Set'],
            'Pages' => $params['Pages'],
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}