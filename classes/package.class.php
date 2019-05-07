<?php
/**
 * Пакет CMS
 */
namespace RAAS\CMS;

use SOME\File;
use SOME\Pages;
use SOME\SOME;
use SOME\Text;
use RAAS\Attachment;
use RAAS\Application;
use RAAS\Package as RAASPackage;

/**
 * Класс пакета CMS
 * @property-read string $cacheDir Директория, где хранятся кэши
 * @property-read string $cachePrefix Префикс файлов кэшей
 * @property-read string $formTemplateFile Файл стандартного уведомления формы
 * @property-read string $stdFormTemplate Текст стандартного уведомления формы
 * @property-read string $stdMaterialInterfaceFile Файл стандартного интерфейса
 *                                                 материалов
 * @property-read string $stdMaterialViewFile Файл стандартного виджета
 *                                            материалов
 * @property-read string $stdMaterialView Текст стандартного виджета материалов
 * @property-read string $stdMenuInterfaceFile Файл стандартного интерфейса меню
 * @property-read string $stdMenuInterface Текст стандартного интерфейса меню
 * @property-read string $stdMenuViewFile Файл стандартного виджета меню
 * @property-read string $stdMenuView Текст стандартного виджета меню
 * @property-read string $stdFormInterfaceFile Файл стандартного интерфейса
 *                                             формы
 * @property-read string $stdFormInterface Текст стандартного интерфейса формы
 * @property-read string $stdFormViewFile Файл стандартного виджета формы
 * @property-read string $stdFormView Текст стандартного виджета формы
 * @property-read string $stdSearchInterfaceFile Файл стандартного интерфейса
 *                                               поиска
 * @property-read string $stdSearchInterface Текст стандартного интерфейса
 *                                           поиска
 * @property-read string $stdSearchViewFile Файл стандартного виджета поиска
 * @property-read string $stdSearchView Текст стандартного виджета поиска
 * @property-read string $stdCacheInterfaceFile Файл стандартного интерфейса
 *                                              кэширования
 * @property-read string $stdCacheInterface Файл стандартного интерфейса
 *                                          кэширования
 * @property-read string $stdWatermarkInterfaceFile Файл стандартного интерфейса
 *                                                  водяных знаков
 * @property-read string $stdWatermarkInterface Текст стандартного интерфейса
 *                                              водяных знаков
 * @property-read string $isAndroid Использует ли пользователь Android
 * @property-read string $isAndroidTablet Использует ли пользователь Android на
 *                                        планшете
 * @property-read string $isAndroidPhone Использует ли пользователь Android на
 *                                       телефоне
 * @property-read string $isIPad Использует ли пользователь iPad
 * @property-read string $isIPhone Использует ли пользователь iPhone
 * @property-read string $isIPod Использует ли пользователь iPod
 * @property-read string $isApple Использует ли пользователь устройство Apple
 * @property-read string $isWindowsPhone Использует ли пользователь устройство
 *                                       на Windows Phone
 * @property-read string $isPhone Использует ли пользователь телефон
 * @property-read string $isTablet Использует ли пользователь планшет
 */
class Package extends RAASPackage
{
    /**
     * Папка шаблонов
     */
    const templatesDir = 'templates';

    /**
     * Версия пакета
     */
    const version = '2018-05-01 11:30:00';

    protected static $instance;

