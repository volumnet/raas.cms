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
        Package::i()->getUniqueURN($this);
        parent::commit();
    }


    public function process(array $DATA = array())
    {
        $st = microtime(true);
        extract($DATA);
        $result = eval('?' . '>' . $this->description);
        if ($diag = Controller_Frontend::i()->diag) {
            $diag->handle('snippets', $this->id, microtime(true) - $st);
        }
        return $result;
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
