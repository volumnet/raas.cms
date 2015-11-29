<?php 
namespace RAAS\CMS;

class Group extends \SOME\SOME
{
    protected static $tablename = 'cms_groups';
    protected static $defaultOrderBy = "name";
    protected static $cognizableVars = array();

    protected static $references = array('parent' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Group', 'cascade' => true));
    protected static $parents = array('parents' => 'parent');
    protected static $children = array('children' => array('classname' => 'RAAS\\CMS\\Group', 'FK' => 'pid'));
    protected static $links = array('users' => array('tablename' => 'cms_users_groups_assoc', 'field_from' => 'gid', 'field_to' => 'uid', 'classname' => 'RAAS\\CMS\\User'));

    public function commit()
    {
        if (!$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        Package::i()->getUniqueURN($this);
        parent::commit();
    }
    
    
    public static function importByURN($urn = '')
    {
        $SQL_query = "SELECT * FROM " . self::_tablename() . " WHERE urn = ?";
        if ($SQL_result = self::$SQL->getline(array($SQL_query, $urn))) {
            return new self($SQL_result);
        }
        return null;
    }
}