    public function __get($var)
    {
        $ua = $_SERVER['HTTP_USER_AGENT'];
        switch ($var) {
            case 'cacheDir':
                return $this->application->baseDir . '/cache';
                break;
            case 'cachePrefix':
                return 'raas_cache';
                break;
            case 'formTemplateFile':
                return $this->resourcesDir .
                       '/interfaces/form_notification.php';
                break;
            case 'stdFormTemplate':
                $text = file_get_contents($this->formTemplateFile);
                return $text;
            case 'stdMaterialInterfaceFile':
                return $this->resourcesDir .
                       '/interfaces/material_interface.php';
                break;
            case 'stdMaterialInterface':
                $text = file_get_contents($this->stdMaterialInterfaceFile);
                return $text;
                break;
            case 'stdMaterialViewFile':
                return $this->resourcesDir . '/material.tmp.php';
                break;
            case 'stdMaterialView':
                $text = file_get_contents($this->stdMaterialViewFile);
                return $text;
                break;
            case 'stdMenuInterfaceFile':
                return $this->resourcesDir . '/interfaces/menu_interface.php';
                break;
            case 'stdMenuInterface':
                $text = file_get_contents($this->stdMenuInterfaceFile);
                return $text;
                break;
            case 'stdMenuViewFile':
                return $this->resourcesDir . '/menu.tmp.php';
                break;
            case 'stdMenuView':
                $text = file_get_contents($this->stdMenuViewFile);
                return $text;
                break;
            case 'stdFormInterfaceFile':
                return $this->resourcesDir . '/interfaces/form_interface.php';
                break;
            case 'stdFormInterface':
                $text = file_get_contents($this->stdFormInterfaceFile);
                return $text;
                break;
            case 'stdFormViewFile':
                return $this->resourcesDir . '/form.tmp.php';
                break;
            case 'stdFormView':
                $text = file_get_contents($this->stdFormViewFile);
                return $text;
                break;
            case 'stdSearchInterfaceFile':
                return $this->resourcesDir . '/interfaces/search_interface.php';
                break;
            case 'stdSearchInterface':
                $text = file_get_contents($this->stdSearchInterfaceFile);
                return $text;
                break;
            case 'stdSearchViewFile':
                return $this->resourcesDir . '/search.tmp.php';
                break;
            case 'stdSearchView':
                $text = file_get_contents($this->stdSearchViewFile);
                return $text;
                break;
            case 'stdCacheInterfaceFile':
                return $this->resourcesDir . '/interfaces/cache_interface.php';
                break;
            case 'stdCacheInterface':
                $text = file_get_contents($this->stdCacheInterfaceFile);
                return $text;
                break;
            case 'stdWatermarkInterfaceFile':
                return $this->resourcesDir .
                       '/interfaces/watermark_interface.php';
                break;
            case 'stdWatermarkInterface':
                $text = file_get_contents($this->stdWatermarkInterfaceFile);
                return $text;
                break;
            case 'isAndroid':
                return (bool)stristr($ua, 'android');
                break;
            case 'isAndroidTablet':
                return $this->isAndroid && !(bool)stristr($ua, 'mobile');
                break;
            case 'isAndroidPhone':
                return $this->isAndroid && (bool)stristr($ua, 'mobile');
                break;
            case 'isIPad':
                return (bool)stristr($ua, 'ipad');
                break;
            case 'isIPhone':
                return (bool)stristr($ua, 'iphone');
                break;
            case 'isIPod':
                return (bool)stristr($ua, 'ipod');
                break;
            case 'isApple':
                return $this->iPad || $this->iPhone || $this->iPod;
                break;
            case 'isWindowsPhone':
                return (bool)stristr($ua, 'windows') &&
                       (bool)stristr($ua, 'phone');
                break;
            case 'isPhone':
                return $this->isAndroidPhone ||
                       $this->isWindowsPhone ||
                       $this->isIPhone ||
                       $this->isIPod;
                break;
            case 'isTablet':
                return $this->isAndroidTablet || $this->isIPad;
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function init()
    {
        $_SESSION['KCFINDER']['uploadURL'] = '/files/cms/common/';
        $_SESSION['KCFINDER']['disabled'] = false;
        parent::init();
        Block_Type::registerType(
            Block_HTML::class,
            ViewBlockHTML::class,
            EditBlockHTMLForm::class
        );
        Block_Type::registerType(
            Block_PHP::class,
            ViewBlockPHP::class,
            EditBlockPHPForm::class
        );
        Block_Type::registerType(
            Block_Material::class,
            ViewBlockMaterial::class,
            EditBlockMaterialForm::class
        );
        Block_Type::registerType(
            Block_Menu::class,
            ViewBlockMenu::class,
            EditBlockMenuForm::class
        );
        Block_Type::registerType(
            Block_Form::class,
            ViewBlockForm::class,
            EditBlockFormForm::class
        );
        Block_Type::registerType(
            Block_Search::class,
            ViewBlockSearch::class,
            EditBlockSearchForm::class
        );
        foreach ($this->modules as $module) {
            if (method_exists($module, 'registerBlockTypes')) {
                $module->registerBlockTypes();
            }
        }
    }


    /**
     * Подготовка данных для отображения страницы
     * @return [
     *             'Set' => array<Page> набор подразделов,
     *             'sort' => string Поля для сортировки,
     *             'order' => 'asc'|'desc' Порядок сортировки,
     *             'columns' => array<Page_Field> Колонки для отображения,
     *         ]
     */
    public function show_page()
    {
        $Parent = new Page(
            isset($this->controller->nav['id']) ?
            (int)$this->controller->nav['id'] :
            0
        );
        $columns = array_filter(
            $Parent->fields,
            function ($x) {
                return $x->show_in_table;
            }
        );
        $Set = $Parent->children;
        if (isset($this->controller->nav['id'])) {
            $sort = 'priority';
        } else {
            $sort = 'urn';
            if (isset($this->controller->nav['sort'])) {
                if (isset($columns[$this->controller->nav['sort']]) &&
                    ($row = $columns[$this->controller->nav['sort']])
                ) {
                    $sort = $row->urn;
                } else {
                    switch ($this->controller->nav['sort']) {
                        case 'name':
                            $sort = 'name';
                            break;
                    }
                }
            }
        }
        if (!isset($this->controller->nav['id']) &&
            isset($this->controller->nav['order']) &&
            ($this->controller->nav['order'] == 'desc')
        ) {
            $order = 'desc';
            $reverse = true;
        } else {
            $order = 'asc';
            $reverse = false;
        }
        $f = $this->getCompareFunction($sort, $reverse);
        usort($Set, $f);
        return [
            'Set' => $Set,
            'sort' => $sort,
            'order' => $order,
            'columns' => $columns
        ];
    }


    /**
     * Подготовка данных для отображения справочников
     * @return [
     *             'Set' => array<Dictionary> набор подразделов,
     *             'Pages' => Pages Постраничная разбивка,
     *             'sort' => string Поля для сортировки,
     *             'order' => 'asc'|'desc' Порядок сортировки,
     *         ]
     */
    public function dev_dictionaries()
    {
        $Parent = new Dictionary(
            isset($this->controller->nav['id']) ?
            (int)$this->controller->nav['id'] :
            0
        );
        $sqlQuery = "SELECT SQL_CALC_FOUND_ROWS *
                       FROM " . Dictionary::_tablename()
                  . " WHERE pid = " . (int)$Parent->id;
        if ($Parent->orderby && ($Parent->orderby != 'priority')) {
            $sort = $Parent->orderby;
        }
        if ($Parent->orderby &&
            isset($this->controller->nav['order']) &&
            ($this->controller->nav['order'] == 'desc')
        ) {
            $order = 'desc';
        } else {
            $order = 'asc';
        }
        $sqlQuery .= " ORDER BY priority " . strtoupper($order)
                  .  ($sort ? ", " . $sort . " " . strtoupper($order) : "");
        $Pages = new Pages(
            (
                isset($this->controller->nav['page']) ?
                $this->controller->nav['page'] :
                1
            ),
            Application::i()->registryGet('rowsPerPage')
        );
        $Set = Dictionary::getSQLSet($sqlQuery, $Pages);
        return [
            'Set' => $Set,
            'Pages' => $Pages,
            'sort' => $sort,
            'order' => $order
        ];
    }


    /**
     * Загружает справочник из файла
     * @param Dictionary $dictionary Справочник, в который загружаем
     * @param string $file Имя файла
     */
    public function dev_dictionaries_loadFile(Dictionary $dictionary, $file)
    {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $text = file_get_contents($file['tmp_name']);
        if (in_array($ext, ['csv', 'ini', 'sql']) && !mb_check_encoding($text)) {
            switch ($this->view->language) {
                default:
                    $text = iconv('Windows-1251', mb_internal_encoding(), $text);
                    break;
            }
        }
        switch ($ext) {
            case 'csv':
                $dictionary->parseCSV($text);
                break;
            case 'ini':
                $dictionary->parseINI($text);
                break;
            case 'xml':
                $dictionary->parseXML($text);
                break;
            case 'sql':
                $dictionary->parseSQL($text);
                break;
        }
    }


    /**
     * Готовит данные для отображения списка шаблонов
     * @return array<Template>
     */
    public function dev_templates()
    {
        return Template::getSet();
    }


    /**
     * Готовит данные для отображения списка типов материалов
     * @return array<Material_Type>
     */
    public function material_types()
    {
        return Material_Type::getSet();
    }


    /**
     * Готовит данные для отображения списка форм
     * @return array<Form>
     */
    public function forms()
    {
        return Form::getSet();
    }


    /**
     * Готовит данные для отображения списка полей страниц
     * @return array<Page_Field>
     */
    public function dev_pages_fields()
    {
        return Page_Field::getSet();
    }


    /**
     * Возвращает список корневых справочников
     * @return array<Dictionary>
     */
    public function getDictionaries()
    {
        return Dictionary::getSet(['where' => "NOT pid"]);
    }


    /**
     * Получает материалы страницы
     * @param Page $page Страница, для которой получаем материалы
     * @param Material_Type $mType Тип материалов, которые получаем
     * @param string $searchString Поисковая строка
     * @param string $sort Поле для сортировки
     * @param 'asc'|'desc' $order Порядок сортировки
     * @param int $pageNum Номер страницы в постраничной разбивке
     * @return [
     *             'Set' => array<Material> набор материалов,
     *             'Pages' => Pages Постраничная разбивка,
     *             'sort' => string Поля для сортировки,
     *             'order' => 'asc'|'desc' Порядок сортировки,
     *         ]
     */
    public function getPageMaterials(
        Page $page,
        Material_Type $mType,
        $searchString = null,
        $sort = 'post_date',
        $order = 'asc',
        $pageNum = 1
    ) {
        $columns = array_filter(
            $mType->fields,
            function ($x) {
                return $x->show_in_table;
            }
        );

        $sqlQuery = "SELECT SQL_CALC_FOUND_ROWS tM.*
                        FROM " . Material::_tablename() . " AS tM ";
        // 2016-01-14, AVS: добавил поиск по данным
        if ($searchString) {
            $sqlQuery .= " LEFT JOIN " . Material::_dbprefix() . Material_Field::data_table . " AS tD ON tD.pid = tM.id
                            LEFT JOIN " . Material_Field::_tablename() . " AS tF ON tD.fid = tF.id ";
        }
        if (!$mType->global_type) {
            $sqlQuery .= " LEFT JOIN " . Material::_dbprefix() . "cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id ";
        }
        $sqlQuery .= " WHERE tM.pid IN (" . implode(", ", $mType->selfAndChildrenIds) . ")";
        if (!$mType->global_type) {
            $sqlQuery .= " AND (tMPA.pid = " . (int)$this->controller->id . " OR tMPA.pid IS NULL) ";
        }
        // 2016-01-14, AVS: добавил поиск по данным
        if ($searchString) {
            $likeSearchString = $this->SQL->real_escape_string($searchString);
            $sqlQuery .= " AND tF.classname = 'RAAS\\\\CMS\\\\Material_Type' AND tF.pid
                            AND (
                                    tM.name LIKE '%" . $likeSearchString . "%'
                                 OR tM.description LIKE '%" . $likeSearchString . "%'
                                 OR tM.urn LIKE '%" . $likeSearchString . "%'
                                 OR tD.value LIKE '%" . $likeSearchString . "%'
                            )";
        }
        $sqlQuery .= " GROUP BY tM.id ";
        $Pages = new Pages($pageNum, Application::i()->registryGet('rowsPerPage'));
        if (isset($sort, $columns[$sort]) && ($row = $columns[$sort])) {
            $_sort = $row->urn;
            $Set = Material::getSQLSet($sqlQuery);
            if (isset($order) && ($order == 'desc')) {
                $_order = 'desc';
                $reverse = true;
            } else {
                $_order = 'asc';
                $reverse = false;
            }
            $f = $this->getCompareFunction($_sort, $reverse, true);
            usort($Set, $f);
            $Set = SOME::getArraySet($Set, $Pages);
        } else {
            switch ($sort) {
                case 'name':
                case 'urn':
                case 'modify_date':
                    $_sort = 'tM.' . $sort;
                    break;
                default:
                    $sort = 'post_date';
                    $_sort = 'tM.post_date';
                    break;
            }
            if (isset($order) && ($order == 'desc')) {
                $_order = 'desc';
            } elseif (!isset($order) &&
                in_array($sort, ['post_date', 'modify_date'])
            ) {
                $_order = 'desc';
            } else {
                $_order = 'asc';
            }

            $sqlQuery .= " ORDER BY NOT tM.priority ASC,
                                    tM.priority ASC, "
                      .             $_sort . " " . strtoupper($_order);
            $Set = Material::getSQLSet($sqlQuery, $Pages);
        }
        return [
            'Set' => $Set,
            'Pages' => $Pages,
            'sort' => $sort,
            'order' => $_order
        ];
    }


    /**
     * Получает связанные материалы для текущего материала
     * @param Material $item Материал, для которой получаем связанные
     * @param Material_Type $mType Тип материалов, которые получаем
     * @param string $searchString Поисковая строка
     * @param string $sort Поле для сортировки
     * @param 'asc'|'desc' $order Порядок сортировки
     * @param int $pageNum Номер страницы в постраничной разбивке
     * @return [
     *             'Set' => array<Material> набор материалов,
     *             'Pages' => Pages Постраничная разбивка,
     *             'sort' => string Поля для сортировки,
     *             'order' => 'asc'|'desc' Порядок сортировки,
     *         ]
     */
    public function getRelatedMaterials(
        Material $item,
        Material_Type $mType,
        $searchString = null,
        $sort = 'post_date',
        $order = 'asc',
        $pageNum = 1
    ) {
        $columns = array_filter(
            $mType->fields,
            function ($x) {
                return $x->show_in_table;
            }
        );

        $ids = array_merge([0], $item->material_type->selfAndParentsIds);
        $sqlQuery = "SELECT tF.id
                        FROM " . Material_Field::_tablename() . " AS tF
                       WHERE tF.classname = 'RAAS\\\\CMS\\\\Material_Type'
                         AND tF.pid = " . (int)$mType->id . "
                         AND tF.datatype = 'material'
                         AND source IN (" . implode(", ", $ids) . ")";
        $fields = $this->SQL->getcol($sqlQuery);

        $sqlQuery = "SELECT SQL_CALC_FOUND_ROWS tM.* FROM " . Material::_tablename() . " AS tM
                        JOIN " . Material_Field::_dbprefix() . Material_Field::data_table . " AS tD ON tD.pid = tM.id";
        // 2016-01-14, AVS: добавил поиск по данным
        if ($searchString) {
            $sqlQuery .= " LEFT JOIN " . Material::_dbprefix() . Material_Field::data_table . " AS tD2 ON tD2.pid = tM.id
                            LEFT JOIN " . Material_Field::_tablename() . " AS tF2 ON tD2.fid = tF2.id ";
        }
        $types = $mType->selfAndChildrenIds;
        $sqlQuery .= " WHERE tM.pid IN (" . implode(", ", $types) . ") AND tD.fid IN (" . implode(", ", $fields) . ") AND tD.value = " . (int)$item->id;
        if ($searchString) {
            $likeSearchString = $this->SQL->real_escape_string($searchString);
            $sqlQuery .= " AND tF2.classname = 'RAAS\\\\CMS\\\\Material_Type' AND tF2.pid
                            AND (
                                    tM.name LIKE '%" . $likeSearchString . "%'
                                 OR tM.urn LIKE '%" . $likeSearchString . "%'
                                 OR tD2.value LIKE '%" . $likeSearchString . "%'
                            )";
        }
        // 2016-12-27, AVS: добавил группировку одинаковых материалов, иначе ссылающиеся несколько раз перечислялись тоже несколько раз
        $sqlQuery .= " GROUP BY tM.id ";
        $Pages = new Pages($pageNum, Application::i()->registryGet('rowsPerPage'));
        if (isset($sort, $columns[$sort]) && ($row = $columns[$sort])) {
            $_sort = $row->urn;
            $Set = Material::getSQLSet($sqlQuery);
            if (isset($order) && ($order == 'desc')) {
                $_order = 'desc';
                $reverse = true;
            } else {
                $_order = 'asc';
                $reverse = false;
            }
            $f = $this->getCompareFunction($_sort, $reverse, true);
            usort($Set, $f);
            $Set = SOME::getArraySet($Set, $Pages);
        } else {
            switch ($sort) {
                case 'name':
                case 'urn':
                case 'modify_date':
                    $_sort = "tM." . $sort;
                    break;
                default:
                    $sort = 'post_date';
                    $_sort = 'tM.post_date';
                    break;
            }
            if (isset($order) && ($order == 'desc')) {
                $_order = 'desc';
            } elseif (!isset($order) &&
                in_array($sort, ['post_date', 'modify_date'])
            ) {
                $_order = 'desc';
            } else {
                $_order = 'asc';
            }

            $sqlQuery .= " ORDER BY NOT tM.priority,
                                    tM.priority, "
                      .             $_sort . " " . strtoupper($_order);
            $Set = Material::getSQLSet($sqlQuery, $Pages);
        }
        return [
            'Set' => $Set,
            'Pages' => $Pages,
            'sort' => $sort,
            'order' => $_order
        ];
    }


    /**
     * Получает данные для отображения заявок обратной связи
     * @param bool $pagination Включить постраничную разбивку
     * @return [
     *             'Set' => array<Feedback> набор заявок,
     *             'Pages' => Pages Постраничная разбивка,
     *             'Parent' => Form Родительская форма,
     *             'columns' => array<Form_Field> Поля для отображения,
     *         ]
     */
    public function feedback($pagination = true)
    {
        $Parent = new Form(
            isset($this->controller->nav['id']) ?
            (int)$this->controller->nav['id'] :
            0
        );
        $col_where = "classname = 'RAAS\\\\CMS\\\\Form' ";
        if ($pagination) {
            $col_where .= " AND show_in_table ";
        }
        $sqlQuery = "SELECT SQL_CALC_FOUND_ROWS tF.*
                        FROM " . Feedback::_tablename() .  " AS tF
                   LEFT JOIN " . Field::_tablename() .  " AS tFi ON tFi.pid = tF.pid AND tFi.classname = 'RAAS\\\\CMS\\\\Form'
                   LEFT JOIN " . Feedback::_dbprefix() . Material_Field::data_table . " AS tD ON tD.pid = tF.id AND tD.fid = tFi.id
                       WHERE 1 ";
        $columns = [];
        if ($Parent->id) {
            $sqlQuery .= " AND tF.pid = " . (int)$Parent->id;
            $col_where .= " AND pid = " . (int)$Parent->id;
            $columns = Form_Field::getSet(['where' => $col_where]);
        }
        if (isset($this->controller->nav['search_string']) &&
            $this->controller->nav['search_string']
        ) {
            $likeSearchString = $this->SQL->escape_like(
                $this->controller->nav['search_string']
            );
            $sqlQuery .= " AND (
                                (tF.id = '" . $this->SQL->real_escape_string($this->controller->nav['search_string']) . "') OR
                                (tF.ip LIKE '%" . $likeSearchString . "%') OR
                                (tD.value LIKE '%" . $likeSearchString . "%')
                            ) ";
        }
        if (isset($this->controller->nav['from']) && $this->controller->nav['from']) {
            $t = strtotime($this->controller->nav['from']);
            if ($t > 0) {
                $sqlQuery .= " AND tF.post_date >= '" . date('Y-m-d H:i:s', $t) . "'";
            }
        }
        if (isset($this->controller->nav['to']) && $this->controller->nav['to']) {
            $t = strtotime($this->controller->nav['to']);
            if ($t > 0) {
                $sqlQuery .= " AND tF.post_date <= '" . date('Y-m-d H:i:s', $t) . "'";
            }
        }

        $sqlQuery .= " GROUP BY tF.id ORDER BY tF.post_date DESC ";
        if ($pagination) {
            $Pages = new Pages(
                (
                    isset($this->controller->nav['page']) ?
                    $this->controller->nav['page'] :
                    1
                ),
                Application::i()->registryGet('rowsPerPage')
            );
        } else {
            $Pages = null;
        }
        $Set = Feedback::getSQLSet($sqlQuery);
        return [
            'Set' => $Set,
            'Pages' => $Pages,
            'Parent' => $Parent,
            'columns' => $columns
        ];
    }


    /**
     * Очищает кэши
     * @param bool $all Очистить все кэши (если false, то по времени)
     */
    public function clearCache($all = true)
    {
        $files = [];
        $t = $this->registryGet('clear_cache_by_time');
        if (is_dir($this->cacheDir)) {
            $dir = File::scandir($this->cacheDir);
            foreach ($dir as $f) {
                if (is_file($this->cacheDir . '/' . $f) && preg_match(
                    '/^' . preg_quote($this->cachePrefix) . '(.*?)\\.php$/i',
                    $f
                )) {
                    $f = $this->cacheDir . '/' . $f;
                    if ($all || !$t || (filemtime($f) < time() - ($t * 60))) {
                        $files[] = $f;
                    }
                }
            }
        }
        foreach ($files as $file) {
            unlink($file);
        }
    }


    /**
     * Очищает кэши блоков
     */
    public function clearBlocksCache()
    {
        $files = [];
        if (is_dir($this->cacheDir)) {
            $dir = File::scandir($this->cacheDir);
            foreach ($dir as $f) {
                if (is_file($this->cacheDir . '/' . $f) && preg_match(
                    '/^' . preg_quote($this->cachePrefix) . '_block(.*?)\\.php$/i',
                    $f
                )) {
                    $f = $this->cacheDir . '/' . $f;
                    @unlink($f);
                }
            }
        }
    }


    /**
     * Получает карту необходимых кэшей
     * @return array<int[] ID# страницы => array<int[] ID# материала => array<
     *             'id' => int ID# страницы,
                   'mid' =>? int ID# материала,
                   'url' => string Полный адрес страницы или страницы материала,
                   'name' => string Наименование страницы или материала,
                   'cache' => int Включено ли кэширование (1) или отключено (0)
     *         >>>
     */
    public function getCacheMap()
    {
        $Set = [];

        // Строим полную карту сайта
        $siteMap = [];
        $sqlQuery = "SELECT *
                       FROM " . Page::_tablename()
                  . " WHERE vis
                        AND NOT response_code";
        $sqlResult = Page::_SQL()->get($sqlQuery);
        foreach ($sqlResult as $row) {
            $row = new Page($row);
            if (preg_match(
                '/(^| )' . preg_quote($_SERVER['HTTP_HOST']) . '( |$)/i',
                $row->Domain->urn
            )) {
                $domainUrl = 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '')
                           . '://' . $_SERVER['HTTP_HOST'];
            } else {
                $domainUrl = $row->domain;
            }
            $siteMap[(int)$row->id][0] = [
                'id' => $row->id,
                'url' => $domainUrl . $row->url,
                'name' => $row->name,
                'cache' => $row->cache
            ];
            foreach ($row->affectedMaterials as $row2) {
                $siteMap[(int)$row->id][(int)$row2->id] = [
                    'id' => $row->id,
                    'mid' => $row2->id,
                    'url' => $domainUrl . $row2->url,
                    'name' => $row2->name,
                    'cache' => $row->cache
                ];
                $row2->reload();
            }
            $row->reload();
        }

        // Страницы
        foreach ($siteMap as $pid => $temp) {
            foreach ($temp as $mid => $val) {
                if ($val['cache']) {
                    $Set[$pid][$mid] = $val;
                }
            }
        }

        // Блоки
        $sqlQuery = "SELECT *
                       FROM " . Block::_tablename()
                  . " WHERE cache_type";
        $blocksData = Block::_SQL()->get($sqlQuery);
        foreach ($blocksData as $block) {
            $block = Block::spawn($block);
            if ($block->cache_single_page) {
                // Блок везде разный. Нужны все страницы, на которых
                // присутствует блок
                foreach ($block->pages_ids as $pid) {
                    foreach ($siteMap[$pid] as $mid => $val) {
                        if (($block->vis_material == Block::BYMATERIAL_BOTH) ||
                            (
                                $mid &&
                                ($block->vis_material == Block::BYMATERIAL_WITH)
                            ) ||
                            (
                                !$mid &&
                                ($block->vis_material == Block::BYMATERIAL_WITHOUT)
                            )
                        ) {
                            $Set[$pid][$mid] = $val;
                        }
                    }
                }
            } else {
                // Блок везде одинаковый. Найдем хотя бы одну подходящую страницу
                foreach ($block->pages_ids as $pid) {
                    if (isset($Set[$pid])) {
                        if (($block->vis_material == Block::BYMATERIAL_BOTH) ||
                            (
                                ($block->vis_material == Block::BYMATERIAL_WITH) &&
                                (array_keys($Set[$pid]) != [0])
                            ) ||
                            (
                                ($block->vis_material == Block::BYMATERIAL_WITHOUT) &&
                                isset($Set[$pid][0])
                            )
                        ) {
                            continue;
                        }
                    }
                }
                $pid = $block->pages_ids[0];
                if (($block->vis_material == Block::BYMATERIAL_BOTH) ||
                    ($block->vis_material == Block::BYMATERIAL_WITHOUT)
                ) {
                    $Set[$pid][0] = $siteMap[$pid][0];
                } else {
                    foreach ($siteMap[$pid] as $mid => $val) {
                        if ($mid) {
                            $Set[$pid][$mid] = $val;
                            break;
                        }
                    }
                }
            }
        }

        $Set2 = [];
        foreach ($Set as $pid => $temp) {
            foreach ($temp as $mid => $val) {
                unset($val['cache']);
                $Set2[] = $val;
            }
        }
        $Set = $Set2;
        return $Set;
    }


