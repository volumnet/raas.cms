<?php
namespace RAAS\CMS;
use \RAAS\Table as Table;
use \RAAS\Column as Column;
use \RAAS\Row as Row;

class View_Web extends \RAAS\Package_View_Web
{
    protected static $instance;
    
    public function header()
    {
        $this->css[] = $this->publicURL . '/style.css';
        $c = Feedback::unreadFeedbacks();
        $this->menu[] = array('href' => $this->url . '&sub=main', 'name' => $this->_('PAGES'), 'active' => (!$this->sub || ($this->sub == 'main')) && !$this->moduleName);
        $this->menu[] = array('href' => $this->url . '&sub=feedback', 'name' => $this->_('FEEDBACK') . ($c ? ' (' . $c . ')' : ''));
        $this->menu[] = array('href' => $this->url . '&sub=dev', 'name' => $this->_('DEVELOPMENT'));
    }
}