<?php
namespace RAAS\CMS;

class Material_Type extends \SOME\SOME
{
    protected static $tablename = 'cms_material_types';
    protected static $defaultOrderBy = "name";
    protected static $objectCascadeDelete = true;
    protected static $references = array(
        'parent' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Material_Type', 'cascade' => true),
    );
    protected static $parents = array('parents' => 'parent');
    protected static $children = array('children' => array('classname' => 'RAAS\\CMS\\Material_Type', 'FK' => 'pid'));
    protected static $cognizableVars = array('fields', 'selfFields', 'affectedPages');

    public function commit()
    {
        if (!$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        Package::i()->getUniqueURN($this);
        parent::commit();
    }


    public static function delete(self $object)
    {
        foreach ($object->selfFields as $row) {
            Material_Field::delete($row);
        }
        parent::delete($object);
    }


    protected function _selfFields()
    {
        $SQL_query = "SELECT * FROM " . Material_Field::_tablename() . " WHERE classname = ? AND pid = ? ORDER BY priority";
        $SQL_bind = array(get_class($this), (int)$this->id);
        $temp = Material_Field::getSQLSet(array($SQL_query, $SQL_bind));
        $arr = array();
        foreach ($temp as $row) {
            $arr[$row->urn] = $row;
        }
        return $arr;
    }


    protected function _fields()
    {
        $arr1 = array();
        if ($this->parent->id) {
            $arr1 = (array)$this->parent->fields;
        }
        $arr2 = (array)$this->selfFields;
        $arr = array_merge($arr1, $arr2);
        return $arr;
    }


    public static function importByURN($urn)
    {
        $SQL_query = "SELECT * FROM " . self::_tablename() . " WHERE urn = ?";
        $SQL_bind = array($urn);
        if ($SQL_result = self::$SQL->getline(array($SQL_query, $SQL_bind))) {
            return new self($SQL_result);
        } else {
            return new self();
        }
    }

    protected function _affectedPages()
    {
        if (!$this->global_type) {
            $SQL_query = "SELECT tP.id
                            FROM " . Page::_tablename() . " AS tP
                            JOIN " . self::$dbprefix . "cms_materials_pages_assoc AS tMPA ON tMPA.pid = tP.id
                            JOIN " . Material::_tablename() . " AS tM ON tM.id = tMPA.id
                           WHERE tM.pid = " . (int)$this->id . "
                        ORDER BY tP.priority";
            $col1 = (array)self::$SQL->getcol($SQL_query);
        } else {
            $col1 = array();
        }
        $SQL_query = "SELECT tP.id
                        FROM " . Page::_tablename() . " AS tP
                        JOIN " . self::$dbprefix . "cms_blocks_pages_assoc AS tBPA ON tBPA.page_id = tP.id
                        JOIN " . Block::_tablename() . " AS tB ON tB.id = tBPA.block_id
                        JOIN " . Block::_dbprefix() . "cms_blocks_material AS tBM ON tBM.id = tB.id
                       WHERE tBM.material_type = " . (int)$this->id;
        $col2 = (array)self::$SQL->getcol($SQL_query);
        $Set = array_values(array_unique(array_merge($col1, $col2)));
        $Set = array_map(function($x) { return new \RAAS\CMS\Page($x); }, $Set);
        return $Set;
    }
}
