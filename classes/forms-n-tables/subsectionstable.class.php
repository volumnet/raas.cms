<?php
namespace RAAS\CMS;
use \RAAS\Column;

class SubsectionsTable extends \RAAS\Table
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
        if ($params['Item']->id) {
            $columns['name'] = array(
                'caption' => $this->view->_('NAME'),
                'callback' => function($row) use ($view) { 
                    return '<a href="' . $view->url . '&id=' . (int)$row->id . '" class="' . (!$row->vis ? 'muted' : ($row->response_code ? ' text-error' : '')) . ($row->pvis ? '' : ' cms-inpvis') . '">' 
                         .    htmlspecialchars($row->name) 
                         . '</a>';
                }
            );
            $columns['urn'] = array(
                'caption' => $this->view->_('URN'),
                'callback' => function($row) use ($view) { 
                    return '<a href="http://' . htmlspecialchars(str_replace('http://', '', $row->domain . array_shift(explode(' ', $row->url)))) . '" class="' . (!$row->vis ? 'muted' : ($row->response_code ? ' text-error' : '')) . ($row->pvis ? '' : ' cms-inpvis') . '">' 
                         .    htmlspecialchars(str_replace('http://', '', array_shift(explode(' ', $row->urn)))) 
                         . '</a>';
                }
            );
            $columns['priority'] = array(
                'caption' => $this->view->_('PRIORITY'),
                'callback' => function($row, $i) { 
                    return '<input type="number" name="page_priority[' . (int)$row->id . ']" value="' . (($i + 1) * 10) . '" class="span1" min="0" />';
                }
            );
            foreach ($params['columns'] as $key => $col) {
                $columns[$col->urn] = array('caption' => $col->name, 'callback' => function($row) use ($col) { return $row->fields[$col->urn]->doRich(); });
            }
            $columns[' '] = array('callback' => function ($row, $i) use ($view, $params) { return rowContextMenu($view->getPageContextMenu($row, $i, count($params['Set']))); });
        } else {
            $columns['name'] = array(
                'caption' => $this->view->_('NAME'),
                'sortable' => Column::SORTABLE_REVERSABLE,
                'callback' => function($row) use ($view) { 
                    return '<a href="' . $view->url . '&id=' . (int)$row->id . '" ' . (!$row->vis ? ' class="muted"' : '') . '>' 
                         .    htmlspecialchars($row->name)
                         . '</a>';
                }
            );
            $columns['urn'] = array(
                'caption' => $this->view->_('DOMAIN'),
                'sortable' => Column::SORTABLE_REVERSABLE,
                'callback' => function($row) use ($view) { 
                    return '<a href="http://' . htmlspecialchars(str_replace('http://', '', array_shift(explode(' ', $row->urn)))) . '"' . (!$row->vis ? ' class="muted"' : '') . '>' 
                         .    htmlspecialchars(str_replace('http://', '', array_shift(explode(' ', $row->urn)))) 
                         . '</a>';
                }
            );
            foreach ($params['columns'] as $key => $col) {
                $columns[$col->urn] = array(
                    'caption' => $col->name,
                    'sortable' => Column::SORTABLE_REVERSABLE,
                    'callback' => function($row) use ($col) { return $row->fields[$col->urn]->doRich(); }   
                );
            }
            $columns[' '] = array('callback' => function ($row, $i) use ($view, $params) { return rowContextMenu($view->getPageContextMenu($row, $i, count($params['Set']))); });
        }
        $arr = $params;
        $arr['data-role'] = 'multitable';
        $arr['meta']['allContextMenu'] = $view->getAllPagesContextMenu();
        $arr['meta']['allValue'] = 'all&pid=' . (int)$params['Item']->id;
        $arr['columns'] = $columns;
        parent::__construct($arr);
        if ($params['Item']->id) {
            $this->class = 'table-condensed';
        } else {
            $this->sort = $params['sort'];
            $this->order = ((strtolower($params['order']) == 'desc') ? Column::SORT_DESC : Column::SORT_ASC);
            $this->emptyString = $this->view->_('NO_SITES_FOUND');
        }
        
    }
}