<?php
namespace RAAS\CMS;

class Snippet extends \SOME\SOME
{
    protected static $tablename = 'cms_snippets';
    protected static $defaultOrderBy = "name";
    protected static $cognizableVars = array();

    protected static $references = array(
        'parent' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Snippet_Folder', 'cascade' => true),
    );

    public function commit()
    {
        if (!$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        if ($this->updates['urn']) {
            $this->urn = \SOME\Text::beautify($this->urn);
        }
        while ((int)self::$SQL->getvalue(array("SELECT COUNT(*) FROM " . self::_tablename() . " WHERE urn = ? AND id != ?", $this->urn, (int)$this->id))) {
            $this->urn = '_' . $this->urn . '_';
        }
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