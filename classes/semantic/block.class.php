<?php
namespace RAAS\CMS;

abstract class Block extends \SOME\SOME
{
    protected static $tablename = 'cms_blocks';
    protected static $tablename2;
    protected static $defaultOrderBy = "priority";
    protected static $cognizableVars = array('Location');

    protected static $references = array(
        'author' => array('FK' => 'author_id', 'classname' => 'RAAS\\User', 'cascade' => false),
        'editor' => array('FK' => 'editor_id', 'classname' => 'RAAS\\User', 'cascade' => false),
    );
    protected static $parents = array();
    protected static $children = array();
    protected static $links = array('pages' => array('tablename' => 'cms_blocks_pages_assoc', 'field_from' => 'block_id', 'field_to' => 'page_id', 'classname' => 'RAAS\\CMS\\Page'));
    
    protected static $caches = array();
    
    public static function spawn($import_data)
    {
        if (is_array($import_data)) {
            if (isset($import_data['block_type']) && ($classname = $import_data['block_type'])) {
                if (class_exists($classname)) {
                    return new $classname($import_data);
                }
            }
        } else {
            $SQL_query = "SELECT block_type FROM " . self::_tablename() . " WHERE id = ?";
            if ($classname = self::$SQL->getvalue(array($SQL_query, array($import_data)))) {
                if (class_exists($classname)) {
                    return new $classname($import_data);
                }
            }
        }
        return new Block_HTML($import_data);
    }


    public function __construct($import_data = null)
    {
        parent::__construct($import_data);
        $this->block_type = get_class($this);
        if (static::$tablename2) {
            $SQL_query = "SELECT * FROM " . static::$dbprefix . static::$tablename2 . " WHERE id = " . (int)$this->id;
            if ($SQL_result = self::$SQL->getline($SQL_query)) {
                foreach ($SQL_result as $key => $val) {
                    if (($key != 'id') && !isset($this->$key)) {
                        $this->$key = $val;
                    }
                }
            }
        }
    }


    public function __get($var)
    {
        switch ($var) {
            case 'Interface':
                return new Snippet((int)$this->interface_id);
                break;
            case 'Widget':
                return new Snippet((int)$this->widget_id);
                break;
            case 'parent':
                if ($this->pages) {
                    return new Page($this->pages_ids[0]);
                } else {
                    return new Page();
                }
                break;
            case 'pid':
                return $this->parent->id;
                break;
            case 'title':
                return htmlspecialchars($this->name);
                break;
            case 'pages_assoc':
                return parent::__get('pages');
                break;
            default:
                return parent::__get($var);
                break;
        }
    }
    
    
    public function commit()
    {
        $this->modify_date = date('Y-m-d H:i:s');
        if (!$this->id) {
            $this->post_date = $this->modify_date;
        }
        parent::commit();
        $this->exportPages();
        if (static::$tablename2 && ($arr = $this->getAddData())) {
            self::$SQL->query("DELETE FROM " . static::$tablename2 . " WHERE id = " . (int)$this->id);
            self::$SQL->add(static::$tablename2, $arr);
        }
    }


    protected function getAddData()
    {}
    

    private function exportPages()
    {
        if ($this->cats) {
            $ids = array_merge($this->cats, (array)$Parent->all_children_ids);
            $old_ids = array_diff($this->pages_ids, $ids);
            $new_ids = array_diff($ids, $this->pages_ids);
            if ($old_ids) {
                $SQL_query = "DELETE FROM " . self::_dbprefix() . self::$links['pages']['tablename'] . " 
                               WHERE block_id = " . (int)$this->id . " AND page_id IN (" . implode(", ", array_map('intval', $old_ids)) . ")";
                self::$SQL->query($SQL_query);
            }
            if ($new_ids) {
                $SQL_query = "SELECT MAX(priority) FROM " . self::$dbprefix . self::$links['pages']['tablename'];
                $priority = (int)self::$SQL->getvalue($SQL_query);
                $arr = array();
                foreach ($new_ids as $id) {
                    $arr[] = array('block_id' => $this->id, 'page_id' => (int)$id, 'priority' => ++$priority);
                }
                self::$SQL->add(self::$dbprefix . self::$links['pages']['tablename'], $arr);
            }
        }
    }
    
    
    public function swap($step, Page $Page)
    {
        $SQL_query = "SELECT priority FROM " . self::$dbprefix . self::$links['pages']['tablename'] 
                   . " WHERE block_id = " . (int)$this->id . " AND page_id = " . (int)$Page->id;
        $priority = (int)self::$SQL->getvalue($SQL_query);
        
        $SQL_query = "SELECT tBPA.block_id, tBPA.priority 
                        FROM " . self::$dbprefix . self::$links['pages']['tablename'] . " AS tBPA
                        JOIN " . self::_tablename() . " AS tB ON tB.id = tBPA.block_id 
                       WHERE tBPA.priority " . ($step < 0 ? "<" : ">") . " " . (int)$priority . " 
                         AND tBPA.page_id = " . (int)$Page->id . " 
                         AND tB.location = '" . self::$SQL->real_escape_string($this->location) . "' 
                    ORDER BY tBPA.priority " . ($step < 0 ? "DESC" : "ASC") . (!is_infinite($step) ? " LIMIT " . abs((int)$step) : "");
        $swapwith = static::$SQL->get($SQL_query);
        $save_ok = true;
        if ($swapwith) {
            for ($i = 0; $i < count($swapwith); $i++) {
                $swapId = static::$SQL->quote($swapwith[$i]['block_id']);
                $swapPri = (int)($i ? $swapwith[$i - 1]['priority'] : (int)$priority);
                $save_ok &= static::$SQL->update(
                    self::$dbprefix . self::$links['pages']['tablename'], "page_id = " . (int)$Page->id . " AND block_id = " . $swapId, array('priority' => $swapPri)
                );
            }
            $priority = (int)$swapwith[count($swapwith) - 1]['priority'];
            static::$SQL->update(
                self::$dbprefix . self::$links['pages']['tablename'], "page_id = " . (int)$Page->id . " AND block_id = " . $this->id, array('priority' => $priority)
            );
        }
        return $save_ok;
    }
    

    public function unassoc(Page $Page)
    {
        $SQL_query = "DELETE FROM " . self::$dbprefix . self::$links['pages']['tablename'] . " WHERE block_id = " . (int)$this->id . " AND page_id = " . (int)$Page->id;
        self::$SQL->query($SQL_query);
        $this->reload();
        if (!$this->pages_assoc) {
            self::delete($this);
        }
    }
    

    public function process(Page $Page)
    {
        $SITE = $Page->Domain;
        $Block = $this;
        $config = $this->getAddData();
        $IN = (array)$this->processInterface($config, $Page);
        $IN['config'] = $config;
        $this->processWidget($IN, $Page);
    }
    

    protected function processInterface($config, $Page)
    {
        $SITE = $Page->Domain;
        $Block = $this;
        $OUT = null;
        if ($this->Interface->id) {
            $Interface = $this->Interface;
            eval('?' . '>' . $Interface->description);
        } else {
            $OUT = eval('?' . '>' . $this->interface);
        }
        return $OUT;
    }
    
    protected function processWidget(array $IN = array(), $Page)
    {
        $SITE = $Page->Domain;
        $Block = $this;
        extract($IN);
        if ($this->Widget->id) {
            $Widget = $this->Widget;
            eval('?' . '>' . $Widget->description);
        } else {
            eval('?' . '>' . @$this->widget);
        }
    }
    
    protected function _Location()
    {
        return $this->parent->Template->locations[$this->location];
    }
}