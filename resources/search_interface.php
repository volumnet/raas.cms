<?php
namespace RAAS\CMS;

$IN = (array)$_GET;
$OUT = array();
$search_string = trim(isset($IN[$config['search_var_name']]) ? $IN[$config['search_var_name']] : '');
if (!$search_string) {
    $OUT['localError'] = 'NO_SEARCH_QUERY';
} elseif (isset($config['min_length']) && (int)$config['min_length'] && (mb_strlen($search_string) < (int)$config['min_length'])) {
    $OUT['localError'] = 'SEARCH_QUERY_TOO_SHORT';
} else {
    $SQL_array = array();
    $SQL_search_string = "'%" . \RAAS\Application::i()->SQL->escape_like($search_string) . "%'";
    $SQL_query = "SELECT SQL_CALC_FOUND_ROWS page_id AS id,
                                             page_name AS name,
                                             IF(nat, material_id, 0) AS mat_id,
                                             IF(nat, material_name, '') AS mat_name,
                                             SUM(IF(rPname IS NULL, 0, rPname)) AS srPname,
                                             SUM(IF(tPdata IS NULL, 0, tPdata)) AS srPdata,
                                             SUM(IF(rMname IS NULL, 0, rMname)) AS srMname,
                                             SUM(IF(rMdescription IS NULL, 0, rMdescription)) AS srMdescription,
                                             SUM(IF(rMdata IS NULL, 0, rMdata)) AS srMdata
                    FROM (
                          -- Поиск по материалам
                          SELECT tP.id AS page_id, 
                                 tM.id AS material_id, 
                                 tP.name AS page_name, 
                                 tM.name AS material_name,
                                 tB.nat, 
                                 0 AS rPname, 
                                 0 AS tPdata,
                                 MAX(tM.name LIKE " . $SQL_search_string . ") AS rMname, 
                                 MAX(tM.description LIKE " . $SQL_search_string . ") AS rMdescription, 
                                 MAX(tD.value LIKE " . $SQL_search_string . ") AS rMdata
                            FROM " . Material::_tablename() . " AS tM 
                            JOIN " . Material_Type::_tablename() . " AS tMT ON tMT.id = tM.pid
                            JOIN " . Page::_tablename() . " AS tP
                            JOIN " . Block::_dbprefix() . "cms_blocks_pages_assoc AS tBPA ON tBPA.page_id = tP.id
                            JOIN " . Block::_dbprefix() . "cms_blocks_material AS tBM ON tBM.material_type = tMT.id
                            JOIN " . Block::_tablename() . " AS tB ON tB.id = tBPA.block_id AND tB.vis AND tB.id = tBM.id
                       LEFT JOIN " . Material::_dbprefix() . "cms_materials_pages_assoc AS tMPA ON IF(tMT.global_type, 1, tMPA.id = tM.id AND tP.id = tMPA.pid)
                       LEFT JOIN " . Field::_tablename() . " AS tF ON tF.pid = tM.pid AND tF.classname = 'RAAS\\CMS\\Material_Type' AND tF.datatype IN ('text', 'tel', 'email', 'url', 'textarea', 'htmlarea')
                       LEFT JOIN " . Field::_dbprefix() . "cms_data AS tD ON tD.pid = tM.id AND tD.fid = tF.id 
                           WHERE tP.vis AND NOT tP.response_code AND tM.vis AND (tM.name LIKE " . $SQL_search_string . " OR tM.description LIKE " . $SQL_search_string . " OR tD.value LIKE " . $SQL_search_string . ") 
                             ";
    if ((array)$config['material_types']) {
        $SQL_query .= " AND tM.pid IN (" . implode(", ", array_map('intval', (array)$config['material_types'])) . ")
                        ";
    }
    if ((array)$config['pages']) {
        $SQL_query .= " AND tP.id IN (" . implode(", ", array_map('intval', (array)$config['pages'])) . ") 
                        ";
    }
    if ($config['languages'] = array_filter((array)$config['languages'])) {
        $temp = array_map(function($x) { return "'" . \RAAS\Application::i()->SQL->real_escape_string($x) . "'"; }, (array)$config['languages']);
        $SQL_query .= " AND tP.lang IN (" . implode(", ", $temp) . ") 
                        ";
    }
                             
    $SQL_query .= "       GROUP BY tP.id, tM.id 
                          ";
    if (!$config['material_types'] || in_array(0, (array)$config['material_types'])) {
        $SQL_query .= "   UNION ALL 
                          -- Поиск по страницам
                          SELECT tP.id AS page_id, 
                                 0 AS material_id, 
                                 tP.name AS page_name, 
                                 0 AS material_name, 
                                 0 AS nat,
                                 MAX(tP.name LIKE " . $SQL_search_string . ") AS rPname, 
                                 MAX(tD.value LIKE " . $SQL_search_string . ") AS tPdata,
                                 0 AS rMname, 
                                 MAX(tB.widget LIKE " . $SQL_search_string . ") AS rMdescription, 
                                 0 AS rMdata
                            FROM " . Page::_tablename() . " AS tP
                       LEFT JOIN " . Field::_tablename() . " AS tF ON tF.pid = 0 AND tF.classname = 'RAAS\\CMS\\Material_Type' AND tF.datatype IN ('text', 'tel', 'email', 'url', 'textarea', 'htmlarea')
                       LEFT JOIN " . Field::_dbprefix() . "cms_data AS tD ON tD.pid = tP.id AND tD.fid = tF.id
                            JOIN " . Block::_dbprefix() . "cms_blocks_pages_assoc AS tBPA ON tBPA.page_id = tP.id
                            JOIN " . Block::_tablename() . " AS tB ON tB.id = tBPA.block_id AND tB.vis AND tB.block_type IN ('RAAS\\\\CMS\\\\Block_HTML', 'RAAS\\\\CMS\\\\Block_PHP')
                           WHERE tP.vis AND NOT tP.response_code AND (tP.name LIKE " . $SQL_search_string . " OR tD.value LIKE " . $SQL_search_string . " OR tB.widget LIKE " . $SQL_search_string . ")
                             ";
    if ((array)$config['pages']) {
        $SQL_query .= " AND tP.id IN (" . implode(", ", array_map('intval', (array)$config['pages'])) . ") 
                        ";
    }
    if ($config['languages'] = array_filter((array)$config['languages'])) {
        $temp = array_map(function($x) { return "'" . \RAAS\Application::i()->SQL->real_escape_string($x) . "'"; }, (array)$config['languages']);
        $SQL_query .= " AND tP.lang IN (" . implode(", ", $temp) . ") 
                        ";
    }
                             
    $SQL_query .= "        GROUP BY tP.id
                      ";
    }
    $SQL_query .= "   ) AS tSr
                  GROUP BY id, mat_id
                  ORDER BY srPname DESC, srPdata DESC, srMname DESC, srMdata DESC, srMdescription DESC";
    $Pages = null;
    if (isset($config['pages_var_name'], $config['rows_per_page']) && (int)$config['rows_per_page']) {
        $Pages = new \SOME\Pages(isset($IN[$config['pages_var_name']]) ? (int)$IN[$config['pages_var_name']] : 1, (int)$config['rows_per_page']);
    }
    $f = function($x) { 
        if (isset($x['mat_id']) && $x['mat_id']) {
            $row = new \RAAS\CMS\Material($x['mat_id']);
            $row->page = new \RAAS\CMS\Page($x['id']);
        } else {
            $row = new \RAAS\CMS\Page($x['id']);
        }
        return $row; 
    };
    $Set = \SOME\SOME::getSQLSet($SQL_query, $Pages, $f);
    if (!$Set) {
        $OUT['localError'] = 'NO_RESULTS_FOUND';
    }
    $OUT['Pages'] = $Pages;
    $OUT['Set'] = $Set;
}
$OUT['search_string'] = $search_string;
return $OUT;