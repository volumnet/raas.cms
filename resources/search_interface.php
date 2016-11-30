<?php
namespace RAAS\CMS;

use RAAS\Application;
use SOME\Pages;
use SOME\SOME;

$pageNameRatio = 10;
$pageDataRatio = 1;
$materialNameRatio = 10;
$materialDescriptionRatio = 1;
$materialDataRatio = 1;
$pageMaterialsRatio = 1;
$searchLimit = 100;
$SQL = Application::i()->SQL;

$IN = (array)$_GET;
$OUT = array();
$search_string = trim(isset($IN[$config['search_var_name']]) ? $IN[$config['search_var_name']] : '');
if (!$search_string) {
    $OUT['localError'] = 'NO_SEARCH_QUERY';
} else {
    $searchArray = explode(' ', $search_string);
    $searchArray = array_map('trim', $searchArray);
    $searchArray = array_filter($searchArray);
    if (isset($config['min_length']) && (int)$config['min_length']) {
        $searchArray = array_filter(
            $searchArray,
            function ($x) use ($config) {
                return (mb_strlen($x) >= (int)$config['min_length']);
            }
        );
    }
    if (!$searchArray) {
        $OUT['localError'] = 'SEARCH_QUERY_TOO_SHORT';
    } else {
        $results = $materials = array();

        // Получим начальные условия для страниц и материалов
        $SQL_where_pages = " AND tP.vis AND NOT tP.response_code ";
        $SQL_where_materials = " AND tM.vis ";
        if ((array)$Block->search_pages_ids) {
            $SQL_where_pages .= " AND tP.id IN (" . implode(", ", array_map('intval', (array)$Block->search_pages_ids)) . ") ";
        }
        if ($languages = array_filter((array)$Block->languages)) {
            $temp = array_map(
                function ($x) use ($SQL) {
                    return "'" . $SQL->real_escape_string($x) . "'";
                },
                (array)$languages
            );
            $SQL_where_pages .= " AND tP.lang IN (" . implode(", ", $temp) . ") ";
        }
        if ((array)$Block->material_types_ids) {
            $SQL_where_materials .= " AND tM.pid IN (" . implode(", ", array_map('intval', (array)$Block->material_types_ids)) . ") ";
        }

        // Получим допустимые поля данных для страниц и материалов
        $SQL_query = "SELECT tF.id
                        FROM " . Material_Field::_tablename() . " AS tF
                       WHERE tF.pid = 0
                         AND tF.classname = 'RAAS\\\\CMS\\\\Material_Type'
                         AND tF.datatype IN ('text', 'tel', 'email', 'url', 'textarea', 'htmlarea') ";
        $pagesFields = $SQL->getcol($SQL_query);
        $SQL_query = "SELECT tF.id
                        FROM " . Material_Field::_tablename() . " AS tF
                       WHERE tF.pid
                         AND tF.classname = 'RAAS\\\\CMS\\\\Material_Type'
                         AND tF.datatype IN ('text', 'tel', 'email', 'url', 'textarea', 'htmlarea') ";
        $materialFields = $SQL->getcol($SQL_query);

        // 1. Ищем страницы по имени
        $SQL_query = "SELECT tP.id, (0";
        foreach ($searchArray as $val) {
            $SQL_query .= " + ((tP.name LIKE '%" . $SQL->escape_like($val) . "%') * " . $pageNameRatio . ") ";
        }
        $SQL_query .= " ) AS c
                        FROM " . Page::_tablename() . " AS tP
                       WHERE 1
                         " . $SQL_where_pages . "
                         AND (0 ";
        foreach ($searchArray as $val) {
            $SQL_query .= " OR tP.name LIKE '%" . $SQL->escape_like($val) . "%'";
        }
        $SQL_query .= " ) LIMIT " . $searchLimit;
        // echo $SQL_query; exit;
        $SQL_result = $SQL->get($SQL_query);
        foreach ($SQL_result as $row) {
            $results['p' . $row['id']] += $row['c'];
        }

        // 2. Ищем страницы по данным
        if ($pagesFields) {
            $SQL_query = "SELECT tP.id, (0";
            foreach ($searchArray as $val) {
                $SQL_query .= " + ((tD.value LIKE '%" . $SQL->escape_like($val) . "%') * " . $pageDataRatio . ")";
            }
            $SQL_query .= ") AS c
                            FROM " . Page::_tablename() . " AS tP
                            JOIN " . Material::_dbprefix() . "cms_data AS tD ON tD.pid = tP.id
                           WHERE 1
                             AND tD.fid IN (" . implode(", ", $pagesFields) . ")
                             " . $SQL_where_pages . "
                             AND (0 ";
            foreach ($searchArray as $val) {
                $SQL_query .= " OR tD.value LIKE '%" . $SQL->escape_like($val) . "%'";
            }
            $SQL_query .= " ) LIMIT " . $searchLimit;
            // echo $SQL_query; exit;
            $SQL_result = $SQL->get($SQL_query);
            foreach ($SQL_result as $row) {
                $results['p' . $row['id']] += $row['c'];
            }
        }

        // 3. Ищем все материалы по имени и описанию
        $SQL_query = "SELECT tM.id, tM.pid, (0";
        foreach ($searchArray as $val) {
            $SQL_query .= " + ((tM.name LIKE '%" . $SQL->escape_like($val) . "%') * " . $materialNameRatio . ")
                            + ((IF(tM.description IS NULL, '', tM.description) LIKE '%" . $SQL->escape_like($val) . "%') * " . $materialDescriptionRatio . ")";
        }
        $SQL_query .= " ) AS c
                        FROM " . Material::_tablename() . " AS tM
                       WHERE 1
                         " . $SQL_where_materials . "
                         AND (0 ";
        foreach ($searchArray as $val) {
            $SQL_query .= " OR tM.name LIKE '%" . $SQL->escape_like($val) . "%' OR IF(tM.description IS NULL, '', tM.description) LIKE '%" . $SQL->escape_like($val) . "%' ";
        }
        $SQL_query .= " ) LIMIT " . $searchLimit;
        // echo $SQL_query; exit;
        $SQL_result = $SQL->get($SQL_query);
        foreach ($SQL_result as $row) {
            $materials[$row['pid']][$row['id']] = $row['c'];
        }

        // 4. Ищем все материалы по данным
        $SQL_query = "SELECT tM.id, tM.pid, (0";
        foreach ($searchArray as $val) {
            $SQL_query .= " + ((tD.value LIKE '%" . $SQL->escape_like($val) . "%') * " . $materialDataRatio . ")";
        }
        $SQL_query .= ") AS c
                       FROM " . Material::_tablename() . " AS tM
                       JOIN " . Material::_dbprefix() . "cms_data AS tD ON tD.pid = tM.id
                      WHERE 1
                        AND tD.fid IN (" . implode(", ", $materialFields) . ")
                        " . $SQL_where_materials . "
                        AND (0 ";
        foreach ($searchArray as $val) {
            $SQL_query .= " OR tD.value LIKE '%" . $SQL->escape_like($val) . "%'";
        }
        $SQL_query .= " ) LIMIT " . $searchLimit;
        // echo $SQL_query; exit;
        $SQL_result = $SQL->get($SQL_query);
        foreach ($SQL_result as $row) {
             $materials[$row['pid']][$row['id']] += $row['c'];
        }

        // 5. Выбираем блоки по типам данных
        foreach ($materials as $mtype => $arr) {
            $MType = new Material_Type((int)$mtype);
            $SQL_query = "SELECT tP.id AS pid, IF(tB.nat, tM.id, 0) AS mid
                            FROM " . Material::_tablename() . " AS tM
                            JOIN " . Page::_tablename() . " AS tP
                            JOIN " . Block::_dbprefix() . "cms_blocks_pages_assoc AS tBPA ON tBPA.page_id = tP.id
                            JOIN " . Block::_dbprefix() . "cms_blocks_material AS tBM ON tBM.material_type IN (" . implode(", ", array_merge(array((int)$MType->id), (array)$MType->parents_ids)) . ") AND tBM.id = tBPA.block_id
                            JOIN " . Block::_tablename() . " AS tB ON tB.id = tBPA.block_id AND tB.vis ";
            if (!$MType->global_type) {
                $SQL_query .= " JOIN " . Material::_dbprefix() . "cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id AND tP.id = tMPA.pid ";
            }
            $SQL_query .= " WHERE 1
                              " . $SQL_where_pages . "
                              AND tM.id IN (" . implode(", ", array_keys($arr)) . ") ";
            $SQL_query .= " GROUP BY pid, mid
                            LIMIT " . $searchLimit;
            // echo $SQL_query; exit;
            $SQL_result = $SQL->get($SQL_query);
            $p = array_unique(
                array_map(
                    function ($x) {
                        return $x['pid'];
                    },
                    $SQL_result
                )
            );
            $m = array_unique(
                array_map(
                    function ($x) {
                        return $x['mid'];
                    },
                    $SQL_result
                )
            );
            foreach ($p as $val) {
                $results['p' . $val] += $pageMaterialsRatio;
            }
            foreach ($m as $val) {
                if ($val) {
                    $results['m' . $val] = $materials[$mtype][$val];
                }
            }
        }

        // 6. Выбираем блоки по HTML-коду
        $SQL_query = "SELECT tP.id, (0";
        foreach ($searchArray as $val) {
            $SQL_query .= " + ((IF(tBH.description IS NULL, '', tBH.description) LIKE '%" . $SQL->escape_like($val) . "%') * " . $pageDataRatio . ")";
        }
        $SQL_query .= ") AS c
                        FROM " . Page::_tablename() . " AS tP
                        JOIN " . Block::_dbprefix() . "cms_blocks_pages_assoc AS tBPA ON tBPA.page_id = tP.id
                        JOIN " . Block::_tablename() . " AS tB ON tB.id = tBPA.block_id AND tB.vis
                        JOIN " . Block::_dbprefix() . "cms_blocks_html AS tBH ON tBH.id = tB.id
                       WHERE 1
                         " . $SQL_where_pages . "
                         AND (0 ";
        foreach ($searchArray as $val) {
            $SQL_query .= " OR IF(tBH.description IS NULL, '', tBH.description) LIKE '%" . $SQL->escape_like($val) . "%'";
        }
        $SQL_query .= " ) GROUP BY tP.id
                          LIMIT " . $searchLimit;
        // echo $SQL_query; exit;
        $SQL_result = $SQL->get($SQL_query);
        foreach ($SQL_result as $row) {
            $results['p' . $row['id']] += $row['c'];
        }


        arsort($results);
        $Pages = null;
        if (isset($config['pages_var_name'], $config['rows_per_page']) && (int)$config['rows_per_page']) {
            $Pages = new Pages(isset($IN[$config['pages_var_name']]) ? (int)$IN[$config['pages_var_name']] : 1, (int)$config['rows_per_page']);
        }
        $Set = array_keys($results);
        $Set = array_slice($Set, 0, $searchLimit);
        $Set = array_filter(
            $Set,
            function ($x) {
                if ($x[0] == 'm') {
                    $row = new Material(substr($x, 1));
                    return $row->currentUserHasAccess() && $row->parent->currentUserHasAccess();
                } else {
                    $row = new Page(substr($x, 1));
                    return $row->currentUserHasAccess();
                }
            }
        );
        $Set = SOME::getArraySet(
            $Set,
            $Pages,
            function ($x) {
                if ($x[0] == 'm') {
                    $row = new Material(substr($x, 1));
                } else {
                    $row = new Page(substr($x, 1));
                }
                return $row;
            }
        );
        if (!$Set) {
            $OUT['localError'] = 'NO_RESULTS_FOUND';
        }
        $OUT['Pages'] = $Pages;
        $OUT['Set'] = $Set;
    }
}
$OUT['search_string'] = $search_string;
return $OUT;
