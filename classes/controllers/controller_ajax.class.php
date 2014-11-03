<?php
namespace RAAS\CMS;
use \RAAS\Redirector as Redirector;
use \RAAS\Attachment as Attachment;

class Controller_Ajax extends Abstract_Controller
{
    protected static $instance;
    
    protected function execute()
    {
        switch ($this->action) {
            case 'material_fields':
                $this->{$this->action}();
                break;
        }
    }
    
    
    protected function material_fields()
    {
        
        $Material_Type = new Material_Type((int)$this->id);
        $Set = array(
            (object)array('id' => 'name', 'name' => $this->view->_('NAME')),
            (object)array('id' => 'urn', 'name' => $this->view->_('URN')),
            (object)array('id' => 'description', 'name' => $this->view->_('DESCRIPTION')),
            (object)array('id' => 'post_date', 'name' => $this->view->_('CREATED_BY')),
            (object)array('id' => 'modify_date', 'name' => $this->view->_('EDITED_BY'))
        );
        $Set = array_merge(
            $Set, array_values(array_filter($Material_Type->fields, function($x) { return !($x->multiple || in_array($x->datatype, array('file', 'image'))); }))
        );
        $OUT['Set'] = array_map(function($x) { return array('val' => $x->id, 'text' => $x->name); }, $Set);
        $this->view->show_page($OUT);
    }
}