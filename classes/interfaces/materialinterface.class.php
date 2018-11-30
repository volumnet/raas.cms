<?php
/**
 * Файл стандартного интерфейса материалов
 */
namespace RAAS\CMS;

use SOME\Pages;

/**
 * Класс стандартного интерфейса материалов
 */
class MaterialInterface extends AbstractInterface
{
    /**
     * Конструктор класса
     * @param Block_Material|null $block Блок, для которого применяется интерфейс
     * @param Page|null $page Страница, для которой применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @param array $post Поля $_POST параметров
     * @param array $cookie Поля $_COOKIE параметров
     * @param array $session Поля $_SESSION параметров
     * @param array $server Поля $_SERVER параметров
     * @param array $files Поля $_FILES параметров
     */
    public function __construct(
        Block_Material $block = null,
        Page $page = null,
        array $get = [],
        array $post = [],
        array $cookie = [],
        array $session = [],
        array $server = [],
        array $files = []
    ) {
        parent::__construct($block, $page, $get, $post, $cookie, $session, $server, $files);
    }


    public function process()
    {
        $get = $this->getAllParams($this->block, $this->get);
        $legacy = $this->checkLegacyAddress($this->block, $this->page, $get, $this->server);
        if ($legacy) {
            return;
        }
        if ($this->page->Material && $this->block->nat) {
            $result = $this->processMaterial(
                $this->block,
                $this->page,
                $this->page->Material,
                $get,
                $this->server
            );
        } else {
            $result = $this->processList($this->block, $this->page, $get);
        }
        return $result;
    }


    /**
     * Обрабатывает один материал
     * @param Block_Material $block Блок, для которого применяется интерфейс
     * @param Page $page Страница, для которой применяется интерфейс
     * @param Material $item Материал для обработки
     * @param array $get Поля $_GET параметров
     * @param array $server Поля $_SERVER параметров
     * @return [
     *             'Item' => Material Обрабатываемый материал,
     *             'prev' ?=> Material Предыдущий материал,
     *             'next' ?=> Material Следующий материал
     *         ]
     */
    public function processMaterial(Block_Material $block, Page $page, Material $item, array $get = [], array $server = [])
    {
        $legacy = $this->checkLegacyArbitraryMaterialAddress($block, $page, $item, $server);
        if ($legacy) {
            return;
        }
        $this->setPageMetatags($page, $item);
        $item->proceed = true;
        $result = ['Item' => $item];

        $prevNext = $this->getPrevNext($block, $page, $item, $get);
        foreach (['prev', 'next'] as $key) {
            if (isset($prevNext[$key])) {
                $result[$key] = $prevNext[$key];
            }
        }
        return $result;
    }


    /**
     * Ищет предыдущий и следующий материалы по списку для заданного
     * @param Block_Material $block Блок, для которого применяется интерфейс
     * @param Page $page Страница, для которой применяется интерфейс
     * @param Material $item Материал для обработки
     * @param array $get Поля $_GET параметров
     * @return [
     *             'prev' ?=> Material Предыдущий материал,
     *             'next' ?=> Material Следующий материал
     *         ]
     */
    public function getPrevNext(Block_Material $block, Page $page, Material $item, array $get = [])
    {
        $result = [];
        $idsList = $this->getIdsList($block, $page, $get);
        $index = array_search($item->id, $idsList);
        if ($index !== false) {
            if ($index > 0) {
                $result['prev'] = new Material($idsList[$index - 1]);
            }
            if ($index < count($idsList) - 1) {
                $result['next'] = new Material($idsList[$index + 1]);
            }
        }
        return $result;
    }


    /**
     * Устанавливает теги страницы
     * @param Page|null $page Страница, для которой применяется интерфейс
     * @param Material $item Текущий материал
     */
    public function setPageMetatags(Page $page, Material $item)
    {
        foreach (['name', 'meta_title', 'meta_keywords', 'meta_description', 'h1'] as $key) {
            if (!isset($page->{'old' . ucfirst($key)})) {
                $page->{'old' . ucfirst($key)} = $page->$key;
                $page->$key = trim($item->$key);
            }
        }
    }


