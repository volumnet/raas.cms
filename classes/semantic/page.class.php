<?php
/**
 * Страница
 */
declare(strict_types=1);

namespace RAAS\CMS;

use Error;
use Exception;
use SOME\SOME;
use SOME\Text;
use RAAS\Attachment;
use RAAS\Application;
use RAAS\User as RAASUser;

/**
 * Страница
 * @property-read array<Block> $blocksOrdered Упорядоченные по порядку
 *                                            отображения блоки страницы
 * @property-read array<Page_Field> $fields Поля страницы
 *                                          с установленным свойством $Owner
 * @property-read array<
 *                    Material_Type
 *                > $affectedMaterialTypesWithChildren Типы материалов,
 *                                                     присутствующие на данной
 *                                                     и дочерних страницах
 * @property-read Page $Domain Доменная страница
 * @property-read array<Page> $selfAndChildren Текущая и дочерние страницы
 * @property-read array<int> $selfAndChildrenIds ID# текущей и дочерних страницы
 * @property-read array<Page> $selfAndParents Текущая и родительские страницы
 * @property-read array<int> $selfAndParentsIds ID# текущей и родительских
 *                                                  страниц
 * @property-read Page $parent Родительская страница
 * @property-read RAASUser $author Автор страницы
 * @property-read RAASUser $editor Редактор страницы
 * @property-read Template $Template Шаблон страницы
 * @property-read array<Page> $parents Родительские страницы
 * @property-read array<Page> $children Дочерние страницы
 * @property-read array<CMSAccess> $access Доступы страницы
 * @property-read array<Block> $blocks Блоки страницы
 * @property-read array<Material> $materials Материалы, привязанные к странице
 * @property-read array<User> $allowedUsers Пользователи, которым разрешен
 *                                          просмотр страницы
 * @property-read array<
 *                    Material_Type
 *                > $affectedMaterialTypes Типы материалов, задействованные
 *                                         на странице
 * @property-read array<Material> $affectedMaterials Материалы, задействованные
 *                                                   на странице
 * @property-read array<string> $URLArray Массив URN из URL
 * @property-read string $url URL страницы
 * @property-read string $fullURL полный URL страницы (с указанием домена)
 * @property-read string $conditionalDomainURL URL страницы с указанием домена,
 *     если текущий домен отличается от набора стандартных
 * @property-read string $additionalURL Дополнительная часть ("хвост") URL
 * @property-read array<string> $additionalURLArray Массив URN из дополнительной
 *                                                  части ("хвоста") URL
 * @property-read array<
 *                    string[] URN размещения => array<Block>
 *                > $blocksByLocations Блоки по размещениям
 * @property-read string $domain URL домена (включая схему)
 * @property-read array<Page> $visChildren Видимые страницы
 * @property-read array<
 *                    string[] URN размещения => array<string>
 *                > $locationBlocksText Тексты обработанных блоков
 *                                      по размещениям
 * @property-read string $cacheFile Путь к файлу кэша
 */
class Page extends SOME
{
    use RecursiveTrait;
    use AccessibleTrait;
    use PageoidTrait;

    protected static $tablename = 'cms_pages';

    protected static $defaultOrderBy = "priority";

