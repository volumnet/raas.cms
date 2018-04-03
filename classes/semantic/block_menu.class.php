<?php
namespace RAAS\CMS;

class Block_Menu extends Block
{
    protected static $tablename2 = 'cms_blocks_menu';

    protected static $references = array(
        'author' => array('FK' => 'author_id', 'classname' => 'RAAS\\User', 'cascade' => false),
        'editor' => array('FK' => 'editor_id', 'classname' => 'RAAS\\User', 'cascade' => false),
        'Menu' => array('FK' => 'menu', 'classname' => 'RAAS\\CMS\\Menu', 'cascade' => false),
    );

    public function __get($var)
    {
        switch ($var) {
            default:
                return parent::__get($var);
                break;
        }
    }


    public function commit()
    {
        if (!$this->name && $this->Menu->id) {
            $this->name = $this->Menu->name;
        }
        parent::commit();
    }


    protected function getAddData()
    {
        return array(
            'id' => (int)$this->id,
            'menu' => (int)$this->menu,
            'full_menu' => (int)$this->full_menu,
        );
    }
}