    /**
     * Обрабатывает список материалов
     * @param Block_Material|null $block Блок, для которого применяется интерфейс
     * @param Page|null $page Страница, для которой применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @return [
     *             'Set' => array<Material> Список материалов,
     *             'MType' => Material_Type Тип материалов,
     *             'Pages' => Pages Постраничная разбивка,
     *             'sort' => Значение сортировки для вывода в виджет
     *                       (наименование нативного поля или URN кастомного)
     *             'order' => Значение порядка для вывода в виджет
     *         ] Данные по списку материалов
     */
    public function processList(Block_Material $block, Page $page, array $get = [])
    {
        $pages = null;
        $sort = $order = '';
        if (isset($block->pages_var_name, $block->rows_per_page) && (int)$block->rows_per_page) {
            $pages = new Pages(
                isset($get[$block->pages_var_name]) ? (int)$get[$block->pages_var_name] : 1,
                (int)$block->rows_per_page
            );
        }

        $set = $this->getList($block, $page, $get, $pages);
        $this->getOrderVar($block, $get, $sort, $order);
        $result = [
            'MType' => $block->Material_Type,
            'Set' => $set,
            'sort' => $sort,
            'order' => $order,
        ];
        if ($pages !== null) {
            $result['Pages'] = $pages;
        }
        return $result;
    }


    /**
     * Получает список материалов
     * @param Block_Material|null $block Блок, для которого применяется интерфейс
     * @param Page|null $page Страница, для которой применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @param Pages|null $pages Постраничная разбивка
     * @return array<Material>
     */
    public function getList(Block_Material $block, Page $page, array $get = [], Pages $pages = null)
    {
        $sqlParts = $this->getSQLParts($block, $page, $get);
        $sqlQuery = $this->getSQLQuery($sqlParts['from'], $sqlParts['where'], $sqlParts['sort'], $sqlParts['order']);
        $set = Material::getSQLSet([$sqlQuery, $sqlParts['bind']], $pages);
        $set = array_filter($set, function ($x) {
            return $x->currentUserHasAccess();
        });
        return $set;
    }


    /**
     * Получает список ID# всех материалов
     * @param Block_Material|null $block Блок, для которого применяется интерфейс
     * @param Page|null $page Страница, для которой применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @return array<int>
     */
    public function getIdsList(Block_Material $block, Page $page, array $get = [])
    {
        $sqlParts = $this->getSQLParts($block, $page, $get);
        $sqlQuery = $this->getSQLQuery($sqlParts['from'], $sqlParts['where'], $sqlParts['sort'], $sqlParts['order'], true);
        $set = Material::_SQL()->getcol([$sqlQuery, $sqlParts['bind']]);
        $set = array_map('intval', $set);
        return $set;
    }


    /**
     * Получает части SQL-выражения
     * @param Block_Material|null $block Блок, для которого применяется интерфейс
     * @param Page|null $page Страница, для которой применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @return [
     *             'from' => array<
     *                 string[] псевдоним поля => string SQL-инструкция по выборке таблицы
     *             > Список подключаемых таблиц,
     *             'where' => array<string SQL-инструкция> Ограничения для SQL WHERE,
     *             'sort' => string Сортировка для SQL ORDER BY
     *             'order' => ""|"ASC"|"DESC" Порядок сортировки для SQL ORDER BY,
     *             'bind' => array<mixed Значение связки> Связки для SQL-выражения
     *         ]
     */
    public function getSQLParts(Block_Material $block, Page $page, array $get = [])
    {
        $sqlFrom = $sqlFromBind = $sqlWhere = $sqlWhereBind = $result = [];
        $sqlSort = $sqlOrder = "";
        $this->getListAccessSQL($sqlFrom, $sqlFromBind, $sqlWhere);
        $this->getMaterialsSQL($block, $page, $sqlFrom, $sqlWhere, $sqlWhereBind);
        $this->getFilteringSQL($sqlFrom, $sqlFromBind, $sqlWhere, $sqlWhereBind, (array)$block->filter, $get);
        $this->getOrderSQL($block, $get, $sqlFrom, $sqlFromBind, $sqlSort, $sqlOrder);
        $result = [
            'from' => $sqlFrom,
            'where' => $sqlWhere,
            'sort' => $sqlSort,
            'order' => $sqlOrder,
            'bind' => array_merge($sqlFromBind, $sqlWhereBind),
        ];
        return $result;
    }



