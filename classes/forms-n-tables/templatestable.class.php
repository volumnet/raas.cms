<?php
namespace RAAS\CMS;

class TemplatesTable extends \RAAS\Table
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
        $defaultParams = array(
            'columns' => array(
                'name' => array(
                    'caption' => $this->view->_('NAME'), 
                    'callback' => function($row) use ($view) { 
                        return '<a href="' . $view->url . '&action=edit_template&id=' . (int)$row->id . '">
                                  ' . htmlspecialchars($row->name) . '
                                </a>';
                    }
                ),
                'urn' => array(
                    'caption' => $this->view->_('URN'), 
                    'callback' => function($row) use ($view, $Item) { 
                        return '<a href="' . $view->url . '&action=edit_template&id=' . (int)$row->id . '">
                                  ' . htmlspecialchars($row->urn) . '
                                </a>';
                    }
                ),
                ' ' => array(
                    'callback' => function ($row, $i) use ($view, $contextMenuName, $IN) { 
                        return rowContextMenu($view->getTemplateContextMenu($row, $i, count($IN['Set']))); 
                    }
                )

            ),
            'emptyString' => $this->view->_('NO_TEMPLATES_FOUND'),
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}