<?php
namespace RAAS\CMS;

class Block_Search extends Block
{
    protected static $tablename2 = 'cms_blocks_search';

    protected static $links = array(
        'pages' => array('tablename' => 'cms_blocks_pages_assoc', 'field_from' => 'block_id', 'field_to' => 'page_id', 'classname' => 'RAAS\\CMS\\Page'),
        'mtypes' => array('tablename' => 'cms_blocks_search_material_types_assoc', 'field_from' => 'id', 'field_to' => 'material_type'),
        'material_types' => array('tablename' => 'cms_blocks_search_material_types_assoc', 'field_from' => 'id', 'field_to' => 'material_type', 'classname' => 'RAAS\\CMS\\Material_Type'),
        'languages' => array('tablename' => 'cms_blocks_search_languages_assoc', 'field_from' => 'id', 'field_to' => 'language'),
        'search_pages' => array('tablename' => 'cms_blocks_search_pages_assoc', 'field_from' => 'id', 'field_to' => 'page_id', 'classname' => 'RAAS\\CMS\\Page'),
    );


    public function __get($var)
    {
        switch ($var) {
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct($import_data = null)
    {
        parent::__construct($import_data);
    }


    public function commit()
    {
        if (!$this->name) {
            $this->name = Package::i()->view->_('SITE_SEARCH');
        }
        parent::commit();
        self::$SQL->query("DELETE FROM " . self::$dbprefix . "cms_blocks_search_material_types_assoc WHERE id = " . (int)$this->id);
        $arr = array();
        if ($this->meta['mtypes'] && is_array($this->meta['mtypes'])) {
            for ($i = 0; $i < count($this->meta['mtypes']); $i++) {
                $val = $this->meta['mtypes'][$i];
                $arr[] = array('id' => (int)$this->id, 'material_type' => (int)$val);
            }
        }
        if ($arr) {
            self::$SQL->add(self::$dbprefix . "cms_blocks_search_material_types_assoc", $arr);
        }

        self::$SQL->query("DELETE FROM " . self::$dbprefix . "cms_blocks_search_languages_assoc WHERE id = " . (int)$this->id);
        $arr = array();
        if ($this->meta['languages'] && is_array($this->meta['languages'])) {
            for ($i = 0; $i < count($this->meta['languages']); $i++) {
                if ($val = $this->meta['languages'][$i]) {
                    $arr[] = array('id' => (int)$this->id, 'language' => (string)$val);
                }
            }
        }
        if ($arr) {
            self::$SQL->add(self::$dbprefix . "cms_blocks_search_languages_assoc", $arr);
        }

        self::$SQL->query("DELETE FROM " . self::$dbprefix . "cms_blocks_search_pages_assoc WHERE id = " . (int)$this->id);
        $arr = array();
        if ($this->meta['search_pages_ids'] && is_array($this->meta['search_pages_ids'])) {
            for ($i = 0; $i < count($this->meta['search_pages_ids']); $i++) {
                if ($val = $this->meta['search_pages_ids'][$i]) {
                    $arr[] = array('id' => (int)$this->id, 'page_id' => (int)$val);
                }
            }
        }
        if ($arr) {
            self::$SQL->add(self::$dbprefix . "cms_blocks_search_pages_assoc", $arr);
        }
    }


    public function getAddData()
    {
        return array(
            'id' => (int)$this->id,
            'search_var_name' => (string)$this->search_var_name,
            'min_length' => (int)$this->min_length,
            'pages_var_name' => (string)$this->pages_var_name,
            'rows_per_page' => (int)$this->rows_per_page,
        );
    }
}