    /**
     * Очищает кэши
     * @deprecated
     */
    public function cleanCache()
    {
        $this->clearCache();
    }


    /**
     * Копирует сущность, без коммита
     * @param SOME $item Копируемая сущность
     * @return SOME Скопированная сущность
     */
    public function copyItem(SOME $item)
    {
        $classname = get_class($item);
        $item2 = clone($item);
        // 2018-04-03, AVS: заменил везде '/\\d+$/umi' на / \\d+$/umi,
        // чтобы не травмировать числовые наименования
        $sqlQuery = "SELECT COUNT(*)
                       FROM " . $classname::_tablename()
                  . " WHERE name = ?";
        do {
            if (preg_match('/ \\d+$/umi', trim($item2->name), $regs)) {
                $i = (int)$regs[0] + 1;
                $item2->name = preg_replace(
                    '/ \\d+$/umi',
                    ' ' . $i,
                    trim($item2->name)
                );
            } else {
                $i = 2;
                $item2->name .= ' ' . $i;
            }
        } while ((int)$this->SQL->getvalue([$sqlQuery, $item2->name]));

        // 2018-04-03, AVS: заменил везде '/\\d+$/umi' на /_\\d+$/umi,
        // чтобы не травмировать числовые URN'ы
        if (preg_match('/_\\d+$/umi', trim($item2->urn), $regs)) {
            $item2->urn = preg_replace(
                '/_\\d+$/umi',
                '_' . $i,
                trim($item2->urn)
            );
        } else {
            $item2->urn .= '_' . $i;
        }
        $sqlQuery = "SELECT COUNT(*)
                       FROM " . $classname::_tablename()
                  . " WHERE urn = ?
                        AND id != ?";
        while ((int)Package::i()->SQL->getvalue([
            $sqlQuery,
            $item2->urn,
            (int)$item2->id
        ])) {
            $item2->urn = '_' . $item2->urn . '_';
        }
        return $item2;
    }