    protected static $cognizableVars = [
        'blocksOrdered',
        'fields',
        'visFields',
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

    /**
     * Список наследуемых полей
     * @var array<
     *          string[] URN поля наследования => string URN оригинального поля
     *      >
     */
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


    /**
     * Расшифровки HTTP-статусов
     * 2021-06-18, AVS: удалять нельзя, т.к. иначе не отправляется реальный заголовок
     * и не сохраняется в кэше страницы
     * @var array<int[] Код статуса => string Расшифровка>
     */
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
     * Блоки по размещениям
     * @var array<string[] URN размещения => array<Block>>
     */
    private $locationBlocksText = [];

    public function __get($var)
    {
        switch ($var) {
            case 'URLArray':
                return explode('/', trim($this->cache_url, '/'));
                break;
            case 'url':
                return (string)$this->cache_url;
                break;
            case 'fullURL':
                $domains = $this->domains;
                if (in_array($_SERVER['HTTP_HOST'], $domains)) {
                    $result = '//' . $_SERVER['HTTP_HOST'];
                } else {
                    $result = $this->domain;
                }
                $result .= $this->url;
                return $result;
                break;
            case 'conditionalDomainURL':
                $result = '';
                if (!in_array($_SERVER['HTTP_HOST'], $this->domains)) {
                    $result .= $this->domain;
                }
                $result .= $this->url;
                return $result;
                break;
            case 'additionalURL':
                $url = preg_replace('/^' . preg_quote((string)$this->url, '/') . '/umi', '', (string)$this->initialURL);
                $url = trim($url);
                return $url;
            case 'additionalURLArray':
                $url = trim($this->additionalURL, '/');
                $url = trim($url);
                $urlArray = explode('/', $url);
                $urlArray = array_filter($urlArray, function ($x) {
                    return trim($x) !== '';
                });
                $urlArray = array_values($urlArray);
                return $urlArray;
            case 'blocksByLocations':
                $blocks = [];
                $locations = $this->Template->locations;
                foreach ($this->blocksOrdered as $row) {
                    if (isset($locations[$row->location])) {
                        $blocks[$row->location][] = $row;
                    } else {
                        $blocks[''][] = $row;
                    }
                }
                return $blocks;
                break;
            case 'domains':
                $result = explode(' ', (string)$this->Domain->urn);
                $result = array_map(function ($x) {
                    $x = trim($x);
                    $x = preg_replace('/^http(s)?:\\/\\//umi', '', $x);
                    return $x;
                }, $result);
                $result = array_values(array_filter($result));
                return $result;
                break;
            case 'domain':
                $domains = $this->domains;
                if ($domains[0] ?? '') {
                    return '//' . $this->domains[0];
                }
                return '';
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
                if ($this->Material && $this->Material->id) {
                    return $this->Material->cacheFile;
                }
                $url = 'http';
                if (isset($_SERVER['HTTPS'])) {
                    if (($_SERVER['HTTPS'] === true) || (mb_strtolower((string)$_SERVER['HTTPS'] ?? '') == 'on')) {
                        $url .= 's';
                    }
                }
                $url .= ':' . $this->fullURL;
                $file = Package::i()->cacheDir . '/' . Package::i()->cachePrefix . '.' . urlencode($url) . '.php';
                return $file;
                break;
            default:
                $val = parent::__get($var);
                if ($val !== null) {
                    return $val;
                } else {
                    $vis = false;
                    if (substr($var, 0, 3) == 'vis') {
                        $var = strtolower(substr($var, 3));
                        $vis = true;
                    }
                    $fields = $this->fields;
                    if (isset($fields[$var])) {
                        if ($fields[$var] &&
                            ($fields[$var] instanceof Page_Field)
                        ) {
                            $temp = $fields[$var]->getValues();
                            if ($vis) {
                                $temp = array_values(
                                    array_filter(
                                        $temp,
                                        function ($x) {
                                            return $x->vis;
                                        }
                                    )
                                );
                            }
                            return $temp;
                        }
                    } else {
                        // 2015-03-02 AVS: из-за утечки памяти при ненулевом ->pid
                        unset($fields);
                    }
                }
                break;
        }
    }


    public function commit()
    {
        $new = !$this->id;
        $urnUpdated = false;
        $pidUpdated = false;

        $this->modify(false);
        $this->modify_date = date('Y-m-d H:i:s');
        if (!$this->id) {
            $this->post_date = $this->modify_date;
        }
        if (!$this->id || !$this->priority) {
            $sqlQuery = "SELECT MAX(priority) FROM " . static::_tablename();
            $this->priority = static::$SQL->getvalue($sqlQuery) + 1;
        }
        if ($this->pid && !$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        if ($this->updates['urn'] ?? false) {
            $urnUpdated = true;
        }
        if ($this->updates['pid'] ?? false) {
            $pidUpdated = true;
        }
        if (($this->updates['urn'] ?? false) && $this->pid) {
            $this->urn = Text::beautify($this->urn, '-');
            $this->urn = preg_replace('/\\-\\-/umi', '-', $this->urn);
            $this->urn = trim($this->urn, '-');
        }
        for ($i = 0; $this->checkForSimilarPages() || $this->checkForSimilarMaterials(); $i++) {
            $this->urn = Application::i()->getNewURN($this->urn, !$i, '-');
        }


        foreach (static::$inheritedFields as $inheritanceFieldURN => $originalFieldURN) {
            // Только если значение изменено (включая создание нового)
            // и включено наследование (актуальное)
            if (($this->updates[$originalFieldURN] ?? false) &&
                $this->$inheritanceFieldURN
            ) {
                $inheritanceChildrenIds = $this->getInheritedChildren(
                    $inheritanceFieldURN,
                    $originalFieldURN,
                    ($this->properties[$originalFieldURN] ?? null)
                );
                if ($inheritanceChildrenIds) {
                    static::_SQL()->update(
                        static::_tablename(),
                        "id IN (" . implode(", ", $inheritanceChildrenIds) . ")",
                        [$originalFieldURN => $this->updates[$originalFieldURN]]
                    );
                }
            }
        }

        parent::commit();

        if (($this->template == $this->parent->template) && $new) {
            $sqlQuery = "SELECT tB.*
                            FROM " . Block::_tablename() . " AS tB
                            JOIN " . static::$dbprefix . static::$links['blocks']['tablename'] . " AS tBPA ON tBPA.block_id = tB.id
                           WHERE tBPA.page_id = " . (int)$this->pid . " AND inherit ORDER BY priority";
            $sqlResult = array_map(
                function ($x) {
                    return Block::spawn($x);
                },
                SOME::getSQLSet($sqlQuery)
            );
            if ($sqlResult) {
                $arr = [];
                $sqlQuery = "SELECT MAX(priority)
                               FROM " . static::$dbprefix . static::$links['blocks']['tablename'];
                $priority = (int)static::$SQL->getvalue($sqlQuery);
                foreach ($sqlResult as $row) {
                    $arr[] = [
                        'page_id' => $this->id,
                        'block_id' => $row->id,
                        'priority' => ++$priority
                    ];
                }
                static::$SQL->add(
                    static::$dbprefix . static::$links['blocks']['tablename'],
                    $arr
                );
            }
        }

        if (!($this->meta['dontUpdateAffectedPages'] ?? false)) {
            // 2019-04-25, AVS: обновим связанные страницы типов материалов
            // 2020-02-10, AVS: добавил условие для загрузчика прайсов
            // (чтобы было быстрее)
            if ($new) {
                Material_Type::updateAffectedPagesForMaterials();
                Material_Type::updateAffectedPagesForSelf();
            } elseif ($urnUpdated || $pidUpdated) {
                Material::updateAffectedPages();
            }
        }
    }


    /**
     * Находит ID# всех дочерних страниц (всех уровней) по наследуемому полю
     * @param string $inheritanceFieldURN URN поля наследования
     * @param string $originalFieldURN URN оригинального поля
     * @param mixed $value Значение оригинального поля для поиска
     * @return array<int>
     */
    public function getInheritedChildren(
        $inheritanceFieldURN,
        $originalFieldURN,
        $value
    ) {
        $ch = [(int)$this->id];
        $result = [];
        do {
            $sqlQuery = "SELECT id
                           FROM " . static::_tablename()
                      . " WHERE pid IN (" . implode(", ", $ch) . ")
                            AND " . $inheritanceFieldURN
                      . "   AND " . $originalFieldURN . " = ?";
            $ch = static::_SQL()->getcol([$sqlQuery, $value]);
            $result = array_merge($result, $ch);
        } while ($ch);
        return $result;
    }


    /**
     * Возвращает страницу (текущую или вверх по родительским) с заданным кодом
     * @param int $code Код ответа для поиска страницы
     * @return Page
     */
    public function getCodePage($code = 404)
    {
        $sqlQuery = "SELECT *
                       FROM " . Page::_tablename()
                  . " WHERE pid = ?
                        AND response_code = ?
                   ORDER BY priority
                      LIMIT 1";
        $sqlBind = [$this->id, $code];
        if ($sqlResult = static::$SQL->getline([$sqlQuery, $sqlBind])) {
            return new static($sqlResult);
        } elseif ($this->id) {
            return $this->parent->getCodePage($code);
        } else {
            return new static();
        }
    }


    /**
     * Отрабатывает страницу
     * @return string HTML-код страницы
     */
    public function process()
    {
        ob_start();
        $this->processHeaders();
        $SITE = $this->Domain;
        $Page = $this;
        $blocksByLocations = $this->blocksByLocations;
        if ($blocksByLocations[''] ?? null) {
            echo $this->location('');
        }
        if ($this->template) {
            foreach ($this->Template->locations as $l => $loc) {
                $this->location($l);
            }
            $st = microtime(true);
            $this->Template->process([
                'SITE' => $this->Domain,
                'Page' => $this,
            ]);
            if ($this->template && ($diag = Controller_Frontend::i()->diag)) {
                $diag->handle('templates', $this->template, microtime(true) - $st);
            }
        }
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }


    /**
     * Возвращает связанные HTTP-заголовки
     */
    protected function processHeaders()
    {
        $lastModificationTime = strtotime((string)$this->last_modified);
        $ifModifiedSinceTime = 0;
        if (isset($_ENV['HTTP_IF_MODIFIED_SINCE'])) {
            $ifModifiedSinceTime = strtotime(
                substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5)
            );
        } elseif (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $ifModifiedSinceTime = strtotime(
                substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5)
            );
        }
        if ($ifModifiedSinceTime &&
            $this->cache &&
            ($ifModifiedSinceTime >= $lastModificationTime)
        ) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
            exit;
        } elseif ($this->cache) {
            header(
                'Last-Modified: '.
                gmdate('D, d M Y H:i:s \G\M\T', $lastModificationTime)
            );
        } else {
            header('Last-Modified: '. gmdate('D, d M Y H:i:s \G\M\T'));
        }
        if ($this->response_code && ($this->response_code != 200)) {
            http_response_code((int)$this->response_code);

            // 2021-06-18, AVS: удалять нельзя, т.к. иначе не отправляется реальный заголовок
            // и не сохраняется в кэше страницы
            header('HTTP/1.0 ' . Page::$httpStatuses[(int)$this->response_code]);
            header('Status: ' . Page::$httpStatuses[(int)$this->response_code]);
        }
        if ($this->mime) {
            header('Content-Type: ' . $this->mime . '; charset=UTF-8');
        }
    }


    /**
     * Отрабатывает размещение
     * @param string $location URN размещения
     * @return string HTML-код размещения
     */
    public function location($location)
    {
        if (!isset($this->locationBlocksText[$location])) {
            $Location = new Location($this->Template, $location);
            $blocksByLocations = $this->blocksByLocations;
            $Set = (array)($blocksByLocations[$Location->urn] ?? []);
            // $texts = [];
            foreach ($Set as $row) {
                // 2021-03-31, AVS: перенесли условие совместимости
                // с активным материалом сюда, чтобы блок не глючил, если он
                // предполагается к использованию только с активным материалом
                if ($row->vis && $row->tuneWithMaterial($this)) {
                    ob_start();
                    $row->process($this);
                    // $texts[$row->id] = ob_get_clean();
                    $this->locationBlocksText[$location][] = ob_get_clean();
                }
            }
            // foreach ($Set as $row) {
            //     if ($row->tuneWithMaterial($this)) {
            //         $this->locationBlocksText[$location][] = $texts[$row->id];
            //     }
            // }
        }
        return implode('', (array)($this->locationBlocksText[$location] ?? []));
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


    /**
     * Получить заголовок H1
     * @param bool $old Старое имя (у страницы)
     * @return string
     */
    public function getH1(bool $old = false): string
    {
        if ($old && $this->Material->id) {
            return trim((string)$this->oldH1) ?: trim((string)$this->oldName);
        }
        return trim((string)$this->h1) ?: trim((string)$this->name);
    }


    /**
     * Получить имя для меню
     * @param bool $old Старое имя (у страницы)
     * @return string
     */
    public function getMenuName(bool $old = true): string
    {
        if ($old && $this->Material && $this->Material->id) {
            return trim((string)$this->oldMenu_name) ?: trim((string)$this->oldName);
        }
        return trim((string)$this->menu_name) ?: trim((string)$this->name);
    }


    /**
     * Получить имя для хлебных крошек
     * @param bool $old Старое имя (у страницы)
     * @return string
     */
    public function getBreadcrumbsName(bool $old = true): string
    {
        if ($old && $this->Material->id) {
            return trim((string)$this->oldBreadcrumbs_name) ?: trim((string)$this->oldName);
        }
        return trim((string)$this->breadcrumbs_name) ?: trim((string)$this->name);
    }


    public static function delete(SOME $object)
    {
        // Удалим файловые поля с проверкой на совместное использование
        // Найдем используемые значения файловых полей в данном материале
        $sqlQuery = "SELECT tD.value
                       FROM " . static::_dbprefix() . "cms_data AS tD
                       JOIN " . Field::_tablename() . " AS tF ON tF.id = tD.fid
                      WHERE tD.pid = ?
                        AND tF.classname = ?
                        AND NOT tF.pid
                        AND tF.datatype IN ('image', 'file')";
        $sqlResult = static::_SQL()->getcol([
            $sqlQuery,
            (int)$object->id,
            Material_Type::class
        ]);
        $affectedAttachmentsIds = array_map(function ($x) {
            $json = (array)json_decode($x, true);
            $attachmentId = (int)$json['attachment'];
            return $attachmentId;
        }, $sqlResult);
        $affectedAttachmentsIds = array_filter($affectedAttachmentsIds);
        $affectedAttachmentsIds = array_values($affectedAttachmentsIds);

        // Найдем используемые значения файловых полей вне данного материала
        $sqlQuery = "SELECT tD.value
                       FROM " . static::_dbprefix() . "cms_data AS tD
                       JOIN " . Field::_tablename() . " AS tF ON tF.id = tD.fid
                      WHERE NOT (
                                    tD.pid = ?
                                AND tF.classname = ?
                                AND NOT tF.pid
                            )
                        AND tF.datatype IN ('image', 'file')";
        $sqlResult = static::_SQL()->getcol([
            $sqlQuery,
            (int)$object->id,
            Material_Type::class
        ]);
        $otherAttachmentsIds = array_map(function ($x) {
            $json = (array)json_decode($x, true);
            $attachmentId = (int)$json['attachment'];
            return $attachmentId;
        }, $sqlResult);
        $otherAttachmentsIds = array_filter($otherAttachmentsIds);
        $otherAttachmentsIds = array_values($otherAttachmentsIds);

        $attachmentsToDelete = array_diff(
            $affectedAttachmentsIds,
            $otherAttachmentsIds
        );

        foreach ($attachmentsToDelete as $attachmentToDelete) {
            $att = new Attachment($attachmentToDelete);
            Attachment::delete($att);
        }


        // Удалим данные из cms_data
        $sqlQuery = "DELETE tD
                       FROM " . static::_dbprefix() . "cms_data AS tD
                       JOIN " . Field::_tablename() . " AS tF ON tF.id = tD.fid
                      WHERE tD.pid = ?
                        AND tF.classname = ?
                        AND NOT tF.pid";
        $sqlResult = static::_SQL()->getcol([
            $sqlQuery,
            (int)$object->id,
            Material_Type::class
        ]);

        parent::delete($object);
        static::clearLostBlocks();
        static::clearLostMaterials();

        // 2019-04-25, AVS: обновим связанные страницы типов материалов
        Material_Type::updateAffectedPagesForMaterials();
        Material_Type::updateAffectedPagesForSelf();

        PageRecursiveCache::i()->refresh();
        PageRecursiveCache::i()->save();
    }


    /**
     * Упорядоченные по порядку отображения блоки страницы
     * @return array<Block>
     */
    protected function _blocksOrdered()
    {
        $sqlQuery = "SELECT tB.*, tBPA.priority
                        FROM " . Block::_tablename() . " AS tB
                        JOIN " . static::$dbprefix . static::$links['blocks']['tablename'] . " AS tBPA ON tB.id = tBPA.block_id
                       WHERE tBPA.page_id = " . (int)$this->id . "
                    ORDER BY tBPA.priority";
        $sqlResult = SOME::getSQLSet($sqlQuery);
        return array_map(
            function ($x) {
                return Block::spawn($x);
            },
            $sqlResult
        );
    }


    /**
     * Поля страницы с установленным свойством $Owner
     * @return Page_Field[]
     */
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
     * Список видимых полей
     * @return Page_Field[]
     */
    protected function _visFields()
    {
        return array_filter($this->fields, function ($x) {
            return $x->vis;
        });
    }


    /**
     * Типы материалов, присутствующие на данной и дочерних страницах
     *
     * Присутствующими считаются типы, если либо на странице есть материальный
     * блок данного типа, либо хотя бы один материал напрямую связан
     * со страницей
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


    /**
     * Доменная страница
     * @return Page
     */
    protected function _Domain()
    {
        $selfAndParentsIds = PageRecursiveCache::i()->getSelfAndParentsIds($this->id);
        if (!$selfAndParentsIds) {
            return new static();
        }
        $domainData = PageRecursiveCache::i()->cache[$selfAndParentsIds[0]];
        return new static($domainData);
    }


    /**
     * Импортирует страницу по URL
     * @param string $url URL для импорта (возможно, включая схему и хост)
     * @return Page
     */
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
        $domainArr = array_keys($domainData);
        $domainId = array_shift($domainArr);
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
        $globPrefix = Package::i()->cacheDir . '/' . Package::i()->cachePrefix;
        $globs = [];
        foreach ($this->domains as $domain) {
            $globs[] = $globPrefix . '*'
                . urlencode('//' . $domain . $this->cache_url) . '.php';
            $globs[] = $globPrefix
                . '*' . urlencode('//' . $domain . $this->cache_url . '?')
                . '*.php';
        }
        $files = [];
        foreach ($globs as $glob) {
            $files = array_merge($files, glob($glob));
        }
        foreach ($files as $file) {
            @unlink($file);
        }
    }


    /**
     * Перестроить кэш страницы
     * @return string|null Текст страницы
     */
    public function rebuildCache()
    {
        if ($this->Material && $this->Material->id) {
            return $this->Material->rebuildCache();
        }
        $this->clearCache();
        $url = $this->fullURL;
        $url = preg_replace('/^(http(s)?:)?\/\//umis', '', $url);
        $url = 'http' . (($_SERVER['HTTPS'] == 'on') ? 's' : '') . '://' . $url;
        $text = @file_get_contents($url, false, stream_context_create([
            'ssl' => [
                'allow_self_signed' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]));
        return $text;
    }


    /**
     * Удаляем "ничейные" блоки
     */
    protected static function clearLostBlocks()
    {
        $blockLink = static::$links['blocks'];
        $tablename = static::_dbprefix() . $blockLink['tablename'];
        // 2017-02-10, AVS: сначала почистим связки на страницы, без реальных страниц
        // так сказать, во избежание
        $sqlQuery = "DELETE tBPA
                        FROM " . $tablename . " AS tBPA
                   LEFT JOIN " . static::_tablename() . "
                          AS tP
                          ON tP." . static::_idN() . " = tBPA." . $blockLink['field_from']
                   . " WHERE tP." . static::_idN() . " IS NULL ";
        static::$SQL->query($sqlQuery);

        // сейчас выберем и удалим блоки, которые не привязаны ни к одной странице
        $sqlQuery = "SELECT tB." . Block::_idN() . "
                       FROM " . Block::_tablename() . " AS tB
                  LEFT JOIN " . $tablename . "
                         AS tBPA
                         ON tB." . Block::_idN() . " = tBPA." . $blockLink['field_to']
                  . " WHERE tBPA." . $blockLink['field_from'] . " IS NULL ";
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
        $materialLink = static::$links['materials'];
        $tablename = static::_dbprefix() . $materialLink['tablename'];
        // 2017-02-10, AVS: сначала почистим связки на страницы,
        // без реальных страниц - так сказать, во избежание
        $sqlQuery = "DELETE tMPA
                        FROM  " . $tablename . " AS tMPA
                   LEFT JOIN " . static::_tablename() . "
                          AS tP
                          ON tP." . static::_idN() . " = tMPA." . $materialLink['field_from']
                   . " WHERE tP." . static::_idN() . " IS NULL ";
        static::$SQL->query($sqlQuery);

        // сейчас выберем и удалим материалы, которые не привязаны ни к одной странице, при этом не глобальные
        $sqlQuery = "SELECT tM.* FROM " . Material::_tablename() . " AS tM
                        JOIN " . Material_Type::_tablename() . "
                          AS tMT
                          ON tMT.id = tM.pid
                   LEFT JOIN " . $tablename . "
                          AS tMPA
                          ON tM." . Material::_idN() . " = tMPA." . $materialLink['field_to']
                   . " WHERE NOT tMT.global_type
                         AND tMPA." . $materialLink['field_from'] . " IS NULL ";
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
                       FROM " . static::_tablename()
                  . " WHERE urn = ?
                        AND pid = ?
                        AND id != ?";
        $sqlResult = static::$SQL->getvalue([
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
        $sqlResult = static::$SQL->getvalue([$sqlQuery, $this->urn]);
        $c = (bool)(int)$sqlResult;
        return $c;
    }
}
