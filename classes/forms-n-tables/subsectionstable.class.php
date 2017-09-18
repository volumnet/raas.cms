<?php
namespace RAAS\CMS;

use RAAS\Column;

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
            $i = 0;
            foreach (array_filter(
                Page_Field::getSet(),
                function ($x) {
                    return ($x->datatype == 'image') && $x->show_in_table;
                }
            ) as $key => $col) {
                if ($i < 3) {
                    $columns[$col->urn] = array(
                        'caption' => $col->name,
                        'callback' => function ($row) use ($col, $view, $params) {
                            $f = $row->fields[$col->urn];
                            $v = $f->getValue();
                            if ($v->id) {
                                return '<a href="' . $view->url . '&action=edit&id=' . (int)$row->id . '&pid=' . (int)$params['Item']->id . '" ' . (!$row->vis ? 'class="muted"' : '') . '>
                                          <img src="/' . $v->tnURL . '" style="max-width: 48px;" /></a>';
                            }
                        }
                    );
                    $i++;
                }
            }
            $columns['name'] = array(
                'caption' => $this->view->_('NAME'),
                'callback' => function ($row) use ($view) {
                    return '<a href="' . $view->url . '&id=' . (int)$row->id . '" class="' . (!$row->vis ? 'muted' : ($row->response_code ? ' text-error' : '')) . ($row->pvis ? '' : ' cms-inpvis') . '">'
                         .    htmlspecialchars($row->name)
                         . '</a>';
                }
            );
            $columns['urn'] = array(
                'caption' => $this->view->_('URN'),
                'callback' => function ($row) use ($view) {
                    return '<a href="http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . htmlspecialchars(preg_replace('/^http(s)?:\\/\\//umi', '', $row->domain . array_shift(explode(' ', $row->url)))) . '" class="' . (!$row->vis ? 'muted' : ($row->response_code ? ' text-error' : '')) . ($row->pvis ? '' : ' cms-inpvis') . '">'
                         .    htmlspecialchars(preg_replace('/^http(s)?:\\/\\//umi', '', array_shift(explode(' ', $row->urn))))
                         . '</a>';
                }
            );
            foreach (array_filter(
                Page_Field::getSet(),
                function ($x) {
                    return ($x->datatype != 'image') && $x->show_in_table;
                }
            ) as $key => $col) {
                if ($i < 3) {
                    $columns[$col->urn] = array(
                        'caption' => $col->name,
                        'callback' => function ($row) use ($col, $view) {
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
                'callback' => function ($row, $i) {
                    return '<input type="number" name="page_priority[' . (int)$row->id . ']" value="' . (($i + 1) * 10) . '" class="span1" min="0" />';
                }
            );
            $columns[' '] = array(
                'callback' => function ($row, $i) use ($view, $params) {
                    return rowContextMenu($view->getPageContextMenu($row, $i, count($params['Set'])));
                }
            );
        } else {
            $columns['name'] = array(
                'caption' => $this->view->_('NAME'),
                'sortable' => Column::SORTABLE_REVERSABLE,
                'callback' => function ($row) use ($view) {
                    return '<a href="' . $view->url . '&id=' . (int)$row->id . '" ' . (!$row->vis ? ' class="muted"' : '') . '>'
                         .    htmlspecialchars($row->name)
                         . '</a>';
                }
            );
            $columns['urn'] = array(
                'caption' => $this->view->_('DOMAIN'),
                'sortable' => Column::SORTABLE_REVERSABLE,
                'callback' => function ($row) use ($view) {
                    return '<a href="http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . htmlspecialchars(preg_replace('/^http(s)?:\\/\\//umi', '', array_shift(explode(' ', $row->urn)))) . '"' . (!$row->vis ? ' class="muted"' : '') . '>'
                         .    htmlspecialchars(preg_replace('/^http(s)?:\\/\\//umi', '', array_shift(explode(' ', $row->urn))))
                         . '</a>';
                }
            );
            $columns[' '] = array(
                'callback' => function ($row, $i) use ($view, $params) {
                    return rowContextMenu($view->getPageContextMenu($row, $i, count($params['Set'])));
                }
            );
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
