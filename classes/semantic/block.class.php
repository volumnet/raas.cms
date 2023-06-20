<?php
/**
 * Блок
 */
namespace RAAS\CMS;

use Error;
use SOME\SOME;
use RAAS\User as RAASUser;

/**
 * Класс блока
 * @property-read Location $Location Размещение блока
 * @property-read RAASUser $author Автор блока
 * @property-read RAASUser $editor Редактор блока
 * @property-read array<CMSAccess> $access Доступы блока
 * @property-read array<Page> $pages Страницы, на которых размещен блок
 * @property-read array<User> $allowedUsers Пользователи, которым разрешено
 *                                          просматривать блок
 * @property-read Snippet $Interface Интерфейс блока
 * @property-read Snippet $Widget Виджет блока
 * @property-read Snippet $CacheInterface Интерфейс кэширования блока
 * @property-read string $interface Текст интерфейса блока
 * @property-read string $widget Текст виджета блока
 * @property-read string $cache_interface Текст интерфейса кэширования блока
 * @property-read Page $parent Родительская страница (первая из $pages)
 * @property-read int $pid ID# родительской страницы (первой из $pages)
 * @property-read string $title Заголовок блока
 * @property-read array<Page> $pages_assoc Страницы, на которых размещен блок
 * @property-read array<string[] => mixed> $config Конфигурация блока
 * @property-read array $additionalParams Дополнительные параметры блока
 *                                        <pre>array<string[] => mixed></pre>
 */
abstract class Block extends SOME
{
    use AccessibleTrait;

    /**
     * Не кэшировать блок
     */
    const CACHE_NONE = 0;

    /**
     * Кэшировать данные, полученные от интерфейса блока
     */
    const CACHE_DATA = 1;

    /**
     * Кэшировать HTML-выдачу блока
     */
    const CACHE_HTML = 2;

    /**
     * Отображать и с активным материалом, и без него
     */
    const BYMATERIAL_BOTH = 0;

    /**
     * Отображать только с активным материалом
     */
    const BYMATERIAL_WITH = 1;

    /**
     * Отображать только без активного материала
     */
    const BYMATERIAL_WITHOUT = 2;

    protected static $tablename = 'cms_blocks';

    /**
     * Таблица с дополнительными параметрами блока
     */
    protected static $tablename2;

    protected static $defaultOrderBy = "priority";

    protected static $cognizableVars = ['Location'];

    protected static $references = [
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
    ];

    protected static $parents = [];

    protected static $children = [
        'access' => [
            'classname' => CMSAccess::class,
            'FK' => 'block_id'
        ],
    ];

    protected static $links = [
        'pages' => [
            'tablename' => 'cms_blocks_pages_assoc',
            'field_from' => 'block_id',
            'field_to' => 'page_id',
            'classname' => Page::class
        ],
        'allowedUsers' => [
            'tablename' => 'cms_access_blocks_cache',
            'field_from' => 'block_id',
            'field_to' => 'uid',
            'classname' => User::class
        ],
    ];

    protected static $caches = [];

    /**
     * Генерировать блок нужного типа
     * @param int|array $importData Данные для импорта
     * @return Block
     */
    public static function spawn($importData)
    {
        if (is_array($importData)) {
            if (isset($importData['block_type']) &&
                ($classname = $importData['block_type'])
            ) {
                if (class_exists($classname)) {
                    return new $classname($importData);
                }
            }
        } else {
            $sqlQuery = "SELECT block_type
                            FROM " . self::_tablename()
                       . " WHERE id = ?";
            if ($classname = self::$SQL->getvalue([
                $sqlQuery,
                [$importData]
            ])) {
                if (class_exists($classname)) {
                    return new $classname($importData);
                }
            }
        }
        return new Block_HTML($importData);
    }