    /**
     * Устанавливает порядки отображения для сущностей
     * @param string $classname Класс сущности
     * @param array<
     *            (int|string)[] ID# сущности => int Порядок отображения
     *        > $priorities Порядки отображения
     */
    public function setEntitiesPriority($classname, array $priorities = [])
    {
        foreach ($priorities as $key => $val) {
            $this->SQL->update(
                $classname::_tablename(),
                "id = " . (int)$key,
                ['priority' => (int)$val]
            );
        }
    }


    /**
     * Получает материалы по поиску
     * @param string $search Поисковая строка
     * @param int $mtypeId ID# типа материалов
     * @param int $limit Лимит выборки
     * @return array<Material>
     */
    public function getMaterialsBySearch($search, $mtypeId = 0, $limit = 50)
    {
        // 2016-01-14, AVS: Сделал $limit 50 вместо 10
        $Material_Type = new Material_Type((int)$mtypeId);
        // 2016-01-14, AVS: сделал поиск по данным вместо названия.
        // Возможно, вызовет перегруз, но нужно тогда решать вопрос с базой
        // 2017-09-25, AVS: заменил JOIN на LEFT JOIN у cms_data и cms_fields,
        // т.к. у материалов может и не быть кастомных полей
        $likeSearchString = $this->SQL->escape_like($search);
        $sqlQuery = "SELECT tM.* FROM " . Material::_tablename() . " AS tM
                   LEFT JOIN " . Material_Field::_dbprefix() . Material_Field::data_table . " AS tD ON tD.pid = tM.id
                   LEFT JOIN " . Material_Field::_tablename() . " AS tF ON tF.classname = 'RAAS\\\\CMS\\\\Material_Type' AND tF.id = tD.fid
                       WHERE (
                                tM.name LIKE '%" . $likeSearchString . "%'
                             OR tM.urn LIKE '%" . $likeSearchString . "%'
                             OR tM.description LIKE '%" . $likeSearchString . "%'
                             OR tD.value LIKE '%" . $likeSearchString . "%'
                        ) ";
        // $sqlQuery = "SELECT tM.* FROM " . Material::_tablename() . " AS tM
        //                WHERE (
        //                         tM.name LIKE '%" . $this->SQL->escape_like($search) . "%'
        //                      OR tM.description LIKE '%" . $this->SQL->escape_like($search) . "%'
        //                 ) ";
        if ($Material_Type->id) {
            $ids = $Material_Type->selfAndChildrenIds;
            $sqlQuery .= " AND tM.pid IN (" . implode(", ", $ids) . ") ";
        }
        $sqlQuery .= " GROUP BY tM.id ORDER BY tM.name LIMIT " . (int)$limit;
        $Set = Material::getSQLSet($sqlQuery);
        return $Set;
    }


