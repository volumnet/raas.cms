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
        $i = 0;
        foreach (array_filter($params['mtype']->fields, function($x) { return ($x->datatype == 'image') && $x->show_in_table; }) as $key => $col) {
            if ($i < 3) {
                $columns[$col->urn] = array(
                    'caption' => $col->name,
                    'sortable' => Column::SORTABLE_REVERSABLE,
                    'callback' => function($row) use ($col, $view, $params) { 
                        $f = $row->fields[$col->urn];
                        $v = $f->getValue();
                        if ($v->id) {
                            return '<a href="' . $view->url . '&action=edit_material&id=' . (int)$row->id . '&pid=' . (int)$params['Item']->id . '" ' . (!$row->vis ? 'class="muted"' : '') . '>
                                      <img src="/' . $v->tnURL . '" style="max-width: 48px;" /></a>';
                        }
                    }
                );
                $i++;
            }
        }
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
        foreach (array_filter($params['mtype']->fields, function($x) { return ($x->datatype != 'image') && $x->show_in_table; }) as $key => $col) {
            if ($i < 3) {
                $columns[$col->urn] = array(
                    'caption' => $col->name,
                    'sortable' => Column::SORTABLE_REVERSABLE,
                    'callback' => function($row) use ($col, $view) { 
                        $f = $row->fields[$col->urn];
                        switch ($f->datatype) {
                            case 'htmlarea':
                                return strip_tags($f->doRich());
                                break;
                            case 'file':
                                $v = $f->getValue();
                                return '<a href="/' . $view->fileURL . '" ' . (!$row->vis ? 'class="muted"' : '') . '>' . htmlspecialchars($row->name) . '</a>';
                                break;
                            case 'material':
                                $v = $f->getValue();
                                $m = new Material($v);
                                if ($m->id) {
                                    return '<a href="' . $view->url . '&action=edit_material&id=' . (int)$m->id . '" ' . (!$m->vis ? 'class="muted"' : '') . '>' 
                                         .    htmlspecialchars($m->name) 
                                         . '</a>';
                                }
                                break;
                            case 'checkbox':
                                if ($f->multiple) {
                                    return $f->doRich();
                                } else {
                                    if ((int)$f->getValue()) {
                                        return '<span class="icon icon-ok"></span>';
                                    }
                                }
                                break;
                            default:
                                return $f->doRich(); 
                                break;
                        }
                        
                    }
                );
                $i++;
            }
        }
        $columns['priority'] = array(
            'caption' => $this->view->_('PRIORITY'),
            'callback' => function($row) { 
                return '<input type="number" name="priority[' . (int)$row->id . ']" value="' . ($row->priority ? (int)$row->priority : '') . '" class="span1" min="0" />';
            }
        );
        $columns[' '] = array('callback' => function ($row) use ($view) { return rowContextMenu($view->getMaterialContextMenu($row)); });

        $arr = array_merge(
            array(
                'meta' => array(
                    'allContextMenu' => $view->getAllMaterialsContextMenu(),
                    'allValue' => 'all&mtype=' . (int)$params['mtype']->id,
                ),
                'data-role' => 'multitable',
                'columns' => $columns
            ), 
            $params
        );
        parent::__construct($arr);
    }
}