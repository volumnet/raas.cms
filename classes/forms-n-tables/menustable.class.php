<?php
namespace RAAS\CMS;

class MenusTable extends \RAAS\Table
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


    public function __construct(array $params = array())
    {
        $view = $this->view;
        $thisObj = $this;
        $Item = $params['Item'];
        $defaultParams = array(
            'columns' => array(),
            'emptyString' => $this->view->_('NO_NOTES_FOUND'),
            'meta' => array('realizedCounter' => 0)
        );
        $defaultParams['columns']['name'] = array(
            'caption' => $this->view->_('NAME'), 
            'callback' => function($row) use ($view, $Item, $thisObj) { 
                if ($row->realized || !$Item->id) {
                    $thisObj->meta['realizedCounter'] = $thisObj->meta['realizedCounter'] + 1;
                    return '<a href="' . $view->url . '&action=menus&id=' . (int)$row->id . '" class="' . (!$row->vis ? ' muted' : '') . ($row->pvis ? '' : ' cms-inpvis') . '">
                              ' . htmlspecialchars($row->name) . '
                            </a>';
                } else {
                    return htmlspecialchars($row->name); 
                }
            }
        );
        if (!$Item->id) {
            $defaultParams['columns']['urn'] = array(
                'caption' => $this->view->_('URN'), 
                'callback' => function($row) use ($view, $Item) { 
                    if ($row->realized || !$Item->id) {
                        return '<a href="' . $view->url . '&action=menus&id=' . (int)$row->id . '" class="' . (!$row->vis ? ' muted' : '') . ($row->pvis ? '' : ' cms-inpvis') . '">
                                  ' . htmlspecialchars($row->urn) . '
                                </a>';
                    } else {
                        return htmlspecialchars($row->urn); 
                    }
                }
            );
        }
        $defaultParams['columns']['url'] = array(
            'caption' => $this->view->_('URL'), 
            'callback' => function($row) use ($view, $Item) { 
                if ($row->realized || !$Item->id) {
                    return '<span class="' . (!$row->vis ? ' muted' : '') . ($row->pvis ? '' : ' cms-inpvis') . '">
                              ' . htmlspecialchars($row->url) . '
                            </span>';
                } else {
                    return htmlspecialchars($row->url); 
                }
            }
        );
        $defaultParams['columns']['priority'] = array(
            'caption' => $this->view->_('PRIORITY'), 
            'callback' => function($row) use ($view, $Item) { 
                if ($row->realized || !$Item->id) {
                    return '<input type="text" class="span1" maxlength="3" name="reorder[' . (int)$row->id . ']" value="' . (int)$row->priority . '" />';
                } else {
                    return htmlspecialchars($row->priority); 
                }
            }
        );
        $defaultParams['columns'][' '] = array(
            'callback' => function ($row, $i) use ($view, $Item) { 
                if ($row->realized || !$Item->id) {
                    return rowContextMenu($view->getMenuContextMenu($row, $i, count($params['Set'])));
                } else {
                    return null;
                }
            }
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}