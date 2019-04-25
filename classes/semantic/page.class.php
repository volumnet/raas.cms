<?php
namespace RAAS\CMS;

use RAAS\Attachment;
use RAAS\Application;
use RAAS\User as RAASUser;

class Page extends \SOME\SOME implements IAccessible
{
    use RecursiveTrait;

    protected static $tablename = 'cms_pages';

    protected static $defaultOrderBy = "priority";

    protected static $cognizableVars = [
        'blocksOrdered',
        'fields',
        'affectedMaterialTypesWithChildren',
        'Domain',
        'selfAndChildren',
        'selfAndChildrenIds',
        'selfAndParents',
        'selfAndParentsIds',
    ];

    protected static $objectCascadeDelete = true;

    protected static $references = [
        'parent' => [
            'FK' => 'pid',
            'classname' => Page::class,
            'cascade' => true
        ],
        'author' => [
            'FK' => 'author_id',
            'classname' => RAASUser::class,
            'cascade' => false
        ],
        'editor' => [
            'FK' => 'editor_id',
            'classname' => RAASUser::class,
            'cascade' => false
        ],
        'Template' => [
            'FK' => 'template',
            'classname' => Template::class,
            'cascade' => false
        ],
    ];

    protected static $parents = [
        'parents' => 'parent'
    ];

    protected static $children = [
        'children' => [
            'classname' => Page::class,
            'FK' => 'pid'
        ],
        'access' => [
            'classname' => CMSAccess::class,
            'FK' => 'page_id'
        ],
    ];

    protected static $links = [
        'blocks' => [
            'tablename' => 'cms_blocks_pages_assoc',
            'field_from' => 'page_id',
            'field_to' => 'block_id',
            'classname' => Block::class
        ],
        'materials' => [
            'tablename' => 'cms_materials_pages_assoc',
            'field_from' => 'pid',
            'field_to' => 'id',
            'classname' => Material::class
        ],
        'allowedUsers' => [
            'tablename' => 'cms_access_pages_cache',
            'field_from' => 'page_id',
            'field_to' => 'uid',
            'classname' => User::class
        ],
        'affectedMaterialTypes' => [
            'tablename' => 'cms_material_types_affected_pages_for_self_cache',
            'field_from' => 'page_id',
            'field_to' => 'material_type_id',
            'classname' => Material_Type::class
        ],
        'affectedMaterials' => [
            'tablename' => 'cms_materials_affected_pages_cache',
            'field_from' => 'page_id',
            'field_to' => 'material_id',
            'classname' => Material::class
        ],
    ];

    protected static $caches = [
        'pvis' => [
            'affected' => ['parent'],
            'sql' => "IF(parent.id, (parent.vis AND parent.pvis), 1)"
        ],
        'cache_url' => [
            'affected' => ['parent'],
            'sql' => "IF(parent.id, CONCAT(parent.cache_url, __SOME__.urn, '/'), '/')",
        ],
    ];

    public static $httpStatuses = [
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
    ];

    /**
     * MIME-типы
     * @var array<string>
     */
    public static $mimeTypes = [
        'text/html',
        'text/css',
        'text/javascript',
        'text/plain',
        'application/xml',
        'application/json',
    ];

    protected static $inheritedFields = [
        'inherit_meta_title' => 'meta_title',
        'inherit_meta_description' => 'meta_description',
        'inherit_meta_keywords' => 'meta_keywords',
        'inherit_changefreq' => 'changefreq',
        'inherit_sitemaps_priority' => 'sitemaps_priority',
        'inherit_template' => 'template',
        'inherit_lang' => 'lang',
        'inherit_cache' => 'cache'
    ];

    private $locationBlocksText = [];

