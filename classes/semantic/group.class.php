<?php 
namespace RAAS\CMS;

class Group extends \SOME\SOME
{
    protected static $tablename = 'groups';
    protected static $defaultOrderBy = "name";
    protected static $cognizableVars = array();

    protected static $references = array('parent' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Group', 'cascade' => true));
    protected static $parents = array('parents' => 'parent');
    protected static $children = array('children' => array('classname' => 'RAAS\\CMS\\Group', 'FK' => 'pid'));
    protected static $links = array('users' => array('tablename' => 'cms_users_groups_assoc', 'field_from' => 'gid', 'field_to' => 'uid', 'classname' => 'RAAS\\CMS\\User'));
}