    public function install()
    {
        if (!$this->registryGet('installDate')) {
            if (!$this->registryGet('tnsize')) {
                $this->registrySet('tnsize', 300);
            }
            if (!$this->registryGet('maxsize')) {
                $this->registrySet('maxsize', 1920);
            }
            parent::install();
            Attachment::clearLostFiles($this->filesDir);
            CMSAccess::refreshPagesAccessCache();
            CMSAccess::refreshMaterialsAccessCache();
            CMSAccess::refreshBlocksAccessCache();
            Material_Type::updateAffectedPagesForSelf();
            Material_Type::updateAffectedPagesForMaterials();
        }
    }


    /**
     * Получает имя файла эскиза к изображению
     * @param string $filename Имя файла оригинального изображения
     * @param int|null $width Ширина эскиза
     * @param int|null $height Высота эскиза
     * @param 'inline'|'frame'|'crop'|null $mode Режим создания эскиза
     * @return string
     */
    public static function tn($filename, $width = null, $height = null, $mode = null)
    {
        $temp = pathinfo($filename);
        $outputFile = ltrim($temp['dirname'] ? $temp['dirname'] . '/' : '')
                    . $temp['filename'] . '.'
                    . ($width ?: 'auto') . 'x' . ($height ?: 'auto')
                    . ($mode ? '_' . $mode : '') . '.' . $temp['extension'];
        return $outputFile;
    }