    /**
     * Получает SQL-инструкции по правам доступа
     * @param array<
     *            string[] псевдоним поля => string SQL-инструкция по выборке таблицы
     *        > $sqlFrom Список подключаемых таблиц
     * @param array<mixed Значение связки> $sqlFromBind Связки для SQL FROM
     * @param array<string SQL-инструкция> $sqlWhere Ограничения для SQL WHERE
     */
    public function getListAccessSQL(array &$sqlFrom, array &$sqlFromBind, array &$sqlWhere)
    {
        $sqlFrom['tA'] = " LEFT JOIN " . Material::_dbprefix() . "cms_access_materials_cache
                                  AS tA
                                  ON tA.material_id = tM.id
                                 AND tA.uid = ?";
        $sqlFromBind[] = (int)Controller_Frontend::i()->user->id;
        $sqlWhere[] = " (tA.allow OR (tA.allow IS NULL)) ";
    }


    /**
     * Получает SQL-инструкции по материалам
     * @param Block_Material|null $block Блок, для которого применяется интерфейс
     * @param Page|null $page Страница, для которой применяется интерфейс
     * @param array<
     *            string[] псевдоним поля => string SQL-инструкция по выборке таблицы
     *        > $sqlFrom Список подключаемых таблиц
     * @param array<string SQL-инструкция> $sqlWhere Ограничения для SQL WHERE
     * @param array<mixed Значение связки> $sqlWhereBind Связки для SQL WHERE
     */
    public function getMaterialsSQL(
        Block_Material $block,
        Page $page,
        array &$sqlFrom,
        array &$sqlWhere,
        array &$sqlWhereBind
    ) {
        if (!$block->Material_Type->global_type) {
            $sqlFrom['tMPA'] = " JOIN " . Material::_dbprefix() . "cms_materials_pages_assoc
                                   AS tMPA
                                   ON tMPA.id = tM.id ";
        }
        $sqlWhere[] = " tM.vis";
        $sqlWhere[] = " (NOT tM.show_from OR tM.show_from <= NOW())";
        $sqlWhere[] = " (NOT tM.show_to OR tM.show_to >= NOW())";
        $sqlMaterialTypeSelfAndChildrenIds = implode(
            ", ",
            array_fill(0, count($block->Material_Type->selfAndChildrenIds), "?")
        );
        $sqlWhere[] = " tM.pid IN (" . $sqlMaterialTypeSelfAndChildrenIds . ") ";
        $sqlWhereBind = array_merge($sqlWhereBind, $block->Material_Type->selfAndChildrenIds);
        if (!$block->Material_Type->global_type) {
            $sqlWhere[] = " tMPA.pid = ?";
            $sqlWhereBind[] = (int)$page->id;
        }
    }


