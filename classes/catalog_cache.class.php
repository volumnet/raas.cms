<?php
namespace RAAS\CMS;

use \RAAS\Attachment;

class Catalog_Cache
{
    use CacheTrait;

    protected $_mtype = array();
    protected $_textKeys = array(
        'urn', 'name', 'description', 'meta_title', 'meta_description', 'meta_keywords',
        'h1', 'menu_name', 'breadcrumbs_name', 'article'
    );

    public function __get($var)
    {
        switch ($var) {
            case 'data':
            case 'mtype':
                return $this->{'_' . $var};
                break;
        }
    }


    public function __construct(Material_Type $MType)
    {
        $this->_mtype = $MType;
    }


    public function getCache()
    {
        $t = $this;
        $st = microtime(true);
        $this->clear();

        $SQL_what = array();

        // Категория непосредственно с товарами
        $SQL_from = $SQL_where = array();
        $sort = $order = "";
        if (!$this->_mtype->global_type) {
            $SQL_from['tMPA'] = " LEFT JOIN " . Material::_dbprefix() . "cms_materials_pages_assoc AS tMPA ON tMPA.id = tM.id ";
        }
        $SQL_where[] = " tM.vis ";
        $types = array_merge(array((int)$this->_mtype->id), (array)$this->_mtype->all_children_ids);
        $SQL_where[] = " tM.pid IN (" . implode(", ", $types) . ") ";

        /*** FILTERING ***/
        $SQL_array = array();

        // Набор полей
        if (!$this->_mtype->global_type) {
            $SQL_what['pages_ids'] = "GROUP_CONCAT(DISTINCT tMPA.pid SEPARATOR '@@@') AS pages_ids";
        }
        foreach (array_merge(array($this->_mtype), (array)$this->_mtype->children) as $mtype) {
            foreach ($mtype->selfFields as $row) {
                if (in_array($row->urn, array('price', 'price_old'))) {
                    $temp = "CAST(value AS UNSIGNED)";
                } else {
                    $temp = "value";
                }
                $SQL_what[$row->urn] = "(
                    SELECT GROUP_CONCAT(DISTINCT " . $temp . " ORDER BY fii ASC SEPARATOR '@@@')
                      FROM " . Field::data_table . "
                     WHERE pid = tM.id
                       AND fid = " . (int)$row->id . "
                ) AS `" . Field::_SQL()->real_escape_string($row->urn) . "`";
            }
        }

        /*** QUERY ***/
        Material::_SQL()->query("SET SESSION group_concat_max_len=1000000");
        Material::_SQL()->query("SET SQL_BIG_SELECTS=1");
        $SQL_query = "SELECT SQL_CALC_FOUND_ROWS tM.* " . ($SQL_what ? ", " . implode(", ", $SQL_what) : "")
                   . "  FROM " . Material::_tablename() . " AS tM " . implode(" ", $SQL_from)
                   . ($SQL_where ? " WHERE " . implode(" AND ", $SQL_where) : "")
                   . " GROUP BY tM.id ORDER BY NOT tM.priority, tM.priority ASC ";
        // echo $SQL_query; exit;
        $SQL_result = Material::_SQL()->get($SQL_query);
        $SQL_result = array_map(
            function ($x) use ($t) {
                $y = $x;
                foreach ($y as $key => $val) {
                    if (stristr($val, '@@@')) {
                        $y[$key] = explode('@@@', $val);
                        $y[$key] = array_unique($y[$key]);
                        $y[$key] = array_values($y[$key]);
                        if (count($y[$key]) == 1) {
                            $y[$key] = array_shift($y[$key]);
                        }
                    }
                    $y[$key] = $t->checkDeepNumeric($y[$key], $key);
                }
                return $y;
            },
            $SQL_result
        );

        // print_r ($SQL_result); exit;

        $this->data = $SQL_result;
        $this->save();
    }


    public function getFilename()
    {
        return Package::i()->cacheDir . '/system/raas_cache_materials' . $this->_mtype->id . '.php';
    }


    public function checkDeepNumeric($data, $key = null)
    {
        if (is_array($data)) {
            $temp = array();
            foreach ($data as $key => $val) {
                $temp[$key] = $this->checkDeepNumeric($val, $key);
            }
            return $temp;
        } else {
            $data = trim($data);
            if (is_numeric($data) && (!$key || !in_array($key, $this->_textKeys))) {
                return (float)$data;
            }
            return $data;
        }
    }
}