    /**
     * Ищет сущности с таким же URN, как и текущая (для проверки на уникальность)
     * @param SOME $Object сущность для проверки
     * @return bool true, если уже есть сущность с таким URN, как и текущий,
     *              false в противном случае
     */
    public function checkForSimilar(SOME $Object)
    {
        $classname = get_class($Object);
        $sqlQuery = "SELECT COUNT(*)
                       FROM " . $classname::_tablename()
                  . " WHERE urn = ?
                        AND id != ?";
        $sqlResult = $classname::_SQL()->getvalue([
            $sqlQuery,
            $Object->urn,
            (int)$Object->id
        ]);
        $c = (bool)(int)$sqlResult;
        return $c;
    }


    /**
     * Меняет URN до тех пор, пока не находит уникальный
     * @param SOME $Object сущность для изменения URN
     * @return string Назначенный URN
     */
    public function getUniqueURN(SOME $Object)
    {
        $Object->urn = Text::beautify($Object->urn);
        for ($i = 0; $this->checkForSimilar($Object); $i++) {
            $Object->urn = Application::i()->getNewURN($Object->urn, !$i);
        }
        return $Object->urn;
    }


    /**
     * Возвращает функцию сравнения для полей
     * @param string $key поле для сравнения
     * @param bool $reverse В обратном порядке
     * @param bool $priorityFirst Сначала по порядку отображения
     * @return callable
     */
    public function getCompareFunction(
        $key,
        $reverse = false,
        $priorityFirst = false
    ) {
        return function ($a, $b) use ($key, $reverse, $priorityFirst) {
            if ($priorityFirst && ($a->priority != $b->priority)) {
                if ($a->priority && !$b->priority) {
                    return -1;
                } elseif ($b->priority && !$a->priority) {
                    return 1;
                }
                return (int)$a->priority - (int)$b->priority;
            }
            if (in_array($key, ['urn', 'name', 'post_date', 'modify_date'])) {
                $c = strcasecmp($a->$key, $b->$key);
            } elseif (in_array($key, ['priority'])) {
                $c = ((int)$a->$key - (int)$b->$key);
            } elseif (isset($a->fields[$key], $b->fields[$key])) {
                if (is_object($a->fields[$key]->doRich()) ||
                    is_object($b->fields[$key]->doRich())
                ) {
                    $c = (bool)$a->fields[$key]->doRich()
                       - (bool)$b->fields[$key]->doRich();
                } else {
                    $c = strcasecmp(
                        $a->fields[$key]->doRich(),
                        $b->fields[$key]->doRich()
                    );
                }
            } else {
                $c = strcasecmp($a->$key, $b->$key);
            }
            return ($reverse ? -1 : 1) * $c;
        };
    }


    public function autoload($class)
    {
        require_once $this->classesDir . '/controller_frontend.class.php';
        parent::autoload($class);
    }
}