    /**
     * Получает SQL-инструкции по фильтрации
     * @param Block_Material|null $block Блок, для которого применяется интерфейс
     * @param array<
     *            string[] псевдоним поля => string SQL-инструкция по выборке таблицы
     *        > $sqlFrom Список подключаемых таблиц
     * @param array<mixed Значение связки> $sqlFromBind Связки для SQL FROM
     * @param array<string SQL-инструкция> $sqlWhere Ограничения для SQL WHERE
     * @param array<mixed Значение связки> $sqlWhereBind Связки для SQL WHERE
     * @param array<[
     *            'var' => string Переменная для фильтрации
     *            'relation' => '='|'<='|'>='|'LIKE'|'CONTAINED'|'FULLTEXT' Отношение для фильтрации
     *            'field' => string|int URN нативного поля или ID# кастомного
     *        ]> $filter Данные по фильтрации
     * @param array $get Поля $_GET параметров
     */
    public function getFilteringSQL(
        array &$sqlFrom,
        array &$sqlFromBind,
        array &$sqlWhere,
        array &$sqlWhereBind,
        array $filter = [],
        array $get = []
    ) {
        $sqlArray = [];
        foreach ((array)$filter as $filterItem) {
            if (isset(
                $filterItem['var'],
                $filterItem['relation'],
                $filterItem['field'],
                $get[$filterItem['var']]
            )) {
                $var = $filterItem['var'];
                $val = $get[$var];
                $relation = $filterItem['relation'];
                $field = $filterItem['field'];
                $sqlField = $this->getField($field, 't' . $field, $sqlFrom, $sqlFromBind);
                $filteringItemSQL = $this->getFilteringItemSQL($sqlField, $relation, $val);
                if ($filteringItemSQL) {
                    $sqlArray[$var][] = $filteringItemSQL[0];
                    $sqlWhereBind[] = $filteringItemSQL[1];
                }
            }
        }
        foreach ($sqlArray as $key => $arr) {
            $sqlWhere[$key] = $arr ? "(" . implode(" AND ", $arr) . ")" : "";
        }
    }


    /**
     * Получает SQL-инструкции по фильтрации для одной записи фильтрации
     * @param string $sqlField SQL-инструкция поля для SELECT
     * @param '='|'<='|'>='|'LIKE'|'CONTAINED'|'FULLTEXT' $relation Отношение для фильтрации
     * @param mixed $val Значение поля
     * @return [string SQL-инструкция, mixed Связка для запроса]|null null, если отношение неверное
     */
    public function getFilteringItemSQL($sqlField, $relation, $val)
    {
        $result = [];
        switch ($relation) {
            case '=':
            case '<=':
            case '>=':
                $result = ["(" . $sqlField . " " . $relation . " ?)", $val];
                break;
            case 'LIKE':
                $result = ["(" . $sqlField . " LIKE ?)", "%" . $val . "%"];
                break;
            case 'CONTAINED':
                $result = ["(? LIKE CONCAT('%', " . $sqlField . ", '%'))", $val];
                break;
            case 'FULLTEXT':
                $result = [
                    "(MATCH (" . $sqlField . ") AGAINST(? IN NATURAL LANGUAGE MODE))",
                    $val
                ];
                break;
        }
        return $result;
    }


    /**
     * Находит параметр сортировки, соответствующий значению переменной сортировки
     * @param string $sortVal Значение переменной сортировки
     * @param array<[
     *            'var' => string Значение переменной сортировки
     *            'relation' => 'asc'|'desc'|'asc!'|'desc!' отношение сортировки
     *            'field' => string|int URN нативного поля или ID# кастомного
     *        ]> $sortParams Настройки сортировки блока
     * @param string $var По какому полю настройки сравниваем
     * @return [
     *             'var' => string Значение переменной сортировки
     *             'relation' => 'asc'|'desc'|'asc!'|'desc!' отношение сортировки
     *             'field' => string|int URN нативного поля или ID# кастомного
     *         ]|null Найденная настройка сортировка блока, либо null, если не найдено
     */
    public function getMatchingSortParam($sortVal, array $sortParams = [], $var = 'var')
    {
        foreach ($sortParams as $sortParam) {
            if (isset($sortParam['var'], $sortParam['field']) &&
                ($sortVal == $sortParam[$var])
            ) {
                return $sortParam;
            }
        }
        return null;
    }


