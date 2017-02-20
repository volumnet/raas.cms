<?php
namespace RAAS\CMS;

use \RAAS\Column;
use RAAS\Table;

class FeedbackExportTable extends Table
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


    public function __construct(array $params = array())
    {
        $view = $this->view;
        $columns = array();
        $columns['post_date'] = array(
            'caption' => $this->view->_('POST_DATE'),
            'callback' => function ($row) use ($view) {
                return date($view->_('DATETIMEFORMAT'), strtotime($row->post_date));
            }
        );
        if (!$params['Item']->id) {
            $columns['pid'] = array(
                'caption' => $this->view->_('FORM'),
                'callback' => function ($row) use ($view) {
                    return $row->parent->name;
                }
            );
        }
        $columns['name'] = array(
            'caption' => $this->view->_('PAGE'),
            'callback' => function ($row) use ($view) {
                return ($row->material->id ? $row->material->name : $row->page->name);
            }
        );
        $columns['ip'] = array(
            'caption' => $this->view->_('IP_ADDRESS'),
            'callback' => function($row) use ($view) {
                return $row->ip;
            }
        );
        foreach ($params['columns'] as $key => $col) {
            $columns[$col->urn] = array(
                'caption' => $col->name,
                'callback' => function ($row) use ($col) {
                    $f = $row->fields[$col->urn];
                    $text = '';
                    switch ($f->datatype) {
                        case 'htmlarea':
                            $text .= strip_tags($f->doRich());
                            break;
                        case 'file':
                            $v = $f->getValue();
                            $text .= $v->name;
                            break;
                        case 'image':
                            $v = $f->getValue();
                            $text .=  $v->tnURL;
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
                                    $text .= '+';
                                } else {
                                    $text .= '-';
                                }
                            }
                            break;
                        default:
                            if (isset($f)) {
                                $y = $f->doRich();
                            }
                            $text .= $y ? $y : '';
                            break;
                    }
                    return $text;
                }
            );
        }

        $defaultParams = array(
            'caption' => $params['Item']->name ? $params['Item']->name : $this->view->_('FEEDBACK'),
            'columns' => $columns,
            'callback' => function ($Row) {
                if (!$Row->source->vis) {
                    $Row->class = 'info';
                }
            },
            'Set' => $params['Set'],

        );
        unset($params['columns']);

        $arr = $defaultParams;
        parent::__construct($arr);
    }
}
