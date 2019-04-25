<?php
namespace RAAS\CMS;

use \RAAS\Application;

class Dictionary extends \RAAS\Dictionary
{
    use RecursiveTrait;

    protected static $tablename = 'cms_dictionaries';
    protected static $references = array('parent' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Dictionary', 'cascade' => true));
    protected static $children = array('children' => array('classname' => 'RAAS\\CMS\\Dictionary', 'FK' => 'pid'));
    protected static $caches = array('pvis' => array('affected' => array('parent'), 'sql' => "IF(parent.id, (parent.vis AND parent.pvis), 1)"));

    protected static $cognizableVars = [
        'selfAndChildren',
        'selfAndChildrenIds',
        'selfAndParents',
        'selfAndParentsIds',
    ];

    public function __get($var)
    {
        switch ($var) {
            case 'visChildren':
                return array_values(array_filter($this->children, function($x) { return $x->vis; }));
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function commit()
    {
        if (!$this->pid) {
            if (!$this->urn && $this->name) {
                $this->urn = $this->name;
            }
            $this->urn = \SOME\Text::beautify($this->urn);
            for ($i = 0; $this->checkForSimilar($this); $i++) {
                $this->urn = Application::i()->getNewURN($this->urn, !$i);
            }
        }
        parent::commit();
    }


    public function checkForSimilar()
    {
        $SQL_query = "SELECT COUNT(*) FROM " . self::_tablename() . " WHERE urn = ? AND id != ? AND pid = ?";
        $SQL_result = self::_SQL()->getvalue(array($SQL_query, $this->urn, (int)$this->id, (int)$this->pid));
        $c = (bool)(int)$SQL_result;
        return $c;
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