    /**
     * Получает значения сортировки и порядка для вывода в виджет
     * @param Block_Material|null $block Блок, для которого применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @param string $sort Значение сортировки для вывода в виджет
     *                     (наименование нативного поля или URN кастомного)
     * @param 'asc'|'desc'|'' $order Значение порядка для вывода в виджет
     */
    public function getOrderVar(Block_Material $block, $get, &$sort, &$order)
    {
        $sortVar = (string)$block->sort_var_name;
        $sortVal = isset($get[$sortVar]) ? $get[$sortVar] : '';
        $orderVar = (string)$block->order_var_name;
        $sortParams = (array)$block->sort;
        $sortDefField = (string)$block->sort_field_default;
        $orderRelDefault = (string)$block->sort_order_default;
        if ($sortVar && $sortVal && $sortParams) {
            // Выберем подходящую запись
            // (у которой значение var совпадает со значением переменной сортировки $_GET)
            $sortItem = $this->getMatchingSortParam($sortVal, $sortParams, 'var');
            if ($sortItem) {
                $orderRelation = isset($sortItem['relation'])
                               ? (string)$sortItem['relation']
                               : '';
                $sort = $sortItem['var'];
                $order = $this->getOrder($orderVar, $orderRelation, $get);
                return;
            }
        }
        // Ни с чем не совпадает, но есть сортировка по умолчанию
        if ($sortDefField) {
            if ($sortParams) {
                // Есть параметры сортировки, найдем совпадение по полю
                $sortItem = $this->getMatchingSortParam(
                    $sortDefField,
                    $sortParams,
                    'field'
                );
                if ($sortItem) {
                    $orderRelation = isset($sortItem['relation'])
                                   ? (string)$sortItem['relation']
                                   : '';
                    $sort = $sortItem['var'];
                    $order = $this->getOrder($orderVar, $orderRelation, $get);
                    return;
                }
            }

            if (is_numeric($sortDefField)) {
                $fieldObject = new Material_Field((int)$sortDefField);
                $sort = $fieldObject->urn;
            } else {
                $sort = $sortDefField;
            }
            $order = $this->getOrder($orderVar, $orderRelDefault, $get);
        }
    }


    /**
     * Получает SQL-инструкции по сортировке
     * @param Block_Material|null $block Блок, для которого применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @param array<
     *            string[] псевдоним поля => string SQL-инструкция по выборке таблицы
     *        > $sqlFrom Список подключаемых таблиц
     * @param array<mixed Значение связки> $sqlFromBind Связки для SQL FROM
     * @param string $sqlSort Сортировка для SQL ORDER BY
     * @param ""|"ASC"|"DESC" $sqlOrder Порядок сортировки для SQL ORDER BY
     */
    public function getOrderSQL(
        Block_Material $block,
        array $get,
        array &$sqlFrom,
        array &$sqlFromBind,
        &$sqlSort,
        &$sqlOrder
    ) {
        $sortVar = (string)$block->sort_var_name;
        $sortVal = isset($get[$sortVar]) ? $get[$sortVar] : '';
        $orderVar = (string)$block->order_var_name;
        $sortParams = (array)$block->sort;
        $sortDefField = (string)$block->sort_field_default;
        $orderRelDefault = (string)$block->sort_order_default;
        if ($sortVar && $sortVal && $sortParams) {
            // Выберем подходящую запись
            // (у которой значение var совпадает со значением переменной сортировки $_GET)
            $sortItem = $this->getMatchingSortParam($sortVal, $sortParams, 'var');
            if ($sortItem) {
                $sqlSort = $this->getField(
                    $sortItem['field'],
                    'tOr',
                    $sqlFrom,
                    $sqlFromBind
                );
                $orderRelation = isset($sortItem['relation'])
                               ? (string)$sortItem['relation']
                               : '';
                $sqlOrder = mb_strtoupper(
                    $this->getOrder($orderVar, $orderRelation, $get)
                );
                return;
            }
        }
        // Ни с чем не совпадает, но есть сортировка по умолчанию
        if ($sortDefField) {
            $sqlSort = $this->getField(
                $sortDefField,
                'tOr',
                $sqlFrom,
                $sqlFromBind
            );
            $sqlOrder = mb_strtoupper(
                $this->getOrder($orderVar, $orderRelDefault, $get)
            );
        }
    }


