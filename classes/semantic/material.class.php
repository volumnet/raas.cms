<?php
namespace RAAS\CMS;

class Material extends \SOME\SOME
{
    protected static $tablename = 'cms_materials';
    protected static $defaultOrderBy = "post_date DESC";
    protected static $cognizableVars = array('fields');

    protected static $references = array(
        'material_type' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Material_Type', 'cascade' => true),
        'author' => array('FK' => 'author_id', 'classname' => 'RAAS\\User', 'cascade' => false),
        'editor' => array('FK' => 'editor_id', 'classname' => 'RAAS\\User', 'cascade' => false),
    );
    protected static $links = array('pages' => array('tablename' => 'cms_materials_pages_assoc', 'field_from' => 'id', 'field_to' => 'pid', 'classname' => 'RAAS\\CMS\\Page'));
    
    public function __get($var)
    {
        switch ($var) {
            case 'parent':
                if ($this->pages) {
                    return $this->pages[0];
                } else {
                    return new Page();
                }
                break;
            default:
                $val = parent::__get($var);
                if ($val !== null) {
                    return $val;
                } else {
                    if (substr($var, 0, 3) == 'vis') {
                        $var = strtolower(substr($var, 3));
                        $vis = true;
                    }
                    if (isset($this->fields[$var]) && ($this->fields[$var] instanceof Material_Field)) {
                        $temp = $this->fields[$var]->getValues();
                        if ($vis) {
                            $temp = array_values(array_filter($temp, function($x) { return isset($x->vis) && $x->vis; }));
                        }
                        return $temp;
                    }
                }
                break;
        }
    }
    
    
    public function commit()
    {
        $this->modify_date = date('Y-m-d H:i:s');
        if (!$this->id) {
            $this->post_date = $this->modify_date;
        }
        if ($this->pid && !$this->urn && $this->name) {
            $this->urn = \SOME\Text::beautify($this->name);
        }
        while (
            (int)self::$SQL->getvalue(array("SELECT COUNT(*) FROM " . self::_tablename() . " WHERE urn = ? AND id != ?", $this->urn, (int)$this->id)) ||
            (int)self::$SQL->getvalue(array("SELECT COUNT(*) FROM " . Page::_tablename() . " WHERE urn = ?", $this->urn))
        ) {
            $this->urn = '_' . $this->urn . '_';
        }
        parent::commit();
        $this->exportPages();
    }
    
    
    private function exportPages()
    {
        if ($this->cats) {
            $SQL_query = "DELETE FROM " . self::_dbprefix() . self::$links['pages']['tablename'] . " WHERE id = " . (int)$this->id;
            self::$SQL->query($SQL_query);
            $id = (int)$this->id;
            $arr = array_map(function($x) use ($id) { return array('id' => $id, 'pid' => $x); }, (array)$this->cats);
            unset($this->cats);
            self::$SQL->add(self::$dbprefix . self::$links['pages']['tablename'], $arr);
        } elseif ($this->material_type->global_type) {
            $SQL_query = "DELETE FROM " . self::_dbprefix() . self::$links['pages']['tablename'] . " WHERE id = " . (int)$this->id;
            self::$SQL->query($SQL_query);
        }
    }
    
    
    public static function delete(self $object)
    {
        foreach ($object->fields as $row) {
            $row->deleteValues();
        }
        parent::delete($object);
    }
    
    
    protected function _fields()
    {
        $temp = $this->material_type->fields;
        $arr = array();
        foreach ($temp as $row) {
            $row->Owner = $this;
            $arr[$row->urn] = $row;
        }
        return $arr;
    }
}