    public function __get($var)
    {
        switch ($var) {
            case 'URLArray':
                return explode('/', trim($this->cache_url, '/'));
                break;
            case 'url':
                return $this->cache_url;
                break;
            case 'additionalURL':
                $url = preg_replace(
                    '/^' . preg_quote($this->url, '/') . '/umi',
                    '',
                    $this->initialURL
                );
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
                $blocks = [];
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
                return 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' .
                       preg_replace('/^http(s)?:\\/\\//umi', '', $temp[0]);
                break;
            case 'visChildren':
                return array_values(
                    array_filter(
                        $this->children,
                        function ($x) {
                            return $x->vis;
                        }
                    )
                );
                break;
            case 'locationBlocksText':
                return $this->locationBlocksText;
                break;
            case 'cacheFile':
                if (preg_match(
                    '/(^| )' . preg_quote($_SERVER['HTTP_HOST']) . '( |$)/i',
                    $this->Domain->urn
                )) {
                    $url = $_SERVER['HTTP_HOST'];
                } else {
                    $url = preg_replace(
                        '/^http(s)?:\\/\\//umi',
                        '',
                        $this->domain
                    );
                }
                if ($this->Material->id) {
                    $url .= $this->Material->url;
                } else {
                    $url .= $this->url;
                }
                $url = Package::i()->cacheDir . '/' . Package::i()->cachePrefix
                     . '.' . urlencode($url) . '.php';
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
                    if (isset($this->fields[$var]) &&
                        ($this->fields[$var] instanceof Page_Field)
                    ) {
                        $temp = $this->fields[$var]->getValues();
                        if ($vis) {
                            $temp = array_values(
                                array_filter(
                                    $temp,
                                    function ($x) {
                                        return isset($x->vis) && $x->vis;
                                    }
                                )
                            );
                        }
                        return $temp;
                    } else {
                        // 2015-03-02 AVS: из-за утечки памяти при ненулевом ->pid
                        unset($this->fields);
                    }
                }
                break;
        }
    }


    public function commit()
    {
        $new = !$this->id;
        $urnUpdated = false;

        $this->modify(false);
        $this->modify_date = date('Y-m-d H:i:s');
        if (!$this->id) {
            $this->post_date = $this->modify_date;
        }
        if (!$this->id || !$this->priority) {
            $sqlQuery = "SELECT MAX(priority) FROM " . self::_tablename();
            $this->priority = self::$SQL->getvalue($sqlQuery) + 1;
        }
        if ($this->pid && !$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        if ($this->updates['urn']) {
            $urnUpdated = true;
        }
        if ($this->updates['urn'] && $this->pid) {
            $this->urn = \SOME\Text::beautify($this->urn, '-');
            $this->urn = preg_replace('/\\-\\-/umi', '-', $this->urn);
            $this->urn = trim($this->urn, '-');
        }
        for ($i = 0; $this->checkForSimilarPages() || $this->checkForSimilarMaterials(); $i++) {
            $this->urn = Application::i()->getNewURN($this->urn, !$i, '-');
        }


        $enableHeritage = false;
        foreach (self::$inheritedFields as $key => $val) {
            if ($this->$key) {
                $enableHeritage = true;
            }
        }
        if ($enableHeritage) {
            foreach ($this->children as $row) {
                // 2014-11-18, AVS: добавлено, поскольку childrens создаются
                // по SQL-запросу и массив properties у них нулевой,
                // поэтому сравнивать проблематично
                $row->reload();
                foreach (self::$inheritedFields as $key => $val) {
                    // Если наследуется и значение дочернего элемента
                    // совпадает со старым значением текущего
                    // 2014-11-18, AVS: сменил $this->update[$key] на $this->$key,
                    // т.к. сам факт наследования не обязательно должен меняться
                    if ($this->$key && ($row->$key == $this->properties[$key])) {
                        $row->$val = $this->$val;
                    }
                }

                $row->commit();
            }
        }

        parent::commit();

        if (($this->template == $this->parent->template) && $new) {
            $sqlQuery = "SELECT tB.*
                            FROM " . Block::_tablename() . " AS tB
                            JOIN " . self::$dbprefix . self::$links['blocks']['tablename'] . " AS tBPA ON tBPA.block_id = tB.id
                           WHERE tBPA.page_id = " . (int)$this->pid . " AND inherit ORDER BY priority";
            $sqlResult = array_map(
                function ($x) {
                    return Block::spawn($x);
                },
                \SOME\SOME::getSQLSet($sqlQuery)
            );
            if ($sqlResult) {
                $arr = [];
                $sqlQuery = "SELECT MAX(priority)
                               FROM " . self::$dbprefix . self::$links['blocks']['tablename'];
                $priority = (int)self::$SQL->getvalue($sqlQuery);
                foreach ($sqlResult as $row) {
                    $arr[] = [
                        'page_id' => $this->id,
                        'block_id' => $row->id,
                        'priority' => ++$priority
                    ];
                }
                self::$SQL->add(
                    self::$dbprefix . self::$links['blocks']['tablename'],
                    $arr
                );
            }
        }

        // 2019-04-25, AVS: обновим связанные страницы типов материалов
        if ($new) {
            Material_Type::updateAffectedPagesForMaterials();
            Material_Type::updateAffectedPagesForSelf();
        } elseif ($urnUpdated) {
            Material::updateAffectedPages();
        }
    }


    public function getCodePage($code = 404)
    {
        $sqlQuery = "SELECT *
                       FROM " . Page::_tablename()
                  . " WHERE pid = ?
                        AND response_code = ?
                   ORDER BY priority
                      LIMIT 1";
        $sqlBind = [$this->id, $code];
        if ($sqlResult = self::$SQL->getline([$sqlQuery, $sqlBind])) {
            return new self($sqlResult);
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
        if ($this->mime) {
            header('Content-Type: ' . $this->mime . '; charset=UTF-8');
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
            $texts = [];
            foreach ($Set as $row) {
                if ($row->vis) {
                    ob_start();
                    $row->process($this);
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
        self::$SQL->update(
            self::_tablename(),
            "id = " . (int)$this->id,
            ['visit_counter' => $this->visit_counter++]
        );
    }


    public function modify($commit = true)
    {
        $d0 = time();
        $d1 = strtotime($this->modify_date);
        $d2 = strtotime($this->last_modified);
        $arr = [];
        if ((time() - $d1 >= 3600) && (time() - $d2 >= 3600)) {
            $arr['last_modified'] = $this->last_modified = date('Y-m-d H:i:s');
            $arr['modify_counter'] = $this->modify_counter++;
            if ($commit) {
                self::$SQL->update(
                    self::_tablename(),
                    "id = " . (int)$this->id,
                    $arr
                );
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
            if (in_array($row->datatype, ['image', 'file'])) {
                foreach ($row->getValues(true) as $att) {
                    Attachment::delete($att);
                }
            }
            $row->deleteValues();
        }
        parent::delete($object);
        static::clearLostBlocks();
        static::clearLostMaterials();

        // 2019-04-25, AVS: обновим связанные страницы типов материалов
        Material_Type::updateAffectedPagesForMaterials();
        Material_Type::updateAffectedPagesForSelf();
    }


    protected function _blocksOrdered()
    {
        $sqlQuery = "SELECT tB.*, tBPA.priority
                        FROM " . Block::_tablename() . " AS tB
                        JOIN " . self::$dbprefix . self::$links['blocks']['tablename'] . " AS tBPA ON tB.id = tBPA.block_id
                       WHERE tBPA.page_id = " . (int)$this->id . "
                    ORDER BY tBPA.priority";
        $sqlResult = \SOME\SOME::getSQLSet($sqlQuery);
        return array_map(
            function ($x) {
                return Block::spawn($x);
            },
            $sqlResult
        );
    }


    protected function _fields()
    {
        $arr = [];
        $temp = Page_Field::getSet();
        foreach ($temp as $row) {
            $row->Owner = $this;
            $arr[$row->urn] = $row;
        }
        return $arr;
    }


    /**
     * Типы материалов, присутствующие на данной и дочерних страницах
     *
     * Присутствующими считаются типы, если либо на странице есть материальный блок
     * данного типа, либо хотя бы один материал напрямую связан со страницей
     * @return array<Material_Type> у NAT-типов добавляется nat = true,
     *                              также counter - количество страниц,
     *                              на которых задействован тип
     */
    protected function _affectedMaterialTypesWithChildren()
    {
        $sqlQuery = "SELECT tMT.*,
                            MAX(tMTAPS.nat) AS nat,
                            COUNT(tMTAPS.page_id) AS counter
                       FROM " . Material_Type::_tablename() . " AS tMT
                       JOIN cms_material_types_affected_pages_for_self_cache AS tMTAPS ON tMTAPS.material_type_id = tMT.id
                      WHERE tMTAPS.page_id IN (" . implode(", ", $this->selfAndChildrenIds) . ")
                   GROUP BY tMT.id";
        $mtypes = Material_Type::getSQLSet($sqlQuery);
        return $mtypes;
    }


    protected function _Domain()
    {
        $id = $this->pid ? $this->parents[0]->id : $this->id;
        return new static((int)$id);
    }


    public static function importByURL($url)
    {
        $pageCache = PageRecursiveCache::i();
        if (!is_array($url)) {
            $url = preg_replace('/^(http(s)?:\\/\\/)?(www\\.)?/umi', '', $url);
            $url = explode('/', trim(str_replace('\\', '/', $url), '/'));
        }
        if (is_array($url)) {
            $url = array_filter($url, 'trim');
        }
        $domain = array_shift($url);

        // Найдем домен
        $domainData = array_filter(
            $pageCache->cache,
            function ($x) use ($domain) {
                return !$x['pid'] && preg_match(
                    '/(^| )' . preg_quote($domain) . '( |$)/umi',
                    $x['urn']
                );
            }
        );
        if (!$domainData) {
            return new static();
        }
        $domainId = array_shift(array_keys($domainData));
        $domainPagesIds = array_merge(
            [$domainId],
            $pageCache->getAllChildrenIds($domainId)
        );
        $domainPagesData = array_intersect_key($pageCache->cache, array_flip($domainPagesIds));

        for ($i = count($url); $i >= 0; $i--) {
            $urlArrayToFind = array_slice($url, 0, $i);
            if ($i > 0) {
                $urlToFind = '/' . implode('/', $urlArrayToFind) . '/';
            } else {
                $urlToFind = '/';
            }
            $pageData = array_filter(
                $domainPagesData,
                function ($x) use ($urlToFind) {
                    return ($x['cache_url'] == $urlToFind);
                }
            );
            if ($pageData) {
                return new static(array_shift($pageData));
            }
        }
        return new static();
    }


    /**
     * Очистить кэши страницы
     */
    public function clearCache()
    {
        $globUrl = $this->cacheFile;
        $globUrl = preg_replace(
            '/\\.php$/umi',
            urlencode('?') . '*.php',
            $globUrl
        );
        @unlink($this->cacheFile);
        $glob = glob($globUrl);
        foreach ($glob as $file) {
            @unlink($file);
        }
    }


    /**
     * Перестроить кэш страницы
     */
    public function rebuildCache()
    {
        $this->clearCache();
        $url = 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://'
             . (
                    preg_match(
                        '/(^| )' . preg_quote($_SERVER['HTTP_HOST']) . '( |$)/i',
                        $this->Domain->urn
                    ) ?
                    $_SERVER['HTTP_HOST'] :
                    preg_replace('/^http(s)?:\\/\\//umi', '', $this->domain)
                );
        if ($this->Material->id) {
            $url .= $this->Material->url;
        } else {
            $url .= $this->url;
        }
        @file_get_contents($url);
    }


    /**
     * Удаляем "ничейные" блоки
     */
    protected static function clearLostBlocks()
    {
        // 2017-02-10, AVS: сначала почистим связки на страницы, без реальных страниц
        // так сказать, во избежание
        $sqlQuery = "DELETE tBPA
                        FROM " . static::_dbprefix() . static::$links['blocks']['tablename'] . " AS tBPA
                   LEFT JOIN " . static::_tablename() . " AS tP ON tP." . static::_idN() . " = tBPA." . static::$links['blocks']['field_from']
                   . " WHERE tP." . static::_idN() . " IS NULL ";
        static::$SQL->query($sqlQuery);

        // сейчас выберем и удалим блоки, которые не привязаны ни к одной странице
        $sqlQuery = "SELECT tB." . Block::_idN() . " FROM " . Block::_tablename() . " AS tB
                        LEFT JOIN " . static::_dbprefix() . static::$links['blocks']['tablename'] . " AS tBPA ON tB." . Block::_idN() . " = tBPA." . static::$links['blocks']['field_to']
                   . " WHERE tBPA." . static::$links['blocks']['field_from'] . " IS NULL ";
        $sqlResult = static::$SQL->getcol($sqlQuery);
        if ($sqlResult) {
            foreach ($sqlResult as $id) {
                $row = Block::spawn($id);
                $classname = get_class($row);
                $classname::delete($row);
            }
        }
    }


    /**
     * Удаляем "ничейные" материалы
     */
    protected static function clearLostMaterials()
    {
        // 2017-02-10, AVS: сначала почистим связки на страницы,
        // без реальных страниц - так сказать, во избежание
        $sqlQuery = "DELETE tMPA
                        FROM  " . static::_dbprefix() . static::$links['materials']['tablename'] . " AS tMPA
                   LEFT JOIN " . static::_tablename() . " AS tP ON tP." . static::_idN() . " = tMPA." . static::$links['materials']['field_from']
                   . " WHERE tP." . static::_idN() . " IS NULL ";
        static::$SQL->query($sqlQuery);

        // сейчас выберем и удалим материалы, которые не привязаны ни к одной странице, при этом не глобальные
        $sqlQuery = "SELECT tM.* FROM " . Material::_tablename() . " AS tM
                        JOIN " . Material_Type::_tablename() . " AS tMT ON tMT.id = tM.pid
                   LEFT JOIN " . static::_dbprefix() . static::$links['materials']['tablename'] . " AS tMPA ON tM." . Material::_idN() . " = tMPA." . static::$links['materials']['field_to']
                   . " WHERE NOT tMT.global_type AND tMPA." . static::$links['materials']['field_from'] . " IS NULL ";
        $Set = Material::getSQLSet($sqlQuery);
        if ($Set) {
            foreach ($Set as $row) {
                Material::delete($row);
            }
        }
    }


    /**
     * Ищет страницы с таким же URN и родителем, как и текущая
     * (для проверки на уникальность)
     * @return bool true, если в том же родительском разделе уже есть страница
     *              с таким URN,
     *              false в противном случае
     */
    protected function checkForSimilarPages()
    {
        $sqlQuery = "SELECT COUNT(*)
                       FROM " . self::_tablename()
                  . " WHERE urn = ?
                        AND pid = ?
                        AND id != ?";
        $sqlResult = self::$SQL->getvalue([
            $sqlQuery,
            $this->urn,
            $this->pid,
            (int)$this->id
        ]);
        $c = (bool)(int)$sqlResult;
        return $c;
    }


    /**
     * Ищет материалы с таким же URN, как и текущая страница
     * (для проверки на уникальность)
     * @return bool true, если есть материал с таким URN, как и текущая страница,
     *              false в противном случае
     */
    protected function checkForSimilarMaterials()
    {
        $sqlQuery = "SELECT COUNT(*)
                       FROM " . Material::_tablename()
                  . " WHERE urn = ?";
        $sqlResult = self::$SQL->getvalue([$sqlQuery, $this->urn]);
        $c = (bool)(int)$sqlResult;
        return $c;
    }
}