    /**
     * Получает запрос на получение списка материалов
     * @param array<
     *            string[] псевдоним поля => string SQL-инструкция по выборке таблицы
     *        > $sqlFrom Список подключаемых таблиц
     * @param array<string SQL-инструкция> $sqlWhere Ограничения для SQL WHERE
     * @param string $sqlSort SQL-инструкция для сортировки
     * @param string $sqlOrder SQL-инструкция для упорядочения
     * @param bool $idsOnly Получать только ID# всех материалов
     * @return string SQL-запрос
     */
    public function getSQLQuery(array $sqlFrom, array $sqlWhere, $sqlSort = '', $sqlOrder = '', $idsOnly = false)
    {
        if ($idsOnly) {
            $sqlQuery = "SELECT tM.id";
        } else {
            $sqlQuery = "SELECT SQL_CALC_FOUND_ROWS tM.* ";
        }
        $sqlQuery .= " FROM " . Material::_tablename() . " AS tM " . implode(" ", $sqlFrom)
                  . ($sqlWhere ? " WHERE " . implode(" AND ", $sqlWhere) : "")
                  . " GROUP BY tM.id
                      ORDER BY NOT tM.priority,
                               tM.priority ASC"
                  . ($sqlSort ? ", " . $sqlSort . ($sqlOrder ? " " . $sqlOrder : "") : "");
        return $sqlQuery;
    }


    /**
     * Получает полный список параметров (включая GET и дополнительные из блока)
     * @param Block_Material|null $block Блок, для которого применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @return array<string[] Ключ параметра => mixed Значение>
     */
    public function getAllParams(Block_Material $block, array $get)
    {
        $result = (array)$get;
        if (isset($result['id']) && !$block->nat) {
            unset($result['id']);
        }
        parse_str(trim($block->params), $temp);
        $result = array_merge($result, (array)$temp);
        return $result;
    }


    /**
     * Получить SQL-представление поля для запроса
     * @param string|int $field URN нативного поля или ID# кастомного
     * @param string $as Псевдоним таблицы
     * @param array<
     *            string[] псевдоним поля => string SQL-инструкция по выборке таблицы
     *        > $sqlFrom Список подключаемых таблиц
     * @param array<mixed Значение связки> $sqlBind Связки
     * @return string SQL-инструкция поля для SELECT
     */
    public function getField($field, $as, array &$sqlFrom, array &$sqlBind)
    {
        $fieldSQL = '';
        if (is_numeric($field)) {
            if (!isset($sqlFrom[$as]) || !$sqlFrom[$as]) {
                // 2015-03-31, AVS: заменил JOIN на LEFT JOIN, т.к. если добавить
                // новое поле и сделать сортировку по нему, материалы пропадают
                $sqlFrom[$as] = " LEFT JOIN " . Field::data_table . " AS `" . $as . "`
                                         ON `" . $as . "`.pid = tM.id
                                        AND `" . $as . "`.fid = ?";
                $sqlBind[] = (int)$field;
            }
            $fieldObject = new Material_Field((int)$field);
            if ($fieldObject->datatype == 'number') {
                $fieldSQL = "CAST(" . $as . ".value AS SIGNED)";
            } else {
                $fieldSQL = $as . ".value";
            }
        } else {
            $fieldSQL = "tM." . $field;
        }
        return $fieldSQL;
    }


