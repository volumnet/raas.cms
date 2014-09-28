<?php
namespace RAAS\CMS;

class DictionariesTable extends \RAAS\Table
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
                return '<a href="' . $view->url . '&action=dictionaries&id=' . (int)$row->id . '" class="' . (!$row->vis ? ' muted' : '') . ($row->pvis ? '' : ' cms-inpvis') . '">'
                     .    htmlspecialchars($row->name) 
                     . '</a>'; 
            }
        );
        if ($params['Item']->id) {
            $columns['urn'] = array(
                'caption' => $this->view->_('VALUE'),
                'callback' => function($row) { 
                    return '<span class="' . (!$row->vis ? ' muted' : '') . ($row->pvis ? '' : ' cms-inpvis') . '">' . htmlspecialchars($row->urn) . '</span>'; 
                }
            );
        }
        $columns[' '] = array(
            'callback' => function($row, $i) use ($view, $params) { return rowContextMenu($view->getDictionaryContextMenu($row, $i, count($params['Set']))); }
        );
        $defaultParams = array(
            'emptyString' => $this->view->_('NO_NOTES_FOUND'),
            'template' => 'dev_dictionaries',
        );
        $arr = array_merge($defaultParams, $params);
        $arr['columns'] = $columns;
        parent::__construct($arr);
    }
}