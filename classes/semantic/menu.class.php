<?php
namespace RAAS\CMS;

class Menu extends \SOME\SOME
{
    protected static $tablename = 'cms_menus';
    protected static $defaultOrderBy = "priority";
    protected static $cognizableVars = array('subMenu');

    protected static $references = array(
        'parent' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Menu', 'cascade' => true),
        'page' => array('FK' => 'page_id', 'classname' => 'RAAS\\CMS\\Page', 'cascade' => true)
    );
    protected static $parents = array('parents' => 'parent');
    protected static $children = array('children' => array('classname' => 'RAAS\\CMS\\Menu', 'FK' => 'pid'));
    protected static $links = array();
    
    protected static $caches = array('pvis' => array('affected' => array('parent'), 'sql' => "IF(parent.id, (parent.vis AND parent.pvis), 1)"));
    
    public function __get($var)
    {
        switch ($var) {
            case 'url': // 2015-09-23, AVS: сделал, чтобы при переносе страницы URL сохранялся
                if ($this->page->id) {
                    return $this->page->url;
                }
                return parent::__get($var);
                break;
            case 'visSubMenu':
                return array_values(array_filter($this->subMenu, function($x) { return $x->vis; }));
                break;
            case 'visChildren':
                return array_values(array_filter($this->children, function($x) { return $x->vis; }));
                break;
            default:
                return parent::__get($var);
                break;
        }
    }
    
    
    public function commit()
    {
        if ($this->page_id) {
            $this->url = $this->page->url;
            if (!$this->name) {
                $this->name = $this->page->getMenuName();
            }
        }
        if (!$this->pid) {
            if (!$this->urn && $this->name) {
                $this->urn = $this->name;
            }
            Package::i()->getUniqueURN($this);
        }
        parent::commit();
    }
    
    
    public function realize()
    {
        if ($this->page->id && ($this->inherit > 0)) {
            $i = 0;
            foreach ($this->children as $row) {
                if ($row->page_id) {
                    $realized[] = $row->page_id;
                }
            }
            foreach ($this->page->visChildren as $row2) {
                if (!in_array($row2->id, $realized) && !$row2->response_code) {
                    $row = new Menu();
                    $row->pid = $this->id;
                    $row->vis = $row2->vis && $row2->pvis;
                    $row->pvis = $this->vis && $this->pvis;
                    $row->name = $row2->getMenuName();
                    $row->url = $row2->url;
                    $row->page_id = $row2->id;
                    $row->inherit = $this->inherit - 1;
                    $row->priority = $i++;
                    $row->realized = false;
                    $row->commit();
                }
            }
            $this->inherit = 0;
            if (!$this->pid) {
                $this->page_id = 0;
            }
            $this->commit();
        }
    }
    
    
    public function findPage(Page $Page)
    {
        if (($this->page_id == $Page->id) || ($this->url == $Page->url)) {
            return $this;
        }
        foreach ($this->visSubMenu as $row) {
            if ($row2 = $row->findPage($Page)) {
                return $row2;
            }
        }
        return false;
    }
    
    
    public static function importByURN($urn = '')
    {
        $SQL_query = "SELECT * FROM " . self::_tablename() . " WHERE urn = ?";
        if ($SQL_result = self::$SQL->getline(array($SQL_query, $urn))) {
            return new self($SQL_result);
        }
        return null;
    }
    
    
    protected function _subMenu()
    {
        $temp = array();
        $realized = array();
        if ($this->id) {
            foreach ($this->children as $row) {
                $row->realized = true;
                $temp[] = $row;
                if ($row->page_id) {
                    $realized[] = $row->page_id;
                }
            }
        }
        if ($this->page->id && ($this->inherit > 0)) {
            $i = 0;
            foreach ($this->page->visChildren as $row2) {
                if (!in_array($row2->id, $realized) && !$row2->response_code) {
                    $row = new Menu();
                    $row->pid = $this->id;
                    $row->vis = $row2->vis && $row2->pvis;
                    $row->pvis = $this->vis && $this->pvis;
                    $row->name = $row2->getMenuName();
                    $row->url = $row2->url;
                    $row->page_id = $row2->id;
                    $row->inherit = $this->inherit - 1;
                    $row->priority = $i++;
                    $row->realized = false;
                    $temp[] = $row;
                }
            }
        }
        usort(
            $temp, 
            function($a, $b) { 
                if ($a->priority < $b->priority) {
                    return -1;
                } elseif ($a->priority > $b->priority) {
                    return 1;
                } elseif ((int)$a->realized < (int)$b->realized) {
                    return -1;
                } elseif ((int)$a->realized > (int)$b->realized) {
                    return 1;
                } else {
                    return 0;
                }
            }
        );
        return $temp;
    }
}