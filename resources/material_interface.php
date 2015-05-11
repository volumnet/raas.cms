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
            // 2015-03-31, AVS: заменил JOIN на LEFT JOIN, т.к. если добавить новое поле и сделать сортировку по нему, материалы пропадают
            $SQL_from[$as] = " LEFT JOIN " . Field::data_table . " AS " . $as . " ON " . $as . ".pid = tM.id AND " . $as . ".fid = " . (int)$field;
        }
        $sort = $as . ".value";
    }
    return $sort;
};
$getOrder = function($relation, $var) use ($IN) {
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
if (!$Page->Material && isset($IN['id'])) {
    // Старый способ - по id
    $Page->Material = $Item = new Material($IN['id']);
    if (((int)$Item->id == (int)$IN['id']) && ($Item->pid == $config['material_type']) && (int)$config['legacy']) {
        // Если материал действительно к месту, перенаправляем на новый адрес
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $Item->url); 
        exit;
    } else {
        // Такого материала нет, возвращаем (не обрабатываем). Далее контроллер перекинет на 404
        return;
    }
}
if ($Page->Material && $Block->nat) {
    $Item = $Page->Material;
    if ($Page->initialURL != $Item->url) {
        // Адреса не совпадают
        if ((int)$config['legacy'] && ($Item->pid == $config['material_type'])) {
            // Установлена переадресация
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: http://' . $_SERVER['HTTP_HOST'] . $Item->url); 
            exit;
        } else {
            return;
        }
    }
    $OUT['Item'] = $Item;
    foreach (array('name', 'meta_title', 'meta_keywords', 'meta_description') as $key) {
        $Page->{'old' . ucfirst($key)} = $Page->$key;
        $Page->$key = $Item->$key;
    }
    $Item->proceed = true;
} else {
    $SQL_from = $SQL_where = array();
    $sort = $order = "";
    // Права доступа
    $SQL_from['tA'] = " LEFT JOIN " . Material::_dbprefix() . "cms_access_materials_cache AS tA ON tA.material_id = tM.id AND tA.uid = " . (int)Controller_Frontend::i()->user->id;
    $SQL_where[] = " (tA.allow OR (tA.allow IS NULL)) ";

    $MType = new Material_Type((int)$config['material_type']);
    if (!$MType->global_type) {
        $SQL_from['tMPA'] = " JOIN " . Material::_dbprefix() . "cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id ";
    }
    $SQL_where[] = " tM.vis ";
    $types = array_merge(array((int)$MType->id), (array)$MType->all_children_ids);
    $SQL_where[] = " tM.pid IN (" . implode(", ", $types) . ") ";
    if (!$MType->global_type) {
        $SQL_where[] = " tMPA.pid = " . (int)$Page->id;
    }
    
    /*** FILTERING ***/
    if ($Block->filter) {
        $SQL_array = array();
        foreach((array)$Block->filter as $row) {
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
            $SQL_where[$key] = $arr ? "(" . implode(" AND ", $arr) . ")" : "";
        }
    }
    
    /*** SORTING ***/
    if (isset($config['sort_var_name'], $IN[(string)$config['sort_var_name']]) && $Block->sort) {
        foreach ((array)$Block->sort as $row) {
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
               . " GROUP BY tM.id ORDER BY NOT tM.priority, tM.priority ASC " . ($sort ? ", " . $sort . $order : "");
    $Pages = null;
    if (isset($config['pages_var_name'], $config['rows_per_page']) && (int)$config['rows_per_page']) {
        $Pages = new \SOME\Pages(isset($IN[$config['pages_var_name']]) ? (int)$IN[$config['pages_var_name']] : 1, (int)$config['rows_per_page']);
    }
    $Set = Material::getSQLSet($SQL_query, $Pages);
    $Set = array_filter($Set, function($x) { return $x->currentUserHasAccess(); });
    $OUT['Set'] = $Set;
    $OUT['MType'] = $MType;
    if ($Pages !== null) {
        $OUT['Pages'] = $Pages;
    }
    
}
return $OUT;