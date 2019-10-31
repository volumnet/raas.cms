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
                            return '<a href="' . $view->url . '&action=' . $editAction . '&id=' . (int)$row->id . '">' .
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
                            return '<a href="' . $view->url . '&action=' . $editAction . '&id=' . (int)$row->id . '">' .
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
                'priority' => [
                    'caption' => $this->view->_('PRIORITY'),
                    'callback' => function ($row) use ($params) {
                        if ($row->id && ($row->pid == $params['Item']->id)) {
                            return '<input type="number" name="priority[' . (int)$row->id . ']" value="' . ($row->priority ? (int)$row->priority : '') . '" class="span1" min="0" />';
                        } elseif ($row->id) {
                            return $row->priority;
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
