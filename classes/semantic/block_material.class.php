<?php
namespace RAAS\CMS;

class Block_Material extends Block
{
    protected static $tablename2 = 'cms_blocks_material';

    protected static $references = array(
        'author' => array('FK' => 'author_id', 'classname' => 'RAAS\\User', 'cascade' => false),
        'editor' => array('FK' => 'editor_id', 'classname' => 'RAAS\\User', 'cascade' => false),
        'Material_Type' => array('FK' => 'material_type', 'classname' => 'RAAS\\CMS\\Material_Type', 'cascade' => true),
    );

    public static $filterRelations = array(
        '=' => 'EQUALS', 'LIKE' => 'CONTAINS', 'CONTAINED' => 'CONTAINED', 'FULLTEXT' => 'FULLTEXT', '<=' => 'EQUALS_OR_SMALLER', '>=' => 'EQUALS_OR_GREATER'
    );

    public static $orderRelations = array('asc!' => 'ASCENDING_ONLY', 'desc!' => 'DESCENDING_ONLY', 'asc' => 'ASCENDING_FIRST', 'desc' => 'DESCENDING_FIRST');

    public function __construct($import_data = null)
    {
        parent::__construct($import_data);
        $SQL_query = "SELECT var, relation, field FROM " . self::$dbprefix . "cms_blocks_material_filter WHERE id = " . (int)$this->id . " ORDER BY priority";
        $this->filter = self::$SQL->get($SQL_query);
        $SQL_query = "SELECT var, field, relation FROM " . self::$dbprefix . "cms_blocks_material_sort WHERE id = " . (int)$this->id . " ORDER BY priority";
        $this->sort = self::$SQL->get($SQL_query);
    }


    public function commit()
    {
        if (!$this->name && $this->Material_Type->id) {
            $this->name = $this->Material_Type->name;
        }
        parent::commit();
        self::$SQL->query("DELETE FROM " . self::$dbprefix . "cms_blocks_material_filter WHERE id = " . (int)$this->id);
        $arr = array();
        if ($this->filter && is_array($this->filter)) {
            for ($i = 0; $i < count($this->filter); $i++) {
                if ($row = $this->filter[$i]) {
                    $arr[] = array(
                        'id' => (int)$this->id, 'var' => (string)$row['var'], 'relation' => (string)$row['relation'], 'field' => (string)$row['field'], 'priority' => ($i + 1)
                    );
                }
            }
        }
        if ($arr) {
            self::$SQL->add(self::$dbprefix . "cms_blocks_material_filter", $arr);
        }

        self::$SQL->query("DELETE FROM " . self::$dbprefix . "cms_blocks_material_sort WHERE id = " . (int)$this->id);
        $arr = array();
        if ($this->sort && is_array($this->sort)) {
            for ($i = 0; $i < count($this->sort); $i++) {
                if ($row = $this->sort[$i]) {
                    $arr[] = array(
                        'id' => (int)$this->id, 'var' => (string)$row['var'], 'field' => (string)$row['field'], 'relation' => (string)$row['relation'], 'priority' => ($i + 1)
                    );
                }
            }
        }
        if ($arr) {
            self::$SQL->add(self::$dbprefix . "cms_blocks_material_sort", $arr);
        }
    }


    public function getAddData()
    {
        return array(
            'id' => (int)$this->id,
            'material_type' => (int)$this->material_type,
            'pages_var_name' => (string)$this->pages_var_name,
            'rows_per_page' => (int)$this->rows_per_page,
            'sort_var_name' => (string)$this->sort_var_name,
            'order_var_name' => (string)$this->order_var_name,
            'sort_field_default' => (string)$this->sort_field_default,
            'sort_order_default' => (string)$this->sort_order_default,
            'legacy' => (int)$this->legacy,
        );
    }
}