    public function __construct($importData = null)
    {
        parent::__construct($importData);
        $this->block_type = get_class($this);
        if ($t2 = static::_tablename2()) {
            $sqlQuery = "SELECT *
                           FROM " . $t2
                      . " WHERE id = ?";
            if ($sqlResult = self::$SQL->getline([$sqlQuery, (int)$this->id])) {
                foreach ($sqlResult as $key => $val) {
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
            case 'CacheInterface':
                return new Snippet((int)$this->cache_interface_id);
                break;
            case 'interface':
                return $this->Interface->description;
                break;
            case 'widget':
                return $this->Widget->description;
                break;
            case 'cache_interface':
                return $this->CacheInterface->description;
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
            case 'config':
                return $this->getAddData();
                break;
            case 'additionalParams':
                parse_str(trim($this->params), $temp);
                return $temp;
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
            $sqlQuery = "DELETE FROM " . static::$tablename2 . " WHERE id = ?";
            self::$SQL->query([$sqlQuery, (int)$this->id]);
            self::$SQL->add(static::$tablename2, $arr);
        }
        $this->reload();
        // 2023-01-26, AVS: заменил на прямой SQL-запрос, поскольку при большом количестве товаров сильно тормозит
        // foreach ($this->pages as $row) {
        //     $row->modify();
        // }
        if ($pagesIds = array_map('intval', $this->pages_ids)) {
            $sqlQuery = "UPDATE " . Page::_tablename()
                      . "   SET last_modified = NOW(), modify_counter = modify_counter + 1
                          WHERE id IN (" . implode(", ", $pagesIds) . ")";
            Page::_SQL()->query($sqlQuery);
        }
    }


    /**
     * Проверяет совместимость с материалом
     * @param Page $Page страница, на которой предполагается отобразиться блоку
     * @return bool показывать ли блок
     */
    public function tuneWithMaterial(Page $Page)
    {
        switch ($this->vis_material) {
            case self::BYMATERIAL_BOTH:
                return true;
                break;
            case self::BYMATERIAL_WITH:
                return (bool)($Page->Material->id ?? null);
                break;
            case self::BYMATERIAL_WITHOUT:
                return !($Page->Material->id ?? null);
                break;
        }
        return true;
    }


    /**
     * Получает дополнительные данные блока
     * @return array<string[] => mixed>
     */
    public function getAddData()
    {
        return [];
    }


    /**
     * Сохраняет привязку к страницам
     */
    private function exportPages()
    {
        if ($this->cats) {
            $tablename = self::_dbprefix() . self::$links['pages']['tablename'];
            $ids = (array)($this->cats ?? []);
            $old_ids = array_map('intval', array_diff($this->pages_ids, $ids));
            $new_ids = array_map('intval', array_diff($ids, $this->pages_ids));
            if ($old_ids) {
                $sqlQuery = "DELETE FROM " . $tablename
                          . " WHERE block_id = ?
                                AND page_id IN (" . implode(", ", $old_ids) . ")";
                self::$SQL->query([$sqlQuery, (int)$this->id]);
            }
            if ($new_ids) {
                $sqlQuery = "SELECT MAX(priority) FROM " . $tablename;
                $priority = (int)self::$SQL->getvalue($sqlQuery);
                $arr = [];
                foreach ($new_ids as $id) {
                    $arr[] = [
                        'block_id' => $this->id,
                        'page_id' => (int)$id,
                        'priority' => ++$priority
                    ];
                }
                self::$SQL->add($tablename, $arr);
            }
        }
    }


    /**
     * Перемещает блок по списку внутри размещения
     * @param int $step Шаг перемещения, <0 - вверх, >0 - вниз
     * @param Page $page На какой странице
     */
    public function swap($step, Page $page)
    {
        $tablename = self::_dbprefix() . self::$links['pages']['tablename'];
        $sqlQuery = "SELECT priority
                       FROM " . $tablename
                  . " WHERE block_id = ?
                        AND page_id = ?";
        $priority = (int)self::$SQL->getvalue([
            $sqlQuery,
            (int)$this->id,
            (int)$page->id
        ]);

        $sqlQuery = "SELECT tBPA.block_id, tBPA.priority
                        FROM " . $tablename . " AS tBPA
                        JOIN " . self::_tablename()
                  . "     AS tB
                          ON tB.id = tBPA.block_id
                       WHERE tBPA.priority " . ($step < 0 ? "<" : ">") . " ?
                         AND tBPA.page_id = ?
                         AND tB.location = ?
                    ORDER BY tBPA.priority " . ($step < 0 ? "DESC" : "ASC");
        $sqlBind = [(int)$priority, (int)$page->id, $this->location];
        if (!is_infinite($step)) {
            $sqlQuery .= " LIMIT ?";
            $sqlBind[] = abs((int)$step);
        }
        $swapwith = static::$SQL->get([$sqlQuery, $sqlBind]);
        $save_ok = true;
        // 2015-03-12 AVS: закомментил page_id = ..., т.к. менять порядок
        // на каждой странице не удобно
        if ($swapwith) {
            for ($i = 0; $i < count($swapwith); $i++) {
                $swapId = static::$SQL->quote($swapwith[$i]['block_id']);
                if ($i) {
                    $swapPri = (int)$swapwith[$i - 1]['priority'];
                } else {
                    $swapPri = (int)$priority;
                }
                $save_ok = static::$SQL->update(
                    $tablename,
                    /*"page_id = " . (int)$page->id . " AND " .*/
                    " block_id = " . $swapId,
                    ['priority' => $swapPri]
                );
            }
            $priority = (int)$swapwith[count($swapwith) - 1]['priority'];
            static::$SQL->update(
                $tablename,
                /*"page_id = " . (int)$page->id . " AND " .*/
                " block_id = " . $this->id,
                ['priority' => $priority]
            );
        }
        return $save_ok;
    }


    /**
     * Убрать блок со страницы
     * @param Page $page Страница, с которой убираем
     */
    public function unassoc(Page $page)
    {
        $tablename = self::$dbprefix . self::$links['pages']['tablename'];
        $sqlQuery = "DELETE FROM " . $tablename
                  . " WHERE block_id = ?
                        AND page_id = ?";
        self::$SQL->query([$sqlQuery, (int)$this->id, (int)$page->id]);
        $this->reload();
        if (!$this->pages_assoc) {
            self::delete($this);
        }
    }


    /**
     * Отрабатывает блок
     * @param Page $page На какой странице
     * @param bool $nocache Без кэша
     * @return mixed|null Данные, возвращенные из виджета (если есть)
     */
    public function process(Page $page, $nocache = false)
    {
        $bst = microtime(true);
        if (!$this->currentUserHasAccess()) {
            return null;
        }
        $config = $this->getAddData();

        // Пытаемся прочесть из HTML-кэша
        $in = [];
        if (!$nocache && ($this->cache_type == static::CACHE_HTML)) {
            $in = (array)$this->loadCache($_SERVER['REQUEST_URI']);
        }
        if (!$in) {
            // Пытаемся прочесть из кэша данных
            if (!$nocache && ($this->cache_type == static::CACHE_DATA)) {
                $in = (array)$this->loadCache($_SERVER['REQUEST_URI']);
            }
            // 2015-11-23, AVS: перенес ob_start, т.к., допустим, у блока
            // Яндекс-Маркета нет виджета, а только интерфейс
            ob_start();
            if (!$in) {
                // Не удалось, загрузим интерфейс
                $in = (array)$this->processInterface($config, $page);
                $in['config'] = $config;
                if ($this->cache_type == static::CACHE_DATA) {
                    // Запишем в кэш данных
                    $in = $this->processCache($in, $page);
                }
            }
            if ($this->Widget->id) {
                $data = $this->processWidget($in, $page);
            } elseif ($page->mime == 'application/json') {
                $data = $in;
                unset($data['config']);
                echo json_encode($data);
            }
            if ($this->cache_type == static::CACHE_HTML) {
                // Запишем в HTML-кэш
                $this->processCache($in, $page);
            }
            $content = ob_get_clean();
            $content = Package::processInternalLinks($content, $page);
            echo $content;
            if ($diag = Controller_Frontend::i()->diag) {
                $diagId = $this->id;
                if (($this instanceof Block_Material) &&
                    $this->nat &&
                    (($page->Material && $page->Material->id) || ($page->Item && $page->Item->id))
                ) {
                    $diagId .= '@m';
                }
                $diag->handle('blocks', $diagId, microtime(true) - $bst);
            }
            if ($data ?? null) {
                return $data;
            }
        }
    }


    /**
     * Получает файл кэша
     * @param string $url С какого URL получить файл кэша
     * @param Page $page У какой страницы получить файл кэша
     * @return string
     */
    public function getCacheFile($url = null, Page $page = null)
    {
        if ($this->cache_type != static::CACHE_NONE) {
            $domain = $page
                    ? preg_replace('/^http(s)?:\\/\\//umi', '', $page->domain)
                    : $_SERVER['HTTP_HOST'];
            if (!$url) {
                $url = $page ? $page->url : $_SERVER['REQUEST_URI'];
            }
            $filename = Package::i()->cacheDir . '/' . Package::i()->cachePrefix
                      . '_block' . (int)$this->id;
            if ($url && $this->cache_single_page) {
                $filename .= '.' . urlencode($domain . $url);
            }
            $filename .= '.php';
            return $filename;
        }
        return null;
    }


    /**
     * Обрабатывает интерфейс
     * @param array $config Конфигурация блока
     * @param Page $page Страница, для которой обрабатываем интерфейс
     * @return mixed Результат обработки интерфейса
     */
    protected function processInterface(array $config = [], Page $page = null)
    {
        if ($this->Interface->id) {
            $st = microtime(true);
            $out = $this->Interface->process([
                'SITE' => $page->Domain,
                'Page' => $page,
                'page' => $page,
                'Block' => $this,
                'block' => $this,
                'Interface' => $this->Interface,
                'Widget' => $this->Widget,
                'config' => $config,
            ]);
            if ($diag = Controller_Frontend::i()->diag) {
                $diagId = $this->id;
                if (($this instanceof Block_Material) &&
                    $this->nat &&
                    (($page->Material && $page->Material->id) || ($page->Item && $page->Item->id))
                ) {
                    $diagId .= '@m';
                }
                $diag->handle(
                    'blocks',
                    $diagId,
                    microtime(true) - $st,
                    null,
                    'interfaceTime'
                );
            }
            return $out;
        }
    }


    /**
     * Обрабатывает виджет
     * @param array $in Входные данные
     * @param Page $page Страница, для которой обрабатываем виджет
     */
    protected function processWidget(array $in = [], $page = null)
    {
        if ($this->Widget->id) {
            $st = microtime(true);
            $this->Widget->process(array_merge($in, [
                'IN' => $in,
                'SITE' => $page->Domain,
                'Page' => $page,
                'page' => $page,
                'Block' => $this,
                'block' => $this,
                'Interface' => $this->Interface,
                'Widget' => $this->Widget,
            ]));
            if ($diag = Controller_Frontend::i()->diag) {
                $diagId = $this->id;
                if (($this instanceof Block_Material) &&
                    $this->nat &&
                    (($page->Material && $page->Material->id) || ($page->Item && $page->Item->id))
                ) {
                    $diagId .= '@m';
                }
                $diag->handle(
                    'blocks',
                    $diagId,
                    microtime(true) - $st,
                    null,
                    'widgetTime'
                );
            }
        }
    }


    /**
     * Отрабатывает кэш
     * @param array $in Входные данные
     * @param Page $page Страница, для которой обрабатываем кэш
     */
    protected function processCache(array $in = [], $page = null)
    {
        $out = $in;
        if ($this->CacheInterface->id) {
            $result = $this->CacheInterface->process(array_merge($in, [
                'IN' => $in,
                'OUT' => $in,
                'SITE' => $page->Domain,
                'Page' => $page,
                'page' => $page,
                'Block' => $this,
                'block' => $this,
            ]));
            if (is_array($result)) {
                $out = array_merge($out, $result);
            }
        }
        return $out;
    }


    /**
     * Загрузить кэш
     * @param string $url Файл кэша
     * @return mixed
     */
    public function loadCache($url = null)
    {
        $out = [];
        if ($this->cache_type != static::CACHE_NONE) {
            $filename = $this->getCacheFile($url);
            if (is_file($filename)) {
                // 2022-07-08, AVS: добавил @, чтобы при вызове ошибочного файла не выводил ошибку
                try {
                    $out = @include $filename;
                } catch (Error $e) {
                }
            }
        }
        return $out;
    }


    /**
     * Очистить кэш
     */
    public function clearCache()
    {
        $OUT = [];
        if ($this->cache_type != static::CACHE_NONE) {
            $filename = $this->getCacheFile();
            $globname = str_replace('.php', '.*.php', $filename);
            $glob = array_merge(
                glob($filename),
                glob($globname)
            );
            foreach ($glob as $file) {
                @unlink($file);
            }
        }
        return $OUT;
    }


    /**
     * Размещение блока
     * @return Location
     */
    protected function _Location()
    {
        return $this->parent->Template->locations[$this->location];
    }


    /**
     * Таблица дополнительных данных
     * @return string
     */
    public static function _tablename2()
    {
        if (static::$tablename2) {
            return static::$dbprefix . static::$tablename2;
        }
    }


    public static function delete(SOME $item)
    {
        if ($t2 = static::_tablename2()) {
            $sqlQuery = "DELETE FROM " . $t2 . " WHERE id = ?";
            self::$SQL->query([$sqlQuery, (int)$item->id]);
        }
        parent::delete($item);
    }
}
