<?php
namespace RAAS\CMS;

use \RAAS\Attachment;
use \RAAS\Application;

class Page extends \SOME\SOME implements IAccessible
{
    protected static $tablename = 'cms_pages';
    protected static $defaultOrderBy = "priority";
    protected static $cognizableVars = array('blocksOrdered', 'fields', 'affectedMaterialTypes', 'affectedMaterials', 'Domain');
    protected static $objectCascadeDelete = true;
        
    protected static $references = array(
        'parent' => array('FK' => 'pid', 'classname' => 'RAAS\\CMS\\Page', 'cascade' => true),
        'author' => array('FK' => 'author_id', 'classname' => 'RAAS\\User', 'cascade' => false),
        'editor' => array('FK' => 'editor_id', 'classname' => 'RAAS\\User', 'cascade' => false),
        'Template' => array('FK' => 'template', 'classname' => 'RAAS\\CMS\\Template', 'cascade' => false),
    );
    protected static $parents = array('parents' => 'parent');
    protected static $children = array(
        'children' => array('classname' => 'RAAS\\CMS\\Page', 'FK' => 'pid'),
        'access' => array('classname' => 'RAAS\\CMS\\CMSAccess', 'FK' => 'page_id'),
    );
    protected static $links = array(
        'blocks' => array('tablename' => 'cms_blocks_pages_assoc', 'field_from' => 'page_id', 'field_to' => 'block_id', 'classname' => 'RAAS\\CMS\\Block'),
        'materials' => array('tablename' => 'cms_materials_pages_assoc', 'field_from' => 'pid', 'field_to' => 'id', 'classname' => 'RAAS\\CMS\\Material')
    );
    
    protected static $caches = array('pvis' => array('affected' => array('parent'), 'sql' => "IF(parent.id, (parent.vis AND parent.pvis), 1)"));

    public static $httpStatuses = array(
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        102 => '102 Processing',
        200 => '200 OK',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        207 => '207 Multi-Status',
        226 => '226 IM Used',
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Moved Temporarily',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        307 => '307 Temporary Redirect',
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        407 => '407 Proxy Authentication Required',
        408 => '408 Request Timeout',
        409 => '409 Conflict',
        410 => '410 Gone',
        411 => '411 Length Required',
        412 => '412 Precondition Failed',
        413 => '413 Request Entity Too Large',
        414 => '414 Request-URI Too Large',
        415 => '415 Unsupported Media Type',
        416 => '416 Requested Range Not Satisfiable',
        417 => '417 Expectation Failed',
        422 => '422 Unprocessable Entity',
        423 => '423 Locked',
        424 => '424 Failed Dependency',
        425 => '425 Unordered Collection',
        426 => '426 Upgrade Required',
        449 => '449 Retry With',
        456 => '456 Unrecoverable Error',
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Timeout',
        505 => '505 HTTP Version Not Supported',
        506 => '506 Variant Also Negotiates',
        507 => '507 Insufficient Storage',
        508 => '508 Loop Detected',
        509 => '509 Bandwidth Limit Exceeded',
        510 => '510 Not Extended',
    );

    protected static $inheritedFields = array(
        'inherit_meta_title' => 'meta_title', 
        'inherit_meta_description' => 'meta_description', 
        'inherit_meta_keywords' => 'meta_keywords', 
        'inherit_changefreq' => 'changefreq', 
        'inherit_sitemaps_priority' => 'sitemaps_priority', 
        'inherit_template' => 'template',
        'inherit_lang' => 'lang',
        'inherit_cache' => 'cache'
    );

    private $locationBlocksText = array();
    
