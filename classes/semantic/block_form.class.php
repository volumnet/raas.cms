<?php
namespace RAAS\CMS;

class Block_Form extends Block
{
    protected static $tablename2 = 'cms_blocks_form';

    protected static $references = array(
        'author' => array('FK' => 'author_id', 'classname' => 'RAAS\\User', 'cascade' => false),
        'editor' => array('FK' => 'editor_id', 'classname' => 'RAAS\\User', 'cascade' => false),
        'Form' => array('FK' => 'form', 'classname' => 'RAAS\\CMS\\Form', 'cascade' => true),
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
        if (!$this->name && $this->Form->id) {
            $this->name = $this->Form->name;
        }
        parent::commit();
    }


    public function getAddData()
    {
        return array(
            'id' => (int)$this->id,
            'form' => (int)$this->form,
        );
    }
}
