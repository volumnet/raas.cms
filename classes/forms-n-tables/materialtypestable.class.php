<?php
namespace RAAS\CMS;
use \RAAS\Column;

class MaterialTypesTable extends \RAAS\Table
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
        $columns = array();
        $columns['name'] = array(
            'caption' => $this->view->_('NAME'), 
            'callback' => function($row) use ($view) { 
                return '<a href="' . $view->url . '&action=edit_material_type&id=' . (int)$row->id . '">' . htmlspecialchars($row->name) . '</a>'; 
            }
        );
        $columns['urn'] = array('caption' => $this->view->_('URN'));
        $columns['global_type'] = array(
            'caption' => $this->view->_('IS_GLOBAL_TYPE'), 
            'title' => $this->view->_('GLOBAL_MATERIALS'), 
            'callback' => function($row) { return $row->global_type ? '<i class="icon-ok"></i>' : ''; }
        );
        $columns[' '] = array('callback' => function ($row) use ($view) { return rowContextMenu($view->getMaterialTypeContextMenu($row)); });
        $defaultParams = array(
            'emptyString' => $this->view->_('NO_MATERIAL_TYPES_FOUND')
        );
        $arr = array_merge($defaultParams, $params);
        $arr['columns'] = $columns;
        parent::__construct($arr);
    }
}