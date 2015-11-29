<?php
namespace RAAS\CMS;

class Form extends \SOME\SOME
{
    protected static $tablename = 'cms_forms';
    protected static $defaultOrderBy = "name";
    protected static $objectCascadeDelete = true;
    protected static $cognizableVars = array('fields', 'unreadFeedbacks');
    protected static $references = array(
        'Material_Type' => array('FK' => 'material_type', 'classname' => 'RAAS\\CMS\\Material_Type', 'cascade' => false),
        'Interface' => array('FK' => 'interface_id', 'classname' => 'RAAS\\CMS\\Snippet', 'cascade' => false),
    );

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
        foreach ($object->fields as $row) {
            Form_Field::delete($row);
        }
        parent::delete($object);
    }
    
    
    public static function importByURN($urn = '')
    {
        $SQL_query = "SELECT * FROM " . self::_tablename() . " WHERE urn = ?";
        if ($SQL_result = self::$SQL->getline(array($SQL_query, $urn))) {
            return new self($SQL_result);
        }
        return null;
    }


    protected function _fields()
    {
        $SQL_query = "SELECT * FROM " . Form_Field::_tablename() . " WHERE classname = ? AND pid = ? ORDER BY priority";
        $SQL_bind = array(get_class($this), (int)$this->id);
        $temp = Form_Field::getSQLSet(array($SQL_query, $SQL_bind));
        $arr = array();
        foreach ($temp as $row) {
            $arr[$row->urn] = $row;
        }
        return $arr;
    }


    protected function _unreadFeedbacks()
    {
        $SQL_query = "SELECT COUNT(*) FROM " . Feedback::_tablename() . " WHERE pid = " . (int)$this->id . " AND NOT vis";
        return self::$SQL->getvalue($SQL_query);
    }
}