    /**
     * Получает порядок для SQL-сортировки
     * @param string $var Переменная из $_GET, содержащая значение 'asc' или 'desc' для сортировки
     * @param 'asc'|'desc'|'asc!'|'desc!' $relation Отношение сортировки из настроек блока
     * @param array $get Поля $_GET параметров
     * @return ""|"ASC"|"DESC"
     */
    public function getOrder($var, $relation, array $get = [])
    {
        $rel = trim($relation);
        $val = isset($get[$var]) ? mb_strtolower($get[$var]) : '';
        if (in_array($rel, ['asc!', 'desc!'])) {
            return trim($rel, '!');
        } elseif (in_array($rel, ['asc', 'desc'])) {
            $contrary = (($rel == 'desc') ? 'asc' : 'desc');
            return ($val == $contrary) ? $val : $rel;
        }
        return '';
    }


    /**
     * Проверяет старые адреса материалов, при необходимости делает редирект
     * @param Block_Material|null $block Блок, для которого применяется интерфейс
     * @param Page|null $page Страница, для которой применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @param array $server Поля $_SERVER параметров
     * @param bool $debug Режим отладки (возвращает заголовки вместо их установки, не завершает выполнение)
     * @return array<string>|bool Возвращает массив заголовков в режиме отладки (в боевом режиме делается редирект),
     *                            true если нельзя обработать материал (будет выдана ошибка),
     *                            false, если старая адресация не задействована
     */
    public function checkLegacyAddress(
        Block_Material $block,
        Page $page,
        array $get = [],
        array $server = [],
        $debug = false
    ) {
        if (!$page->Material && isset($get['id'])) {
            // Старый способ - по id
            $item = new Material($get['id']);
            if (((int)$item->id == (int)$get['id']) &&
                ($item->pid == $block->material_type) &&
                (int)$block->legacy
            ) {
                // Если материал действительно к месту, перенаправляем на новый адрес
                $headers = [
                    'HTTP/1.1 301 Moved Permanently',
                    'Location: http' . ($server['HTTPS'] == 'on' ? 's' : '') . '://' .
                    $server['HTTP_HOST'] . $item->url,
                ];
                if ($debug) {
                    return $headers;
                } else {
                    foreach ($headers as $header) {
                        header($header);
                    }
                    exit;
                }
            } else {
                // Такого материала нет, возвращаем (не обрабатываем). Далее контроллер перекинет на 404
                return true;
            }
        }
        return false;
    }


    /**
     * Проверяет материал по произвольному адресу с URN материала
     * @param Block_Material $block Блок, для которого проверяется адрес
     * @param Page $page Страница, для которой проверяется адрес
     * @param Material $item Материал, для которого проверяется адрес
     * @param array $server Поля $_SERVER параметров
     * @param bool $debug Режим отладки (возвращает заголовки вместо их установки, не завершает выполнение)
     * @return array<string>|bool Возвращает массив заголовков в режиме отладки (в боевом режиме делается редирект),
     *                            true если нельзя обработать материал (будет выдана ошибка),
     *                            false, если старая адресация не задействована
     */
    public function checkLegacyArbitraryMaterialAddress(
        Block_Material $block,
        Page $page,
        Material $item,
        array $server = [],
        $debug = false
    ) {
        if ($page->initialURL != $item->url) {
            // Адреса не совпадают
            if ((int)$block->legacy && ($item->pid == $block->material_type)) {
                // Установлена переадресация
                $headers = [
                    'HTTP/1.1 301 Moved Permanently',
                    'Location: http' . ($server['HTTPS'] == 'on' ? 's' : '') . '://' .
                    $server['HTTP_HOST'] . $item->url,
                ];
                if ($debug) {
                    return $headers;
                } else {
                    foreach ($headers as $header) {
                        header($header);
                    }
                    exit;
                }
            } else {
                return true;
            }
        }
        return false;
    }
}
