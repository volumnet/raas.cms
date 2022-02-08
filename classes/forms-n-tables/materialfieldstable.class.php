<?php
/**
 * Таблица полей типа материалов
 */
namespace RAAS\CMS;

/**
 * Класс таблицы полей типа материалов
 */
class MaterialFieldsTable extends FieldsTable
{
    public function __construct(array $params = [])
    {
        $view = $this->view;
        $editAction = 'edit_material_field';
        $ctxMenu = 'getMaterialFieldContextMenu';
        $shift = 2
               + count($params['Item']->fields)
               - count($params['Item']->selfFields);
        unset($params['editAction'], $params['ctxMenu'], $params['shift']);
        $defaultParams = [
            'meta' => [
                'allContextMenu' => $view->getAllMaterialFieldsContextMenu(),
                'allValue' => 'all'
                           .  ($params['Item'] ? '&pid=' . (int)$params['Item']->id : ''),
            ],
            'data-role' => 'multitable',
            'columns' => [
                'id' => [
                    'caption' => $this->view->_('ID'),
                    'callback' => function ($row) use (
                        $view,
                        $editAction,
                        $params
                    ) {
                        if ($row->id && ($row->pid == $params['Item']->id)) {
                            return '<a href="' . $view->url . '&action=' . $editAction . '&id=' . (int)$row->id . '" ' . (!$row->vis ? 'class="muted"' : '') . '>' .
                                      (int)$row->id .
                                   '</a>';
                        } elseif ($row->id) {
                            return (int)$row->id;
                        }
                    }
                ],
                'name' => [
                    'caption' => $this->view->_('NAME'),
                    'callback' => function ($row) use (
                        $view,
                        $editAction,
                        $params
                    ) {
                        if ($row->id && ($row->pid == $params['Item']->id)) {
                            return '<a href="' . $view->url . '&action=' . $editAction . '&id=' . (int)$row->id . '" ' . (!$row->vis ? 'class="muted"' : '') . '>' .
                                      htmlspecialchars($row->name) .
                                   '</a>';
                        } else {
                            return htmlspecialchars($row->name);
                        }
                    }
                ],
                'urn' => [
                    'caption' => $this->view->_('URN'),
                    'callback' => function ($row) use ($view) {
                        $text = htmlspecialchars($row->urn);
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
                        return $text;
                    }
                ],
                'datatype' => [
                    'caption' => $this->view->_('DATATYPE'),
                    'callback' => function ($row) use ($view) {
                        return htmlspecialchars($view->_(
                            'DATATYPE_' .
                            str_replace('-', '_', strtoupper($row->datatype))
                        ));
                    }
                ],
                'show_in_table' => [
                    'caption' => $this->view->_('SHOW_IN_TABLE'),
                    'title' => $this->view->_('SHOW_IN_TABLE'),
                    'callback' => function ($row) {
                        return $row->show_in_table ?
                               '<i class="icon-ok"></i>' :
                               '';
                    }
                ],
                'show_in_form' => [
                    'caption' => $this->view->_('SHOW_IN_FORM') . ' / '
                        . $this->view->_('INHERIT'),
                    'callback' => function ($row, $i) use ($params) {
                        if ($row->id) {
                            return '<input type="checkbox" style="margin-top: 0; " name="show_in_form[' . (int)$row->id . ']" value="1"' . (in_array($row->id, $params['Item']->formFields_ids) ? ' checked="checked"' : '') . ' /> /
                                    <input type="checkbox" style="margin-top: 0; " name="inherit_show_in_form[' . (int)$row->id . ']" value="1" />';
                        }
                    }
                ],
                'priority' => [
                    'caption' => $this->view->_('PRIORITY'),
                    'callback' => function ($row, $i) use ($params) {
                        if ($row->id && ($row->pid == $params['Item']->id)) {
                            return '<input type="number" name="priority[' . (int)$row->id . ']" value="' . (($i + 1) * 10) . '" class="span1" min="0" />';
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
                        $shift
                    ) {
                        if ($row->id && ($row->pid == $params['Item']->id)) {
                            return rowContextMenu($view->$ctxMenu(
                                $row,
                                $i - $shift,
                                count($params['Set']) - $shift
                            ));
                        }
                    }
                ]
            ],
            'Set' => $params['Set'],
            'Pages' => $params['Pages'],
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}
