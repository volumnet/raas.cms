<?php
/**
 * Таблица полей
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\Table;
use RAAS\Row;

/**
 * Класс таблицы полей
 * @property-read ViewSub_Dev $view Представление
 */
class FieldsTable extends Table
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


    public function __construct(array $params = [])
    {
        $view = $this->view;
        $editAction = isset($params['editAction']) ? $params['editAction'] : '';
        $editGroupAction = isset($params['editGroupAction']) ? $params['editGroupAction'] : '';
        $ctxMenu = isset($params['ctxMenu']) ? $params['ctxMenu'] : '';
        $groupCtxMenu = isset($params['groupCtxMenu']) ? $params['groupCtxMenu'] : '';
        $allCtxMenu = str_replace('get', 'getAll', $ctxMenu);
        $allCtxMenu = str_replace('ContextMenu', 'sContextMenu', $allCtxMenu);
        $shift = isset($params['shift']) ? (int)$params['shift'] : 0;
        unset($params['editAction'], $params['ctxMenu'], $params['shift']);
        $defaultParams = [
            'meta' => [
                'allContextMenu' => ($ctxMenu && $allCtxMenu) ?  $view->$allCtxMenu() : null,
                'allValue' => 'all' . (($params['Item'] ?? null) ? '&pid=' . (int)$params['Item']->id : ''),
                'priorityColumn' => 'priority',
            ],
            'data-role' => 'multitable',
            'columns' => [
                'id' => [
                    'caption' => $this->view->_('ID'),
                    'callback' => function ($row) use ($view, $editAction, $editGroupAction, $params) {
                        if ($row->id && ($row->pid == ($params['Item']->id ?? 0))) {
                            if ($row instanceof FieldGroup) {
                                return '<a href="' . $view->url . '&action=' . $editGroupAction . '&id=' . (int)$row->id . '">' .
                                          (int)$row->id .
                                       '</a>';
                            } else {
                                return '<a href="' . $view->url . '&action=' . $editAction . '&id=' . (int)$row->id . '" ' . (!$row->vis ? 'class="muted"' : '') . '>' .
                                          (int)$row->id .
                                       '</a>';
                            }
                        } elseif ($row->id) {
                            return (int)$row->id;
                        }
                    }
                ],
                'name' => [
                    'caption' => $this->view->_('NAME'),
                    'callback' => function ($row) use ($view, $editAction, $editGroupAction, $params) {
                        if ($row->id && ($row->pid == ($params['Item']->id ?? 0))) {
                            if ($row instanceof FieldGroup) {
                                return '<a href="' . $view->url . '&action=' . $editGroupAction . '&id=' . (int)$row->id . '">' .
                                          htmlspecialchars($row->name) .
                                       '</a>';
                            } else {
                                return '<a href="' . $view->url . '&action=' . $editAction . '&id=' . (int)$row->id . '" ' . (!$row->vis ? 'class="muted"' : '') . '>' .
                                          htmlspecialchars($row->name) .
                                       '</a>';
                            }
                        } else {
                            return htmlspecialchars($row->name);
                        }
                    }
                ],
                'urn' => [
                    'caption' => $this->view->_('URN'),
                    'callback' => function ($row) use ($view) {
                        $text = htmlspecialchars((string)$row->urn);
                        if ($row instanceof Field) {
                            if ($row->multiple) {
                                $text .= '<strong title="' . $view->_('MULTIPLE') . '">'
                                      .    '[]'
                                      .  '</strong>';
                            }
                            if ($row->required) {
                                $text .= ' <span class="text-error" title="' . $view->_('REQUIRED') . '">'
                                      .      '*'
                                      .   '</span>';
                            }
                        }
                        return $text;
                    }
                ],
                'datatype' => [
                    'caption' => $this->view->_('DATATYPE'),
                    'callback' => function ($row) use ($view) {
                        if ($row instanceof Field) {
                            return htmlspecialchars($view->_(
                                'DATATYPE_' .
                                str_replace('-', '_', strtoupper($row->datatype))
                            ));
                        }
                    }
                ],
                'show_in_table' => [
                    'caption' => $this->view->_('SHOW_IN_TABLE'),
                    'title' => $this->view->_('SHOW_IN_TABLE'),
                    'callback' => function ($row) {
                        if ($row instanceof Field) {
                            return $row->show_in_table ?
                                   '<i class="icon-ok"></i>' :
                                   '';
                        }
                    }
                ],
                'priority' => [
                    'caption' => $this->view->_('PRIORITY'),
                    'callback' => function ($row, $i) use ($params) {
                        if ($row->id && ($row->pid == ($params['Item']->id ?? 0))) {
                            if ($row instanceof FieldGroup) {
                                return '<input type="number" name="fieldgrouppriority[' . (int)$row->id . ']" value="' . (($i + 1) * 10) . '" class="span1" min="0" />';
                            } else {
                                return '<input type="number" name="priority[' . (int)$row->id . ']" value="' . (($i + 1) * 10) . '" class="span1" min="0" />';
                            }
                        } elseif ($row->id) {
                            return (($i + 1) * 10);
                        }
                    }
                ],
                ' ' => [
                    'callback' => function (
                        $row,
                        $i
                    ) use (
                        $view,
                        $params,
                        $ctxMenu,
                        $groupCtxMenu,
                        $shift
                    ) {
                        if ($row->id && ($row->pid == ($params['Item']->id ?? 0))) {
                            if ($row instanceof FieldGroup) {
                                $menuFunction = $groupCtxMenu;
                            } else {
                                $menuFunction = $ctxMenu;
                            }
                            return rowContextMenu($view->$menuFunction(
                                $row,
                                $i - $shift,
                                count($params['Set']) - $shift
                            ));
                        }
                    }
                ]
            ],
            'callback' => function (Row $tableRow) {
                if ($tableRow->source instanceof FieldGroup) {
                    $tableRow->class = 'info';
                    $tableRow->disableMulti = true;
                }
            },
            'Set' => $params['Set'],
            'Pages' => $params['Pages'] ?? null,
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}
