<?php
namespace RAAS\CMS;

class Dictionary extends \RAAS\Dictionary
{
    protected static $tablename = 'cms_dictionaries';
    protected static $references = array('parent' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Dictionary', 'cascade' => true));
    protected static $children = array('children' => array('classname' => 'RAAS\\CMS\\Dictionary', 'FK' => 'pid'));
    protected static $caches = array('pvis' => array('affected' => array('parent'), 'sql' => "IF(parent.id, (parent.vis AND parent.pvis), 1)"));
}