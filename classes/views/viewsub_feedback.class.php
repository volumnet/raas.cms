<?php
namespace RAAS\CMS;

use \RAAS\Table as Table;
use \RAAS\Column as Column;
use \RAAS\Row as Row;
use SOME\HTTP;

class ViewSub_Feedback extends \RAAS\Abstract_Sub_View
{
    protected static $instance;

    public function view(array $IN = array())
    {
        $this->assignVars($IN);
        $this->title = $IN['Form']->caption;
        $this->path[] = array('name' => $this->_('FEEDBACK'), 'href' => $this->url);
        $this->path[] = array('name' => $IN['Item']->parent->name, 'href' => $this->url . '&id=' . $IN['Item']->pid);
        foreach ((array)$IN['Forms'] as $row) {
            $this->submenu[] = array(
                'name' => $row->name . ($row->unreadFeedbacks ? ' (' . (int)$row->unreadFeedbacks . ')' : ''),
                'href' => $this->url . '&id=' . (int)$row->id,
                'active' => ($row->id == $IN['Item']->pid)
            );
        }
        $this->contextmenu = $this->getFeedbackContextMenu($IN['Item']);
        $this->template = $IN['Form']->template;
    }


    public function getFeedbackContextMenu(Feedback $Item)
    {
        $arr = array();
        if ($Item->id) {
            $edit = ($this->action == 'view');
            if (!$edit) {
                $arr[] = array('href' => $this->url . '&action=view&id=' . (int)$Item->id, 'name' => $this->_('VIEW'), 'icon' => 'edit');
                if ($Item->vis) {
                    $arr[] = array('href' => $this->url . '&action=chvis&id=' . (int)$Item->id, 'name' => $this->_('MARK_AS_UNREAD'), 'icon' => 'eye-close');
                }
            }
            $arr[] = array(
                'href' => $this->url . '&action=delete&id=' . (int)$Item->id . ($edit ? '' : '&back=1'),
                'name' => $this->_('DELETE'),
                'icon' => 'remove',
                'onclick' => 'return confirm(\'' . $this->_('DELETE_TEXT') . '\')'
            );
        }
        return $arr;
    }


    public function getAllFeedbacksContextMenu()
    {
        $arr = array();
        $arr[] = array(
            'name' => $this->_('MARK_AS_UNREAD'),
            'href' => $this->url . '&action=chvis&back=1',
            'icon' => 'eye-close',
            'title' => $this->_('MARK_AS_UNREAD')
        );
        $arr[] = array(
            'name' => $this->_('DELETE'),
            'href' => $this->url . '&action=delete&back=1',
            'icon' => 'remove',
            'onclick' => 'return confirm(\'' . $this->_('DELETE_MULTIPLE_TEXT') . '\')'
        );
        return $arr;
    }


    public function feedback(array $IN = array())
    {
        $view = $this;
        $IN['Table'] = new FeedbackTable($IN);
        $this->assignVars($IN);
        if ($IN['Item']->id) {
            $this->path[] = array('name' => $this->_('FEEDBACK'), 'href' => $this->url);
        }
        foreach ((array)$IN['Forms'] as $row) {
            $this->submenu[] = array(
                'name' => $row->name . ($row->unreadFeedbacks ? ' (' . (int)$row->unreadFeedbacks . ')' : ''),
                'href' => $this->url . '&id=' . (int)$row->id,
                'active' => ($row->id == $IN['Item']->id)
            );
        }
        $this->title = $IN['Table']->caption;
        $this->template = $IN['Table']->template;
        $this->contextmenu = array(
            array(
                'name' => $this->_('EXPORT_CSV_UTF8'),
                'href' => HTTP::queryString('action=export&format=csv'),
            ),
            array(
                'name' => $this->_('EXPORT_CSV_WIN1251'),
                'href' => HTTP::queryString('action=export&format=csv1251'),
            ),
            array(
                'name' => $this->_('EXPORT_EXCEL'),
                'href' => HTTP::queryString('action=export&format=xls'),
            ),
            array(
                'name' => $this->_('EXPORT_EXCEL2007'),
                'href' => HTTP::queryString('action=export&format=xlsx'),
            ),
        );
    }
}
