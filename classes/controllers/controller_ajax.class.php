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
            case 'material_fields': case 'get_materials_by_field': case 'rebuild_block_cache': case 'clear_cache': case 'get_cache_map':
                $this->{$this->action}();
                break;
        }
    }


    protected function get_cache_map()
    {
        $OUT['Set'] = array_values($this->model->getCacheMap());
        $this->view->get_cache_map($OUT);
    }


    protected function clear_cache()
    {
        $this->model->clearCache(true);
        $this->view->clear_cache(array());
    }


    protected function rebuild_block_cache()
    {
        $Block = Block::spawn($this->id);
        $Page = new Page($this->nav['pid']);
        $url = $Page->url;
        if (isset($this->nav['mid'])) {
            $Material = new Material($this->nav['mid']);
            $Page->Material = $Material;
            foreach (array('name', 'meta_title', 'meta_keywords', 'meta_description') as $key) {
                $Page->{'old' . ucfirst($key)} = $Page->$key;
                $Page->$key = $Material->$key;
            }
            $Material->proceed = true;
            $url = $Material->url;
        }
        $oldServer = $_SERVER;
        if (!preg_match('/(^| )' . preg_quote($_SERVER['HTTP_HOST']) . '( |$)/i', $Page->Domain->urn)) {
            $_SERVER['HTTP_HOST'] = $Page->domain;
        }
        $_SERVER['REQUEST_URI'] = $url;
        $Block->process($Page, true);
        $_SERVER = $oldServer;
        $this->view->clear_cache(array());
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


    protected function get_materials_by_field()
    {
        $Field = new Material_Field((int)$this->id);
        $Set = array();
        if ($Field->datatype == 'material') {
            $mtype = (int)$Field->source;
            $Set = $this->model->getMaterialsBySearch(isset($_GET['search_string']) ? $_GET['search_string'] : '', $mtype);
        }
        $OUT['Set'] = array_map(
            function($x) { 
                $y = array(
                    'id' => (int)$x->id, 
                    'name' => $x->name, 
                    'description' => \SOME\Text::cuttext(html_entity_decode(strip_tags($x->description), ENT_COMPAT | ENT_HTML5, 'UTF-8'), 256, '...')
                );
                if ($x->parents) {
                    $y['pid'] = (int)$x->parents_ids[0];
                }
                foreach ($x->fields as $row) {
                    if ($row->datatype == 'image') {
                        if ($val = $row->getValue()) {
                            if ($val->id) {
                                $y['img'] = '/' . $val->fileURL;
                            }
                        }
                    }
                }
                return $y;
            },
            $Set
        );
        $this->view->show_page($OUT);
    }
}