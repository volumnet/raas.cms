<?php
/**
 * Таблица обратной связи
 */
namespace RAAS\CMS;

use RAAS\Table;

/**
 * Класс таблицы обратной связи
 */
class FeedbackTable extends Table
{
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Feedback::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct(array $params = [])
    {
        $columns = [];
        $columns['id'] = [
            'caption' => $this->view->_('ID'),
            'callback' => function ($row) {
                return '<a href="' . $this->view->url . '&action=view&id=' . (int)$row->id . '">' .
                          (int)$row->id .
                       '</a>';
            }
        ];
        $columns['post_date'] = [
            'caption' => $this->view->_('POST_DATE'),
            'callback' => function ($row) {
                return '<a href="' . $this->view->url . '&action=view&id=' . (int)$row->id . '">' .
                          date(DATETIMEFORMAT, strtotime($row->post_date)) .
                       '</a>';
            }
        ];
        if (!$params['Item']->id) {
            $columns['pid'] = [
                'caption' => $this->view->_('FORM'),
                'callback' => function ($row) {
                    return '<a href="' . $this->view->url . '&action=view&id=' . (int)$row->id . '">' .
                              htmlspecialchars($row->parent->name) .
                           '</a>';
                }
            ];
        }
        $columns['name'] = [
            'caption' => $this->view->_('PAGE'),
            'callback' => function ($row) {
                $name = $row->material->id
                      ? $row->material->name
                      : $row->page->name;
                return '<a href="' . $this->view->url . '&action=view&id=' . (int)$row->id . '">' .
                          htmlspecialchars($name) .
                       '</a>';
            }
        ];
        $columns['ip'] = [
            'caption' => $this->view->_('IP_ADDRESS'),
            'callback' => function ($row) {
                return '<a href="' . $this->view->url . '&action=view&id=' . (int)$row->id . '" title="' . htmlspecialchars($row->description) . '">'
                     .    htmlspecialchars($row->ip)
                     . '</a>';
            }
        ];
        foreach ($params['columns'] as $key => $col) {
            $columns[$col->urn] = [
                'caption' => $col->name,
                'callback' => function ($row) use ($col) {
                    $text = '<a href="' . $this->view->url . '&action=view&id=' . (int)$row->id . '" title="' . htmlspecialchars($row->description) . '">';
                    $f = $row->fields[$col->urn];
                    switch ($f->datatype) {
                        case 'color':
                            $v = $f->getValue();
                            return '<span style="color: ' . htmlspecialchars($v) . '">' .
                                      htmlspecialchars($v) .
                                   '</span>';
                            break;
                        case 'htmlarea':
                            $text .= strip_tags($f->doRich());
                            break;
                        case 'file':
                            $v = $f->getValue();
                            $text .= $v->name;
                            break;
                        case 'image':
                            $v = $f->getValue();
                            $text .= '<img src="/' . $v->tnURL . '" style="max-width: 48px;" />';
                            break;
                        case 'material':
                            $v = $f->getValue();
                            $m = new Material($v);
                            if ($m->id) {
                                $text .= htmlspecialchars($m->name);
                            }
                            break;
                        case 'checkbox':
                            if ($f->multiple) {
                                $text .= $f->doRich();
                            } else {
                                if ((int)$f->getValue()) {
                                    $text .= '<span class="icon icon-ok"></span>';
                                }
                            }
                            break;
                        default:
                            if (isset($f)) {
                                $y = htmlspecialchars($f->doRich());
                            }
                            $text .= $y ? $y : '';
                            break;
                    }
                    $text .= '</a>';
                    return $text;
                }
            ];
        }
        $columns[' '] = [
            'callback' => function ($row) {
                return rowContextMenu($this->view->getFeedbackContextMenu($row));
            }
        ];

        $defaultParams = [
            'caption' => $params['Item']->name
                      ?  $params['Item']->name
                      :  $this->view->_('FEEDBACK'),
            'columns' => $columns,
            'emptyString' => $this->view->_('NO_NOTES_FOUND'),
            'callback' => function ($Row) {
                if (!$Row->source->vis) {
                    $Row->class = 'info';
                }
            },
            'Set' => $params['Set'],
            'Pages' => $params['Pages'],
            'template' => 'feedback',
            'data-role' => 'multitable',
            'meta' => [
                'allContextMenu' => $this->view->getAllFeedbacksContextMenu(),
            ],
        ];
        unset($params['columns']);

        // $arr = array_merge($defaultParams, $params);
        $arr = $defaultParams;
        parent::__construct($arr);
    }
}
