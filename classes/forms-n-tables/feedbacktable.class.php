<?php
namespace RAAS\CMS;
use \RAAS\Column;

class FeedbackTable extends \RAAS\Table
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
            'callback' => function($row) use ($view) { 
                return '<a href="' . $view->url . '&action=view&id=' . (int)$row->id . '">' . date(DATETIMEFORMAT, strtotime($row->post_date)) . '</a>';
            }
        );
        if (!$params['Item']->id) {
            $columns['pid'] = array(
                'caption' => $this->view->_('FORM'),
                'callback' => function($row) use ($view) { 
                    return '<a href="' . $view->url . '&action=view&id=' . (int)$row->id . '">' . htmlspecialchars($row->parent->name) . '</a>';
                }
            );
        }
        $columns['name'] = array(
            'caption' => $this->view->_('PAGE'),
            'callback' => function($row) use ($view) { 
                return '<a href="' . $view->url . '&action=view&id=' . (int)$row->id . '">' . htmlspecialchars($row->page->name) . '</a>';
            }
        );
        $columns['ip'] = array(
            'caption' => $this->view->_('IP_ADDRESS'),
            'callback' => function($row) use ($view) { 
                return '<a href="' . $view->url . '&action=view&id=' . (int)$row->id . '" title="' . htmlspecialchars($row->description) . '">' 
                     .    htmlspecialchars($row->ip)
                     . '</a>';
            }
        );
        foreach ($params['columns'] as $key => $col) {
            $columns[$col->urn] = array(
                'caption' => $col->name,
                'callback' => function($row) use ($col) { if (isset($row->fields[$col->urn])) { $y = $row->fields[$col->urn]->doRich(); } return $y ? $y : ''; }
            );
        }
        $columns[' '] = array('callback' => function ($row) use ($view) { return rowContextMenu($view->getFeedbackContextMenu($row)); });

        $defaultParams = array(
            'caption' => $params['Item']->name ? $params['Item']->name : $this->view->_('FEEDBACK'),
            'columns' => $columns,
            'emptyString' => $this->view->_('NO_NOTES_FOUND'),
            'callback' => function($Row) { if (!$Row->source->vis) { $Row->class = 'info'; } },
            'Set' => $params['Set'],
            'template' => 'feedback'
        );
        unset($params['columns']);

        // $arr = array_merge($defaultParams, $params);
        $arr = $defaultParams;
        parent::__construct($arr);
    }
}