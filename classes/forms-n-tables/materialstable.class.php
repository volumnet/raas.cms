<?php
/**
 * Таблица материалов
 */
namespace RAAS\CMS;

use RAAS\Column;
use RAAS\Table;

/**
 * Класс таблицы материалов
 * @property-read ViewSub_Main $view Представление
 */
class MaterialsTable extends Table
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


    public function __construct(array $params = [])
    {
        $view = $this->view;
        $columns = [];
        $i = 0;
        $pidText = ($params['Item'] instanceof Page)
                 ? '&pid=' . (int)$params['Item']->id
                 : '';
        $columns['id'] = [
            'caption' => $this->view->_('ID'),
            'callback' => function ($row) use ($view, $params, $pidText) {
                return '<a href="' . $view->url . '&action=edit_material&id=' . (int)$row->id . $pidText . '" ' . (!$row->vis ? 'class="muted"' : '') . '>'
                     .    (int)$row->id
                     . '</a>';
            }
        ];
        foreach (array_filter(
            $params['mtype']->fields,
            function ($x) {
                return ($x->datatype == 'image') && $x->show_in_table;
            }
        ) as $key => $col) {
            if ($i < 3) {
                $columns[$col->urn] = [
                    'caption' => $col->name,
                    'sortable' => Column::SORTABLE_REVERSABLE,
                    'callback' => function ($row) use (
                        $col,
                        $view,
                        $params,
                        $pidText
                    ) {
                        $f = $row->fields[$col->urn];
                        $v = $f->getValue();
                        if ($v->id) {
                            return '<a href="' . $view->url . '&action=edit_material&id=' . (int)$row->id . $pidText . '" ' . (!$row->vis ? 'class="muted"' : '') . '>' .
                                     '<img src="/' . $v->tnURL . '" style="max-width: 48px;" />' .
                                   '</a>';
                        }
                    }
                ];
                $i++;
            }
        }
        $columns['name'] = [
            'caption' => $this->view->_('NAME'),
            'sortable' => Column::SORTABLE_REVERSABLE,
            'callback' => function ($row) use ($view, $params, $pidText) {
                $text = '<a href="' . $view->url . '&action=edit_material&id=' . (int)$row->id . $pidText . '" ' . (!$row->vis ? 'class="muted"' : '') . '>'
                      .    htmlspecialchars($row->name)
                      . '</a>';
                if (!$params['mtype']->global_type) {
                    $pagesCounter = (int)$row->pages_counter;
                    if ($pagesCounter != 1) {
                        $text .= '<sup title="' . $this->view->_('ASSOCIATED_WITH_PAGES_COUNTER') . '">(' . $pagesCounter . ')</sup>';
                    }
                }
                return $text;
            }
        ];
        $columns['post_date'] = [
            'caption' => $this->view->_('CREATED_BY'),
            'sortable' => Column::SORTABLE_REVERSABLE,
            'callback' => function ($row) use ($view) {
                $t = strtotime($row->post_date);
                return '<span' . (!$row->vis ? ' class="muted"' : '') . '>' .
                          (($t > 0) ? date(DATETIMEFORMAT, $t) : '') .
                       '</span>';
            }
        ];
        $columns['modify_date'] = [
            'caption' => $this->view->_('EDITED_BY'),
            'sortable' => Column::SORTABLE_REVERSABLE,
            'callback' => function ($row) use ($view) {
                $t = strtotime($row->modify_date);
                return '<span' . (!$row->vis ? ' class="muted"' : '') . '>' .
                          (($t > 0) ? date(DATETIMEFORMAT, $t) : '') .
                       '</span>';
            }
        ];
        foreach (array_filter(
            $params['mtype']->fields,
            function ($x) {
                return ($x->datatype != 'image') && $x->show_in_table;
            }
        ) as $key => $col) {
            if ($i < 3) {
                $columns[$col->urn] = [
                    'caption' => $col->name,
                    'sortable' => Column::SORTABLE_REVERSABLE,
                    'callback' => function ($row) use ($col, $view) {
                        $f = $row->fields[$col->urn];
                        switch ($f->datatype) {
                            case 'color':
                                $v = $f->getValue();
                                return '<span style="color: ' . htmlspecialchars($v) . '">
                                          ' . htmlspecialchars($v) . '
                                        </span>';
                                break;
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
                ];
                $i++;
            }
        }
        $columns['priority'] = [
            'caption' => $this->view->_('PRIORITY'),
            'callback' => function ($row) {
                return '<input type="number" name="priority[' . (int)$row->id . ']" value="' . ($row->priority ? (int)$row->priority : '') . '" class="span1" min="0" />';
            }
        ];
        $columns[' '] = [
            'callback' => function ($row) use ($view) {
                return rowContextMenu($view->getMaterialContextMenu($row));
            }
        ];

        $arr = array_merge(
            [
                'meta' => [
                    'allContextMenu' => $view->getAllMaterialsContextMenu(
                        $params['mtype']
                    ),
                    'allValue' => 'all&mtype='
                               .  (isset($params['mtype']) ? (int)$params['mtype']->id : 0)
                               .  $pidText,
                ],
                'data-role' => 'multitable',
                'columns' => $columns
            ],
            $params
        );
        parent::__construct($arr);
    }
}