    public function __get($var)
    {
        switch ($var) {
            case 'URLArray':
                $temp = array();
                if ($this->parents) {
                    foreach ($this->parents as $row) {
                        if ($row->pid) {
                            $temp[] = $row->urn;
                        }
                    }
                }
                if ($this->pid) {
                    $temp[] = $this->urn;
                }
                return $temp;
                break;
            case 'url':
                $url = implode('/', $this->URLArray);
                return $url ? '/' . $url . '/' : '/';
                break;
            case 'additionalURL':
                $url = preg_replace('/^' . preg_quote($this->url, '/') . '/ui', '', $this->initialURL);
                $url = trim($url);
                return $url;
            case 'additionalURLArray':
                $url = trim($this->additionalURL, '/');
                $url = trim($url);
                $urlArray = explode('/', $url);
                $urlArray = array_filter($urlArray, 'trim');
                $urlArray = array_values($urlArray);
                return $urlArray;
            case 'blocksByLocations':
                $blocks = array();
                foreach ($this->blocksOrdered as $row) {
                    if (isset($this->Template->locations[$row->location])) {
                        $blocks[$row->location][] = $row;
                    } else {
                        $blocks[''][] = $row;
                    }
                }
                return $blocks;
                break;
            case 'domain':
                $temp = explode(' ', $this->Domain->urn);
                return 'http://' . str_replace('http://', '', $temp[0]);
                break;
            case 'visChildren':
                return array_values(array_filter($this->children, function($x) { return $x->vis; }));
                break;
            case 'locationBlocksText':
                return $this->locationBlocksText;
                break;
            case 'cacheFile':
                $url = preg_match('/(^| )' . preg_quote($_SERVER['HTTP_HOST']) . '( |$)/i', $this->Domain->urn) ? $_SERVER['HTTP_HOST'] : str_replace('http://', '', $this->domain);
                if ($this->Material->id) {
                    $url .= $this->Material->url;
                } else {
                    $url .= $this->url;
                }
                $url = Package::i()->cacheDir . '/' . Package::i()->cachePrefix . '.' . urlencode($url) . '.php';
                return $url;
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
                    if (isset($this->fields[$var]) && ($this->fields[$var] instanceof Page_Field)) {
                        $temp = $this->fields[$var]->getValues();
                        if ($vis) {
                            $temp = array_values(array_filter($temp, function($x) { return isset($x->vis) && $x->vis; }));
                        }
                        return $temp;
                    } else {
                        // 2015-03-02 AVS: ��-�� ������ ������ ��� ��������� ->pid
                        unset($this->fields);
                    }
                }
                break;
        }
    }
    
    
    public function commit()
    {
        $new = !$this->id;
        $this->modify(false);
        $this->modify_date = date('Y-m-d H:i:s');
        if (!$this->id) {
            $this->post_date = $this->modify_date;
        }
        if (!$this->id || !$this->priority) {
            $this->priority = self::$SQL->getvalue("SELECT MAX(priority) FROM " . self::_tablename()) + 1;
        }
        if ($this->pid && !$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        if ($this->updates['urn'] && $this->pid) {
            $this->urn = \SOME\Text::beautify($this->urn);
        }
        for ($i = 0; $this->checkForSimilarPages() || $this->checkForSimilarMaterials(); $i++) {
            $this->urn = Application::i()->getNewURN($this->urn, !$i);
        }
        
        
        $enableHeritage = false;
        foreach (self::$inheritedFields as $key => $val) {
            if ($this->$key) {
                $enableHeritage = true;
            }
        }
        if ($enableHeritage) {
            foreach ($this->children as $row) {
                // 2014-11-18, AVS: ���������, ��������� childrens ��������� �� SQL-������� � ������ properties � ��� �������, ������� ���������� �������������
                $row->reload(); 
                foreach (self::$inheritedFields as $key => $val) {
                    // ���� ����������� � �������� ��������� �������� ��������� �� ������ ��������� ��������
                    // 2014-11-18, AVS: ������ $this->update[$key] �� $this->$key, �.�. ��� ���� ������������ �� ����������� ������ ��������
                    if ($this->$key && ($row->$key == $this->properties[$key])) { 
                        $row->$val = $this->$val;
                    }
                }
                
                $row->commit();
            }
        }
        
        parent::commit();
        
        if (($this->template == $this->parent->template) && $new) {
            $SQL_query = "SELECT tB.*
                            FROM " . Block::_tablename() . " AS tB
                            JOIN " . self::$dbprefix . self::$links['blocks']['tablename'] . " AS tBPA ON tBPA.block_id = tB.id 
                           WHERE tBPA.page_id = " . (int)$this->pid . " AND inherit ORDER BY priority";
            $SQL_result = array_map(function($x) { return Block::spawn($x); }, \SOME\SOME::getSQLSet($SQL_query));
            if ($SQL_result) {
                $arr = array();
                $priority = (int)self::$SQL->getvalue("SELECT MAX(priority) FROM " . self::$dbprefix . self::$links['blocks']['tablename']);
                foreach ($SQL_result as $row) {
                    $arr[] = array('page_id' => $this->id, 'block_id' => $row->id, 'priority' => ++$priority);
                }
                self::$SQL->add(self::$dbprefix . self::$links['blocks']['tablename'], $arr);
            }
        }
    }
    
    
    public function getCodePage($code = 404)
    {
        $SQL_query = "SELECT * FROM " . Page::_tablename() . " WHERE pid = ? AND response_code = ? ORDER BY priority LIMIT 1";
        $SQL_bind = array($this->id, $code);
        if ($SQL_result = self::$SQL->getline(array($SQL_query, $SQL_bind))) {
            return new self($SQL_result);
        } elseif ($this->id) {
            return $this->parent->getCodePage($code);
        } else {
            return new self();
        }
    }
    
    
    public function process()
    {
        ob_start();
        if ($this->response_code && ($this->response_code != 200)) {
            header('HTTP/1.0 ' . Page::$httpStatuses[(int)$this->response_code]);
            header('Status: ' . Page::$httpStatuses[(int)$this->response_code]);
        }
        
        $SITE = $this->Domain;
        $Page = $this;
        if ($this->blocksByLocations['']) {
            echo $this->location('');
        }
        if ($this->template) {
            foreach ($this->Template->locations as $l => $loc) {
                $this->location($l);
            }
            eval('?' . '>' . $this->Template->description);
        }
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    
    public function location($location)
    {
        if (!isset($this->locationBlocksText[$location])) {
            $Location = new Location($this->Template, $location);
            $Set = (array)$this->blocksByLocations[$Location->urn];
            $texts = array();
            foreach ($Set as $row) {
                if ($row->vis) {
                    ob_start();
                    $bst = microtime(true);
                    $row->process($this);
                    Controller_Frontend::i()->diag ? Controller_Frontend::i()->diag->blockHandler($row, microtime(true) - $bst) : null;
                    $texts[$row->id] = ob_get_contents();
                    ob_end_clean();
                }
            }
            foreach ($Set as $row) {
                if ($row->tuneWithMaterial($this)) {
                    $this->locationBlocksText[$location][] = $texts[$row->id];
                }
            }
        }
        return implode('', (array)$this->locationBlocksText[$location]);
    }


    public function visit()
    {
        self::$SQL->update(self::_tablename(), "id = " . (int)$this->id, array('visit_counter' => $this->visit_counter++));
    }


    public function modify($commit = true)
    {
        $d0 = time();
        $d1 = strtotime($this->modify_date);
        $d2 = strtotime($this->last_modified);
        $arr = array();
        if ((time() - $d1 >= 3600) && (time() - $d2 >= 3600)) {
            $arr['last_modified'] = $this->last_modified = date('Y-m-d H:i:s');
            $arr['modify_counter'] = $this->modify_counter++;
            if ($commit) {
                self::$SQL->update(self::_tablename(), "id = " . (int)$this->id, $arr);
            }
        }
    }


    public function userHasAccess(User $user)
    {
        $a = CMSAccess::userHasCascadeAccess($this, $user);
        if ($a) {
            return ($a > 0);
        }
        if ($this->parent->id) {
            return $this->parent->userHasAccess($user);
        }
        return true;
    }


    public function currentUserHasAccess()
    {
        return $this->userHasAccess(Controller_Frontend::i()->user);
    }


    public function getH1($old = false)
    {
        if ($old && $this->Material->id) {
            return trim($this->oldH1) ?: trim($this->oldName);
        }
        return trim($this->h1) ?: trim($this->name);
    }


    public function getMenuName($old = true)
    {
        if ($old && $this->Material->id) {
            return trim($this->oldMenu_name) ?: trim($this->oldName);
        }
        return trim($this->menu_name) ?: trim($this->name);
    }
    
    
    public function getBreadcrumbsName($old = true)
    {
        if ($old && $this->Material->id) {
            return trim($this->oldBreadcrumbs_name) ?: trim($this->oldName);
        }
        return trim($this->breadcrumbs_name) ?: trim($this->name);
    }
    
    
    public static function delete(self $object)
    {
        foreach ($object->fields as $row) {
            if (in_array($row->datatype, array('image', 'file'))) {
                foreach ($row->getValues(true) as $att) {
                    Attachment::delete($att);
                }
            }
            $row->deleteValues();
        }
        parent::delete($object);
        static::clearLostBlocks();
        static::clearLostMaterials();
    }
    
    
    protected function _blocksOrdered()
    {
        $SQL_query = "SELECT tB.*, tBPA.priority 
                        FROM " . Block::_tablename() . " AS tB JOIN " . self::$dbprefix . self::$links['blocks']['tablename'] . " AS tBPA ON tB.id = tBPA.block_id 
                       WHERE tBPA.page_id = " . (int)$this->id . "
                    ORDER BY tBPA.priority";
        $SQL_result = \SOME\SOME::getSQLSet($SQL_query);
        return array_map(function($x) { return Block::spawn($x); }, $SQL_result);
    }
    
    protected function _fields()
    {
        $arr = array();
        $temp = Page_Field::getSet();
        foreach ($temp as $row) {
            $row->Owner = $this;
            $arr[$row->urn] = $row;
        }
        return $arr;
    }
    
    protected function _affectedMaterialTypes()
    {
        $SQL_query = "SELECT tMt.id 
                        FROM " . Material::_tablename() . " AS tM 
                        JOIN " . self::$dbprefix . "cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id
                        JOIN " . Material_Type::_tablename() . " AS tMt ON tMt.id = tM.pid
                       WHERE NOT tMt.global_type AND tMPA.pid = " . (int)$this->id . "
                    ORDER BY tMt.name";
        $col1 = (array)self::$SQL->getcol($SQL_query);
        $SQL_query = "SELECT tMt.id 
                        FROM " . Material_Type::_tablename() . " AS tMt
                        JOIN " . Block::_dbprefix() . "cms_blocks_material AS tBM ON tBM.material_type = tMt.id
                        JOIN " . Block::_tablename() . " AS tB ON tB.id = tBM.id
                        JOIN " . self::$dbprefix . "cms_blocks_pages_assoc AS tBPA ON tBPA.block_id = tB.id
                       WHERE tBPA.page_id = " . (int)$this->id;
        $col2 = (array)self::$SQL->getcol($SQL_query);
        $Set = array_values(array_unique(array_merge($col1, $col2)));
        $Set = array_map(function($x) { return new \RAAS\CMS\Material_Type($x); }, $Set);
        return $Set;
    }


    protected function _affectedMaterials()
    {
        $SQL_query = "SELECT tMt.*
                        FROM " . Material_Type::_tablename() . " AS tMt
                        JOIN " . Block::_dbprefix() . "cms_blocks_material AS tBM ON tBM.material_type = tMt.id
                        JOIN " . Block::_tablename() . " AS tB ON tB.id = tBM.id
                        JOIN " . self::$dbprefix . "cms_blocks_pages_assoc AS tBPA ON tBPA.block_id = tB.id
                       WHERE tB.vis AND tB.nat AND tBPA.page_id = " . (int)$this->id;
        $SQL_result = Material_Type::getSQLSet($SQL_query);
        $mts = array();
        foreach ($SQL_result as $row) {
            $mts = array_merge($mts, array($row), (array)$row->all_children);
        }
        $Set = array();
        // ����������
        if ($mts_global = array_map(function($x) { return (int)$x->id; }, array_values(array_filter($mts, function($x) { return $x->global_type; })))) {
            $SQL_query = "SELECT tM.* FROM " . Material::_tablename() . " AS tM WHERE tM.vis AND tM.pid IN(" . implode(", ", $mts_global) . ")";
            $Set = array_merge($Set, Material::getSQLSet($SQL_query));
        }
        if ($mts_nonGlobal = array_map(function($x) { return (int)$x->id; }, array_values(array_filter($mts, function($x) { return !$x->global_type; })))) {
            $SQL_query = "SELECT tM.* 
                            FROM " . Material::_tablename() . " AS tM 
                            JOIN " . Material::_dbprefix() . "cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id
                           WHERE tM.vis 
                             AND tM.pid IN(" . implode(", ", $mts_nonGlobal) . ") 
                             AND tMPA.pid = " . (int)$this->id . "
                             AND (NOT tM.show_from OR tM.show_from <= NOW())
                             AND (NOT tM.show_to OR tM.show_to >= NOW())
                        GROUP BY tM.id";
            $Set = array_merge($Set, Material::getSQLSet($SQL_query));
        }
        return $Set;
    }


    protected function _Domain()
    {
        $id = $this->pid ? $this->parents[0]->id : $this->id;
        return new static((int)$id);
    }


    public static function importByURL($url)
    {
        if (!is_array($url)) {
            $url = preg_replace('/^(http:\\/\\/)?(www\\.)?/i', '', $url);
            $url = explode('/', trim(str_replace('\\', '/', $url), '/'));
        } 
        if (is_array($url)) {
            $url = array_filter($url, 'trim');
        }
        $domain = array_shift($url);
        
        $Page = new self();
        
        // ������ �����
        $SQL_query = "SELECT * FROM " . Page::_tablename() . " WHERE NOT pid AND urn REGEXP ?";
        $SQL_bind = array('(^| )' . preg_quote($domain) . '( |$)');
        if ($SQL_result = self::$SQL->getline(array($SQL_query, $SQL_bind))) {
            $Page = new self($SQL_result);
        } else {
            return $Page;
        }
        
        // ������ ��������
        foreach ($url as $urn) {
            $SQL_query = "SELECT * FROM " . Page::_tablename() . " WHERE urn = ? AND pid = ?";
            $SQL_bind = array($urn, $Page->id);
            if ($SQL_result = self::$SQL->getvalue(array($SQL_query, $SQL_bind))) {
                $Page = new self($SQL_result);
            } else {
                break;
            }
        }
        return $Page;
    }


    public function rebuildCache()
    {
        @unlink($this->cacheFile);
        $url = 'http://' . (preg_match('/(^| )' . preg_quote($_SERVER['HTTP_HOST']) . '( |$)/i', $this->Domain->urn) ? $_SERVER['HTTP_HOST'] : str_replace('http://', '', $this->domain));
        if ($this->Material->id) {
            $url .= $this->Material->url;
        } else {
            $url .= $this->url;
        }
        file_get_contents($url);
    }


    /**
     * ������� "��������" �����
     */
    protected static function clearLostBlocks()
    {
        $SQL_query = "SELECT tB." . Block::_idN() . " FROM " . Block::_tablename() . " AS tB
                        LEFT JOIN " . static::_dbprefix() . static::$links['blocks']['tablename'] . " AS tBPA ON tB." . Block::_idN() . " = tBPA." . static::$links['blocks']['field_to']
                   . "  LEFT JOIN " . static::_tablename() . " AS tP ON tP." . static::_idN() . " = tBPA." . static::$links['blocks']['field_from']
                   . " WHERE tP." . static::_idN() . " IS NULL ";
        $SQL_result = static::$SQL->getcol($SQL_query);
        if ($SQL_result) {
            foreach ($SQL_result as $id) {
                $row = Block::spawn($id);
                $classname = get_class($row);
                $classname::delete($row);
            }
        }
    }


    /**
     * ������� "��������" ���������
     */
    protected static function clearLostMaterials()
    {
        $SQL_query = "SELECT tM.* FROM " . Material::_tablename() . " AS tM
                        JOIN " . Material_Type::_tablename() . " AS tMT ON tMT.id = tM.pid 
                   LEFT JOIN " . static::_dbprefix() . static::$links['materials']['tablename'] . " AS tMPA ON tM." . Material::_idN() . " = tMPA." . static::$links['materials']['field_to']
                   . "  LEFT JOIN " . static::_tablename() . " AS tP ON tP." . static::_idN() . " = tMPA." . static::$links['materials']['field_from']
                   . " WHERE NOT tMT.global_type AND tP." . static::_idN() . " IS NULL ";
        $Set = Material::getSQLSet($SQL_query);
        if ($Set) {
            foreach ($Set as $row) {
                Material::delete($row);
            }
        }
    }


    /**
     * ���� �������� � ����� �� URN � ���������, ��� � ������� (��� �������� �� ������������)
     * @return bool TRUE, ���� � ��� �� ������������ ������� ��� ���� �������� � ����� URN, FALSE � ��������� ������
     */
    protected function checkForSimilarPages()
    {
        $SQL_query = "SELECT COUNT(*) FROM " . self::_tablename() . " WHERE urn = ? AND pid = ? AND id != ?";
        $SQL_result = self::$SQL->getvalue(array($SQL_query, $this->urn, $this->pid, (int)$this->id));
        $c = (bool)(int)$SQL_result;
        return $c;
    }


    /**
     * ���� ��������� � ����� �� URN, ��� � ������� �������� (��� �������� �� ������������)
     * @return bool TRUE, ���� ���� �������� � ����� URN, ��� � ������� ��������, FALSE � ��������� ������
     */
    protected function checkForSimilarMaterials()
    {
        $SQL_query = "SELECT COUNT(*) FROM " . Material::_tablename() . " WHERE urn = ?";
        $SQL_result = self::$SQL->getvalue(array($SQL_query, $this->urn));
        $c = (bool)(int)$SQL_result;
        return $c;
    }
}