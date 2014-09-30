<?php
namespace RAAS\CMS;
$IN = (array)$_GET;
if (!$Block->nat) {
    unset($IN['id']);
}
parse_str(trim($Block->params), $temp);
$IN = array_merge($IN, (array)$temp);
$getField = function($field, $as, array &$SQL_from) {
    $sort = '';
    if (in_array($field, array('name', 'urn', 'description', 'post_date', 'modify_date'))) {
        $sort = "tM." . $field;
    } elseif (is_numeric($field)) {
        if (!isset($SQL_from[$as]) || !$SQL_from[$as]) {
            $SQL_from[$as] = " JOIN " . Field::data_table . " AS " . $as . " ON " . $as . ".pid = tM.id AND " . $as . ".fid = " . (int)$field;
        }
        $sort = $as . ".value";
    }
    return $sort;
};
$getOrder = function($relation, $var) {
    $order = '';
    switch ((string)$relation) {
        case 'asc!':
            $order = " ASC";
            break;
        case 'desc!':
            $order = " DESC";
            break;
        case 'asc':
            $order = (isset($IN[(string)$var]) && strtolower($IN[(string)$var]) == 'desc') ? " DESC" : " ASC";
            break;
        case 'desc':
            $order = (isset($IN[(string)$var]) && strtolower($IN[(string)$var]) == 'asc') ? " ASC" : " DESC";
            break;
    }
    return $order;
};

$OUT = array();
if (isset($IN['id'])) {
    $Item = new Material($IN['id']);
}
if ($Item->id) {
    $OUT['Item'] = $Item;
    $Page->Material = $Item;
    foreach (array('name', 'meta_title', 'meta_keywords', 'meta_description') as $key) {
        if ($Item->$key) {
            $Page->{'old' . ucfirst($key)} = $Page->$key;
            $Page->$key = $Item->$key;
        }
    }
} else {
    $SQL_from = $SQL_where = array();
    $sort = $order = "";
    $MType = new Material_Type((int)$config['material_type']);
    if (!$MType->global_type) {
        $SQL_from['tMPA'] = " JOIN " . Material::_dbprefix() . "cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id ";
    }
    $SQL_where[] = " tM.vis ";
    $SQL_where[] = " tM.pid = " . (int)$MType->id;
    if (!$MType->global_type) {
        $SQL_where[] = " tMPA.pid = " . (int)$Page->id;
    }
    
    /*** FILTERING ***/
    if (isset($config['filter'])) {
        $SQL_array = array();
        foreach((array)$config['filter'] as $row) {
            if (isset($row['var'], $row['relation'], $row['field'], $IN[$row['var']])) {
                $tmp_field = $getField($row['field'], 't' . $row['field'], $SQL_from);
                switch ($row['relation']) {
                    case '=': case '<=': case '>=':
                        $SQL_array[$row['var']][] = "(" . $tmp_field . " " . $row['relation'] . " '" . Field::_SQL()->real_escape_string($IN[$row['var']]) . "')";
                        break;
                    case 'LIKE':
                        $SQL_array[$row['var']][] = "(" . $tmp_field . " LIKE '%" . Field::_SQL()->escape_like($IN[$row['var']]) . "%')";
                        break;
                    case 'CONTAINED':
                        $SQL_array[$row['var']][] = "('" . Field::_SQL()->real_escape_string($IN[$row['var']]) . "' LIKE CONCAT('%', " . $tmp_field . ", '%'))";
                        break;
                    case 'FULLTEXT':
                        $SQL_array[$row['var']][] = "(MATCH (" . $tmp_field . ") AGAINST('" . Field::_SQL()->real_escape_string($IN[$row['var']]) . "' IN NATURAL LANGUAGE MODE))";
                        break;
                }
            }
        }
        foreach ($SQL_array as $key => $arr) {
            $SQL_where[$key] = $arr ? "(" . implode(" OR ", $arr) . ")" : "";
        }
    }
    
    /*** SORTING ***/
    if (isset($config['sort_var_name'], $IN[(string)$config['sort_var_name']], $config['sort'])) {
        foreach ((array)$config['sort'] as $row) {
            if (isset($row['var'], $row['field']) && $IN[(string)$config['sort_var_name']] == $row['var']) {
                $sort = $getField($row['field'], 'tOr', $SQL_from);
                $order = $getOrder(isset($row['relation']) ? (string)$row['relation'] : '', isset($row['order_var_name']) ? (string)$row['order_var_name'] : '');
                break;
            }
        }
    }
    if (!$sort && isset($config['sort_field_default'])) {
        $sort = $getField($config['sort_field_default'], 'tOr', $SQL_from);
        $order = $getOrder(
            isset($config['sort_order_default']) ? (string)$config['sort_order_default'] : '', isset($row['order_var_name']) ? (string)$row['order_var_name'] : ''
        );
    }
    if ($sort) {
        $OUT['sort'] = $sort;
    }
    if ($order) {
        $OUT['order'] = $order;
    }
    
    /*** QUERY ***/
    $SQL_query = "SELECT SQL_CALC_FOUND_ROWS tM.* FROM " . Material::_tablename() . " AS tM " . implode(" ", $SQL_from)
               . ($SQL_where ? " WHERE " . implode(" AND ", $SQL_where) : "")
               . " GROUP BY tM.id " . ($sort ? " ORDER BY " . $sort . $order : "");
    $Pages = null;
    if (isset($config['pages_var_name'], $config['rows_per_page']) && (int)$config['rows_per_page']) {
        $Pages = new \SOME\Pages(isset($IN[$config['pages_var_name']]) ? (int)$IN[$config['pages_var_name']] : 1, (int)$config['rows_per_page']);
    }
    $Set = Material::getSQLSet($SQL_query, $Pages);
    $OUT['Set'] = $Set;
    $OUT['MType'] = $MType;
    if ($Pages !== null) {
        $OUT['Pages'] = $Pages;
    }
    
}
return $OUT;