<?php
/**
 * HTML-представление
 */
namespace RAAS\CMS;

use RAAS\Package_View_Web as RAASPackageViewWeb;

/**
 * Класс HTML-представления
 */
class View_Web extends RAASPackageViewWeb
{
    protected static $instance;

    public function header()
    {
        $this->js[] = $this->publicURL . '/package.js';
        $this->css[] = $this->publicURL . '/package.css';
        $this->css[] = $this->publicURL . '/style.css';
        $c = Feedback::unreadFeedbacks();
        $this->menu[] = [
            'href' => $this->url . '&sub=main',
            'name' => $this->_('PAGES'),
            'active' => (
                (
                    !$this->sub ||
                    ($this->sub == 'main')
                ) &&
                !$this->moduleName
            )
        ];
        $this->menu[] = [
            'href' => $this->url . '&sub=feedback',
            'name' => $this->_('FEEDBACK') . ($c ? ' (' . $c . ')' : '')
        ];
        $this->menu[] = [
            'href' => $this->url . '&sub=dev',
            'name' => $this->_('DEVELOPMENT')
        ];
    }
}
